@extends('layouts.guest')

@section('title', 'Sign in')

@section('content')
    <div class="auth-eyebrow"><span class="eyebrow-dot"></span> YOUR GROWTH WORKSPACE</div>
    <h1>Welcome back<span class="text-brand">.</span></h1>
    <p class="auth-subtitle">Sign in to pick up where your team left off.</p>

    @if ($errors->any())
        <div class="alert alert-error"><x-icon name="alert" size="18" /><span>{{ $errors->first() }}</span></div>
    @endif

    <form method="POST" action="{{ route('login.store') }}" class="form-stack">
        @csrf
        <label class="field-label" for="email">Work email</label>
        <div class="input-icon"><x-icon name="mail" size="18" /><input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="you@yourgym.com" autocomplete="username" required autofocus></div>
        @error('email') <span class="field-error">{{ $message }}</span> @enderror

        <div class="field-row field-between"><label class="field-label" for="password">Password</label><a class="link-subtle" href="{{ route('password.request') }}">Forgot password?</a></div>
        <div class="input-icon"><x-icon name="lock" size="18" /><input id="password" name="password" type="password" placeholder="Enter your password" autocomplete="current-password" required><button class="input-action" type="button" data-toggle-password="password" aria-label="Show password"><x-icon name="eye" size="18" /></button></div>
        @error('password') <span class="field-error">{{ $message }}</span> @enderror

        <label class="check-label"><input type="checkbox" name="remember" value="1"><span class="check-box"></span><span>Keep me signed in</span></label>
        <button type="submit" class="btn btn-primary btn-block">Sign in <x-icon name="arrow-right" size="17" /></button>
    </form>
    <div class="auth-assurance"><x-icon name="shield" size="16" /> Your workspace is protected with secure sign-in.</div>
@endsection
