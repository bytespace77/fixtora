@extends('layouts.auth')

@section('title', 'Confirm Password — Fixtora')

@section('content')

<div class="f-eyebrow">Security Check</div>
<div class="f-title">Confirm your<br>password</div>
<div class="f-sub">Please verify your password before continuing.</div>

@if ($errors->any())
<div class="alert alert-danger">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
    </svg>
    {{ $errors->first() }}
</div>
@endif

<form method="POST" action="{{ route('password.confirm') }}" novalidate>
    @csrf

    <div class="f-group">
        <label class="f-label" for="password">Password</label>
        <div class="f-wrap">
            <svg class="f-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <rect x="3" y="11" width="18" height="11" rx="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
            <input id="password" type="password" name="password"
                required autocomplete="current-password" placeholder="Enter your password"
                class="f-input has-toggle {{ $errors->has('password') ? 'is-invalid' : '' }}">
            <button type="button" class="f-toggle" onclick="togglePw('password','eye-pw')">
                <svg id="eye-pw" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                </svg>
            </button>
        </div>
        @error('password') <div class="f-error">{{ $message }}</div> @enderror
    </div>

    <button type="submit" class="f-btn">Confirm Password</button>

    @if (Route::has('password.request'))
    <div class="f-footer">
        <a href="{{ route('password.request') }}">Forgot your password?</a>
    </div>
    @endif

</form>

@endsection
