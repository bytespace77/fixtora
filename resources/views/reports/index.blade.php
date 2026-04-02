@extends('layouts.app')

@section('title', 'Reports - Fixtora')

@section('styles')
<style>
.reports-page { max-width: 1220px; }
.reports-head { display:flex; justify-content:space-between; gap:16px; align-items:flex-start; flex-wrap:wrap; margin-bottom:18px; }
.reports-head h1 { font-size:22px; font-weight:800; letter-spacing:-.5px; color:var(--navy); margin-bottom:4px; }
.reports-head .subtitle { color:var(--muted); font-size:13px; margin:0; }
/* Page header / Actions */
.header-actions { display:flex; gap:10px; margin-top:4px; align-items:center; flex-wrap:wrap; }

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

.stats-grid { display:grid; grid-template-columns:repeat(4, minmax(0,1fr)); gap:14px; margin-bottom:14px; }
.stat-card { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); padding:20px 22px; box-shadow:var(--shadow); text-align:left; }
.stat-label { font-size:11px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.6px; margin:0 0 4px; }
.stat-value { font-size:34px; line-height:1; font-weight:800; color:var(--navy-3); letter-spacing:-1px; }
.stat-sub { font-size:11.5px; color:var(--green); font-weight:600; margin-top:6px; }

.panel-grid { display:grid; grid-template-columns:2fr 1fr; gap:16px; margin-bottom:20px; }
.panel { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); padding:20px; box-shadow:var(--shadow); }
.panel-head { display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; gap:8px; }
.panel-title { font-size:14px; font-weight:700; color:var(--navy); margin-bottom:2px; }
.panel-sub { color:var(--muted); font-size:12px; }
.dot-legend { display:flex; gap:12px; font-size:11px; font-weight:700; color:var(--muted); }
.dot-legend span::before { content:''; width:8px; height:8px; border-radius:50%; display:inline-block; margin-right:6px; vertical-align:middle; }
.dot-new::before { background:var(--blue-2); }
.dot-closed::before { background:var(--muted-lt); }
.dot-task-new::before { background:#16a34a; }
.dot-task-done::before { background:#86efac; }
.chart-holder { height:250px; }
.doughnut-holder { height:210px; display:flex; align-items:center; justify-content:center; }
.issue-list { margin-top:4px; }
.issue-row { display:flex; justify-content:space-between; font-size:12px; padding:5px 0; color:var(--text-2); font-weight:600; }
.issue-left { display:flex; align-items:center; gap:8px; }
.issue-dot { width:9px; height:9px; border-radius:50%; display:inline-block; }

.team-panel { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); box-shadow:var(--shadow); overflow:hidden; }
.team-head { display:flex; align-items:flex-start; justify-content:space-between; padding:16px 18px; border-bottom:1px solid var(--border); }
.team-head h3 { font-size:14px; font-weight:700; color:var(--navy); margin-bottom:4px; }
.team-head p { color:var(--muted); font-size:12px; margin:0; }
.team-head a { color:var(--blue-2); font-size:11.5px; font-weight:600; text-decoration:none; }
.team-table { width:100%; border-collapse:collapse; }
.team-table th { text-align:left; font-size:10.5px; color:var(--muted); letter-spacing:.5px; text-transform:uppercase; padding:10px 18px; border-bottom:1px solid #f1f5f9; }
.team-table td { padding:12px 18px; border-bottom:1px solid #f8fafc; vertical-align:middle; font-size:13px; }
.agent-cell { display:flex; align-items:center; gap:10px; }
.agent-avatar { width:32px; height:32px; border-radius:50%; background:linear-gradient(135deg,var(--blue),var(--navy-3)); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:12px; }
.agent-name { font-weight:700; color:var(--text); font-size:13px; line-height:1.2; }
.agent-role { font-size:11px; color:var(--muted); }
.load-wrap { width:86px; background:#e5e7eb; border-radius:999px; height:6px; overflow:hidden; }
.load-fill { height:100%; background:var(--blue-2); border-radius:999px; }
.status-badge { font-size:10px; font-weight:800; letter-spacing:.4px; text-transform:uppercase; }
.online { color:var(--green); }
.away { color:var(--orange); }
.csat { color:#059669; background:#ecfdf5; border-radius:999px; padding:3px 8px; font-size:11px; font-weight:800; display:inline-block; }
.empty-msg { text-align:center; padding:26px 10px; color:var(--muted); font-size:13px; font-weight:600; }
.role-group-row td { padding:8px 18px 6px; background:var(--bg); border-bottom:1px solid var(--border); border-top:1px solid var(--border); }
.role-group-label { display:inline-flex; align-items:center; gap:7px; font-size:10.5px; font-weight:800; letter-spacing:.8px; text-transform:uppercase; color:var(--navy); }
.role-group-dot { width:8px; height:8px; border-radius:50%; display:inline-block; flex-shrink:0; }

@media (max-width: 1120px) {
  .stats-grid { grid-template-columns:repeat(2, minmax(0,1fr)); }
  .panel-grid { grid-template-columns:1fr; }
}
@media (max-width: 640px) {
  .reports-head h1 { font-size:18px; }
  .panel-title, .team-head h3 { font-size:13px; }
  .stat-value { font-size:26px; }
}
</style>
@endsection

@section('content')
<div class="reports-page">
    <div class="reports-head">
        <div>
            <h1>Reports & Analytics</h1>
            <p class="subtitle">Real-time performance overview for the last 30 days</p>
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
                  <div class="range-option {{ $range === $k ? 'selected' : '' }}" onclick="applyRange('{{ $k }}')">{{ $v }}</div>
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
            @if(auth()->user()->hasPermission('export_reports') || auth()->user()->hasPermission('view_reports'))
            <div class="export-wrap">
              <button class="export-btn" id="exportBtn" onclick="toggleDropdown('exportMenu','exportBtn')">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Export Report
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
              </button>
              <div class="export-dropdown" id="exportMenu">
                <a class="export-option" href="{{ route('reports.index') }}?range={{ $range }}&export=pdf" target="_blank">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                  Export as PDF
                </a>
                <a class="export-option" href="{{ route('reports.index') }}?range={{ $range }}&export=excel">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                  Export as Excel (CSV)
                </a>
              </div>
            </div>
            @endif
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Tickets</div>
            <div class="stat-value">{{ $totalTickets }}</div>
            <div class="stat-sub">{{ $totalTasks }} tasks this period</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Avg Resolution Time</div>
            <div class="stat-value">{{ $avgResolution }}h</div>
            <div class="stat-sub">Tickets &amp; tasks combined</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">SLA Task Success Rate</div>
            <div class="stat-value">{{ $slaCompliance }}%</div>
            <div class="stat-sub">Tasks completed on time</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Customer CSAT</div>
            <div class="stat-value">{{ $csat }}</div>
            <div class="stat-sub">Average rating</div>
        </div>
    </div>

    <div class="panel-grid">
        <div class="panel">
            <div class="panel-head">
                <div>
                    <div class="panel-title">Ticket Volume Trends</div>
                    <div class="panel-sub">Ticket &amp; task volume over the selected period</div>
                </div>
                <div class="dot-legend">
                    <span class="dot-new">New Tickets</span>
                    <span class="dot-closed">Closed Tickets</span>
                    <span class="dot-task-new">New Tasks</span>
                    <span class="dot-task-done">Done Tasks</span>
                </div>
            </div>
            <div class="chart-holder">
                <canvas id="ticketTrendChart"></canvas>
            </div>
        </div>

        <div class="panel">
            <div class="panel-title">Issue Distribution</div>
            <div class="panel-sub">Resolution by infrastructure type</div>
            <div class="doughnut-holder">
                <canvas id="issueDistributionChart"></canvas>
            </div>
            <div class="issue-list">
                <div class="issue-row">
                    <div class="issue-left"><span class="issue-dot" style="background:#0f3f83"></span>Backend Infrastructure</div>
                    <div>{{ $distribution[0] }}</div>
                </div>
                <div class="issue-row">
                    <div class="issue-left"><span class="issue-dot" style="background:#3b82f6"></span>Frontend / UI Issues</div>
                    <div>{{ $distribution[1] }}</div>
                </div>
                <div class="issue-row">
                    <div class="issue-left"><span class="issue-dot" style="background:#bfdbfe"></span>API Integrations</div>
                    <div>{{ $distribution[2] }}</div>
                </div>
            </div>
        </div>
    </div>

    @if($isSuperAdmin)
    <div class="team-panel">
        <div class="team-head">
            <div>
                <h3>Team Performance</h3>
                <p>Metrics grouped by role for all staff</p>
            </div>
        </div>
        <table class="team-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Resolved</th>
                    <th>Avg Response</th>
                    <th>Load</th>
                    <th>CSAT</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $roleColors = [
                        'Super Admin' => '#1e3a6e',
                        'Developer'   => '#2a7a5e',
                        'Admin'       => '#5a3e8a',
                    ];
                @endphp
                @forelse($agentsByRole as $roleLabel => $members)
                    {{-- Role header row --}}
                    <tr class="role-group-row">
                        <td colspan="5">
                            <span class="role-group-label">
                                <span class="role-group-dot" style="background:{{ $roleColors[$roleLabel] ?? '#6b7280' }}"></span>
                                {{ $roleLabel }}
                            </span>
                        </td>
                    </tr>
                    {{-- Member rows --}}
                    @foreach($members as $a)
                    <tr>
                        <td>
                            <div class="agent-cell">
                                <div class="agent-avatar" style="background:{{ $a['color'] }}">{{ $a['initials'] }}</div>
                                <div class="agent-name">{{ $a['name'] }}</div>
                            </div>
                        </td>
                        <td style="font-weight:700">{{ $a['resolved'] }}</td>
                        <td>{{ $a['avg_response'] }}</td>
                        <td>
                            <div class="load-wrap"><div class="load-fill" style="width:{{ $a['load'] }}%"></div></div>
                        </td>
                        <td><span class="csat">—</span></td>
                    </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-msg">No team performance data yet.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
(() => {
  const trendEl = document.getElementById('ticketTrendChart');
  if (trendEl && window.Chart) {
    new Chart(trendEl, {
      type: 'line',
      data: {
        labels: {!! json_encode($labels) !!},
        datasets: [
          {
            label: 'New Tickets',
            data: {!! json_encode($newTrend) !!},
            borderColor: '#0f3f83',
            backgroundColor: 'rgba(15,63,131,0.07)',
            tension: 0.42,
            fill: true,
            pointRadius: 0,
            borderWidth: 2.5
          },
          {
            label: 'Closed Tickets',
            data: {!! json_encode($closedTrend) !!},
            borderColor: '#94a3b8',
            tension: 0.35,
            pointRadius: 0,
            borderWidth: 2,
            borderDash: [4, 3]
          },
          {
            label: 'New Tasks',
            data: {!! json_encode($newTaskTrend) !!},
            borderColor: '#16a34a',
            backgroundColor: 'rgba(22,163,74,0.06)',
            tension: 0.42,
            fill: true,
            pointRadius: 0,
            borderWidth: 2.5
          },
          {
            label: 'Done Tasks',
            data: {!! json_encode($doneTaskTrend) !!},
            borderColor: '#86efac',
            tension: 0.35,
            pointRadius: 0,
            borderWidth: 2,
            borderDash: [4, 3]
          }
        ]
      },
      options: {
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#fff',
            titleColor: '#111827',
            bodyColor: '#6b7280',
            borderColor: '#e5e7ef',
            borderWidth: 1,
            padding: 10,
            boxPadding: 4,
            usePointStyle: true
          }
        },
        scales: {
          x: { grid: { display: false }, ticks: { color: '#6b7280', maxRotation: 0 } },
          y: { grid: { color: '#eef2f7' }, ticks: { color: '#6b7280', stepSize: 1, callback: v => Number.isInteger(v)?v:'' }, beginAtZero: true }
        }
      }
    });
  }

  const issueEl = document.getElementById('issueDistributionChart');
  if (issueEl && window.Chart) {
    new Chart(issueEl, {
      type: 'doughnut',
      data: {
        labels: ['Backend Infrastructure', 'Frontend / UI Issues', 'API Integrations'],
        datasets: [{
          data: {!! json_encode($distribution) !!},
          backgroundColor: ['#0f3f83', '#3b82f6', '#bfdbfe'],
          borderWidth: 0
        }]
      },
      options: {
        cutout: '72%',
        plugins: { legend: { display: false } },
        maintainAspectRatio: false
      }
    });
  }
})();

// Filter and Export Interactivity Functions
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

function applyRange(r) { window.location.href = '{{ route('reports.index') }}?range=' + r; }

function applyCustomRange() {
  const from = document.getElementById('customFrom').value;
  const to   = document.getElementById('customTo').value;
  if (!from || !to) { alert('Please select both dates.'); return; }
  window.location.href = '{{ route('reports.index') }}?range=custom&custom=' + from + ',' + to;
}
</script>
@endsection