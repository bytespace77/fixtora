@extends('layouts.app')
@section('title', 'Super Admin Dashboard – Fixtora')

@section('styles')
<style>
/* ── Page header ─────────────────────────────────────────── */
.page-header { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:24px; }
.page-header h1 { font-size:24px; font-weight:800; letter-spacing:-.6px; color:var(--navy); }
.page-header p  { font-size:13px; color:var(--muted); margin-top:4px; }
.superadmin-badge { display:inline-flex; align-items:center; gap:5px; font-size:11px; font-weight:700; padding:3px 10px; border-radius:20px; background:#fef3c7; color:#92400e; letter-spacing:.4px; text-transform:uppercase; margin-left:10px; }

/* ── Stat cards ──────────────────────────────────────────── */
.stat-grid { display:grid; grid-template-columns:repeat(5,1fr); gap:16px; margin-bottom:28px; }
.stat-card { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); padding:20px; }
.stat-card .label { font-size:11px; font-weight:700; color:var(--muted); letter-spacing:.5px; text-transform:uppercase; margin-bottom:10px; }
.stat-card .value { font-size:30px; font-weight:800; letter-spacing:-1px; color:var(--navy); line-height:1; }
.stat-card .sub   { font-size:12px; color:var(--muted); margin-top:6px; }
.stat-card.green  { border-left:3px solid var(--green); }
.stat-card.red    { border-left:3px solid var(--red); }
.stat-card.blue   { border-left:3px solid var(--blue); }

/* ── Table ───────────────────────────────────────────────── */
.card { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); }
.card-header { display:flex; align-items:center; justify-content:space-between; padding:18px 22px; border-bottom:1px solid var(--border); }
.card-header h2 { font-size:15px; font-weight:700; color:var(--text); }
.btn { display:inline-flex; align-items:center; gap:6px; padding:8px 16px; border-radius:var(--radius-sm); font-size:13px; font-weight:600; cursor:pointer; border:none; font-family:inherit; text-decoration:none; transition:all .12s; }
.btn-primary { background:var(--blue); color:#fff; }
.btn-primary:hover { background:var(--blue-2); }
.btn-sm { padding:5px 12px; font-size:12px; }
.btn-outline { background:transparent; color:var(--text-2); border:1px solid var(--border-2); }
.btn-outline:hover { border-color:var(--blue); color:var(--blue); background:var(--blue-bg); }
.btn-danger { background:transparent; color:var(--red); border:1px solid var(--border-2); }
.btn-danger:hover { background:var(--red-bg); border-color:var(--red); }

table { width:100%; border-collapse:collapse; }
thead th { padding:10px 16px; text-align:left; font-size:11px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.4px; border-bottom:1px solid var(--border); background:var(--bg); }
tbody td { padding:14px 16px; font-size:13px; color:var(--text-2); border-bottom:1px solid var(--border); vertical-align:middle; }
tbody tr:last-child td { border-bottom:none; }
tbody tr:hover td { background:#fafbfd; }

.company-name { font-weight:700; color:var(--text); font-size:13.5px; }
.company-slug { font-size:11px; color:var(--muted); font-family:monospace; margin-top:2px; }

.badge { display:inline-block; padding:3px 9px; border-radius:20px; font-size:11px; font-weight:700; }
.badge-active   { background:var(--green-bg); color:var(--green); }
.badge-inactive { background:var(--red-bg); color:var(--red); }

.stat-pill { display:inline-flex; align-items:center; gap:5px; font-size:12px; color:var(--muted); }
.stat-pill strong { color:var(--text); font-weight:700; }

.actions-cell { display:flex; gap:6px; flex-wrap:wrap; }

/* Alert */
.alert { padding:12px 18px; border-radius:var(--radius-sm); margin-bottom:18px; font-size:13px; font-weight:500; }
.alert-success { background:var(--green-bg); color:var(--green); border:1px solid #bbf7d0; }
</style>
@endsection

@section('content')
<div class="page-header">
  <div>
    <h1>
      Super Admin Dashboard
      <span class="superadmin-badge">
        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
        Super Admin
      </span>
    </h1>
    <p>Platform-wide overview — all companies, users and tickets.</p>
  </div>
  <a href="{{ route('superadmin.companies.create') }}" class="btn btn-primary">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    New Company
  </a>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

{{-- ── Stat cards ── --}}
<div class="stat-grid">
  <div class="stat-card blue">
    <div class="label">Total Companies</div>
    <div class="value">{{ $totalCompanies }}</div>
    <div class="sub">registered on platform</div>
  </div>
  <div class="stat-card green">
    <div class="label">Active</div>
    <div class="value">{{ $activeCompanies }}</div>
    <div class="sub">companies active</div>
  </div>
  <div class="stat-card red">
    <div class="label">Inactive</div>
    <div class="value">{{ $inactiveCompanies }}</div>
    <div class="sub">companies deactivated</div>
  </div>
  <div class="stat-card">
    <div class="label">Total Users</div>
    <div class="value">{{ $totalUsers }}</div>
    <div class="sub">across all companies</div>
  </div>
  <div class="stat-card">
    <div class="label">Total Tickets</div>
    <div class="value">{{ $totalTickets }}</div>
    <div class="sub">across all companies</div>
  </div>
</div>

{{-- ── Companies table ── --}}
<div class="card">
  <div class="card-header">
    <h2>All Companies</h2>
    <a href="{{ route('superadmin.companies.index') }}" class="btn btn-outline btn-sm">Manage Companies</a>
  </div>

  @if($companyStats->isEmpty())
    <div style="padding:40px; text-align:center; color:var(--muted);">No companies found. <a href="{{ route('superadmin.companies.create') }}" style="color:var(--blue);">Create one now →</a></div>
  @else
  <table>
    <thead>
      <tr>
        <th>Company</th>
        <th>Status</th>
        <th>Users</th>
        <th>Total Tickets</th>
        <th>Open Tickets</th>
        <th>Resolved</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      @foreach($companyStats as $c)
      <tr>
        <td>
          <div class="company-name">{{ $c['name'] }}</div>
          <div class="company-slug">{{ $c['slug'] }}</div>
        </td>
        <td>
          @if($c['is_active'])
            <span class="badge badge-active">Active</span>
          @else
            <span class="badge badge-inactive">Inactive</span>
          @endif
        </td>
        <td><strong>{{ $c['users_count'] }}</strong></td>
        <td><strong>{{ $c['tickets_count'] }}</strong></td>
        <td>
          <span style="color:{{ $c['open_tickets'] > 0 ? 'var(--orange)' : 'var(--muted)' }}; font-weight:{{ $c['open_tickets'] > 0 ? 700 : 400 }}">
            {{ $c['open_tickets'] }}
          </span>
        </td>
        <td><span style="color:var(--green); font-weight:600;">{{ $c['resolved_tickets'] }}</span></td>
        <td>
          <div class="actions-cell">
            <a href="{{ route('superadmin.companies.show', $c['id']) }}" class="btn btn-outline btn-sm">View</a>
            <a href="{{ route('superadmin.companies.edit', $c['id']) }}" class="btn btn-outline btn-sm">Edit</a>
            <form method="POST" action="{{ route('superadmin.companies.toggle', $c['id']) }}" style="display:inline;">
              @csrf @method('PATCH')
              <button type="submit" class="btn btn-sm {{ $c['is_active'] ? 'btn-danger' : 'btn-outline' }}">
                {{ $c['is_active'] ? 'Deactivate' : 'Activate' }}
              </button>
            </form>
          </div>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @endif
</div>
@endsection