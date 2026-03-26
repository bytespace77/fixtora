@extends('layouts.app')

@section('title', 'Dashboard - Fixtora')

@section('content')
<div class="dashboard-container">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1>Operational Overview</h1>
            <p class="subtitle">Welcome back, {{ Auth::user()->name ?? 'Alex' }}. Your concierge metrics are looking optimal today.</p>
        </div>
        <div class="header-actions">
            <button class="btn-secondary">📊 Last 24 Hours</button>
            <button class="btn-primary">📥 Export Report</button>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                    <path d="M2 17h20"></path>
                </svg>
                <span class="stat-badge positive">+12% vs last week</span>
            </div>
            <div class="stat-content">
                <div class="stat-label">ACTIVE TICKETS</div>
                <div class="stat-value">1,248</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                <span class="stat-label-badge">On Target</span>
            </div>
            <div class="stat-content">
                <div class="stat-label">RESOLVED (24H)</div>
                <div class="stat-value">842</div>
            </div>
        </div>

        <div class="stat-card critical">
            <div class="stat-header">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"></path>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">SLA COMPLIANCE</div>
                <div class="stat-value">99.4%</div>
                <small>Critical Metric</small>
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="content-grid">
        <!-- Ticket Inflow Chart -->
        <div class="card">
            <div class="card-header">
                <h3>Ticket Inflow & Resolution</h3>
                <div class="chart-toggles">
                    <button class="toggle active" onclick="switchChart('inflow')">Inflow</button>
                    <button class="toggle" onclick="switchChart('resolution')">Resolution</button>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="ticketChart"></canvas>
            </div>
        </div>

        <!-- System Updates -->
        <div class="card">
            <div class="card-header">
                <h3>System Updates</h3>
                <a href="#" class="view-all">View All →</a>
            </div>
            <div class="updates-list">
                <div class="update-item">
                    <div class="update-icon success">✓</div>
                    <div class="update-content">
                        <h4>Infrastructure Optimized</h4>
                        <p>Node clusters in US-East balanced...</p>
                        <span class="time">2 MINS AGO</span>
                    </div>
                </div>
                <div class="update-item">
                    <div class="update-icon warning">⚠</div>
                    <div class="update-content">
                        <h4>Critical Ticket Spike</h4>
                        <p>Inbound volume for Auth Service...</p>
                        <span class="time critical">14 MINS AGO</span>
                    </div>
                </div>
                <div class="update-item">
                    <div class="update-icon info">ℹ</div>
                    <div class="update-content">
                        <h4>New Architect Joined</h4>
                        <p>Sarah Jenkins is now assigned t...</p>
                        <span class="time">1 HOUR AGO</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Priority Queue -->
    <div class="card">
        <div class="card-header">
            <h3>Priority Concierge Queue</h3>
            <span style="font-size: 13px; color: var(--text-secondary);">Active issues requiring immediate structural intervention.</span>
        </div>
        <div class="queue-table">
            <table>
                <thead>
                    <tr>
                        <th>TICKET ID</th>
                        <th>TITLE</th>
                        <th>DURATION</th>
                        <th>PRIORITY</th>
                        <th>STATUS</th>
                        <th>ASSIGNEE</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong style="color: #dc2626;">#492</strong></td>
                        <td>Database Sharding Failure - Primary Vault</td>
                        <td>12m duration</td>
                        <td><span class="badge-critical">Critical</span></td>
                        <td><span class="status urgent">URGENT</span></td>
                        <td>
                            <div style="display: flex; gap: 4px;">
                                <span class="avatar" style="background: #2563eb;">MP</span>
                                <span class="avatar" style="background: #7c3aed;">SJ</span>
                            </div>
                        </td>
                        <td style="color: var(--text-secondary); cursor: pointer;">⋯</td>
                    </tr>
                    <tr>
                        <td><strong style="color: #2563eb;">#501</strong></td>
                        <td>Global CSS Refactor - Component Library</td>
                        <td>45m duration</td>
                        <td><span class="badge-high">High</span></td>
                        <td><span class="status review">IN REVIEW</span></td>
                        <td>
                            <div style="display: flex; gap: 4px;">
                                <span class="avatar" style="background: #f97316;">ER</span>
                            </div>
                        </td>
                        <td style="color: var(--text-secondary); cursor: pointer;">⋯</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let chartInstance = null;
    
    function createChart(type = 'inflow') {
        const ctx = document.getElementById('ticketChart').getContext('2d');
        
        const inflowData = {
            labels: ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'],
            datasets: [
                {
                    label: 'Inflow',
                    data: [120, 150, 140, 180, 160, 140, 130],
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#2563eb'
                },
                {
                    label: 'Average',
                    data: [100, 100, 100, 100, 100, 100, 100],
                    borderColor: 'rgba(37, 99, 235, 0.3)',
                    backgroundColor: 'rgba(37, 99, 235, 0.05)',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0
                }
            ]
        };
        
        const resolutionData = {
            labels: ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'],
            datasets: [
                {
                    label: 'Resolution Rate',
                    data: [95, 120, 110, 140, 130, 110, 105],
                    borderColor: '#059669',
                    backgroundColor: 'rgba(5, 150, 105, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#059669'
                }
            ]
        };
        
        if (chartInstance) {
            chartInstance.destroy();
        }
        
        chartInstance = new Chart(ctx, {
            type: 'line',
            data: type === 'inflow' ? inflowData : resolutionData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: { family: "'Montserrat', sans-serif", size: 13, weight: '500' },
                            color: '#6b7280',
                            usePointStyle: true,
                            padding: 15,
                            boxWidth: 6,
                            boxHeight: 6
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        titleFont: { family: "'Montserrat', sans-serif" },
                        bodyFont: { family: "'Montserrat', sans-serif" },
                        padding: 12,
                        borderRadius: 6,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 200,
                        ticks: {
                            font: { family: "'Montserrat', sans-serif", size: 12 },
                            color: '#9ca3af',
                            stepSize: 50
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.05)',
                            drawBorder: false
                        }
                    },
                    x: {
                        ticks: {
                            font: { family: "'Montserrat', sans-serif", size: 12, weight: '600' },
                            color: '#9ca3af'
                        },
                        grid: {
                            display: false,
                            drawBorder: false
                        }
                    }
                }
            }
        });
    }
    
    function switchChart(type) {
        // Update active button
        document.querySelectorAll('.toggle').forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');
        
        // Redraw chart
        createChart(type);
    }
    
    // Initialize chart on page load
    document.addEventListener('DOMContentLoaded', () => {
        createChart('inflow');
    });
</script>

<style scoped>
    .dashboard-container {
        padding: 32px 24px;
        max-width: 1400px;
        margin: 0 auto;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 32px;
    }

    .page-header h1 {
        font-size: 28px;
        font-weight: 700;
        margin: 0 0 8px 0;
        font-family: 'Montserrat', sans-serif;
    }

    .subtitle {
        color: var(--text-secondary);
        margin: 0;
        font-size: 14px;
    }

    .header-actions {
        display: flex;
        gap: 12px;
    }

    .btn-primary, .btn-secondary {
        padding: 10px 16px;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.2s;
        font-family: 'Montserrat', sans-serif;
    }

    .btn-primary {
        background: var(--primary-light);
        color: white;
    }

    .btn-primary:hover {
        background: #1d4ed8;
    }

    .btn-secondary {
        background: var(--bg-light);
        color: var(--text-primary);
        border: 1px solid var(--border-color);
    }

    .btn-secondary:hover {
        background: white;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-bottom: 28px;
    }

    .stat-card {
        background: white;
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        padding: 24px;
        box-shadow: var(--shadow);
    }

    .stat-card.critical {
        background: linear-gradient(135deg, #1a3a5c 0%, #1e3a8a 100%);
        color: white;
        border: none;
    }

    .stat-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
    }

    .stat-header svg {
        width: 24px;
        height: 24px;
        color: var(--primary-light);
    }

    .stat-card.critical .stat-header svg {
        color: rgba(255, 255, 255, 0.7);
    }

    .stat-badge, .stat-label-badge {
        font-size: 12px;
        font-weight: 600;
        padding: 4px 8px;
        border-radius: 4px;
    }

    .stat-badge.positive {
        background: rgba(5, 150, 105, 0.1);
        color: var(--success);
    }

    .stat-label-badge {
        background: rgba(37, 99, 235, 0.1);
        color: var(--primary-light);
    }

    .stat-label {
        font-size: 11px;
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .stat-value {
        font-size: 32px;
        font-weight: 700;
        font-family: 'Montserrat', sans-serif;
    }

    .stat-card.critical .stat-label,
    .stat-card.critical .stat-value {
        color: white;
    }

    .stat-card.critical small {
        display: block;
        font-size: 12px;
        color: rgba(255, 255, 255, 0.7);
        margin-top: 8px;
    }

    /* Content Grid */
    .content-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
        margin-bottom: 24px;
    }

    @media (max-width: 1024px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Card Styles */
    .card {
        background: white;
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        padding: 24px;
        box-shadow: var(--shadow);
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--border-color);
    }

    .card-header h3 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        font-family: 'Montserrat', sans-serif;
    }

    .view-all {
        color: var(--primary-light);
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
    }

    .chart-toggles {
        display: flex;
        gap: 8px;
        margin-left: auto;
    }

    .toggle {
        padding: 6px 12px;
        border: 1px solid var(--border-color);
        background: white;
        border-radius: 4px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        font-family: 'Montserrat', sans-serif;
    }

    .toggle.active {
        background: var(--primary-light);
        color: white;
        border-color: var(--primary-light);
    }

    .chart-container {
        position: relative;
        height: 250px;
    }

    /* Updates List */
    .updates-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .update-item {
        display: flex;
        gap: 12px;
    }

    .update-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--bg-light);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: var(--text-secondary);
        font-weight: 600;
        font-size: 16px;
    }

    .update-icon.success {
        background: rgba(5, 150, 105, 0.1);
        color: var(--success);
    }

    .update-icon.warning {
        background: rgba(217, 119, 6, 0.1);
        color: var(--warning);
    }

    .update-icon.info {
        background: rgba(37, 99, 235, 0.1);
        color: var(--primary-light);
    }

    .update-content h4 {
        margin: 0 0 4px 0;
        font-size: 14px;
        font-weight: 600;
        font-family: 'Montserrat', sans-serif;
    }

    .update-content p {
        margin: 0 0 6px 0;
        font-size: 13px;
        color: var(--text-secondary);
    }

    .time {
        font-size: 11px;
        font-weight: 600;
        color: var(--text-secondary);
    }

    .time.critical {
        color: var(--danger);
    }

    /* Table */
    .queue-table {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-family: 'Montserrat', sans-serif;
    }

    thead {
        background: var(--bg-light);
    }

    th {
        padding: 12px 16px;
        text-align: left;
        font-size: 12px;
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    td {
        padding: 16px;
        border-top: 1px solid var(--border-color);
        font-size: 14px;
    }

    tr:hover {
        background: var(--bg-light);
    }

    .badge-critical, .badge-high, .badge-medium {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-critical {
        background: rgba(220, 38, 38, 0.1);
        color: var(--danger);
    }

    .badge-high {
        background: rgba(217, 119, 6, 0.1);
        color: var(--warning);
    }

    .badge-medium {
        background: rgba(37, 99, 235, 0.1);
        color: var(--primary-light);
    }

    .status {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .status.urgent {
        background: rgba(220, 38, 38, 0.1);
        color: var(--danger);
    }

    .status.review {
        background: rgba(37, 99, 235, 0.1);
        color: var(--primary-light);
    }

    .status.backlog {
        background: rgba(107, 114, 128, 0.1);
        color: var(--text-secondary);
    }

    .avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        color: white;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 11px;
    }
</style>
@endsection
