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
.stat-card.amber  { border-left:3px solid #f59e0b; }

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

/* ── CSAT Section ─────────────────────────────────────────── */
.csat-grid { display:grid; grid-template-columns:260px 1fr; gap:18px; margin-bottom:28px; }
.csat-score-card { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); padding:28px 24px; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; }
.csat-big-score { font-size:56px; font-weight:900; letter-spacing:-3px; color:var(--navy); line-height:1; }
.csat-stars-row { display:flex; gap:3px; justify-content:center; margin:8px 0 4px; }
.csat-star { font-size:22px; }
.csat-star.filled { color:#f59e0b; }
.csat-star.empty  { color:#e2e8f0; }
.csat-score-sub { font-size:12px; color:var(--muted); font-weight:600; }
.csat-badge { display:inline-flex; align-items:center; gap:5px; padding:4px 12px; border-radius:20px; font-size:11px; font-weight:700; margin-top:12px; }
.csat-badge.excellent { background:#fef3c7; color:#92400e; }
.csat-badge.good      { background:#dcfce7; color:#166534; }
.csat-badge.average   { background:#eff6ff; color:#1e40af; }
.csat-badge.poor      { background:#fef2f2; color:#991b1b; }
.csat-badge.none      { background:var(--bg); color:var(--muted); }

.csat-breakdown-card { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); }
.csat-breakdown-body { padding:20px 24px; display:flex; flex-direction:column; gap:10px; }
.csat-bar-row { display:flex; align-items:center; gap:12px; }
.csat-bar-label { font-size:12px; font-weight:700; color:var(--muted); width:14px; text-align:right; flex-shrink:0; display:flex; align-items:center; gap:2px; }
.csat-bar-label .star-icon { color:#f59e0b; font-size:11px; }
.csat-bar-track { flex:1; background:var(--bg); border-radius:99px; height:10px; overflow:hidden; border:1px solid var(--border); }
.csat-bar-fill { height:100%; border-radius:99px; background:#f59e0b; transition:width .4s ease; }
.csat-bar-fill.s5 { background:#22c55e; }
.csat-bar-fill.s4 { background:#86efac; }
.csat-bar-fill.s3 { background:#fbbf24; }
.csat-bar-fill.s2 { background:#fb923c; }
.csat-bar-fill.s1 { background:#f87171; }
.csat-bar-count { font-size:12px; font-weight:700; color:var(--text); width:24px; text-align:right; flex-shrink:0; }

.csat-recent-list { display:flex; flex-direction:column; }
.csat-recent-item { display:flex; align-items:flex-start; gap:14px; padding:14px 22px; border-bottom:1px solid var(--border); transition:background .12s; }
.csat-recent-item:last-child { border-bottom:none; }
.csat-recent-item:hover { background:#fafbfd; }
.csat-av { width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:800; color:#fff; flex-shrink:0; background:linear-gradient(135deg,#2563eb,#7c3aed); }
.csat-info { flex:1; min-width:0; }
.csat-info-top { display:flex; align-items:center; gap:8px; margin-bottom:3px; flex-wrap:wrap; }
.csat-user-name { font-size:13px; font-weight:700; color:var(--navy); }
.csat-company-tag { font-size:10.5px; font-weight:700; color:var(--muted); background:var(--bg); border:1px solid var(--border); padding:1px 7px; border-radius:20px; }
.csat-ticket-ref { font-size:10.5px; color:var(--blue); font-weight:600; text-decoration:none; }
.csat-ticket-ref:hover { text-decoration:underline; }
.csat-mini-stars { display:flex; gap:2px; }
.csat-mini-star { font-size:13px; }
.csat-mini-star.filled { color:#f59e0b; }
.csat-mini-star.empty  { color:#e2e8f0; }
.csat-comment { font-size:12px; color:var(--muted); font-style:italic; margin-top:3px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:480px; }
.csat-time { font-size:11px; color:var(--muted-lt,#94a3b8); font-weight:600; flex-shrink:0; white-space:nowrap; }
.csat-empty { padding:32px; text-align:center; color:var(--muted); font-size:13px; }
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
<div class="stat-grid" style="grid-template-columns:repeat(6,1fr)">
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
  <div class="stat-card amber">
    <div class="label">Avg CSAT</div>
    <div class="value" style="color:{{ $csatAvg >= 4 ? '#16a34a' : ($csatAvg >= 3 ? '#d97706' : '#dc2626') }}">
      {{ $csatAvg ?? '—' }}
    </div>
    <div class="sub">{{ $csatCount }} rating{{ $csatCount !== 1 ? 's' : '' }} submitted</div>
  </div>
</div>

{{-- ── CSAT Overview ── --}}
<div style="margin-bottom:10px;display:flex;align-items:center;justify-content:space-between">
  <div>
    <h2 style="font-size:15px;font-weight:800;color:var(--navy);letter-spacing:-.3px">Customer Satisfaction (CSAT)</h2>
    <p style="font-size:12px;color:var(--muted);margin-top:2px">Ratings submitted by customers after ticket resolution</p>
  </div>
</div>
<div class="csat-grid">
  {{-- Score card --}}
  <div class="csat-score-card">
    @if($csatAvg)
      <div class="csat-big-score">{{ $csatAvg }}</div>
      <div class="csat-stars-row">
        @for($i=1;$i<=5;$i++)
          <span class="csat-star {{ $i <= round($csatAvg) ? 'filled' : 'empty' }}">★</span>
        @endfor
      </div>
      <div class="csat-score-sub">out of 5 · {{ $csatCount }} reviews</div>
      @php
        $badge = $csatAvg >= 4.5 ? ['Excellent','excellent'] : ($csatAvg >= 3.5 ? ['Good','good'] : ($csatAvg >= 2.5 ? ['Average','average'] : ['Poor','poor']));
      @endphp
      <span class="csat-badge {{ $badge[1] }}">
        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        {{ $badge[0] }}
      </span>
    @else
      <div class="csat-big-score" style="color:var(--muted);font-size:40px">—</div>
      <div class="csat-score-sub" style="margin-top:8px">No ratings yet</div>
      <span class="csat-badge none">Awaiting feedback</span>
    @endif
  </div>

  {{-- Breakdown card --}}
  <div class="csat-breakdown-card">
    <div class="card-header">
      <h2>Rating Breakdown</h2>
      <span style="font-size:12px;color:var(--muted);font-weight:600">{{ $csatCount }} total</span>
    </div>
    <div class="csat-breakdown-body">
      @foreach([5,4,3,2,1] as $star)
        @php $pct = $csatCount > 0 ? round($csatDistribution[$star] / $csatCount * 100) : 0; @endphp
        <div class="csat-bar-row">
          <div class="csat-bar-label"><span class="star-icon">★</span>{{ $star }}</div>
          <div class="csat-bar-track">
            <div class="csat-bar-fill s{{ $star }}" style="width:{{ $pct }}%"></div>
          </div>
          <div class="csat-bar-count">{{ $csatDistribution[$star] }}</div>
        </div>
      @endforeach
    </div>
  </div>
</div>

{{-- Recent CSAT Submissions --}}
<div class="card" style="margin-bottom:28px">
  <div class="card-header">
    <h2>
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="vertical-align:middle;margin-right:5px;color:var(--muted)"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
      Recent Customer Ratings
    </h2>
    <span style="font-size:12px;color:var(--muted)">Last 5 submissions across all companies</span>
  </div>
  @if($recentCsat->isEmpty())
    <div class="csat-empty">No customer ratings submitted yet.</div>
  @else
    <div class="csat-recent-list">
      @foreach($recentCsat as $t)
        <div class="csat-recent-item">
          <div class="csat-av">{{ strtoupper(substr($t->user->name ?? 'U', 0, 2)) }}</div>
          <div class="csat-info">
            <div class="csat-info-top">
              <span class="csat-user-name">{{ $t->user->name ?? 'Unknown' }}</span>
              @if($t->company)
                <span class="csat-company-tag">{{ $t->company->name }}</span>
              @endif
              <a href="{{ route('tickets.show', $t->id) }}" class="csat-ticket-ref">#TK-{{ str_pad($t->id,4,'0',STR_PAD_LEFT) }}</a>
            </div>
            <div class="csat-mini-stars">
              @for($i=1;$i<=5;$i++)
                <span class="csat-mini-star {{ $i <= $t->csat_rating ? 'filled' : 'empty' }}">★</span>
              @endfor
            </div>
            @if($t->csat_comment)
              <div class="csat-comment">"{{ $t->csat_comment }}"</div>
            @endif
          </div>
          <div class="csat-time">{{ $t->csat_submitted_at->diffForHumans() }}</div>
        </div>
      @endforeach
    </div>
  @endif
</div>

{{-- ── Companies table ── --}}
<div class="card">
  <div class="card-header">
    <h2>All Companies</h2>
    <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
      <a href="{{ route('superadmin.configuration') }}" class="btn btn-outline btn-sm">Configuration</a>
      <a href="{{ route('superadmin.companies.index') }}" class="btn btn-outline btn-sm">Manage Companies</a>
    </div>
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
        <th>CSAT</th>
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
          @if($c['csat_avg'])
            <div style="display:flex;align-items:center;gap:5px">
              <span style="font-size:13.5px;font-weight:800;color:{{ $c['csat_avg'] >= 4 ? '#16a34a' : ($c['csat_avg'] >= 3 ? '#d97706' : '#dc2626') }}">{{ $c['csat_avg'] }}</span>
              <span style="color:#f59e0b;font-size:12px">★</span>
              <span style="font-size:11px;color:var(--muted)">({{ $c['csat_count'] }})</span>
            </div>
          @else
            <span style="color:var(--muted);font-size:12px">—</span>
          @endif
        </td>
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