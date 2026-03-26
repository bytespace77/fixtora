@extends('layouts.auth')

@section('title', 'Login - Fixtora')

@section('content')
<div class="auth-header">
    <div class="brand-logo">F</div>
    <h1>Welcome to Fixtora</h1>
    <p>Architectural Concierge - Ticket Management</p>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        {{ $errors->first() }}
    </div>
@endif

<form method="POST" action="{{ route('login') }}">
    @csrf

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
            autocomplete="current-password"
            placeholder="Enter your password"
            class="form-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
        >
        @error('password')
            <div class="error">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-checkbox">
        <input 
            type="checkbox" 
            name="remember" 
            id="remember" 
            {{ old('remember') ? 'checked' : '' }}
        >
        <label for="remember">Remember Me</label>
    </div>

    <button type="submit" class="btn-submit">Sign In</button>

    <div class="auth-footer">
        <a href="{{ route('password.request') }}">Forgot Your Password?</a>
        <br><br>
        Don't have an account? <a href="{{ route('register') }}">Register here</a>
    </div>
</form>
@endsection