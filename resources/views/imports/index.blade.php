@extends('layouts.app')
@section('title', 'Import leads')
@section('page-name', 'Import leads')

@section('content')
    <div class="page-heading"><div><div class="eyebrow"><span class="eyebrow-dot"></span> BRING YOUR PIPELINE TOGETHER</div><h1>Import leads</h1><p class="page-description">Bring your gym prospects in securely, with a chance to review every row before it lands.</p></div></div>
    <div class="import-layout">
        <div class="import-steps" aria-label="Import steps"><div class="import-step active"><span class="import-step-number">1</span><span>Upload</span></div><i class="import-step-line"></i><div class="import-step"><span class="import-step-number">2</span><span>Preview</span></div><i class="import-step-line"></i><div class="import-step"><span class="import-step-number">3</span><span>Map columns</span></div><i class="import-step-line"></i><div class="import-step"><span class="import-step-number">4</span><span>Validate</span></div><i class="import-step-line"></i><div class="import-step"><span class="import-step-number">5</span><span>Import</span></div></div>
        @if ($errors->any())<div class="form-error-summary">{{ $errors->first() }}</div>@endif
        <section class="surface-card form-card">
            <div class="card-heading" style="padding:0 0 16px"><div><h2>Start with a spreadsheet</h2><p>CSV, Excel, and open formats are all welcome.</p></div></div>
            <form method="POST" action="{{ route('imports.upload') }}" enctype="multipart/form-data" data-upload-form>
                @csrf
                <label class="upload-zone" data-upload-zone tabindex="0" role="button" aria-label="Choose or drop a CSV or Excel file">
                    <input type="file" name="file" accept=".csv,.txt,.xlsx,.xls" data-upload-input required>
                    <span class="upload-icon"><x-icon name="upload-cloud" size="25" /></span>
                    <strong data-upload-name>Drop your file here, or <span class="text-brand">browse</span></strong>
                    <p>We’ll keep your file private and show a preview before importing.</p>
                    <span class="btn btn-secondary btn-sm">Choose file</span>
                </label>
                <div class="upload-meta"><span><x-icon name="check-circle" size="13" /> CSV, XLS, XLSX</span><span><x-icon name="shield" size="13" /> Private by default</span><span>Max 10 MB · 5,000 rows</span></div>
                <div class="form-actions"><button class="btn btn-primary" type="submit" data-upload-submit disabled><x-icon name="arrow-right" size="16" /> Continue to preview</button></div>
            </form>
        </section>
        <section class="surface-card table-card" style="margin-top:14px">
            <div class="card-heading"><div><h2>Recent imports</h2><p>Your team's latest import activity.</p></div><span class="status-pill status-orange">{{ $imports->total() }} total</span></div>
            @if ($imports->isEmpty())
                <div class="empty-state empty-state-compact"><span class="empty-icon"><x-icon name="file" size="23" /></span><strong>No imports yet</strong><p>Completed and in-progress imports will appear here.</p></div>
            @else
                <div class="table-scroll"><table class="data-table"><thead><tr><th>File</th><th>Started by</th><th>Status</th><th>Rows</th><th>Imported</th><th>Updated</th><th></th></tr></thead><tbody>@foreach ($imports as $import)<tr><td><div class="gym-cell"><strong>{{ $import->filename }}</strong><small>Import #{{ str_pad((string)$import->id, 4, '0', STR_PAD_LEFT) }}</small></div></td><td>{{ $import->user?->name ?? '—' }}</td><td><span class="status-pill {{ $import->status === 'imported' ? 'status-converted' : ($import->status === 'validated' ? 'status-trial' : 'status-orange') }}">{{ crm_label($import->status) }}</span></td><td>{{ number_format($import->rows_total) }}</td><td>{{ number_format($import->rows_imported) }}</td><td>{{ $import->updated_at->format('d M Y') }}</td><td><a class="subtle-link" href="{{ route('imports.show', ['leadImport'=>$import, 'step'=>$import->status === 'imported' ? 'summary' : null]) }}">{{ $import->status === 'imported' ? 'Summary' : 'Continue' }} <x-icon name="arrow-right" size="14" /></a></td></tr>@endforeach</tbody></table></div>
                @if($imports->hasPages())<div class="table-footer"><span>{{ $imports->firstItem() }}–{{ $imports->lastItem() }} of {{ $imports->total() }}</span><div class="pagination-links">{{ $imports->links('pagination.crm') }}</div></div>@endif
            @endif
        </section>
    </div>
@endsection
