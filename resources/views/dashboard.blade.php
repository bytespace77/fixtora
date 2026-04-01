@extends('layouts.app')
@section('title', 'Dashboard – Fixtora')

@section('styles')
<style>
:root {
  --blue-mid: #2563eb;
  --blue-light: #dbeafe;
  --blue-bg: #eff6ff;
  --navy-card: #1e3a6e;
  --shadow-card: 0 1px 4px rgba(0,0,0,.06), 0 6px 20px rgba(0,0,0,.05);
}

/* Page header */
.page-header { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:24px; }
.page-header h1 { font-size:24px; font-weight:800; letter-spacing:-.6px; color:var(--navy); }
.page-header p  { font-size:13px; color:var(--muted); margin-top:4px; }
.header-actions { display:flex; gap:10px; margin-top:4px; align-items:center; }

/* Range picker */
.range-btn { display:flex; align-items:center; gap:6px; padding:8px 14px; border:1px solid var(--border-2); border-radius:7px; font-size:12.5px; font-weight:600; color:var(--text-2); background:var(--surface); cursor:pointer; font-family:inherit; transition:all .12s; }
.range-btn:hover, .range-btn.active { background:var(--bg); border-color:var(--blue); color:var(--blue); }
.range-dropdown { display:none; position:absolute; top:calc(100% + 6px); right:0; background:var(--surface); border:1px solid var(--border); border-radius:10px; box-shadow:var(--shadow-md); z-index:300; min-width:180px; padding:6px; }
.range-dropdown.open { display:block; }
.range-option { padding:8px 12px; border-radius:7px; font-size:13px; font-weight:500; cursor:pointer; color:var(--text-2); transition:background .1s; }
.range-option:hover { background:var(--blue-bg); color:var(--blue); }
.range-option.selected { background:var(--blue-bg); color:var(--blue); font-weight:600; }
.range-divider { height:1px; background:var(--border); margin:4px 0; }
.range-custom { padding:8px 12px; }
.range-custom label { font-size:11px; font-weight:700; color:var(--muted); letter-spacing:.4px; text-transform:uppercase; display:block; margin-bottom:5px; }
.range-custom input { width:100%; padding:6px 8px; border:1px solid var(--border); border-radius:6px; font-size:12px; font-family:inherit; outline:none; }
.range-custom input:focus { border-color:var(--blue); }
.range-custom-apply { margin-top:8px; width:100%; padding:7px; background:var(--navy); color:#fff; border:none; border-radius:6px; font-size:12px; font-weight:600; cursor:pointer; font-family:inherit; }

/* Export */
.export-wrap { position:relative; }
.export-btn { display:flex; align-items:center; gap:6px; padding:8px 16px; background:var(--blue); color:#fff; border:none; border-radius:7px; font-size:12.5px; font-weight:600; cursor:pointer; font-family:inherit; transition:background .12s; }
.export-btn:hover { background:var(--blue-2); }
.export-dropdown { display:none; position:absolute; top:calc(100% + 6px); right:0; background:var(--surface); border:1px solid var(--border); border-radius:10px; box-shadow:var(--shadow-md); z-index:300; min-width:160px; padding:6px; }
.export-dropdown.open { display:block; }
.export-option { display:flex; align-items:center; gap:9px; padding:9px 12px; border-radius:7px; font-size:13px; font-weight:500; cursor:pointer; color:var(--text-2); text-decoration:none; transition:background .1s; }
.export-option:hover { background:var(--blue-bg); color:var(--blue); }

/* Search dropdown */
.search-dropdown { display:none; position:absolute; top:calc(100% + 6px); left:0; right:0; background:var(--surface); border:1px solid var(--border); border-radius:10px; box-shadow:var(--shadow-md); z-index:300; max-height:380px; overflow-y:auto; }
.search-dropdown.open { display:block; }
.sd-header { padding:10px 14px 6px; font-size:10px; font-weight:700; letter-spacing:.6px; text-transform:uppercase; color:var(--muted-lt); }
.sd-item { display:flex; align-items:center; gap:10px; padding:9px 14px; cursor:pointer; text-decoration:none; border-bottom:1px solid var(--border); transition:background .1s; color:inherit; }
.sd-item:last-child { border-bottom:none; }
.sd-item:hover { background:var(--blue-bg); }
.sd-badge { font-size:9.5px; font-weight:700; padding:2px 7px; border-radius:12px; text-transform:uppercase; }
.sd-badge.critical { background:#fef2f2; color:#dc2626; }
.sd-badge.high     { background:#fff7ed; color:#f97316; }
.sd-badge.medium   { background:var(--blue-bg); color:var(--blue); }
.sd-badge.low      { background:#f3f4f6; color:#6b7280; }
.sd-title { font-size:13px; font-weight:600; color:var(--text); flex:1; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.sd-meta  { font-size:11px; color:var(--muted); }
.sd-empty { padding:20px 14px; font-size:13px; color:var(--muted); text-align:center; }

/* Stats */
.stats-row { display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px; margin-bottom:20px; }
.stat-card { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); padding:22px 22px 20px; box-shadow:var(--shadow-card); }
.stat-card.navy { background:#1e3a6e; border-color:#1e3a6e; color:#fff; }
.stat-top { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:16px; }
.stat-icon { width:42px; height:42px; border-radius:10px; display:flex; align-items:center; justify-content:center; }
.stat-icon.blue  { background:var(--blue-bg); color:var(--blue); }
.stat-icon.green { background:#dcfce7; color:#16a34a; }
.stat-icon.white { background:rgba(255,255,255,.15); color:#fff; }
.stat-badge { font-size:10px; font-weight:700; letter-spacing:.3px; padding:3px 8px; border-radius:20px; }
.badge-green { background:#dcfce7; color:#15803d; }
.badge-blue  { background:var(--blue-light); color:var(--blue); }
.badge-clear { background:rgba(255,255,255,.18); color:#fff; font-size:9.5px; letter-spacing:.5px; }
.stat-label { font-size:10.5px; font-weight:700; letter-spacing:.8px; text-transform:uppercase; color:var(--muted); margin-bottom:6px; }
.navy .stat-label { color:rgba(255,255,255,.6); }
.stat-value { font-size:36px; font-weight:800; letter-spacing:-1px; color:var(--text); }
.navy .stat-value { color:#fff; font-size:32px; }
.stat-sub { font-size:11.5px; color:var(--muted); margin-top:4px; }
.navy .stat-sub { color:rgba(255,255,255,.55); }

/* Middle row */
.middle-row { display:grid; grid-template-columns:1fr 290px; gap:16px; margin-bottom:20px; }
.chart-card { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); padding:22px; box-shadow:var(--shadow-card); }
.chart-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:18px; }
.chart-title { font-size:15px; font-weight:700; color:var(--text); letter-spacing:-.2px; }
.chart-legend { display:flex; align-items:center; gap:14px; }
.legend-item { display:flex; align-items:center; gap:6px; font-size:12px; color:var(--muted); font-weight:500; }
.legend-dot { width:8px; height:8px; border-radius:50%; }
.dot-blue  { background:var(--blue); }
.dot-green { background:#16a34a; }
.chart-canvas-wrap { position:relative; height:180px; }
.updates-card { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); padding:20px; box-shadow:var(--shadow-card); }
.updates-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; }
.updates-title { font-size:14px; font-weight:700; color:var(--text); }
.view-all { font-size:12px; font-weight:600; color:var(--blue); text-decoration:none; }
.view-all:hover { text-decoration:underline; }
.update-item { display:flex; gap:12px; padding:10px 0; text-decoration:none; color:inherit; border-radius:8px; transition:background .12s; cursor:pointer; }
.update-item:not(:last-child) { border-bottom:1px solid var(--border); }
.update-item:hover { background:var(--blue-bg); padding-left:6px; padding-right:6px; margin-left:-6px; margin-right:-6px; }
.update-item:hover .update-title-text { color:var(--blue); }
.update-icon { width:34px; height:34px; border-radius:8px; background:var(--bg); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:13px; }
.update-body { flex:1; min-width:0; }
.update-title-text { font-size:12.5px; font-weight:700; color:var(--text); margin-bottom:2px; }
.update-desc  { font-size:11px; color:var(--muted); margin-bottom:4px; }
.update-time  { font-size:10px; font-weight:700; letter-spacing:.4px; color:var(--muted-lt); text-transform:uppercase; }
.update-time.alert { color:var(--red); }

/* Queue */
.queue-card { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); padding:22px; box-shadow:var(--shadow-card); }
.queue-header { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:0; }
.queue-title { font-size:16px; font-weight:700; color:var(--text); letter-spacing:-.3px; }
.queue-sub { font-size:12px; color:var(--muted); margin-top:3px; }
.queue-table { width:100%; border-collapse:collapse; margin-top:18px; }
.queue-table th { text-align:left; padding:8px 10px; font-size:10px; font-weight:700; letter-spacing:.6px; text-transform:uppercase; color:var(--muted-lt); border-bottom:1px solid var(--border); }
.queue-table td { padding:12px 10px; border-bottom:1px solid var(--border); font-size:13px; color:var(--text-2); }
.queue-table tr:last-child td { border-bottom:none; }
.queue-table tr:hover td { background:var(--bg); }
.ticket-id { font-size:12px; font-weight:700; color:var(--muted); background:var(--bg); padding:3px 8px; border-radius:5px; border:1px solid var(--border); }
.ticket-title-cell { font-weight:600; color:var(--text); }
.ticket-system { font-size:11px; color:var(--muted); margin-top:2px; }
.status-pill { display:inline-block; padding:3px 10px; border-radius:20px; font-size:10px; font-weight:700; letter-spacing:.4px; text-transform:uppercase; }
.pill-open        { background:#fff7ed; color:#f97316; border:1px solid #fed7aa; }
.pill-in_progress { background:var(--blue-bg); color:var(--blue); border:1px solid #bfdbfe; }
.pill-in_review   { background:#fdf4ff; color:#c026d3; border:1px solid #fae8ff; }
.pill-resolved    { background:#dcfce7; color:#16a34a; border:1px solid #bbf7d0; }
.pill-closed      { background:#f3f4f6; color:#6b7280; border:1px solid #e5e7eb; }
.priority-dot { width:8px; height:8px; border-radius:50%; display:inline-block; margin-right:5px; }
.prio-critical { background:#dc2626; }
.prio-high     { background:#f97316; }
.prio-medium   { background:#2563eb; }
.prio-low      { background:#9ca3af; }
.view-ticket { font-size:11.5px; font-weight:600; color:var(--blue); text-decoration:none; }
.view-ticket:hover { text-decoration:underline; }
.queue-empty { text-align:center; padding:40px 20px; color:var(--muted); font-size:13px; }
</style>
@endsection

@section('content')

<div class="page-header">
  <div>
    <h1>Operational Overview</h1>
    <p>Welcome back, {{ auth()->user()->name }}. Here's your system status.</p>
  </div>
  <div class="header-actions">

    <!-- Date range picker -->
    <div style="position:relative">
      <button class="range-btn" id="rangeBtn" onclick="toggleDropdown('rangeMenu','rangeBtn')">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        @php $rangeLabels = ['24h'=>'Last 24 Hours','7d'=>'Last 7 Days','30d'=>'Last 30 Days','90d'=>'Last 90 Days']; @endphp
        {{ $rangeLabels[$range] ?? 'Custom Range' }}
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
      </button>
      <div class="range-dropdown" id="rangeMenu">
        @foreach(['24h'=>'Last 24 Hours','7d'=>'Last 7 Days','30d'=>'Last 30 Days','90d'=>'Last 90 Days'] as $k=>$v)
          <div class="range-option {{ $range === $k ? 'selected' : '' }}"
               onclick="applyRange('{{ $k }}')">{{ $v }}</div>
        @endforeach
        <div class="range-divider"></div>
        <div class="range-custom">
          <label>Custom range</label>
          <input type="date" id="customFrom" value="{{ $from->format('Y-m-d') }}"/>
          <input type="date" id="customTo"   value="{{ $to->format('Y-m-d') }}" style="margin-top:5px"/>
          <button class="range-custom-apply" onclick="applyCustomRange()">Apply</button>
        </div>
      </div>
    </div>

    <!-- Export -->
    <div class="export-wrap">
      <button class="export-btn" id="exportBtn" onclick="toggleDropdown('exportMenu','exportBtn')">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Export Report
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
      </button>
      <div class="export-dropdown" id="exportMenu">
        <a class="export-option" href="{{ route('home') }}?range={{ $range }}&export=pdf" target="_blank">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          Export as PDF
        </a>
        <a class="export-option" href="{{ route('home') }}?range={{ $range }}&export=excel">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
          Export as Excel (CSV)
        </a>
      </div>
    </div>

  </div>
</div>

<!-- Stats — ✅ Task 5: 4 cards (active, resolved, critical, total) scoped per company -->
<div class="stats-row" style="grid-template-columns:1fr 1fr 1fr 1fr">
  <div class="stat-card">
    <div class="stat-top">
      <div class="stat-icon blue">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2z"/><path d="M13 5v2M13 17v2M13 11v2"/></svg>
      </div>
      <span class="stat-badge badge-green">Open</span>
    </div>
    <div class="stat-label">Active Tickets</div>
    <div class="stat-value">{{ $stats['active'] }}</div>
    <div class="stat-sub">Open &amp; in-progress</div>
  </div>

  <div class="stat-card">
    <div class="stat-top">
      <div class="stat-icon green">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      </div>
      <span class="stat-badge badge-blue">On Target</span>
    </div>
    <div class="stat-label">Resolved</div>
    <div class="stat-value">{{ $stats['resolved'] }}</div>
    <div class="stat-sub">In selected period</div>
  </div>

  <div class="stat-card navy">
    <div class="stat-top">
      <div class="stat-icon white">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
      </div>
      <span class="stat-badge badge-clear">{{ $stats['critical'] === 0 ? 'All Clear' : 'Needs Attention' }}</span>
    </div>
    <div class="stat-label">Critical Open</div>
    <div class="stat-value">{{ $stats['critical'] }}</div>
    <div class="stat-sub">{{ $stats['critical'] === 0 ? 'No critical issues' : 'Requires immediate action' }}</div>
  </div>

  {{-- ✅ Task 5: Total tickets stat — scoped per company via Ticket global scope --}}
  <div class="stat-card">
    <div class="stat-top">
      <div class="stat-icon blue" style="background:#f3f4f6;color:#374151">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
      </div>
      <span class="stat-badge" style="background:#f3f4f6;color:#374151">All Time</span>
    </div>
    <div class="stat-label">Total Tickets</div>
    <div class="stat-value">{{ $stats['total'] }}</div>
    <div class="stat-sub">All tickets in your company</div>
  </div>
</div>

<!-- Middle row -->
<div class="middle-row">
  <div class="chart-card">
    <div class="chart-header">
      <div class="chart-title">Ticket Inflow &amp; Resolution</div>
      <div class="chart-legend">
        <div class="legend-item"><span class="legend-dot dot-blue"></span> Inflow</div>
        <div class="legend-item"><span class="legend-dot dot-green"></span> Resolved</div>
      </div>
    </div>
    <div class="chart-canvas-wrap">
      <canvas id="inflowChart"></canvas>
    </div>
  </div>

  <div class="updates-card">
    <div class="updates-header">
      <span class="updates-title">System Updates</span>
      <a href="{{ route('tickets.index') }}" class="view-all">View All</a>
    </div>
    @php
      $updates = [];
      if ($stats['critical'] > 0) {
          $updates[] = ['icon'=>'🔴','title'=>'Critical Tickets Open','desc'=>$stats['critical'].' ticket(s) marked critical still open.','time'=>'NOW','alert'=>true,'link'=>route('tickets.index').'?priority=critical'];
      }
      $updates[] = ['icon'=>'📋','title'=>'Queue Updated','desc'=>$queueTickets->count().' ticket(s) in priority queue.','time'=>'JUST NOW','alert'=>false,'link'=>route('tickets.index')];
      $updates[] = ['icon'=>'✅','title'=>'Resolved This Period','desc'=>$stats['resolved'].' ticket(s) resolved in selected range.','time'=>'PERIOD','alert'=>false,'link'=>route('tickets.index').'?status=resolved'];
    @endphp
    @foreach($updates as $u)
    <a href="{{ $u['link'] }}" class="update-item">
      <div class="update-icon">{{ $u['icon'] }}</div>
      <div class="update-body">
        <div class="update-title-text">{{ $u['title'] }}</div>
        <div class="update-desc">{{ $u['desc'] }}</div>
        <div class="update-time {{ $u['alert'] ? 'alert' : '' }}">{{ $u['time'] }}</div>
      </div>
    </a>
    @endforeach
  </div>
</div>

<!-- Priority Queue -->
<div class="queue-card">
  <div class="queue-header">
    <div>
      <div class="queue-title">Priority Concierge Queue</div>
      <div class="queue-sub">Active issues requiring immediate attention · {{ $queueTickets->count() }} ticket{{ $queueTickets->count() !== 1 ? 's' : '' }}</div>
    </div>
    <a href="{{ route('tickets.index') }}" style="font-size:12.5px;font-weight:600;color:var(--blue);text-decoration:none">View All Tickets →</a>
  </div>

  @if($queueTickets->isEmpty())
    <div class="queue-empty">🎉 No open tickets — queue is clear!</div>
  @else
  <table class="queue-table">
    <thead>
      <tr>
        <th>Ticket ID</th>
        <th>Title</th>
        <th>Priority</th>
        <th>Status</th>
        <th>Created</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @foreach($queueTickets as $t)
      <tr>
        <td><span class="ticket-id">#{{ $t->id }}</span></td>
        <td>
          <div class="ticket-title-cell">{{ $t->title }}</div>
          @if($t->system)
            <div class="ticket-system">{{ $t->system }}</div>
          @endif
        </td>
        <td>
          <span class="priority-dot prio-{{ $t->priority }}"></span>
          {{ ucfirst($t->priority) }}
        </td>
        <td><span class="status-pill pill-{{ $t->status }}">{{ ucfirst(str_replace('_',' ',$t->status)) }}</span></td>
        <td>{{ $t->created_at->diffForHumans() }}</td>
        <td><a class="view-ticket" href="{{ route('tickets.show', $t->id) }}">View →</a></td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @endif
</div>

<script>
const chartLabels   = {!! json_encode($chartData['labels']) !!};
const chartInflow   = {!! json_encode($chartData['inflow']) !!};
const chartResolved = {!! json_encode($chartData['resolved']) !!};

const ctx = document.getElementById('inflowChart').getContext('2d');
const blueGrad = ctx.createLinearGradient(0,0,0,180);
blueGrad.addColorStop(0,'rgba(37,99,235,0.18)');
blueGrad.addColorStop(1,'rgba(37,99,235,0)');
const greenGrad = ctx.createLinearGradient(0,0,0,180);
greenGrad.addColorStop(0,'rgba(22,163,74,0.12)');
greenGrad.addColorStop(1,'rgba(22,163,74,0)');

new Chart(ctx, {
  type: 'line',
  data: {
    labels: chartLabels,
    datasets: [
      { label:'Inflow',   data:chartInflow,   borderColor:'#2563eb', backgroundColor:blueGrad,  borderWidth:2.5, pointRadius:4, pointHoverRadius:6, pointBackgroundColor:'#2563eb', tension:0.4, fill:true },
      { label:'Resolved', data:chartResolved, borderColor:'#16a34a', backgroundColor:greenGrad, borderWidth:2,   pointRadius:4, pointHoverRadius:6, pointBackgroundColor:'#16a34a', tension:0.4, fill:true }
    ]
  },
  options: {
    responsive:true, maintainAspectRatio:false,
    interaction:{ mode:'index', intersect:false },
    plugins:{
      legend:{ display:false },
      tooltip:{ backgroundColor:'#fff', borderColor:'#e5e7ef', borderWidth:1, titleColor:'#111827', bodyColor:'#6b7280', padding:10, boxPadding:4 }
    },
    scales:{
      x:{ grid:{ display:false }, ticks:{ font:{ size:10.5, weight:'600' }, color:'#9ca3af', maxRotation:0 } },
      y:{ beginAtZero:true, grid:{ color:'#f0f0f0' }, ticks:{ font:{ size:10 }, color:'#9ca3af', stepSize:1, callback: v => Number.isInteger(v)?v:'' } }
    }
  }
});

function toggleDropdown(menuId, btnId) {
  const menu = document.getElementById(menuId);
  const isOpen = menu.classList.contains('open');
  document.querySelectorAll('.range-dropdown,.export-dropdown').forEach(m => m.classList.remove('open'));
  document.querySelectorAll('.range-btn,.export-btn').forEach(b => b.classList.remove('active'));
  if (!isOpen) {
    menu.classList.add('open');
    document.getElementById(btnId).classList.add('active');
  }
}

document.addEventListener('click', function(e) {
  if (!e.target.closest('.header-actions')) {
    document.querySelectorAll('.range-dropdown,.export-dropdown').forEach(m => m.classList.remove('open'));
    document.querySelectorAll('.range-btn,.export-btn').forEach(b => b.classList.remove('active'));
  }
});

function applyRange(r) { window.location.href = '{{ route('home') }}?range=' + r; }

function applyCustomRange() {
  const from = document.getElementById('customFrom').value;
  const to   = document.getElementById('customTo').value;
  if (!from || !to) { alert('Please select both dates.'); return; }
  window.location.href = '{{ route('home') }}?range=custom&custom=' + from + ',' + to;
}
</script>
@endsection