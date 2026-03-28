@extends('layouts.app')
@section('title', 'Dashboard – Fixtora')

@section('styles')
<style>
/* ── PAGE HEADER ── */
.page-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 24px;
}
.page-header h1 {
    font-size: 22px;
    font-weight: 800;
    letter-spacing: -0.5px;
    color: var(--text);
    margin-bottom: 3px;
}
.page-header p {
    font-size: 13px;
    color: var(--muted);
    font-weight: 400;
}
.hdr-btns { display: flex; gap: 8px; }

.btn-sm {
    padding: 8px 14px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
    border: 1.5px solid var(--border);
    background: var(--surface);
    color: var(--text-2);
    font-family: 'Montserrat', sans-serif;
    transition: all 0.15s;
    text-decoration: none;
}
.btn-sm:hover { background: var(--bg); border-color: var(--border-2); }
.btn-sm.primary {
    background: var(--blue);
    color: #fff;
    border-color: var(--blue);
    box-shadow: 0 2px 8px rgba(37,99,235,0.25);
}
.btn-sm.primary:hover { background: var(--blue-2); box-shadow: 0 4px 14px rgba(37,99,235,0.35); transform: translateY(-1px); }

/* ── STAT CARDS ── */
.stats-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 20px;
}

.stat-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 20px 22px;
    box-shadow: var(--shadow);
    display: flex;
    align-items: center;
    gap: 16px;
    transition: box-shadow 0.15s, transform 0.15s;
    position: relative;
    overflow: hidden;
}
.stat-card:hover { box-shadow: var(--shadow-md); transform: translateY(-1px); }

/* colored left border */
.stat-card::before {
    content: '';
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 3px;
    border-radius: 0 2px 2px 0;
}
.stat-card.blue-accent::before  { background: var(--blue); }
.stat-card.green-accent::before { background: var(--green); }
.stat-card.navy-accent::before  { background: var(--navy); }

.stat-icon {
    width: 44px; height: 44px;
    border-radius: 11px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.stat-icon.blue  { background: var(--blue-bg);  color: var(--blue); }
.stat-icon.green { background: var(--green-bg); color: var(--green); }
.stat-icon.navy  { background: var(--navy);     color: #fff; }

.stat-label {
    font-size: 10px; font-weight: 700;
    letter-spacing: 1px; text-transform: uppercase;
    color: var(--muted); margin-bottom: 4px;
}
.stat-val {
    font-size: 30px; font-weight: 800;
    letter-spacing: -1px; color: var(--text); line-height: 1;
}
.stat-badge {
    font-size: 10.5px; font-weight: 700;
    padding: 3px 9px; border-radius: 100px;
    margin-top: 6px; display: inline-block;
}
.badge-up   { background: var(--green-bg); color: var(--green); }
.badge-ok   { background: var(--blue-bg);  color: var(--blue); }
.badge-warn { background: var(--red-bg);   color: var(--red); }

/* ── CONTENT GRID ── */
.content-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 16px;
    margin-bottom: 20px;
}

.card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 20px 22px;
    box-shadow: var(--shadow);
}

.card-hdr {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 18px;
}

.card-title {
    font-size: 14px;
    font-weight: 700;
    color: var(--text);
    letter-spacing: -0.2px;
}

.view-all {
    font-size: 11.5px;
    font-weight: 600;
    color: var(--blue);
    text-decoration: none;
    transition: color 0.15s;
}
.view-all:hover { color: var(--blue-2); text-decoration: underline; }

.chart-wrap { position: relative; height: 170px; }

/* ── SYSTEM UPDATES ── */
.update-item {
    display: flex;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid var(--border);
}
.update-item:last-child { border-bottom: none; }

.upd-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
    margin-top: 5px;
}
.upd-dot.green  { background: var(--green); }
.upd-dot.red    { background: var(--red); }
.upd-dot.blue   { background: var(--blue); }
.upd-dot.orange { background: var(--orange); }

.upd-title { font-size: 13px; font-weight: 600; color: var(--text); margin-bottom: 2px; }
.upd-desc  { font-size: 12px; color: var(--muted); font-weight: 400; }
.upd-time  { font-size: 10.5px; font-weight: 600; color: var(--muted-lt); margin-top: 3px; letter-spacing: 0.3px; }

/* ── QUEUE TABLE ── */
.queue-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    overflow: hidden;
}

.queue-hdr {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 22px;
    border-bottom: 1px solid var(--border);
}

table { width: 100%; border-collapse: collapse; }

th {
    padding: 10px 18px;
    text-align: left;
    font-size: 10px;
    font-weight: 700;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: 0.6px;
    background: var(--bg);
    border-bottom: 1px solid var(--border);
    font-family: 'Montserrat', sans-serif;
}

td {
    padding: 13px 18px;
    font-size: 13px;
    border-bottom: 1px solid var(--border);
    font-family: 'Montserrat', sans-serif;
    vertical-align: middle;
}

tr:last-child td { border-bottom: none; }
tbody tr { transition: background 0.1s; }
tbody tr:hover td { background: #f8faff; }

/* ── PILLS ── */
.pill {
    display: inline-flex;
    align-items: center;
    padding: 3px 10px;
    border-radius: 100px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    white-space: nowrap;
}
.pill-critical { background: var(--red-bg);    color: var(--red); }
.pill-high     { background: var(--orange-bg); color: var(--orange); }
.pill-medium   { background: var(--blue-bg);   color: var(--blue); }
.pill-low      { background: var(--green-bg);  color: var(--green); }
.pill-open     { background: var(--orange-bg); color: var(--orange); }
.pill-resolved { background: var(--green-bg);  color: var(--green); }
.pill-review   { background: var(--blue-bg);   color: var(--blue); }
.pill-closed   { background: var(--bg);        color: var(--muted); border: 1px solid var(--border); }

/* ticket id */
.ticket-id {
    font-size: 11.5px;
    font-weight: 700;
    color: var(--muted);
    background: var(--bg);
    border: 1px solid var(--border);
    padding: 2px 8px;
    border-radius: 6px;
    font-family: 'Montserrat', monospace;
}

/* ── EMPTY STATE ── */
.empty-state {
    text-align: center;
    padding: 48px 20px;
    color: var(--muted);
}
.empty-state svg { margin: 0 auto 14px; display: block; opacity: 0.25; }
.empty-state p   { font-size: 13px; font-weight: 600; }
</style>
@endsection

@section('content')

<!-- PAGE HEADER -->
<div class="page-header">
    <div>
        <h1>Operational Overview</h1>
        <p>Welcome back, {{ Auth::user()->name }}. Here's your system status.</p>
    </div>
    <div class="hdr-btns">
        <button class="btn-sm">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <rect x="3" y="4" width="18" height="18" rx="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            Last 24 Hours
        </button>
        <button class="btn-sm primary">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                <polyline points="7 10 12 15 17 10"/>
                <line x1="12" y1="15" x2="12" y2="3"/>
            </svg>
            Export Report
        </button>
    </div>
</div>

<!-- STATS -->
<div class="stats-row">
    <div class="stat-card blue-accent">
        <div class="stat-icon blue">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2z"/>
            </svg>
        </div>
        <div>
            <div class="stat-label">Active Tickets</div>
            <div class="stat-val">{{ $stats['active'] }}</div>
            @if($stats['total'] > 0)
                <span class="stat-badge badge-up">{{ $stats['total'] }} total</span>
            @endif
        </div>
    </div>

    <div class="stat-card green-accent">
        <div class="stat-icon green">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                <polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
        </div>
        <div>
            <div class="stat-label">Resolved</div>
            <div class="stat-val">{{ $stats['resolved'] }}</div>
            <span class="stat-badge badge-ok">On Target</span>
        </div>
    </div>

    <div class="stat-card navy-accent">
        <div class="stat-icon navy">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
        </div>
        <div>
            <div class="stat-label">Critical Open</div>
            <div class="stat-val">{{ $stats['critical'] }}</div>
            @if($stats['critical'] > 0)
                <span class="stat-badge badge-warn">Needs Attention</span>
            @else
                <span class="stat-badge badge-ok">All Clear</span>
            @endif
        </div>
    </div>
</div>

<!-- CHART + UPDATES -->
<div class="content-grid">
    <div class="card">
        <div class="card-hdr">
            <div class="card-title">Ticket Inflow & Resolution</div>
            <a href="{{ route('reports.index') }}" class="view-all">View Reports →</a>
        </div>
        <div class="chart-wrap">
            <canvas id="inflowChart"></canvas>
        </div>
    </div>

    <div class="card">
        <div class="card-hdr">
            <div class="card-title">System Updates</div>
            <a href="{{ route('notifications.index') }}" class="view-all">View All</a>
        </div>
        <div class="update-item">
            <div class="upd-dot green"></div>
            <div>
                <div class="upd-title">Infrastructure Optimized</div>
                <div class="upd-desc">Node clusters balanced successfully.</div>
                <div class="upd-time">2 MINS AGO</div>
            </div>
        </div>
        <div class="update-item">
            <div class="upd-dot red"></div>
            <div>
                <div class="upd-title">Critical Ticket Spike</div>
                <div class="upd-desc">Inbound volume for Auth Service up 30%.</div>
                <div class="upd-time" style="color:var(--red)">14 MINS AGO</div>
            </div>
        </div>
        <div class="update-item">
            <div class="upd-dot blue"></div>
            <div>
                <div class="upd-title">New Architect Joined</div>
                <div class="upd-desc">Sarah Jenkins assigned to Queue A.</div>
                <div class="upd-time">1 HOUR AGO</div>
            </div>
        </div>
    </div>
</div>

<!-- PRIORITY QUEUE -->
<div class="queue-card">
    <div class="queue-hdr">
        <div class="card-title">Priority Concierge Queue</div>
        <a href="{{ route('tickets.index') }}" class="view-all">View All Tickets →</a>
    </div>

    @if($recentTickets->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Ticket ID</th>
                <th>Title</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recentTickets as $ticket)
            <tr>
                <td><span class="ticket-id">#{{ $ticket->id }}</span></td>
                <td style="font-weight:600;max-width:300px;color:var(--text)">{{ Str::limit($ticket->title, 55) }}</td>
                <td><span class="pill pill-{{ $ticket->priority }}">{{ ucfirst($ticket->priority) }}</span></td>
                <td>
                    @if($ticket->status === 'open')
                        <span class="pill pill-open">Open</span>
                    @elseif($ticket->status === 'resolved')
                        <span class="pill pill-resolved">Resolved</span>
                    @elseif($ticket->status === 'closed')
                        <span class="pill pill-closed">Closed</span>
                    @else
                        <span class="pill pill-review">{{ ucfirst($ticket->status) }}</span>
                    @endif
                </td>
                <td style="color:var(--muted);font-size:12px;font-weight:500">{{ $ticket->created_at->diffForHumans() }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="empty-state">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
        </svg>
        <p>No tickets yet. <a href="{{ route('tickets.create') }}" style="color:var(--blue);font-weight:700">Create your first ticket →</a></p>
    </div>
    @endif
</div>

<script>
const ctx = document.getElementById('inflowChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['MON','TUE','WED','THU','FRI','SAT','SUN'],
        datasets: [{
            label: 'Inflow',
            data: [120,150,140,180,160,140,130],
            borderColor: '#2563eb',
            backgroundColor: 'rgba(37,99,235,0.07)',
            borderWidth: 2.5, fill: true, tension: 0.4,
            pointRadius: 0, pointHoverRadius: 5,
            pointBackgroundColor: '#2563eb'
        },{
            label: 'Resolved',
            data: [95,120,110,140,130,110,105],
            borderColor: '#16a34a',
            backgroundColor: 'rgba(22,163,74,0.05)',
            borderWidth: 2, fill: true, tension: 0.4,
            pointRadius: 0, pointHoverRadius: 5,
            pointBackgroundColor: '#16a34a'
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    font: { family: "'Montserrat',sans-serif", size: 11, weight: '600' },
                    usePointStyle: true, boxWidth: 6, padding: 14,
                    color: '#64748b'
                }
            },
            tooltip: {
                backgroundColor: '#0f172a',
                titleFont: { family: "'Montserrat',sans-serif", size: 11, weight: '700' },
                bodyFont:  { family: "'Montserrat',sans-serif", size: 11 },
                padding: 10, cornerRadius: 8,
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false },
                ticks: { font: { family: "'Montserrat',sans-serif", size: 11 }, color: '#94a3b8', padding: 6 },
                border: { display: false }
            },
            x: {
                grid: { display: false },
                ticks: { font: { family: "'Montserrat',sans-serif", size: 11, weight: '600' }, color: '#94a3b8' },
                border: { display: false }
            }
        }
    }
});
</script>
@endsection
