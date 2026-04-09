@extends('layouts.app')
@section('title', 'SLA Monitor – Fixtora')

@section('styles')
<style>
.page-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px}
.page-header h1{font-size:22px;font-weight:800;letter-spacing:-.5px;color:var(--navy)}
.page-header p{font-size:13px;color:var(--muted);margin-top:4px}
.hdr-btns{display:flex;gap:8px;align-items:center}
.btn-sm{padding:8px 14px;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:6px;border:1px solid var(--border);background:var(--surface);color:var(--text-sub);font-family:inherit;transition:all .15s;text-decoration:none}
.btn-sm:hover{background:var(--bg)}
.btn-primary{background:var(--blue);color:#fff;border-color:var(--blue)}
.btn-primary:hover{background:#1a42c4;color:#fff}

/* ── Quarter dropdown ── */
.quarter-dropdown-wrap{position:relative}
.quarter-trigger{
    padding:8px 14px;border-radius:8px;font-size:12px;font-weight:700;
    cursor:pointer;display:flex;align-items:center;gap:6px;
    border:1px solid var(--border);background:var(--surface);color:var(--text-sub);
    font-family:inherit;transition:all .15s;user-select:none;
}
.quarter-trigger.active{background:var(--blue);color:#fff;border-color:var(--blue)}
.quarter-trigger:hover{background:var(--bg)}
.quarter-trigger.active:hover{background:#1a42c4}
.quarter-chevron{transition:transform .2s;margin-left:2px}
.quarter-menu{
    display:none;position:absolute;top:calc(100% + 6px);right:0;
    background:#fff;border:1px solid var(--border);border-radius:10px;
    box-shadow:0 8px 24px rgba(0,0,0,.12);min-width:190px;z-index:500;
    overflow:hidden;
}
.quarter-menu.open{display:block}
.qm-header{padding:10px 14px 8px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--muted);border-bottom:1px solid var(--border)}
.qm-option{
    display:flex;align-items:center;justify-content:space-between;
    padding:10px 14px;font-size:13px;font-weight:600;color:var(--navy);
    cursor:pointer;transition:background .12s;text-decoration:none;
}
.qm-option:hover{background:#f5f7ff}
.qm-option.selected{background:#eff4ff;color:var(--blue)}
.qm-option.selected .qm-check{opacity:1}
.qm-check{opacity:0;color:var(--blue)}
.qm-divider{border:none;border-top:1px solid var(--border);margin:4px 0}
.qm-clear{display:flex;align-items:center;gap:6px;padding:9px 14px;font-size:12px;font-weight:700;color:var(--muted);cursor:pointer;transition:color .12s;text-decoration:none}
.qm-clear:hover{color:var(--red)}
.qm-badge{font-size:9.5px;font-weight:700;padding:2px 7px;border-radius:20px;background:#dbeafe;color:#1d4ed8}

/* ── Filter banner ── */
.filter-banner{display:flex;align-items:center;justify-content:space-between;background:#eff6ff;border:1px solid #bfdbfe;border-radius:var(--radius);padding:10px 16px;margin-bottom:16px;font-size:12.5px;font-weight:600;color:#1d4ed8}
.filter-banner a{color:#1d4ed8;text-decoration:underline;font-size:12px}

/* STATS */
.sla-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:20px}
.sla-stat{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:20px 22px;box-shadow:var(--shadow);text-align:center}
.sla-stat-val{font-size:34px;font-weight:800;letter-spacing:-1px;margin-bottom:4px}
.sla-stat-lbl{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:var(--muted)}
.sla-stat-change{font-size:11.5px;font-weight:600;margin-top:6px}
.ch-green{color:var(--green)}
.ch-red{color:var(--red)}
.ch-orange{color:var(--orange)}

/* MID GRID */
.sla-mid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px}
.card-box{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:20px;box-shadow:var(--shadow)}
.chart-hdr{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px}
.chart-title{font-size:14px;font-weight:700;color:var(--navy)}
.chart-sub{font-size:11.5px;color:var(--muted);margin-top:2px}
.view-all{font-size:11.5px;font-weight:600;color:var(--blue);text-decoration:none}
.view-all:hover{text-decoration:underline}

/* AT-RISK TICKETS */
.sla-ticket-row{display:flex;align-items:center;gap:14px;padding:13px 14px;border-left:3px solid var(--border);border-radius:0 8px 8px 0;background:var(--bg);margin-bottom:10px;transition:background .12s}
.sla-ticket-row:last-child{margin-bottom:0}
.sla-ticket-row:hover{background:#f0f4ff}
.sla-priority{font-size:9.5px;font-weight:800;letter-spacing:.5px;padding:3px 8px;border-radius:5px;white-space:nowrap}
.sla-t-info{flex:1;min-width:0}
.sla-t-id{font-family:'DM Mono',monospace;font-size:10.5px;font-weight:700;color:var(--muted-lt)}
.sla-t-title{font-size:12.5px;font-weight:700;color:var(--text);margin:2px 0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.sla-t-desc{font-size:11.5px;color:var(--muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.sla-timer{font-size:13px;font-weight:800;font-family:'DM Mono',monospace;white-space:nowrap;flex-shrink:0}
.chart-canvas-wrap{position:relative;height:200px}

/* COMPLIANCE TABLE */
.compliance-table{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);overflow-y:auto;overflow-x:hidden;max-height:500px;box-shadow:var(--shadow)}
.ct-header{display:grid;grid-template-columns:1fr 120px 120px 120px 130px;gap:12px;padding:11px 18px;background:var(--bg);border-bottom:1px solid var(--border);font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);position:sticky;top:0;z-index:10}
.ct-row{display:grid;grid-template-columns:1fr 120px 120px 120px 130px;gap:12px;padding:13px 18px;border-bottom:1px solid var(--border);align-items:center;font-size:13px}
.ct-row:last-child{border-bottom:none}
.ct-row:hover{background:#fafbff}
.bar-wrap{height:6px;background:var(--bg);border-radius:20px;overflow:hidden;margin-top:4px;border:1px solid var(--border)}
.bar-fill{height:100%;border-radius:20px}
.pill{display:inline-block;padding:3px 10px;border-radius:20px;font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.4px}
.pill-ok{background:#dcfce7;color:var(--green);border:1px solid #bbf7d0}
.pill-warning{background:#fff7ed;color:var(--orange);border:1px solid #fed7aa}
.pill-breach{background:#fee2e2;color:var(--red);border:1px solid #fecaca}
.empty-msg{text-align:center;padding:30px;color:var(--muted);font-size:13px;font-weight:600}
.ct-section-title{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px}
.ct-section-title h2{font-size:15px;font-weight:800;color:var(--navy)}
.ct-section-title p{font-size:12px;color:var(--muted);margin-top:2px}
.ct-count-badge{font-size:11px;font-weight:700;background:var(--blue-bg,#eff6ff);color:var(--blue);border:1px solid var(--blue-lt,#dbeafe);padding:4px 12px;border-radius:20px}

/* CONFIGURE SLA MODAL */
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1000;align-items:center;justify-content:center}
.modal-overlay.open{display:flex}
.modal-box{background:#fff;border-radius:16px;padding:32px;width:100%;max-width:460px;box-shadow:0 20px 60px rgba(0,0,0,.18);position:relative}
.modal-title{font-size:17px;font-weight:800;color:var(--navy);margin-bottom:4px}
.modal-sub{font-size:12.5px;color:var(--muted);margin-bottom:24px}
.modal-close{position:absolute;top:16px;right:16px;width:30px;height:30px;border-radius:8px;border:1px solid var(--border);background:var(--bg);cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--muted);font-size:16px;line-height:1}
.modal-close:hover{background:#f0f2f5}
.sla-fields{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:24px}
.sla-field label{display:block;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:6px}
.sla-field input{width:100%;padding:10px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;font-weight:600;font-family:inherit;color:var(--navy);outline:none;transition:border .15s}
.sla-field input:focus{border-color:var(--blue)}
.sla-field-hint{font-size:10.5px;color:var(--muted);margin-top:4px}
.sla-priority-dot{display:inline-block;width:8px;height:8px;border-radius:50%;margin-right:5px}
.modal-footer{display:flex;gap:10px;justify-content:flex-end}
.btn-cancel{padding:10px 20px;border-radius:8px;border:1px solid var(--border);background:var(--surface);color:var(--text-sub);font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;transition:background .15s}
.btn-cancel:hover{background:var(--bg)}
.btn-save{padding:10px 20px;border-radius:8px;border:none;background:var(--blue);color:#fff;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;transition:background .15s}
.btn-save:hover{background:#1a42c4}
.toast-success{position:fixed;bottom:24px;right:24px;background:#16a34a;color:#fff;padding:12px 20px;border-radius:10px;font-size:13px;font-weight:700;box-shadow:0 4px 20px rgba(0,0,0,.15);z-index:2000;opacity:0;transform:translateY(10px);transition:all .3s}
.toast-success.show{opacity:1;transform:translateY(0)}
</style>
@endsection

@section('content')

{{-- CONFIGURE SLA MODAL --}}
<div class="modal-overlay" id="slaModal">
  <div class="modal-box">
    <button class="modal-close" onclick="closeSlaModal()">✕</button>
    <div class="modal-title">Configure SLA Limits</div>
    <div class="modal-sub">Set the maximum resolution time (in hours) for each priority level.</div>
    <form method="POST" action="{{ route('sla-monitor.configure') }}">
      @csrf
      <div class="sla-fields">
        <div class="sla-field">
          <label><span class="sla-priority-dot" style="background:#ef4444"></span>Critical</label>
          <input type="number" name="critical" min="1" max="720" value="{{ $slaLimits['critical'] }}" required>
          <div class="sla-field-hint">Default: 4 hours</div>
        </div>
        <div class="sla-field">
          <label><span class="sla-priority-dot" style="background:#f97316"></span>High</label>
          <input type="number" name="high" min="1" max="720" value="{{ $slaLimits['high'] }}" required>
          <div class="sla-field-hint">Default: 8 hours</div>
        </div>
        <div class="sla-field">
          <label><span class="sla-priority-dot" style="background:#eab308"></span>Medium</label>
          <input type="number" name="medium" min="1" max="720" value="{{ $slaLimits['medium'] }}" required>
          <div class="sla-field-hint">Default: 24 hours</div>
        </div>
        <div class="sla-field">
          <label><span class="sla-priority-dot" style="background:#6b7a8d"></span>Low</label>
          <input type="number" name="low" min="1" max="720" value="{{ $slaLimits['low'] }}" required>
          <div class="sla-field-hint">Default: 72 hours</div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-cancel" onclick="closeSlaModal()">Cancel</button>
        <button type="submit" class="btn-save">Save Limits</button>
      </div>
    </form>
  </div>
</div>

{{-- Toast --}}
@if(session('sla_saved'))
<div class="toast-success" id="toastMsg">✓ {{ session('sla_saved') }}</div>
@endif

{{-- PAGE HEADER --}}
<div class="page-header">
  <div>
    <h1>SLA Monitor</h1>
    <p>Track service level agreement compliance and breach risks in real-time.</p>
  </div>
  <div class="hdr-btns">

    {{-- Quarter dropdown --}}
    <div class="quarter-dropdown-wrap" id="quarterWrap">
      <div class="quarter-trigger {{ $selectedQuarter ? 'active' : '' }}" id="quarterTrigger" onclick="toggleQuarterMenu()">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        {{ $selectedQuarter ? ($filterLabel ?? 'This Quarter') : 'This Quarter' }}
        <svg class="quarter-chevron" id="quarterChevron" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
      </div>

      <div class="quarter-menu" id="quarterMenu">
        <div class="qm-header">Select Quarter</div>

        @foreach($quarterOptions as $opt)
          <a href="{{ route('sla-monitor.index', ['quarter' => $opt['key']]) }}"
             class="qm-option {{ $selectedQuarter === $opt['key'] ? 'selected' : '' }}">
            <span>
              {{ $opt['label'] }}
              @if($loop->first)
                <span class="qm-badge">Current</span>
              @endif
            </span>
            <svg class="qm-check" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          </a>
        @endforeach

        @if($selectedQuarter)
          <hr class="qm-divider">
          <a href="{{ route('sla-monitor.index') }}" class="qm-clear">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            Clear filter (all time)
          </a>
        @endif
      </div>
    </div>

    {{-- Configure SLA --}}
    <button class="btn-sm btn-primary" onclick="openSlaModal()">
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
      Configure SLA
    </button>
  </div>
</div>

{{-- Filter active banner --}}
@if($selectedQuarter && $filterStart && $filterEnd)
<div class="filter-banner">
  <span>
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;margin-right:6px"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
    Showing data for <strong>{{ $filterLabel }}</strong>
    &nbsp;({{ $filterStart->format('M d') }} – {{ $filterEnd->format('M d, Y') }})
  </span>
  <a href="{{ route('sla-monitor.index') }}">Clear filter →</a>
</div>
@endif

{{-- KPI STATS --}}
<div class="sla-grid">
  <div class="sla-stat">
    <div class="sla-stat-val" style="color:var(--green)">{{ $compliance }}%</div>
    <div class="sla-stat-lbl">SLA Compliance Rate</div>
    <div class="sla-stat-change ch-green">↑ {{ $resolved }} of {{ $total }} tickets resolved</div>
  </div>
  <div class="sla-stat">
    <div class="sla-stat-val" style="color:{{ $criticalOpen > 0 ? 'var(--red)' : 'var(--green)' }}">{{ $criticalOpen }}</div>
    <div class="sla-stat-lbl">Active Breaches</div>
    <div class="sla-stat-change {{ $criticalOpen > 0 ? 'ch-red' : 'ch-green' }}">
      {{ $criticalOpen > 0 ? 'Critical-priority open tickets' : '✓ No critical breaches' }}
    </div>
  </div>
  <div class="sla-stat">
    <div class="sla-stat-val">{{ $resolved }}</div>
    <div class="sla-stat-lbl">Resolved Tickets</div>
    <div class="sla-stat-change ch-green">↑ Total resolved tickets</div>
  </div>
  <div class="sla-stat">
    <div class="sla-stat-val" style="color:var(--orange)">{{ $avgResolutionHrs }}h</div>
    <div class="sla-stat-lbl">Avg Resolution Time</div>
    <div class="sla-stat-change ch-orange">→ Mean hours from open → resolved</div>
  </div>
</div>

{{-- AT-RISK + CHART --}}
<div class="sla-mid">
  <div class="card-box">
    <div class="chart-hdr">
      <div>
        <div class="chart-title">At-Risk Tickets</div>
        <div class="chart-sub">Top 5 open tickets by priority &amp; age</div>
      </div>
      <a href="{{ route('tickets.index', ['status' => 'open']) }}" class="view-all">View All →</a>
    </div>
    @forelse($atRisk as $ticket)
      @php
        $borderColor = match($ticket->priority) { 'critical' => '#ef4444', 'high' => '#f97316', default => '#1e3a6e' };
        $bgColor     = match($ticket->priority) { 'critical' => '#fef2f2', 'high' => '#fff7ed', default => '#f4f5f8' };
        $txtColor    = match($ticket->priority) { 'critical' => '#dc2626', 'high' => '#c2410c', default => '#6b7a8d' };
        $timerColor  = match($ticket->priority) { 'critical' => '#dc2626', 'high' => '#f97316', default => '#1e3a6e' };
        $totalMins   = $ticket->created_at->diffInMinutes(now());
        $hrs  = floor($totalMins / 60);
        $mins = $totalMins % 60;
        $timer = $hrs >= 1 ? "{$hrs}h {$mins}m" : "{$mins}m";
      @endphp
      <div class="sla-ticket-row" style="border-left-color:{{ $borderColor }}">
        <div><span class="sla-priority" style="background:{{ $bgColor }};color:{{ $txtColor }}">{{ strtoupper($ticket->priority) }}</span></div>
        <div class="sla-t-info">
          <div class="sla-t-id">#TK-{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}</div>
          <div class="sla-t-title">{{ $ticket->title }}</div>
          <div class="sla-t-desc">{{ $ticket->system ?? 'No system specified' }}</div>
        </div>
        <div class="sla-timer" style="color:{{ $timerColor }}">{{ $timer }}</div>
      </div>
    @empty
      <div class="empty-msg">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="opacity:.3;display:block;margin:0 auto 8px"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        No at-risk tickets — all clear!
      </div>
    @endforelse
  </div>

  <div class="card-box">
    <div class="chart-hdr">
      <div>
        <div class="chart-title">Quarterly SLA % Trend</div>
        <div class="chart-sub">% resolved tickets vs total, per quarter</div>
      </div>
    </div>
    <div class="chart-canvas-wrap"><canvas id="quarterlyChart"></canvas></div>
    <script>
      window._quarterlyLabels = @json(collect($quarterly)->pluck('label'));
      window._quarterlyPcts   = @json(collect($quarterly)->pluck('pct'));
    </script>
  </div>
</div>

{{-- COMPLIANCE TABLE --}}
<div class="ct-section-title">
  <div>
    <h2>Compliance Table</h2>
    <p>All open &amp; in-progress tickets with SLA breach status</p>
  </div>
  <span class="ct-count-badge">{{ $allOpen->count() }} tickets</span>
</div>

<div class="compliance-table">
  <div class="ct-header">
    <span>Ticket</span><span>Priority</span><span>Age</span><span>Status</span><span>SLA Status</span>
  </div>
  @forelse($allOpen as $ticket)
    @php
      $ageHrs    = $ticket->created_at->diffInHours(now());
      $slaLimit  = $slaLimits[$ticket->priority] ?? 72;
      $pct       = min(100, round(($ageHrs / $slaLimit) * 100));
      $slaStatus = $pct >= 100 ? 'breach' : ($pct >= 75 ? 'warning' : 'ok');
      $barColor  = $slaStatus === 'breach' ? '#dc2626' : ($slaStatus === 'warning' ? '#f97316' : '#16a34a');
      $ageDisplay = $ageHrs >= 24 ? floor($ageHrs/24).'d '.($ageHrs%24).'h' : $ageHrs.'h';
    @endphp
    <div class="ct-row">
      <div>
        <div style="font-size:12px;font-family:'DM Mono',monospace;color:var(--muted-lt)">#TK-{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}</div>
        <div style="font-weight:600;font-size:13px">{{ Str::limit($ticket->title, 40) }}</div>
        <div class="bar-wrap"><div class="bar-fill" style="width:{{ $pct }}%;background:{{ $barColor }}"></div></div>
      </div>
      <div><span class="pill pill-{{ $ticket->priority === 'low' ? 'ok' : ($ticket->priority === 'medium' ? 'warning' : 'breach') }}">{{ ucfirst($ticket->priority) }}</span></div>
      <div style="font-size:12.5px;font-weight:600;color:var(--muted)">{{ $ageDisplay }}</div>
      <div style="font-size:12.5px;font-weight:600;color:var(--text-sub)">{{ ucfirst(str_replace('_',' ',$ticket->status)) }}</div>
      <div>
        <span class="pill pill-{{ $slaStatus }}">{{ $slaStatus === 'breach' ? '⚠ Breached' : ($slaStatus === 'warning' ? '⚡ At Risk' : '✓ On Track') }}</span>
        <div style="font-size:10.5px;color:var(--muted-lt);margin-top:3px">{{ $pct }}% of {{ $slaLimit }}h limit</div>
      </div>
    </div>
  @empty
    <div class="empty-msg">No open tickets. Everything is resolved! 🎉</div>
  @endforelse
</div>

@endsection

@section('scripts')
<script>
// Quarter dropdown toggle
function toggleQuarterMenu() {
  const menu    = document.getElementById('quarterMenu');
  const chevron = document.getElementById('quarterChevron');
  const isOpen  = menu.classList.contains('open');
  menu.classList.toggle('open', !isOpen);
  chevron.style.transform = isOpen ? '' : 'rotate(180deg)';
}
// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
  if (!document.getElementById('quarterWrap').contains(e.target)) {
    document.getElementById('quarterMenu').classList.remove('open');
    document.getElementById('quarterChevron').style.transform = '';
  }
});

// Configure SLA modal
function openSlaModal() {
  document.getElementById('slaModal').classList.add('open');
  document.body.style.overflow = 'hidden';
}
function closeSlaModal() {
  document.getElementById('slaModal').classList.remove('open');
  document.body.style.overflow = '';
}
document.getElementById('slaModal').addEventListener('click', function(e) {
  if (e.target === this) closeSlaModal();
});
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') closeSlaModal();
});

// Toast
const toast = document.getElementById('toastMsg');
if (toast) {
  setTimeout(() => toast.classList.add('show'), 100);
  setTimeout(() => toast.classList.remove('show'), 3500);
}

// Chart.js — Quarterly Trend
(function () {
  const labels = window._quarterlyLabels || [];
  const pcts   = window._quarterlyPcts   || [];
  const barColors = pcts.map(p => p >= 95 ? '#16a34a' : p >= 85 ? '#f97316' : '#dc2626');
  const ctx = document.getElementById('quarterlyChart');
  if (!ctx) return;
  new Chart(ctx, {
    data: {
      labels,
      datasets: [
        { type:'bar', label:'SLA Compliance %', data:pcts, backgroundColor:barColors, borderRadius:6, borderSkipped:false, barPercentage:0.55 },
        { type:'line', label:'95% Target', data:labels.map(()=>95), borderColor:'#2563eb', borderWidth:2, borderDash:[5,4], pointRadius:0, tension:0, fill:false }
      ]
    },
    options: {
      responsive:true, maintainAspectRatio:false,
      interaction:{ mode:'index', intersect:false },
      plugins:{
        legend:{ display:true, position:'bottom', labels:{ font:{size:11}, boxWidth:12, padding:16 } },
        tooltip:{ callbacks:{ label: c => c.dataset.type==='line' ? `Target: ${c.parsed.y}%` : `Compliance: ${c.parsed.y}%` } }
      },
      scales:{
        y:{ min:0, max:100, ticks:{ callback:v=>v+'%', font:{size:11}, stepSize:25 }, grid:{color:'#f1f5f9'}, border:{display:false} },
        x:{ ticks:{font:{size:11}}, grid:{display:false}, border:{display:false} }
      }
    }
  });
})();
</script>
@endsection