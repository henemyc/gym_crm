<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#f5f6f7">
    <title>@yield('title', 'Sign in') · GymXBook CRM</title>
    @php
        $publicBase = rtrim(request()->getBaseUrl(), '/');
        $cssFile = public_path('css/app.css');
        $jsFile = public_path('js/app.js');
        $cssVersion = is_file($cssFile) ? filemtime($cssFile) : '1';
        $jsVersion = is_file($jsFile) ? filemtime($jsFile) : '1';
    @endphp
    <link rel="stylesheet" href="{{ $publicBase }}/css/app.css?v={{ $cssVersion }}">
    <script src="{{ $publicBase }}/js/app.js?v={{ $jsVersion }}" defer></script>
</head>
<body class="auth-page auth-minimal-page">
    @include('layouts.partials.icons')
    <main class="auth-minimal-shell">
        <a class="brand brand-auth auth-minimal-brand" href="{{ route('login') }}" aria-label="GymXBook CRM home">
            <span class="brand-mark"><x-icon name="spark" size="19" /></span>
            <span class="brand-copy"><strong>GymXBook</strong><small>GROWTH DESK</small></span>
        </a>
        <div class="auth-wrap">
            <section class="auth-card">
                @if (session('success'))
                    <div class="alert alert-success"><x-icon name="check-circle" size="18" />{{ session('success') }}</div>
                @endif
                @yield('content')
            </section>
            <footer class="auth-foot"><span>© {{ date('Y') }} <span class="brand-word">GymXBook</span> CRM</span><span aria-hidden="true">·</span><span>Private workspace · Secure sign-in</span></footer>
        </div>
    </main>
</body>
</html>
