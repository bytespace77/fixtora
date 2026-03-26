@extends('layouts.app')

@section('title', 'My Profile - Fixtora')

@section('content')
<div class="container py-5">
    <div class="page-header">
        <div>
            <h1>My Profile</h1>
            <p class="subtitle">Manage your profile information</p>
        </div>
    </div>
    <div class="card mt-4">
        <div class="card-body">
            <p><strong>Name:</strong> {{ Auth::user()->name }}</p>
            <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
            <p class="text-muted mt-3">Profile management options coming soon...</p>
        </div>
    </div>
</div>
@endsection