@extends('layouts.app')
@section('title', 'SLA Monitor – Fixtora')

@section('styles')
<style>
.page-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px}
.page-header h1{font-size:22px;font-weight:800;letter-spacing:-.5px;color:var(--navy)}
.page-header p{font-size:13px;color:var(--muted);margin-top:4px}
.hdr-btns{display:flex;gap:8px}
.btn-sm{padding:8px 14px;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:6px;border:1px solid var(--border);background:var(--surface);color:var(--text-sub);font-family:inherit;transition:all .15s}
.btn-sm:hover{background:var(--bg)}
.btn-primary{background:var(--blue);color:#fff;border-color:var(--blue)}
.btn-primary:hover{background:#1a42c4;color:#fff}

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

/* CHART AREA */
.chart-canvas-wrap{position:relative;height:200px}

/* COMPLIANCE TABLE */
.compliance-table{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow)}
.ct-header{display:grid;grid-template-columns:1fr 120px 120px 120px 130px;gap:12px;padding:11px 18px;background:var(--bg);border-bottom:1px solid var(--border);font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--muted)}
.ct-row{display:grid;grid-template-columns:1fr 120px 120px 120px 130px;gap:12px;padding:13px 18px;border-bottom:1px solid var(--border);align-items:center;font-size:13px}
.ct-row:last-child{border-bottom:none}
.ct-row:hover{background:#fafbff}

/* PROGRESS BAR INLINE */
.bar-wrap{height:6px;background:var(--bg);border-radius:20px;overflow:hidden;margin-top:4px;border:1px solid var(--border)}
.bar-fill{height:100%;border-radius:20px}

/* PILLS */
.pill{display:inline-block;padding:3px 10px;border-radius:20px;font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.4px}
.pill-ok{background:#dcfce7;color:var(--green);border:1px solid #bbf7d0}
.pill-warning{background:#fff7ed;color:var(--orange);border:1px solid #fed7aa}
.pill-breach{background:#fee2e2;color:var(--red);border:1px solid #fecaca}

.empty-msg{text-align:center;padding:30px;color:var(--muted);font-size:13px;font-weight:600}

/* Compliance table section header */
.ct-section-title{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px}
.ct-section-title h2{font-size:15px;font-weight:800;color:var(--navy)}
.ct-section-title p{font-size:12px;color:var(--muted);margin-top:2px}
.ct-count-badge{font-size:11px;font-weight:700;background:var(--blue-bg,#eff6ff);color:var(--blue);border:1px solid var(--blue-lt,#dbeafe);padding:4px 12px;border-radius:20px}
</style>
@endsection

@section('content')
<div class="page-header">
  <div>
    <h1>SLA Monitor</h1>
    <p>Track service level agreement compliance and breach risks in real-time.</p>
  </div>
  <div class="hdr-btns">
    <button class="btn-sm">This Quarter</button>
    <button class="btn-sm btn-primary">
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
      Configure SLA
    </button>
  </div>
</div>

{{-- =====================================================================
     TASK 18 + 19 + 20: KPI STAT CARDS
     - Task 18: SLA Compliance Rate (% resolved vs total)
     - Task 19: Active Breach Count (critical open tickets)
     - Task 20: Average Resolution Time (mean hours to resolve)
     ===================================================================== --}}
<div class="sla-grid">

  {{-- Task 18: SLA Compliance Rate --}}
  <div class="sla-stat">
    <div class="sla-stat-val" style="color:var(--green)">{{ $compliance }}%</div>
    <div class="sla-stat-lbl">SLA Compliance Rate</div>
    <div class="sla-stat-change ch-green">↑ {{ $resolved }} of {{ $total ?? ($resolved + $criticalOpen) }} tickets resolved</div>
  </div>

  {{-- Task 19: Active Breach Count --}}
  <div class="sla-stat">
    <div class="sla-stat-val" style="color:{{ $criticalOpen > 0 ? 'var(--red)' : 'var(--green)' }}">{{ $criticalOpen }}</div>
    <div class="sla-stat-lbl">Active Breaches</div>
    <div class="sla-stat-change {{ $criticalOpen > 0 ? 'ch-red' : 'ch-green' }}">
      {{ $criticalOpen > 0 ? 'Critical-priority open tickets' : '✓ No critical breaches' }}
    </div>
  </div>

  {{-- Resolved in SLA --}}
  <div class="sla-stat">
    <div class="sla-stat-val">{{ $resolved }}</div>
    <div class="sla-stat-lbl">Resolved Tickets</div>
    <div class="sla-stat-change ch-green">↑ Total resolved tickets</div>
  </div>

  {{-- Task 20: Average Resolution Time --}}
  <div class="sla-stat">
    <div class="sla-stat-val" style="color:var(--orange)">{{ $avgResolutionHrs }}h</div>
    <div class="sla-stat-lbl">Avg Resolution Time</div>
    <div class="sla-stat-change ch-orange">→ Mean hours from open → resolved</div>
  </div>

</div>

{{-- =====================================================================
     TASK 21 + TASK 18 (Quarterly Trend Chart)
     - Task 21: At-Risk Tickets List (top-5 open by priority & age)
     - Task 18: Quarterly SLA % trend chart (Chart.js bar + line)
     ===================================================================== --}}
<div class="sla-mid">

  {{-- Task 21: At-Risk Tickets (top-5 open sorted by priority & age) --}}
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
        $borderColor = match($ticket->priority) {
          'critical' => '#ef4444',
          'high'     => '#f97316',
          default    => '#1e3a6e',
        };
        $bgColor = match($ticket->priority) {
          'critical' => '#fef2f2',
          'high'     => '#fff7ed',
          default    => '#f4f5f8',
        };
        $txtColor = match($ticket->priority) {
          'critical' => '#dc2626',
          'high'     => '#c2410c',
          default    => '#6b7a8d',
        };
        $timerColor = match($ticket->priority) {
          'critical' => '#dc2626',
          'high'     => '#f97316',
          default    => '#1e3a6e',
        };
        $totalMins = $ticket->created_at->diffInMinutes(now());
        $hrs       = floor($totalMins / 60);
        $mins      = $totalMins % 60;
        $timer     = $hrs >= 1 ? "{$hrs}h {$mins}m" : "{$mins}m";
      @endphp
      <div class="sla-ticket-row" style="border-left-color:{{ $borderColor }}">
        <div>
          <span class="sla-priority" style="background:{{ $bgColor }};color:{{ $txtColor }}">
            {{ strtoupper($ticket->priority) }}
          </span>
        </div>
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

  {{-- Task 18: Quarterly SLA % Trend — Chart.js bar chart with compliance line --}}
  <div class="card-box">
    <div class="chart-hdr">
      <div>
        <div class="chart-title">Quarterly SLA % Trend</div>
        <div class="chart-sub">% resolved tickets vs total, per quarter</div>
      </div>
    </div>
    <div class="chart-canvas-wrap">
      <canvas id="quarterlyChart"></canvas>
    </div>
    {{-- Pass quarterly data to JS --}}
    <script>
      window._quarterlyLabels = @json(collect($quarterly)->pluck('label'));
      window._quarterlyPcts   = @json(collect($quarterly)->pluck('pct'));
    </script>
  </div>

</div>

{{-- =====================================================================
     TASK 22: Compliance Table — all open/in-progress tickets with
     priority, age, status, and SLA status indicator
     ===================================================================== --}}
<div class="ct-section-title">
  <div>
    <h2>Compliance Table</h2>
    <p>All open &amp; in-progress tickets with SLA breach status</p>
  </div>
  <span class="ct-count-badge">{{ $allOpen->count() }} tickets</span>
</div>

<div class="compliance-table">
  <div class="ct-header">
    <span>Ticket</span>
    <span>Priority</span>
    <span>Age</span>
    <span>Status</span>
    <span>SLA Status</span>
  </div>

  @forelse($allOpen as $ticket)
    @php
      $ageHrs   = $ticket->created_at->diffInHours(now());
      $slaLimit = match($ticket->priority) { 'critical' => 4, 'high' => 8, 'medium' => 24, default => 72 };
      $pct      = min(100, round(($ageHrs / $slaLimit) * 100));
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
      <div>
        <span class="pill pill-{{ $ticket->priority === 'low' ? 'ok' : ($ticket->priority === 'medium' ? 'warning' : 'breach') }}">
          {{ ucfirst($ticket->priority) }}
        </span>
      </div>
      <div style="font-size:12.5px;font-weight:600;color:var(--muted)">{{ $ageDisplay }}</div>
      <div style="font-size:12.5px;font-weight:600;color:var(--text-sub)">{{ ucfirst(str_replace('_',' ',$ticket->status)) }}</div>
      <div>
        <span class="pill pill-{{ $slaStatus }}">
          {{ $slaStatus === 'breach' ? '⚠ Breached' : ($slaStatus === 'warning' ? '⚡ At Risk' : '✓ On Track') }}
        </span>
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
// -----------------------------------------------------------------------
// Task 18: Quarterly SLA % Trend — Chart.js bar + target line
// -----------------------------------------------------------------------
(function () {
  const labels = window._quarterlyLabels || [];
  const pcts   = window._quarterlyPcts   || [];

  // Color bars by threshold
  const barColors = pcts.map(p =>
    p >= 95 ? '#16a34a' : p >= 85 ? '#f97316' : '#dc2626'
  );

  const ctx = document.getElementById('quarterlyChart');
  if (!ctx) return;

  new Chart(ctx, {
    data: {
      labels: labels,
      datasets: [
        {
          // Bar dataset — SLA % per quarter
          type: 'bar',
          label: 'SLA Compliance %',
          data: pcts,
          backgroundColor: barColors,
          borderRadius: 6,
          borderSkipped: false,
          barPercentage: 0.55,
        },
        {
          // Line dataset — 95% SLA target reference
          type: 'line',
          label: '95% Target',
          data: labels.map(() => 95),
          borderColor: '#2563eb',
          borderWidth: 2,
          borderDash: [5, 4],
          pointRadius: 0,
          tension: 0,
          fill: false,
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: { mode: 'index', intersect: false },
      plugins: {
        legend: {
          display: true,
          position: 'bottom',
          labels: { font: { size: 11, family: 'Montserrat' }, boxWidth: 12, padding: 16 }
        },
        tooltip: {
          callbacks: {
            label: ctx => ctx.dataset.type === 'line'
              ? `Target: ${ctx.parsed.y}%`
              : `Compliance: ${ctx.parsed.y}%`
          }
        }
      },
      scales: {
        y: {
          min: 0,
          max: 100,
          ticks: {
            callback: v => v + '%',
            font: { size: 11 },
            stepSize: 25,
          },
          grid: { color: '#f1f5f9' },
          border: { display: false },
        },
        x: {
          ticks: { font: { size: 11 } },
          grid: { display: false },
          border: { display: false },
        }
      }
    }
  });
})();
</script>
@endsection