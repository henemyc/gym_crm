@extends('layouts.app')
@section('title', $mode === 'create' ? 'Add lead' : 'Edit lead')
@section('page-name', 'Leads')

@section('content')
    <div class="breadcrumb"><a href="{{ route('leads.index') }}">Leads</a><x-icon name="chevron-right" size="13" /><span>{{ $mode === 'create' ? 'Add lead' : 'Edit lead' }}</span></div>
    <div class="page-heading">
        <div><div class="eyebrow"><span class="eyebrow-dot"></span> LEAD MANAGEMENT</div><h1>{{ $mode === 'create' ? 'Add a lead' : 'Edit lead details' }}</h1><p class="page-description">{{ $mode === 'create' ? 'Capture the essentials and keep the next conversation moving.' : 'Update the information your team needs to make the next move.' }}</p></div>
        <a class="btn btn-secondary" href="{{ $mode === 'edit' ? route('leads.show', $lead) : route('leads.index') }}"><x-icon name="arrow-left" size="16" /> Back</a>
    </div>

    @if ($errors->any())<div class="form-error-summary">Please review the highlighted fields. {{ $errors->first() }}</div>@endif
    @include('leads.partials.fields', ['modal' => false, 'formClass' => 'surface-card form-card'])
@endsection
