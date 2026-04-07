@extends('layouts.auth')

@section('title', 'Create Account — Fixtora')

@section('content')

<div class="f-eyebrow">Get Started</div>
<div class="f-title">Create your account</div>
<div class="f-sub">Join the Fixtora concierge platform today</div>

@if ($errors->any())
<div class="alert alert-danger">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
    </svg>
    Please fix the errors below
</div>
@endif

<form method="POST" action="{{ route('register') }}" novalidate>
    @csrf

    {{-- ✅ NEW: Company Name field --}}
    <div class="f-group">
        <label class="f-label" for="company_name">Company Name</label>
        <div class="f-wrap">
            <svg class="f-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                <polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
            <input id="company_name" type="text" name="company_name" value="{{ old('company_name') }}"
                required autocomplete="organization" placeholder="e.g. Acme Sdn Bhd"
                class="f-input {{ $errors->has('company_name') ? 'is-invalid' : '' }}">
        </div>
        @error('company_name') <div class="f-error">{{ $message }}</div> @enderror
    </div>

    <div class="f-group">
        <label class="f-label" for="name">Full Name</label>
        <div class="f-wrap">
            <svg class="f-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
            </svg>
            <input id="name" type="text" name="name" value="{{ old('name') }}"
                required autocomplete="name" placeholder="John Doe"
                class="f-input {{ $errors->has('name') ? 'is-invalid' : '' }}">
        </div>
        @error('name') <div class="f-error">{{ $message }}</div> @enderror
    </div>

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

    <div class="f-group">
        <label class="f-label" for="phone">Phone Number <span style="font-weight:400;opacity:.6">(optional)</span></label>
        <div class="f-wrap">
            <svg class="f-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.45 2 2 0 0 1 3.59 1.27h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.91a16 16 0 0 0 6.16 6.16l.91-.91a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
            </svg>
            <input id="phone" type="tel" name="phone" value="{{ old('phone') }}"
                autocomplete="tel" placeholder="+60 12-345 6789"
                class="f-input {{ $errors->has('phone') ? 'is-invalid' : '' }}">
        </div>
        @error('phone') <div class="f-error">{{ $message }}</div> @enderror
    </div>

    <div class="f-group">
        <label class="f-label" for="password">Password</label>
        <div class="f-wrap">
            <svg class="f-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <rect x="3" y="11" width="18" height="11" rx="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
            <input id="password" type="password" name="password"
                required autocomplete="new-password" placeholder="Min. 8 characters"
                class="f-input has-toggle {{ $errors->has('password') ? 'is-invalid' : '' }}">
            <button type="button" class="f-toggle" onclick="togglePw('password','eye1')">
                <svg id="eye1" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
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
                required autocomplete="new-password" placeholder="Confirm your password"
                class="f-input has-toggle">
            <button type="button" class="f-toggle" onclick="togglePw('password-confirm','eye2')">
                <svg id="eye2" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                </svg>
            </button>
        </div>
    </div>

    <button type="submit" class="f-btn">Create Account</button>

    <div class="f-footer">
        Already have an account? <a href="{{ route('login') }}">Sign in</a>
    </div>

</form>

@endsection