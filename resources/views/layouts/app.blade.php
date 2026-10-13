<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="presence-ping-url" content="{{ route('presence.ping') }}">
    <meta name="theme-color" content="#1d2935">
    <title>@yield('title', 'Dashboard') · GymXBook CRM</title>
    @php
        $publicBase = rtrim(request()->getBaseUrl(), '/');
        $cssFile = public_path('css/app.css');
        $jsFile = public_path('js/app.js');
        $cssVersion = is_file($cssFile) ? filemtime($cssFile) : '1';
        $jsVersion = is_file($jsFile) ? filemtime($jsFile) : '1';
    @endphp
    <link rel="stylesheet" href="{{ $publicBase }}/css/app.css?v={{ $cssVersion }}">
    <script src="{{ $publicBase }}/js/app.js?v={{ $jsVersion }}" defer></script>
    @stack('head')
</head>
<body class="app-body">
    @include('layouts.partials.icons')
    <a class="skip-link" href="#main-content">Skip to main content</a>
    <div class="app-shell">
        @include('layouts.partials.sidebar')
        <div class="main-column">
            @include('layouts.partials.topbar')
            <main id="main-content" class="page-main">
                @if (session('success'))
                    <div class="toast toast-success" data-toast role="status"><x-icon name="check-circle" size="18" /><span>{{ session('success') }}</span><button type="button" data-toast-close aria-label="Dismiss"><x-icon name="x" size="16" /></button></div>
                @endif
                @if (session('error'))
                    <div class="toast toast-error" data-toast role="alert"><x-icon name="alert" size="18" /><span>{{ session('error') }}</span><button type="button" data-toast-close aria-label="Dismiss"><x-icon name="x" size="16" /></button></div>
                @endif
                @yield('content')
            </main>
            <footer class="app-footer"><span><span class="brand-word">GymXBook</span> CRM</span><span>Thoughtful follow-through, stronger gyms.</span></footer>
        </div>
    </div>
    <div class="lead-dialog-backdrop" data-lead-dialog hidden aria-hidden="true">
        <section class="lead-dialog" role="dialog" aria-modal="true" aria-labelledby="lead-dialog-title" tabindex="-1"><div data-lead-dialog-content></div></section>
    </div>
    <div class="ajax-toast" data-ajax-toast role="status" aria-live="polite" hidden></div>
    <div class="confirm-backdrop" data-confirm-dialog hidden aria-hidden="true">
        <section class="confirm-dialog" role="alertdialog" aria-modal="true" aria-labelledby="confirm-dialog-title" aria-describedby="confirm-dialog-message">
            <div class="confirm-dialog-icon"><x-icon name="trash" size="19" /></div>
            <div class="confirm-dialog-copy"><span class="eyebrow">PLEASE CONFIRM</span><h2 id="confirm-dialog-title" data-confirm-title>Delete this lead?</h2><p id="confirm-dialog-message" data-confirm-message>This lead will be moved to deleted records. You can’t undo this action.</p></div>
            <div class="confirm-dialog-actions"><button class="btn btn-secondary" type="button" data-confirm-cancel>Cancel</button><button class="btn btn-danger" type="button" data-confirm-accept>Delete lead</button></div>
        </section>
    </div>
    @stack('scripts')
</body>
</html>
