@extends('layouts.guest')
@section('title', 'Choose a new password')
@section('content')
    <a class="back-link" href="{{ route('login') }}"><x-icon name="arrow-left" size="16" /> Back to sign in</a>
    <div class="auth-icon-box"><x-icon name="lock" size="21" /></div>
    <h1>Choose a new password<span class="text-brand">.</span></h1>
    <p class="auth-subtitle">Use at least 8 characters and keep it unique to <span class="brand-word">GymXBook</span>.</p>
    @if ($errors->any()) <div class="alert alert-error"><x-icon name="alert" size="18" />{{ $errors->first() }}</div> @endif
    <form method="POST" action="{{ route('password.update') }}" class="form-stack">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <label class="field-label" for="email">Work email</label><input class="form-control" id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus>
        <label class="field-label" for="password">New password</label><input class="form-control" id="password" type="password" name="password" autocomplete="new-password" required>
        <label class="field-label" for="password_confirmation">Confirm new password</label><input class="form-control" id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" required>
        <button type="submit" class="btn btn-primary btn-block">Update password <x-icon name="arrow-right" size="17" /></button>
    </form>
@endsection
