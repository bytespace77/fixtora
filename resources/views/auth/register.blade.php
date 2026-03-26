@extends('layouts.auth')

@section('title', 'Register - Fixtora')

@section('content')
<div class="auth-header">
    <div class="brand-logo">F</div>
    <h1>Join Fixtora</h1>
    <p>Create your concierge account</p>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        Please fix the errors below
    </div>
@endif

<form method="POST" action="{{ route('register') }}">
    @csrf

    <div class="form-group">
        <label for="name">Full Name</label>
        <input 
            id="name" 
            type="text" 
            name="name" 
            value="{{ old('name') }}" 
            required 
            autocomplete="name"
            placeholder="John Doe"
            class="form-input {{ $errors->has('name') ? 'is-invalid' : '' }}"
        >
        @error('name')
            <div class="error">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="email">Email Address</label>
        <input 
            id="email" 
            type="email" 
            name="email" 
            value="{{ old('email') }}" 
            required 
            autocomplete="email"
            placeholder="you@example.com"
            class="form-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
        >
        @error('email')
            <div class="error">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="password">Password</label>
        <input 
            id="password" 
            type="password" 
            name="password" 
            required 
            autocomplete="new-password"
            placeholder="Min. 8 characters"
            class="form-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
        >
        @error('password')
            <div class="error">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="password-confirm">Confirm Password</label>
        <input 
            id="password-confirm" 
            type="password" 
            name="password_confirmation" 
            required 
            autocomplete="new-password"
            placeholder="Confirm password"
            class="form-input"
        >
    </div>

    <button type="submit" class="btn-submit">Create Account</button>

    <div class="auth-footer">
        Already have an account? <a href="{{ route('login') }}">Sign in here</a>
    </div>
</form>
@endsection