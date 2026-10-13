@extends('layouts.guest')
@section('title', 'Reset password')
@section('content')
    <a class="back-link" href="{{ route('login') }}"><x-icon name="arrow-left" size="16" /> Back to sign in</a>
    <div class="auth-icon-box"><x-icon name="mail" size="21" /></div>
    <h1>Reset your password<span class="text-brand">.</span></h1>
    <p class="auth-subtitle">Enter your work email and we’ll send a secure reset link.</p>
    @if ($errors->any()) <div class="alert alert-error"><x-icon name="alert" size="18" />{{ $errors->first() }}</div> @endif
    <form method="POST" action="{{ route('password.email') }}" class="form-stack">
        @csrf
        <label class="field-label" for="email">Work email</label>
        <div class="input-icon"><x-icon name="mail" size="18" /><input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="you@yourgym.com" required autofocus></div>
        <button type="submit" class="btn btn-primary btn-block">Send reset link <x-icon name="arrow-right" size="17" /></button>
    </form>
@endsection
