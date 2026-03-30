@extends('layouts.auth')

@section('title', 'Reset Password — Fixtora')

@section('content')

<div class="f-eyebrow">Account Recovery</div>
<div class="f-title">Forgot your<br>password?</div>
<div class="f-sub">Enter your email and we'll send you a reset link.</div>

@if (session('status'))
<div class="alert alert-success" style="margin-top:0;margin-bottom:20px;">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
        <polyline points="20 6 9 17 4 12"/>
    </svg>
    {{ session('status') }}
</div>
@endif

@if ($errors->any())
<div class="alert alert-danger">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
    </svg>
    {{ $errors->first() }}
</div>
@endif

<form method="POST" action="{{ route('password.email') }}" novalidate>
    @csrf

    <div class="f-group">
        <label class="f-label" for="email">Email Address</label>
        <div class="f-wrap">
            <svg class="f-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                <polyline points="22,6 12,13 2,6"/>
            </svg>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                required autocomplete="email" placeholder="you@company.com"
                class="f-input {{ $errors->has('email') ? 'is-invalid' : '' }}">
        </div>
        @error('email') <div class="f-error">{{ $message }}</div> @enderror
    </div>

    <button type="submit" class="f-btn">Send Reset Link</button>

    <div class="f-footer">
        Remembered it? <a href="{{ route('login') }}">Back to sign in</a>
    </div>

</form>

@endsection
