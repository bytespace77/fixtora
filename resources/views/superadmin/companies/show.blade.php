@extends('layouts.app')
@section('title', $company->name . ' – Fixtora')

@section('styles')
<style>
.page-header { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:24px; }
.page-header h1 { font-size:24px; font-weight:800; letter-spacing:-.6px; color:var(--navy); }
.page-header p  { font-size:13px; color:var(--muted); margin-top:4px; }

.btn { display:inline-flex; align-items:center; gap:6px; padding:8px 16px; border-radius:var(--radius-sm); font-size:13px; font-weight:600; cursor:pointer; border:none; font-family:inherit; text-decoration:none; transition:all .12s; }
.btn-primary { background:var(--blue); color:#fff; }
.btn-primary:hover { background:var(--blue-2); }
.btn-sm { padding:5px 12px; font-size:12px; }
.btn-outline { background:transparent; color:var(--text-2); border:1px solid var(--border-2); }
.btn-outline:hover { border-color:var(--blue); color:var(--blue); background:var(--blue-bg); }
.btn-danger { background:transparent; color:var(--red); border:1px solid var(--border-2); }
.btn-danger:hover { background:var(--red-bg); border-color:var(--red); }

.breadcrumb { display:flex; align-items:center; gap:6px; font-size:12.5px; color:var(--muted); margin-bottom:16px; }
.breadcrumb a { color:var(--blue); text-decoration:none; }
.breadcrumb a:hover { text-decoration:underline; }

.grid-2 { display:grid; grid-template-columns:1fr 2fr; gap:20px; align-items:start; }

.card { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); }
.card-header { display:flex; align-items:center; justify-content:space-between; padding:18px 22px; border-bottom:1px solid var(--border); }
.card-header h2 { font-size:15px; font-weight:700; color:var(--text); }
.card-body { padding:22px; }

.field-row { display:flex; align-items:baseline; justify-content:space-between; padding:11px 0; border-bottom:1px solid var(--border); font-size:13px; }
.field-row:last-child { border-bottom:none; }
.field-label { color:var(--muted); font-weight:600; font-size:12px; text-transform:uppercase; letter-spacing:.4px; }
.field-value { font-weight:600; color:var(--text); }

.stat-row { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; margin-bottom:20px; }
.stat-mini { background:var(--bg); border-radius:var(--radius-sm); padding:14px 16px; text-align:center; }
.stat-mini .num { font-size:26px; font-weight:800; color:var(--navy); letter-spacing:-1px; }
.stat-mini .lbl { font-size:11px; color:var(--muted); font-weight:600; letter-spacing:.4px; text-transform:uppercase; margin-top:4px; }

.badge { display:inline-block; padding:3px 9px; border-radius:20px; font-size:11px; font-weight:700; }
.badge-active   { background:var(--green-bg); color:var(--green); }
.badge-inactive { background:var(--red-bg); color:var(--red); }

table { width:100%; border-collapse:collapse; }
thead th { padding:10px 16px; text-align:left; font-size:11px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.4px; border-bottom:1px solid var(--border); background:var(--bg); }
tbody td { padding:12px 16px; font-size:13px; color:var(--text-2); border-bottom:1px solid var(--border); vertical-align:middle; }
tbody tr:last-child td { border-bottom:none; }
tbody tr:hover td { background:#fafbfd; }
</style>
@endsection

@section('content')
<div class="breadcrumb">
  <a href="{{ route('superadmin.dashboard') }}">Super Admin</a>
  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
  <a href="{{ route('superadmin.companies.index') }}">Companies</a>
  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
  {{ $company->name }}
</div>

<div class="page-header">
  <div>
    <h1>{{ $company->name }}</h1>
    <p>Company detail — users, ticket counts and settings.</p>
  </div>
  <div style="display:flex; gap:8px;">
    <a href="{{ route('superadmin.companies.edit', $company) }}" class="btn btn-outline">Edit</a>
    <form method="POST" action="{{ route('superadmin.companies.toggle', $company) }}">
      @csrf @method('PATCH')
      <button type="submit" class="btn {{ $company->is_active ? 'btn-danger' : 'btn-primary' }}"
        onclick="return confirm('{{ $company->is_active ? 'Deactivate' : 'Activate' }} this company?')">
        {{ $company->is_active ? 'Deactivate' : 'Activate' }}
      </button>
    </form>
  </div>
</div>

<div class="grid-2">
  {{-- Left column: company info --}}
  <div class="card">
    <div class="card-header"><h2>Company Info</h2></div>
    <div class="card-body">
      <div class="field-row">
        <span class="field-label">Name</span>
        <span class="field-value">{{ $company->name }}</span>
      </div>
      <div class="field-row">
        <span class="field-label">Slug</span>
        <span class="field-value" style="font-family:monospace; font-size:12px;">{{ $company->slug }}</span>
      </div>
      <div class="field-row">
        <span class="field-label">Status</span>
        <span>
          @if($company->is_active)
            <span class="badge badge-active">Active</span>
          @else
            <span class="badge badge-inactive">Inactive</span>
          @endif
        </span>
      </div>
      <div class="field-row">
        <span class="field-label">Created</span>
        <span class="field-value">{{ $company->created_at->format('d M Y') }}</span>
      </div>
      <div class="field-row">
        <span class="field-label">Updated</span>
        <span class="field-value">{{ $company->updated_at->format('d M Y') }}</span>
      </div>
    </div>
  </div>

  {{-- Right column: stats + users --}}
  <div>
    {{-- Stats --}}
    <div class="stat-row" style="margin-bottom:20px;">
      <div class="stat-mini">
        <div class="num">{{ $company->users_count }}</div>
        <div class="lbl">Users</div>
      </div>
      <div class="stat-mini">
        <div class="num" style="color:var(--orange);">{{ $openTickets }}</div>
        <div class="lbl">Open Tickets</div>
      </div>
      <div class="stat-mini">
        <div class="num" style="color:var(--green);">{{ $resolvedTickets }}</div>
        <div class="lbl">Resolved</div>
      </div>
    </div>

    {{-- Users table --}}
    <div class="card">
      <div class="card-header"><h2>Users ({{ $users->count() }})</h2></div>
      @if($users->isEmpty())
        <div style="padding:28px; text-align:center; color:var(--muted); font-size:13px;">No users in this company.</div>
      @else
      <table>
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Joined</th>
          </tr>
        </thead>
        <tbody>
          @foreach($users as $user)
          <tr>
            <td><strong>{{ $user->name }}</strong></td>
            <td style="color:var(--muted);">{{ $user->email }}</td>
            <td>
              @if($user->role)
                <span class="badge" style="background:var(--blue-bg); color:var(--blue);">{{ ucfirst($user->role) }}</span>
              @else
                <span style="color:var(--muted-lt);">—</span>
              @endif
            </td>
            <td style="color:var(--muted);">{{ $user->created_at->format('d M Y') }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
      @endif
    </div>
  </div>
</div>
@endsection