@extends('layouts.auth')

@section('title', 'Set New Password — Fixtora')

@section('content')

<div class="f-eyebrow">Account Recovery</div>
<div class="f-title">Set a new<br>password</div>
<div class="f-sub">Choose a strong password for your account.</div>

@if ($errors->any())
<div class="alert alert-danger">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
    </svg>
    {{ $errors->first() }}
</div>
@endif

<form method="POST" action="{{ route('password.update') }}" novalidate>
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <div class="f-group">
        <label class="f-label" for="email">Email Address</label>
        <div class="f-wrap">
            <svg class="f-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                <polyline points="22,6 12,13 2,6"/>
            </svg>
            <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}"
                required autocomplete="email" placeholder="you@company.com"
                class="f-input {{ $errors->has('email') ? 'is-invalid' : '' }}">
        </div>
        @error('email') <div class="f-error">{{ $message }}</div> @enderror
    </div>

    <div class="f-group">
        <label class="f-label" for="password">New Password</label>
        <div class="f-wrap">
            <svg class="f-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <rect x="3" y="11" width="18" height="11" rx="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
            <input id="password" type="password" name="password"
                required autocomplete="new-password" placeholder="Min. 8 characters"
                class="f-input has-toggle {{ $errors->has('password') ? 'is-invalid' : '' }}">
            <button type="button" class="f-toggle" onclick="togglePw('password','eye-pw1')">
                <svg id="eye-pw1" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                </svg>
            </button>
        </div>
        @error('password') <div class="f-error">{{ $message }}</div> @enderror
    </div>

    <div class="f-group">
        <label class="f-label" for="password-confirm">Confirm Password</label>
        <div class="f-wrap">
            <svg class="f-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <rect x="3" y="11" width="18" height="11" rx="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
            <input id="password-confirm" type="password" name="password_confirmation"
                required autocomplete="new-password" placeholder="Confirm new password"
                class="f-input has-toggle">
            <button type="button" class="f-toggle" onclick="togglePw('password-confirm','eye-pw2')">
                <svg id="eye-pw2" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                </svg>
            </button>
        </div>
    </div>

    <button type="submit" class="f-btn">Reset Password</button>

    <div class="f-footer">
        <a href="{{ route('login') }}">Back to sign in</a>
    </div>

</form>

@endsection
