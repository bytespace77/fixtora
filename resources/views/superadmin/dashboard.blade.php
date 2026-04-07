@extends('layouts.app')
@section('title', 'Customer Ratings Dashboard – Fixtora')

@section('styles')
<style>
/* ── Page header ─────────────────────────────────────────── */
.page-header { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:24px; }
.page-header h1 { font-size:24px; font-weight:800; letter-spacing:-.6px; color:var(--navy); line-height: 1.2; }
.page-header p  { font-size:13px; color:var(--muted); margin-top:4px; }
.superadmin-badge { display:inline-flex; align-items:center; gap:5px; font-size:11px; font-weight:700; padding:3px 10px; border-radius:20px; background:#fef3c7; color:#92400e; letter-spacing:.4px; text-transform:uppercase; margin-left:10px; vertical-align: middle; }

/* Alert */
.alert { padding:12px 18px; border-radius:var(--radius-sm); margin-bottom:18px; font-size:13px; font-weight:500; }
.alert-success { background:var(--green-bg); color:var(--green); border:1px solid #bbf7d0; }

.card { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); }
.card-header { display:flex; align-items:center; justify-content:space-between; padding:18px 22px; border-bottom:1px solid var(--border); }
.card-header h2 { font-size:15px; font-weight:700; color:var(--text); }

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
      Customer Ratings Dashboard
    </h1>
    <p>Platform-wide overview of customer satisfaction and feedback.</p>
  </div>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

{{-- ── CSAT Overview ── --}}
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
    <span style="font-size:12px;color:var(--muted)">Recent feedback across all companies</span>
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
    <div style="padding: 16px 22px; border-top: 1px solid var(--border);">
      {{ $recentCsat->links() }}
    </div>
  @endif
</div>

@endsection

@section('scripts')
@endsection