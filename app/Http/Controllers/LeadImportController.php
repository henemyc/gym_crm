<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\LeadImport;
use App\Services\ActivityLogger;
use App\Services\LeadImportService;
use App\Services\LeadSpreadsheetParser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

class LeadImportController extends Controller
{
    public function index(Request $request): View
    {
        $imports = LeadImport::query()->when(! $request->user()->canManageTeam(), fn (Builder $q) => $q->where('user_id', $request->user()->id))
            ->with('user:id,name')->latest()->paginate(12);
        return view('imports.index', compact('imports'));
    }

    public function upload(Request $request, LeadSpreadsheetParser $parser): RedirectResponse
    {
        $validated = $request->validate(['file' => ['required', 'file', 'mimes:csv,txt,xls,xlsx', 'max:10240']]);
        $file = $validated['file'];
        try {
            $path = $file->store('imports/'.$request->user()->id, 'local');
            $parsed = $parser->parse(Storage::disk('local')->path($path), $file->getClientOriginalExtension());
        } catch (Throwable $exception) {
            if (isset($path)) Storage::disk('local')->delete($path);
            return back()->withErrors(['file' => $exception instanceof RuntimeException ? $exception->getMessage() : 'This file could not be read. Please try a valid CSV, XLS, or XLSX file.']);
        }

        $import = LeadImport::create([
            'user_id' => $request->user()->id,
            'filename' => $file->getClientOriginalName(),
            'file_path' => $path,
            'status' => 'uploaded',
            'headers' => $parsed['headers'],
            'mapping' => app(LeadImportService::class)->autoMapping($parsed['headers']),
            'rows_total' => count($parsed['rows']),
            'summary' => ['preview_rows' => array_slice($parsed['rows'], 0, 8)],
        ]);

        return redirect()->route('imports.show', ['leadImport' => $import, 'step' => 'preview'])->with('success', 'File received. Take a moment to review the columns.');
    }

    public function show(Request $request, LeadImport $leadImport, LeadImportService $service): View
    {
        $this->authorizeImport($request, $leadImport);
        $step = $request->query('step', $leadImport->status === 'imported' ? 'summary' : ($leadImport->status === 'validated' ? 'validation' : ($leadImport->status === 'mapped' ? 'mapping' : 'preview')));
        abort_unless(in_array($step, ['preview', 'mapping', 'validation', 'summary'], true), 404);
        $parsed = null;
        $validation = null;
        try {
            if ($step === 'preview' || $step === 'mapping') $parsed = $service->parse($leadImport);
            if ($step === 'validation' && $leadImport->mapping) {
                $validation = $service->validateRows($leadImport, $request->user());
                $validation['errors'] = array_slice($validation['errors'], 0, 100);
            }
        } catch (Throwable $exception) {
            return redirect()->route('imports.index')->with('error', $exception instanceof RuntimeException ? $exception->getMessage() : 'The import file could not be read.');
        }
        $mappingFields = $request->user()->canManageTeam()
            ? LeadImportService::FIELDS
            : array_diff_key(LeadImportService::FIELDS, ['assigned_to' => true]);

        return view('imports.show', compact('leadImport', 'step', 'parsed', 'validation', 'mappingFields'));
    }

    public function saveMapping(Request $request, LeadImport $leadImport, LeadImportService $service): RedirectResponse
    {
        $this->authorizeImport($request, $leadImport);
        abort_if($leadImport->status === 'imported', 409);
        $validFields = implode(',', array_keys(LeadImportService::FIELDS));
        $validated = $request->validate(['mapping' => ['required', 'array'], 'mapping.*' => ['nullable', 'string', 'in:'.$validFields]]);
        $mapping = $validated['mapping'];
        $fields = array_values(array_filter($mapping));
        if (count($fields) !== count(array_unique($fields))) {
            return back()->withErrors(['mapping' => 'Map each GymXBook field only once.']);
        }
        $missing = array_diff(['name', 'gym_name', 'phone'], $fields);
        if ($missing) {
            return back()->withErrors(['mapping' => 'Map Contact name, Gym name, and Phone number to continue.']);
        }
        $leadImport->update(['mapping' => $mapping, 'status' => 'mapped']);
        try {
            $result = $service->validateRows($leadImport, $request->user());
        } catch (RuntimeException $exception) {
            return back()->withErrors(['mapping' => $exception->getMessage()]);
        }
        $leadImport->update([
            'status' => 'validated',
            'rows_total' => $result['total'],
            'rows_valid' => $result['valid_count'],
            'rows_invalid' => $result['invalid_count'],
            'summary' => ['errors' => array_slice($result['errors'], 0, 100), 'valid_count' => $result['valid_count'], 'invalid_count' => $result['invalid_count']],
        ]);

        return redirect()->route('imports.show', ['leadImport' => $leadImport, 'step' => 'validation']);
    }

    public function validateImport(Request $request, LeadImport $leadImport, LeadImportService $service): RedirectResponse
    {
        $this->authorizeImport($request, $leadImport);
        abort_if($leadImport->status === 'imported', 409);
        try {
            $result = $service->validateRows($leadImport, $request->user());
        } catch (RuntimeException $exception) {
            return redirect()->route('imports.show', ['leadImport' => $leadImport, 'step' => 'mapping'])->withErrors(['mapping' => $exception->getMessage()]);
        }
        $leadImport->update([
            'status' => 'validated',
            'rows_total' => $result['total'],
            'rows_valid' => $result['valid_count'],
            'rows_invalid' => $result['invalid_count'],
            'summary' => ['errors' => array_slice($result['errors'], 0, 100), 'valid_count' => $result['valid_count'], 'invalid_count' => $result['invalid_count']],
        ]);

        return redirect()->route('imports.show', ['leadImport' => $leadImport, 'step' => 'validation']);
    }

    public function commit(Request $request, LeadImport $leadImport, LeadImportService $service, ActivityLogger $activityLogger): RedirectResponse
    {
        $this->authorizeImport($request, $leadImport);
        abort_if($leadImport->status !== 'validated', 409, 'Validate this file before importing it.');
        try {
            $result = $service->validateRows($leadImport, $request->user());
        } catch (RuntimeException $exception) {
            return back()->withErrors(['import' => $exception->getMessage()]);
        }

        $imported = 0;
        DB::transaction(function () use ($result, $leadImport, $request, $activityLogger, &$imported): void {
            foreach (array_chunk($result['valid'], 250) as $chunk) {
                foreach ($chunk as $data) {
                    $lead = Lead::create($data);
                    $activityLogger->record($lead, $request->user(), 'lead_imported', 'Lead imported', 'Imported from '.$leadImport->filename.'.', ['import_id' => $leadImport->id]);
                    $imported++;
                }
            }
            $leadImport->update([
                'status' => 'imported',
                'rows_total' => $result['total'],
                'rows_valid' => $result['valid_count'],
                'rows_invalid' => $result['invalid_count'],
                'rows_imported' => $imported,
                'summary' => ['errors' => array_slice($result['errors'], 0, 100), 'valid_count' => $result['valid_count'], 'invalid_count' => $result['invalid_count'], 'imported_count' => $imported],
            ]);
        });

        return redirect()->route('imports.show', ['leadImport' => $leadImport, 'step' => 'summary'])->with('success', $imported.' leads imported successfully.');
    }

    private function authorizeImport(Request $request, LeadImport $leadImport): void
    {
        abort_unless($request->user()->canManageTeam() || $leadImport->user_id === $request->user()->id, 403);
    }
}
