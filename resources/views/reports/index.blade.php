@extends('layouts.app')

@section('title', 'Reports - Fixtora')

@section('styles')
<style>
.reports-page { max-width: 1220px; }
.reports-head { display:flex; justify-content:space-between; gap:16px; align-items:flex-start; flex-wrap:wrap; margin-bottom:18px; }
.reports-head h1 { font-size:22px; font-weight:800; letter-spacing:-.5px; color:var(--navy); margin-bottom:4px; }
.reports-head .subtitle { color:var(--muted); font-size:13px; margin:0; }
.reports-filters { display:flex; gap:10px; align-items:center; flex-wrap:wrap; }
.flt-btn { border:1px solid var(--border); background:var(--surface); color:var(--text-2); border-radius:8px; padding:8px 14px; font-size:12px; font-weight:700; cursor:pointer; }
.flt-btn.primary { background:var(--blue-2); color:#fff; border-color:var(--blue-2); }

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
        <div class="reports-filters">
            <button class="flt-btn">Last 30 Days</button>
            <button class="flt-btn">All Systems</button>
            <button class="flt-btn primary">Apply</button>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Tickets</div>
            <div class="stat-value">0</div>
            <div class="stat-sub">No data yet</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Avg Resolution Time</div>
            <div class="stat-value">0h 0m</div>
            <div class="stat-sub">No data yet</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">SLA Compliance</div>
            <div class="stat-value">0%</div>
            <div class="stat-sub">No data yet</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Customer CSAT</div>
            <div class="stat-value">0.0/5</div>
            <div class="stat-sub">No data yet</div>
        </div>
    </div>

    <div class="panel-grid">
        <div class="panel">
            <div class="panel-head">
                <div>
                    <div class="panel-title">Ticket Volume Trends</div>
                    <div class="panel-sub">Active resolution spikes over the last 30 days</div>
                </div>
                <div class="dot-legend">
                    <span class="dot-new">New</span>
                    <span class="dot-closed">Closed</span>
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
                    <div>0%</div>
                </div>
                <div class="issue-row">
                    <div class="issue-left"><span class="issue-dot" style="background:#3b82f6"></span>Frontend / UI Issues</div>
                    <div>0%</div>
                </div>
                <div class="issue-row">
                    <div class="issue-left"><span class="issue-dot" style="background:#bfdbfe"></span>API Integrations</div>
                    <div>0%</div>
                </div>
            </div>
        </div>
    </div>

    <div class="team-panel">
        <div class="team-head">
            <div>
                <h3>Team Performance</h3>
                <p>Live metrics for active support agents</p>
            </div>
            <a href="#">View All Agents</a>
        </div>
        <table class="team-table">
            <thead>
                <tr>
                    <th>Agent Name</th>
                    <th>Resolved</th>
                    <th>Avg Response</th>
                    <th>Load</th>
                    <th>CSAT</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($agents as $a)
                <tr>
                    <td>
                        <div class="agent-cell">
                            <div class="agent-avatar" style="background:{{ $a['color'] ?? 'var(--blue)' }}">{{ $a['initials'] ?? 'Ag' }}</div>
                            <div>
                                <div class="agent-name">{{ $a['name'] }}</div>
                                <div class="agent-role">{{ $a['role'] }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-weight:700">{{ $a['resolved'] }}</td>
                    <td>{{ $a['avg_response'] }}</td>
                    <td>
                        <div class="load-wrap"><div class="load-fill" style="width:{{ $a['load'] }}%"></div></div>
                    </td>
                    <td><span class="csat">{{ $a['csat'] }}</span></td>
                    <td>
                        @if (($a['status'] ?? '') === 'online')
                            <span class="status-badge online">● ONLINE</span>
                        @else
                            <span class="status-badge away">● AWAY</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-msg">
                            No team performance data yet.
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
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
        labels: ['Sep 01', 'Sep 05', 'Sep 10', 'Sep 14', 'Sep 18', 'Sep 22', 'Sep 26', 'Sep 30'],
        datasets: [
          {
            label: 'New',
            data: [0, 0, 0, 0, 0, 0, 0, 0],
            borderColor: '#0f3f83',
            backgroundColor: 'rgba(15,63,131,0.08)',
            tension: 0.42,
            fill: true,
            pointRadius: 0,
            borderWidth: 3
          },
          {
            label: 'Closed',
            data: [0, 0, 0, 0, 0, 0, 0, 0],
            borderColor: '#94a3b8',
            tension: 0.35,
            pointRadius: 0,
            borderWidth: 2
          }
        ]
      },
      options: {
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          x: { grid: { display: false }, ticks: { color: '#6b7280' } },
          y: { grid: { color: '#eef2f7' }, ticks: { color: '#6b7280' }, beginAtZero: true }
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
          data: [0, 0, 0],
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
</script>
@endsection