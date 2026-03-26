@extends('layouts.app')

@section('title', 'Tasks - Fixtora')

@section('content')
<div class="container py-5">
    <div class="page-header">
        <div>
            <h1>Tasks</h1>
            <p class="subtitle">Manage your assigned tasks</p>
        </div>
        <button class="btn btn-primary">+ New Task</button>
    </div>
    <div class="card mt-4">
        <div class="card-body">
            <p class="text-muted">Task list will display here...</p>
        </div>
    </div>
</div>
@endsection