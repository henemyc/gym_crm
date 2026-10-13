<div class="lead-dialog-header">
    <div class="lead-dialog-heading-mark"><x-icon name="{{ $mode === 'create' ? 'plus' : 'edit' }}" size="19" /></div>
    <div><span class="eyebrow">LEAD MANAGEMENT</span><h2 id="lead-dialog-title">{{ $mode === 'create' ? 'Add a lead' : 'Edit lead' }}</h2><p>{{ $mode === 'create' ? 'Capture the details and keep the next conversation moving.' : 'Update the record without leaving your pipeline.' }}</p></div>
    <button class="icon-button" type="button" data-lead-dialog-close aria-label="Close dialog"><x-icon name="x" size="19" /></button>
</div>
@include('leads.partials.fields', ['modal' => true, 'formClass' => 'lead-modal-form'])
