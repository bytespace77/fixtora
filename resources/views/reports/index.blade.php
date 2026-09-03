@extends('layouts.app')

@section('title', 'Reports - Fixtora')

@section('styles')
<style>
.reports-page { width:100%; max-width:none; min-width:0; }
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

.stats-grid { display:grid; grid-template-columns:repeat(5, minmax(0,1fr)); gap:14px; margin-bottom:14px; }
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

/* Compliance reporting */
.compliance-panel { margin-top:20px; background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); box-shadow:var(--shadow); overflow:hidden; }
.compliance-head { padding:18px 20px 14px; border-bottom:1px solid var(--border); }
.compliance-head h3 { margin:0 0 3px; color:var(--navy); font-size:16px; font-weight:800; }
.compliance-head p { margin:0; color:var(--muted); font-size:12px; }
.filter-area { display:flex; align-items:flex-end; flex-wrap:wrap; gap:10px; min-height:61px; padding:12px 20px; background:var(--bg); border-bottom:1px solid var(--border); }
.filter-toggle-row { display:flex; align-items:center; align-self:flex-end; }
.filter-toggle-btn { display:inline-flex; align-items:center; gap:7px; height:36px; padding:0 15px; border:1px solid var(--border-2); border-radius:7px; color:var(--navy); background:var(--surface); font:inherit; font-size:12px; font-weight:800; cursor:pointer; }
.filter-toggle-btn:hover,.filter-toggle-btn.active { color:#fff; background:var(--navy); border-color:var(--navy); }
.filter-toggle-btn svg { transition:transform .15s ease; }
.filter-toggle-btn.active svg { transform:rotate(180deg); }
.advanced-filter { display:none; align-items:flex-end; flex:1; flex-wrap:wrap; gap:10px; padding:0; background:transparent; border:0; box-shadow:none; }
.advanced-filter.open { display:flex; }
.filter-field { min-width:170px; }
.filter-field label { display:block; margin-bottom:5px; color:var(--muted); font-size:10px; font-weight:800; letter-spacing:.5px; text-transform:uppercase; }
.filter-field select,.filter-field input { width:100%; height:36px; padding:0 10px; color:var(--text); background:var(--surface); border:1px solid var(--border-2); border-radius:7px; font:inherit; font-size:12px; }
.filter-submit,.filter-reset { height:36px; padding:0 15px; border-radius:7px; font:inherit; font-size:12px; font-weight:700; cursor:pointer; }
.filter-submit { color:#fff; background:var(--navy); border:1px solid var(--navy); }
.filter-reset { display:inline-flex; align-items:center; color:var(--text-2); background:var(--surface); border:1px solid var(--border-2); text-decoration:none; }
.report-table-wrap { overflow-x:auto; }
.report-table { width:100%; min-width:930px; border-collapse:collapse; }
.report-table th { padding:10px 12px; color:var(--muted); background:#fbfcfe; border-bottom:1px solid var(--border); font-size:9.5px; font-weight:800; letter-spacing:.45px; text-align:left; text-transform:uppercase; white-space:nowrap; }
.report-table td { padding:12px; color:var(--text-2); border-bottom:1px solid #f1f5f9; font-size:12px; vertical-align:middle; }
.report-table tr:last-child td { border-bottom:0; }
.report-table .ticket-ref { color:var(--blue); font-weight:800; text-decoration:none; }
.compliance-badge { display:inline-flex; padding:4px 8px; border-radius:999px; font-size:9.5px; font-weight:800; letter-spacing:.3px; text-transform:uppercase; white-space:nowrap; }
.compliance-badge.compliant { color:#047857; background:#ecfdf5; }
.compliance-badge.breached { color:#b91c1c; background:#fef2f2; }
.compliance-badge.pending { color:#b45309; background:#fffbeb; }
.compliance-badge.not_applicable { color:#64748b; background:#f1f5f9; }
.penalty-points { font-weight:800; color:#b91c1c; }
.inline-compliance-select { min-width:125px; height:32px; padding:0 8px; color:var(--text); background:#fff; border:1px solid var(--border-2); border-radius:6px; font:inherit; font-size:11px; }
.inline-penalty-input { width:66px; height:32px; padding:0 7px; color:var(--text); background:#fff; border:1px solid var(--border-2); border-radius:6px; font:inherit; font-size:11px; font-weight:700; }
.compliance-edit-control { display:none; }
.report-row-actions { display:flex; align-items:center; justify-content:flex-start; gap:6px; min-height:32px; white-space:nowrap; }
.inline-edit-btn,.inline-save-btn,.inline-cancel-btn { height:32px; padding:0 10px; align-items:center; justify-content:center; line-height:1; border-radius:6px; font:inherit; font-size:10px; font-weight:800; cursor:pointer; }
.inline-edit-btn { display:inline-flex; width:34px; padding:0; }
.inline-edit-btn svg { width:14px; height:14px; }
.inline-edit-btn,.inline-cancel-btn { color:var(--navy); background:#fff; border:1px solid var(--border-2); }
.inline-save-btn { display:none; color:#fff; background:var(--navy); border:1px solid var(--navy); }
.inline-cancel-btn { display:none; }
.inline-save-btn:hover { background:#173b70; }
.compliance-editing .compliance-display { display:none; }
.compliance-editing .compliance-edit-control,.compliance-editing .inline-save-btn,.compliance-editing .inline-cancel-btn { display:inline-flex; }
.compliance-editing .inline-edit-btn { display:none; }
.compliance-pagination { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:14px 20px; border-top:1px solid var(--border); }
.pagination-info { color:var(--muted); font-size:11px; }
.pagination-links { display:flex; align-items:center; gap:5px; }
.pagination-link { display:inline-flex; align-items:center; justify-content:center; min-width:32px; height:32px; padding:0 9px; color:var(--text-2); background:#fff; border:1px solid var(--border-2); border-radius:6px; font-size:11px; font-weight:700; text-decoration:none; }
.pagination-link:hover { color:var(--blue); border-color:var(--blue); }
.pagination-link.active { color:#fff; background:var(--navy); border-color:var(--navy); }
.pagination-link.disabled { color:#aab4c3; background:#f8fafc; cursor:not-allowed; }
.pagination-ellipsis { display:inline-flex; align-items:center; justify-content:center; min-width:24px; height:32px; color:var(--muted); font-size:12px; }
.compliance-panel + .team-panel { margin-top:20px; }

@media (max-width: 1120px) {
  .stats-grid { grid-template-columns:repeat(2, minmax(0,1fr)); }
  .panel-grid { grid-template-columns:1fr; }
}
@media (min-width: 1121px) and (max-width: 1450px) {
  .stats-grid { grid-template-columns:repeat(3, minmax(0,1fr)); }
}
@media (max-width: 640px) {
  .reports-head h1 { font-size:18px; }
  .panel-title, .team-head h3 { font-size:13px; }
  .stat-value { font-size:26px; }
  .filter-area { align-items:stretch; }
  .advanced-filter { flex-basis:100%; width:100%; }
  .filter-field { width:100%; }
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
            @if(auth()->user()->hasPermission('export_reports'))
            <div class="export-wrap">
              <button class="export-btn" id="exportBtn" onclick="toggleDropdown('exportMenu','exportBtn')">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Export Report
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
              </button>
              <div class="export-dropdown" id="exportMenu">
                @php $exportParams = array_merge(request()->except(['export', 'compliance_page']), ['range'=>$range, 'from'=>$from->format('Y-m-d'), 'to'=>$to->format('Y-m-d')]); @endphp
                <a class="export-option" href="{{ route('reports.index', array_merge($exportParams, ['export'=>'pdf'])) }}" target="_blank">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                  Export as PDF
                </a>
                <a class="export-option" href="{{ route('reports.index', array_merge($exportParams, ['export'=>'excel'])) }}">
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
            <div class="stat-label">Tickets Resolved</div>
            <div class="stat-value">{{ $ticketsResolved }}</div>
            <div class="stat-sub">Resolved in this period</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Compliance Followed</div>
            <div class="stat-value">{{ $complianceFollowed }} {{ Str::plural('Case', $complianceFollowed) }}</div>
            <div class="stat-sub">Tickets completed within SLA</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Compliance Not Followed</div>
            <div class="stat-value">{{ $complianceNotFollowed }} {{ Str::plural('Case', $complianceNotFollowed) }}</div>
            <div class="stat-sub">Tickets that breached SLA</div>
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
            <div class="panel-title">Ticket Status Overview</div>
            <div class="panel-sub">Tickets by current status</div>
            <div class="doughnut-holder">
                <canvas id="issueDistributionChart"></canvas>
            </div>
            <div class="issue-list">
                <div class="issue-row">
                    <div class="issue-left"><span class="issue-dot" style="background:#0f3f83"></span>Open</div>
                    <div>{{ $distribution[0] }}</div>
                </div>
                <div class="issue-row">
                    <div class="issue-left"><span class="issue-dot" style="background:#3b82f6"></span>In Progress</div>
                    <div>{{ $distribution[1] }}</div>
                </div>
                <div class="issue-row">
                    <div class="issue-left"><span class="issue-dot" style="background:#22c55e"></span>Resolved</div>
                    <div>{{ $distribution[2] }}</div>
                </div>
                <div class="issue-row">
                    <div class="issue-left"><span class="issue-dot" style="background:#f59e0b"></span>Pending User Response</div>
                    <div>{{ $distribution[3] }}</div>
                </div>
                <div class="issue-row">
                    <div class="issue-left"><span class="issue-dot" style="background:#ef4444"></span>Escalated</div>
                    <div>{{ $distribution[4] }}</div>
                </div>
            </div>
        </div>
    </div>

    @if($isSuperAdmin)
    <div class="compliance-panel">
        <div class="compliance-head">
            <h3>Superadmin Summary</h3>
            <p>Company-level ticket and SLA performance for the selected period</p>
        </div>
        <div class="filter-area">
        <div class="filter-toggle-row"><button type="button" class="filter-toggle-btn {{ request('filter_panel') === 'summary' ? 'active' : '' }}" id="summaryFilterBtn" onclick="toggleReportFilter('summaryFilter','summaryFilterBtn')" aria-expanded="{{ request('filter_panel') === 'summary' ? 'true' : 'false' }}">Filter <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg></button></div>
        <form method="GET" action="{{ route('reports.index') }}" class="advanced-filter auto-filter-form {{ request('filter_panel') === 'summary' ? 'open' : '' }}" id="summaryFilter">
            <input type="hidden" name="filter_panel" value="summary">
            <input type="hidden" name="range" value="{{ $range }}">
            <input type="hidden" name="from" value="{{ $from->format('Y-m-d') }}">
            <input type="hidden" name="to" value="{{ $to->format('Y-m-d') }}">
            <div class="filter-field">
                <label for="company_id">Company</label>
                <select name="company_id" id="company_id">
                    <option value="">All companies</option>
                    @foreach($companies as $company)
                    <option value="{{ $company->id }}" @selected((int)$companyFilter === (int)$company->id)>{{ $company->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-field"><label for="summary_start">Start Date</label><input type="date" id="summary_start" name="table_from" value="{{ $tableFrom->format('Y-m-d') }}"></div>
            <div class="filter-field"><label for="summary_end">End Date</label><input type="date" id="summary_end" name="table_to" value="{{ $tableTo->format('Y-m-d') }}"></div>
            <a class="filter-reset" href="{{ route('reports.index', ['range'=>$range, 'from'=>$from->format('Y-m-d'), 'to'=>$to->format('Y-m-d')]) }}">Reset</a>
        </form>
        </div>
        <div class="report-table-wrap">
            <table class="report-table">
                <thead><tr><th>Company</th><th>Total Tickets</th><th>Total Tickets Closed</th><th>Pending</th><th>Resolved</th><th>First Response Time</th><th>Average Resolution Time</th><th>Compliance Followed</th><th>Compliance Not Followed</th><th>Penalty Points</th></tr></thead>
                <tbody>
                @forelse($complianceSummary as $summary)
                    @php $avgMinutes = $summary['avg_resolution_minutes']; @endphp
                    <tr>
                        <td style="font-weight:800;color:var(--navy)">{{ $summary['company'] }}</td>
                        <td>{{ $summary['total'] }}</td><td>{{ $summary['closed'] }}</td><td>{{ $summary['pending'] }}</td><td>{{ $summary['resolved'] }}</td>
                        <td>{{ $summary['first_response_minutes'] === null ? '—' : intdiv($summary['first_response_minutes'], 60).'h '.($summary['first_response_minutes'] % 60).'m' }}</td>
                        <td>{{ $avgMinutes === null ? '—' : intdiv($avgMinutes, 60).'h '.($avgMinutes % 60).'m' }}</td>
                        <td>{{ $summary['compliant'] }}</td><td>{{ $summary['breached'] }}</td>
                        <td class="penalty-points">{{ $summary['penalty'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="10"><div class="empty-msg">No summary data matches the selected filters.</div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div class="compliance-panel">
        <div class="compliance-head">
            <h3>{{ $isSuperAdmin ? 'Ticket Status Breakdown' : 'Summary' }}</h3>
            <p>{{ $isSuperAdmin ? 'Ticket-level SLA results across permitted companies' : 'SLA results for tickets you are permitted to access' }}</p>
        </div>
        <div class="filter-area">
        <div class="filter-toggle-row"><button type="button" class="filter-toggle-btn {{ request('filter_panel') === 'breakdown' ? 'active' : '' }}" id="breakdownFilterBtn" onclick="toggleReportFilter('breakdownFilter','breakdownFilterBtn')" aria-expanded="{{ request('filter_panel') === 'breakdown' ? 'true' : 'false' }}">Filter <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg></button></div>
        <form method="GET" action="{{ route('reports.index') }}" class="advanced-filter auto-filter-form {{ request('filter_panel') === 'breakdown' ? 'open' : '' }}" id="breakdownFilter">
            <input type="hidden" name="filter_panel" value="breakdown">
            <input type="hidden" name="range" value="{{ $range }}">
            <input type="hidden" name="from" value="{{ $from->format('Y-m-d') }}">
            <input type="hidden" name="to" value="{{ $to->format('Y-m-d') }}">
            @if($isSuperAdmin)
            <div class="filter-field"><label for="breakdown_company">Company</label><select name="company_id" id="breakdown_company"><option value="">All companies</option>@foreach($companies as $company)<option value="{{ $company->id }}" @selected((int)$companyFilter === (int)$company->id)>{{ $company->name }}</option>@endforeach</select></div>
            <div class="filter-field"><label for="compliance_status">Status</label><select name="compliance_status" id="compliance_status"><option value="">All statuses</option><option value="compliant" @selected($complianceFilter === 'compliant')>Compliant</option><option value="breached" @selected($complianceFilter === 'breached')>Breached</option><option value="pending" @selected($complianceFilter === 'pending')>Pending</option><option value="not_applicable" @selected($complianceFilter === 'not_applicable')>Not Applicable</option></select></div>
            @endif
            @unless($isSuperAdmin)
            <div class="filter-field">
                <label for="status">Status</label>
                <select name="status" id="status">
                    <option value="">All statuses</option>
                    @foreach(['open'=>'Open','in_progress'=>'In Progress','in_review'=>'In Review','pending_user_response'=>'Pending User Response','escalated'=>'Escalated','resolved'=>'Resolved','closed'=>'Closed'] as $value=>$label)
                    <option value="{{ $value }}" @selected($statusFilter === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            @endunless
            @unless($isSuperAdmin)
            <div class="filter-field">
                <label for="user_compliance_status">Compliance Status</label>
                <select name="compliance_status" id="user_compliance_status">
                    <option value="">All compliance</option>
                    <option value="compliant" @selected($complianceFilter === 'compliant')>Compliant</option>
                    <option value="breached" @selected($complianceFilter === 'breached')>Breached</option>
                    <option value="pending" @selected($complianceFilter === 'pending')>Pending</option>
                    <option value="not_applicable" @selected($complianceFilter === 'not_applicable')>Not Applicable</option>
                </select>
            </div>
            @endunless
            <div class="filter-field"><label for="breakdown_start">Start Date</label><input type="date" id="breakdown_start" name="table_from" value="{{ $tableFrom->format('Y-m-d') }}"></div>
            <div class="filter-field"><label for="breakdown_end">End Date</label><input type="date" id="breakdown_end" name="table_to" value="{{ $tableTo->format('Y-m-d') }}"></div>
            <a class="filter-reset" href="{{ route('reports.index', ['range'=>$range, 'from'=>$from->format('Y-m-d'), 'to'=>$to->format('Y-m-d')]) }}">Reset</a>
        </form>
        </div>
        <div class="report-table-wrap">
            <table class="report-table">
                <thead><tr><th>Ticket ID</th><th>Name</th>@if($isSuperAdmin)<th>Company</th>@endif<th>Resolver</th><th>Status</th><th>Start Date</th><th>End Date</th><th>Response Time</th><th>Resolution Time</th><th>Compliance</th><th>Penalty Points</th>@if(auth()->user()->isSuperAdmin())<th>Action</th>@endif</tr></thead>
                <tbody>
                @forelse($complianceTickets as $ticket)
                    @php $minutes = $ticket->report_resolution_minutes; $responseMinutes = $ticket->report_first_response_minutes; $compliance = $ticket->report_compliance; @endphp
                    <tr>
                        <td><a class="ticket-ref" href="{{ route('tickets.show', $ticket) }}">#{{ str_pad((string)$ticket->id, 4, '0', STR_PAD_LEFT) }}</a></td>
                        <td>{{ optional($ticket->user)->name ?? '—' }}</td>
                        @if($isSuperAdmin)<td>{{ optional($ticket->company)->name ?? '—' }}</td>@endif
                        <td>{{ optional($ticket->assignedDeveloper)->name ?? '—' }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</td>
                        <td>{{ optional($ticket->assigned_date)->format('Y-m-d H:i') ?? '—' }}</td>
                        <td>{{ optional($ticket->resolved_at ?: $ticket->actual_delivery_date)->format('Y-m-d H:i') ?? '—' }}</td>
                        <td>{{ $responseMinutes === null ? 'No response' : intdiv($responseMinutes, 60).'h '.($responseMinutes % 60).'m' }}</td>
                        <td>{{ $minutes === null ? 'Pending' : intdiv($minutes, 60).'h '.($minutes % 60).'m' }}</td>
                        @if(auth()->user()->isSuperAdmin())
                        <td>
                            <span class="compliance-display compliance-badge {{ $compliance }}">{{ ucfirst(str_replace('_', ' ', $compliance)) }}</span>
                            <form id="compliance-form-{{ $ticket->id }}" method="POST" action="{{ route('reports.compliance.update', $ticket) }}">
                                @csrf
                                @method('PATCH')
                                <select class="inline-compliance-select compliance-edit-control" name="compliance_status" aria-label="Compliance status for ticket #{{ $ticket->id }}">
                                    @foreach(['compliant'=>'Compliant','breached'=>'Breached','pending'=>'Pending','not_applicable'=>'Not Applicable'] as $value=>$label)
                                    <option value="{{ $value }}" @selected($compliance === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </td>
                        <td>
                            <span class="compliance-display penalty-points">{{ $ticket->report_penalty_points }}</span>
                            <input class="inline-penalty-input compliance-edit-control" form="compliance-form-{{ $ticket->id }}" type="number" name="penalty_points" min="0" max="999999" value="{{ $ticket->report_penalty_points }}" aria-label="Penalty points for ticket #{{ $ticket->id }}">
                        </td>
                        <td>
                            <div class="report-row-actions">
                                <button class="inline-edit-btn" type="button" onclick="editComplianceRow(this)" aria-label="Edit compliance" title="Edit compliance">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                </button>
                                <button class="inline-save-btn" form="compliance-form-{{ $ticket->id }}" type="submit">Save</button>
                                <button class="inline-cancel-btn" type="button" onclick="cancelComplianceRow(this)">Cancel</button>
                            </div>
                        </td>
                        @else
                        <td><span class="compliance-badge {{ $compliance }}">{{ ucfirst(str_replace('_', ' ', $compliance)) }}</span></td>
                        <td class="penalty-points">{{ $ticket->report_penalty_points }}</td>
                        @endif
                    </tr>
                @empty
                    <tr><td colspan="{{ ($isSuperAdmin ? 11 : 10) + (auth()->user()->isSuperAdmin() ? 1 : 0) }}"><div class="empty-msg">No tickets match the selected filters.</div></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($complianceTickets->hasPages())
        <div class="compliance-pagination">
            <span class="pagination-info">Showing {{ $complianceTickets->firstItem() }}–{{ $complianceTickets->lastItem() }} of {{ $complianceTickets->total() }}</span>
            <div class="pagination-links">
                @if($complianceTickets->onFirstPage())
                    <span class="pagination-link disabled">Previous</span>
                @else
                    <a class="pagination-link" href="{{ $complianceTickets->previousPageUrl() }}">Previous</a>
                @endif
                @php
                    $currentPage = $complianceTickets->currentPage();
                    $lastPage = $complianceTickets->lastPage();
                    $visiblePages = collect([1, $lastPage])
                        ->merge(range(max(1, $currentPage - 2), min($lastPage, $currentPage + 2)))
                        ->unique()->sort()->values();
                    $previousVisiblePage = null;
                @endphp
                @foreach($visiblePages as $page)
                    @if($previousVisiblePage !== null && $page > $previousVisiblePage + 1)
                        <span class="pagination-ellipsis">…</span>
                    @endif
                    <a class="pagination-link {{ $page === $currentPage ? 'active' : '' }}" href="{{ $complianceTickets->url($page) }}">{{ $page }}</a>
                    @php $previousVisiblePage = $page; @endphp
                @endforeach
                @if($complianceTickets->hasMorePages())
                    <a class="pagination-link" href="{{ $complianceTickets->nextPageUrl() }}">Next</a>
                @else
                    <span class="pagination-link disabled">Next</span>
                @endif
            </div>
        </div>
        @endif
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
                    <th>Pending Tickets</th>
                    <th>Resolved</th>
                    <th>Avg Response</th>
                    <th>Load</th>
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
                                <div class="agent-avatar" style="{{ !empty($a['avatar_url']) ? 'background:none;padding:0;overflow:hidden' : 'background:' . $a['color'] }}">
                                    @if(!empty($a['avatar_url']))
                                        <img src="{{ $a['avatar_url'] }}" alt="{{ $a['name'] }}" style="width:100%;height:100%;object-fit:cover;border-radius:50%">
                                    @else
                                        {{ $a['initials'] }}
                                    @endif
                                </div>
                                <div class="agent-name">{{ $a['name'] }}</div>
                            </div>
                        </td>
                        <td>
                            <span style="font-weight:700;color:{{ ($a['pending_tickets'] ?? 0) > 0 ? 'var(--orange,#f97316)' : 'var(--muted)' }}">
                                {{ $a['pending_tickets'] ?? 0 }}
                            </span>
                        </td>
                        <td style="font-weight:700">{{ $a['resolved'] }}</td>
                        <td>{{ $a['avg_response'] }}</td>
                        <td>
                            <div class="load-wrap"><div class="load-fill" style="width:{{ $a['load'] }}%"></div></div>
                        </td>
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
        labels: ['Open', 'In Progress', 'Resolved', 'Pending User Response', 'Escalated'],
        datasets: [{
          data: {!! json_encode($distribution) !!},
          backgroundColor: ['#0f3f83', '#3b82f6', '#22c55e', '#f59e0b', '#ef4444'],
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
  if (from > to) { alert('Start date must be before end date.'); return; }
  window.location.href = '{{ route('reports.index') }}?range=custom&from=' + from + '&to=' + to;
}

function toggleReportFilter(panelId, buttonId) {
  const panel = document.getElementById(panelId);
  const button = document.getElementById(buttonId);
  if (!panel || !button) return;
  const isOpen = panel.classList.toggle('open');
  button.classList.toggle('active', isOpen);
  button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
}

function editComplianceRow(button) {
  const row = button.closest('tr');
  document.querySelectorAll('.compliance-editing').forEach(activeRow => {
    if (activeRow !== row) cancelComplianceRow(activeRow.querySelector('.inline-cancel-btn'));
  });
  row.classList.add('compliance-editing');
}

function cancelComplianceRow(button) {
  const row = button.closest('tr');
  const form = row.querySelector('form');
  if (form) form.reset();
  row.classList.remove('compliance-editing');
}

document.querySelectorAll('.auto-filter-form select, .auto-filter-form input[type="date"]').forEach(field => {
  field.addEventListener('change', () => field.form.requestSubmit());
});
</script>
@endsection
