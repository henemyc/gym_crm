<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\LeadImport;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class LeadImportService
{
    public const FIELDS = [
        'name' => 'Contact name',
        'gym_name' => 'Gym name',
        'phone' => 'Phone number',
        'location' => 'Location',
        'source' => 'Source',
        'priority' => 'Priority',
        'status' => 'Status',
        'notes' => 'Notes',
        'assigned_to' => 'Assigned to (email or name)',
    ];

    public function __construct(private readonly LeadSpreadsheetParser $parser)
    {
    }

    public function parse(LeadImport $import): array
    {
        $path = Storage::disk('local')->path($import->file_path);
        if (! is_file($path)) throw new RuntimeException('The uploaded file is no longer available. Start a new import.');
        return $this->parser->parse($path, pathinfo($import->filename, PATHINFO_EXTENSION));
    }

    public function autoMapping(array $headers): array
    {
        $aliases = [
            'name' => ['name', 'contact name', 'lead name', 'full name', 'owner name', 'contact'],
            'gym_name' => ['gym name', 'gym', 'business name', 'fitness center', 'fitness centre', 'studio name'],
            'phone' => ['phone', 'phone number', 'mobile', 'mobile number', 'contact number', 'telephone'],
            'location' => ['location', 'city', 'area', 'address'],
            'source' => ['source', 'lead source', 'channel', 'campaign'],
            'priority' => ['priority', 'temperature', 'lead priority'],
            'status' => ['status', 'lead status', 'stage', 'pipeline stage'],
            'notes' => ['notes', 'note', 'remarks', 'remark', 'comments'],
            'assigned_to' => ['assigned to', 'assignee', 'owner email', 'sales rep', 'assigned user'],
        ];
        $used = [];
        $mapping = [];
        foreach ($headers as $index => $header) {
            $normalized = Str::of($header)->lower()->replace(['_', '-'], ' ')->squish()->toString();
            $match = null;
            foreach ($aliases as $field => $options) {
                if (in_array($normalized, $options, true) && ! in_array($field, $used, true)) { $match = $field; break; }
            }
            $mapping[$index] = $match;
            if ($match) $used[] = $match;
        }
        return $mapping;
    }

    /** @return array{total: int, valid: array<int, array<string, mixed>>, errors: array<int, array{row: int, errors: array<int, string>}>, valid_count: int, invalid_count: int} */
    public function validateRows(LeadImport $import, User $actor): array
    {
        $parsed = $this->parse($import);
        $columnMap = [];
        foreach (($import->mapping ?? []) as $index => $field) {
            if ($field && isset(self::FIELDS[$field])) $columnMap[$field] = (int) $index;
        }
        foreach (['name', 'gym_name', 'phone'] as $requiredField) {
            if (! array_key_exists($requiredField, $columnMap)) {
                throw new RuntimeException('Map a column to '.self::FIELDS[$requiredField].' before validation.');
            }
        }

        $existingPhones = [];
        foreach (Lead::query()->visibleTo($actor)->select(['id', 'phone'])->lazyById(1000) as $lead) {
            $normalized = $this->normalizePhone($lead->phone);
            if ($normalized !== '') $existingPhones[$normalized] = true;
        }
        $users = $actor->canManageTeam()
            ? User::query()->where('is_active', true)->get(['id', 'name', 'email'])
            : collect();
        $seen = [];
        $valid = [];
        $errors = [];

        foreach ($parsed['rows'] as $index => $row) {
            $line = $index + 2;
            $value = function (string $field) use ($columnMap, $row): string {
                return isset($columnMap[$field]) ? trim((string) ($row[$columnMap[$field]] ?? '')) : '';
            };
            $name = $value('name');
            $gymName = $value('gym_name');
            $rawPhone = $value('phone');
            $phone = $this->normalizePhone($rawPhone);
            $rowErrors = [];

            if ($name === '') $rowErrors[] = 'Contact name is required.';
            if ($gymName === '') $rowErrors[] = 'Gym name is required.';
            if ($phone === '' || strlen($phone) < 10 || strlen($phone) > 15) $rowErrors[] = 'Phone must contain 10–15 digits.';
            if ($phone !== '' && isset($seen[$phone])) $rowErrors[] = 'Duplicate phone number in this file (also on row '.$seen[$phone].').';
            if ($phone !== '' && isset($existingPhones[$phone])) $rowErrors[] = 'A lead with this phone number already exists.';

            $priority = $this->normalizePriority($value('priority'));
            if ($priority === null) $rowErrors[] = 'Priority must be Hot, Warm, or Cold.';
            $status = $this->normalizeStatus($value('status'));
            if ($status === null) $rowErrors[] = 'Status is not one of the supported pipeline stages.';

            $assigneeId = $actor->canManageTeam() ? $import->user_id : $actor->id;
            $assigneeValue = $value('assigned_to');
            if ($actor->canManageTeam() && $assigneeValue !== '') {
                $assignee = $users->first(fn (User $user) => mb_strtolower($user->email) === mb_strtolower($assigneeValue) || mb_strtolower($user->name) === mb_strtolower($assigneeValue));
                if (! $assignee) $rowErrors[] = 'Assigned to must match an active teammate name or email.';
                else $assigneeId = $assignee->id;
            }

            if ($phone !== '') $seen[$phone] = $line;
            if ($rowErrors) {
                $errors[] = ['row' => $line, 'errors' => $rowErrors];
                continue;
            }

            $valid[] = [
                'name' => Str::limit($name, 120, ''),
                'gym_name' => Str::limit($gymName, 160, ''),
                'phone' => $phone,
                'location' => $value('location') !== '' ? Str::limit($value('location'), 120, '') : null,
                'source' => $value('source') !== '' ? Str::limit($value('source'), 80, '') : 'Import',
                'priority' => $priority,
                'status' => $status,
                'notes' => $value('notes') !== '' ? Str::limit($value('notes'), 10000, '') : null,
                'assigned_to' => $assigneeId,
                'created_by' => $actor->id,
            ];
        }

        return [
            'total' => count($parsed['rows']),
            'valid' => $valid,
            'errors' => $errors,
            'valid_count' => count($valid),
            'invalid_count' => count($errors),
        ];
    }

    private function normalizePhone(string $phone): string
    {
        return preg_replace('/\D+/', '', $phone) ?? '';
    }

    private function normalizePriority(string $priority): ?string
    {
        if ($priority === '') return 'warm';
        $normalized = Str::of($priority)->lower()->trim()->toString();
        return match ($normalized) { 'hot', 'high' => 'hot', 'warm', 'medium', 'mid' => 'warm', 'cold', 'low' => 'cold', default => null };
    }

    private function normalizeStatus(string $status): ?string
    {
        if ($status === '') return 'new';
        $normalized = Str::of($status)->lower()->trim()->replace([' ', '-'], '_')->replaceMatches('/_+/', '_')->toString();
        $normalized = match ($normalized) { 'won', 'closed_won', 'customer' => 'converted', 'meeting' => 'meeting_fixed', 'followup' => 'follow_up', 'closed_lost' => 'lost', default => $normalized };
        return array_key_exists($normalized, Lead::STATUSES) ? $normalized : null;
    }
}
