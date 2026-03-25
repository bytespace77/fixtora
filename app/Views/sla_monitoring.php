<?php

$tickets = [
    [
        'priority'    => 'CRITICAL',
        'priority_color' => '#ef4444',
        'priority_bg'   => '#fef2f2',
        'id'          => '#TK-4429',
        'title'       => 'Foundation Stress Analysis - Block C',
        'desc'        => 'Structural report pending for North-facing wing. Required for client...',
        'time'        => '24m 12s',
        'time_color'  => '#ef4444',
        'border'      => '#ef4444',
    ],
    [
        'priority'    => 'HIGH PRIORITY',
        'priority_color' => '#f97316',
        'priority_bg'   => '#fff7ed',
        'id'          => '#TK-4510',
        'title'       => 'HVAC Ducting Schematic Revision',
        'desc'        => 'Updates needed for the sustainability audit compliance report.',
        'time'        => '1h 45m',
        'time_color'  => '#f97316',
        'border'      => '#f97316',
    ],
    [
        'priority'    => 'STANDARD',
        'priority_color' => '#6b7a8d',
        'priority_bg'   => '#f4f5f8',
        'id'          => '#TK-4602',
        'title'       => 'Glass Facade Reflection Study',
        'desc'        => 'Shadow mapping requested by city council planning board.',
        'time'        => '3h 10m',
        'time_color'  => '#1e3a6e',
        'border'      => '#1e3a6e',
    ],
];

$quarters = [
    ['label'=>'Q1 2024','pct'=>94,'color'=>'#16a34a','bg'=>'#dcfce7','text'=>'#15803d'],
    ['label'=>'Q2 2024','pct'=>97,'color'=>'#16a34a','bg'=>'#dcfce7','text'=>'#15803d'],
    ['label'=>'Q3 2024','pct'=>88,'color'=>'#f97316','bg'=>'#ffedd5','text'=>'#c2410c'],
    ['label'=>'Q4 2024','pct'=>91,'color'=>'#1e3a6e','bg'=>'#dbeafe','text'=>'#1e3a6e'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>SLA Monitor — Helpdesk Pro</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
    --sw:180px;
    --nh:60px;
    --bg:#f0f2f7;
    --white:#fff;
    --navy:#0d1b2e;
    --navy2:#1a2e4a;
    --blue:#1e3a6e;
    --blue-dark:#162b52;
    --border:#e2e6ed;
    --muted:#6b7a8d;
    --text:#1a2335;
    --green:#16a34a;
    --orange:#f97316;
    --red:#ef4444;
    --shadow:0 1px 3px rgba(0,0,0,.07),0 1px 2px rgba(0,0,0,.04);
    --shadow-md:0 4px 16px rgba(0,0,0,.09);
}
html,body{height:100%;font-family:'Inter',sans-serif;font-size:14px;color:var(--text);background:var(--bg);overflow-x:hidden}

.tnav{
    position:fixed;top:0;left:0;right:0;height:var(--nh);
    background:var(--white);border-bottom:1px solid var(--border);
    display:flex;align-items:center;padding:0 20px;gap:16px;z-index:200;
}
.tnav-brand{
    width:var(--sw);flex-shrink:0;
}
.brand-name{font-size:15px;font-weight:800;color:var(--navy);letter-spacing:-.2px}
.brand-sub{font-size:9.5px;font-weight:600;letter-spacing:2px;text-transform:uppercase;color:var(--muted);margin-top:1px}
.tnav-search{
    flex:1;max-width:380px;
    display:flex;align-items:center;gap:8px;
    background:#f4f5f8;border:1px solid var(--border);border-radius:10px;
    padding:0 12px;height:38px;
}
.tnav-search input{border:none;background:transparent;outline:none;font-size:13px;color:var(--text);flex:1}
.tnav-search input::placeholder{color:#a0aab4}
.tnav-shortcut{
    display:flex;align-items:center;gap:3px;
    background:#eef0f4;border-radius:6px;padding:3px 7px;
    font-size:10.5px;font-weight:600;color:var(--muted);
    border:1px solid var(--border);white-space:nowrap;
}
.tnav-right{margin-left:auto;display:flex;align-items:center;gap:6px}
.icon-btn{
    width:36px;height:36px;border-radius:50%;border:none;background:transparent;
    cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--muted);
    transition:background .15s;position:relative;
}
.icon-btn:hover{background:#f0f2f5}
.notif-dot{
    position:absolute;top:6px;right:6px;
    width:8px;height:8px;border-radius:50%;background:var(--red);
    border:2px solid var(--white);
}
.avatar{width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#3b82f6,#1e3a6e);display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;font-weight:700;cursor:pointer;overflow:hidden}

.sidebar{
    position:fixed;top:var(--nh);left:0;bottom:0;width:var(--sw);
    background:var(--white);border-right:1px solid var(--border);
    display:flex;flex-direction:column;z-index:100;padding:20px 0 16px;
}
.nav-item{
    display:flex;align-items:center;gap:11px;
    padding:11px 20px;
    font-size:13.5px;font-weight:500;color:#4a5568;
    text-decoration:none;border-radius:0;
    transition:background .15s,color .15s;
    position:relative;
}
.nav-item:hover{background:#f5f6fa;color:var(--navy)}
.nav-item.active{
    background:#eff4ff;color:var(--blue);font-weight:600;
}
.nav-item.active::before{
    content:'';position:absolute;left:0;top:0;bottom:0;width:3px;background:var(--blue);border-radius:0 3px 3px 0;
}
.sb-spacer{flex:1}
.btn-new{
    margin:0 14px 16px;
    display:flex;align-items:center;justify-content:center;gap:8px;
    padding:11px 0;background:var(--blue);color:#fff;
    border:none;border-radius:10px;font-family:'Inter',sans-serif;
    font-size:13px;font-weight:600;cursor:pointer;
    transition:background .15s;text-decoration:none;
}
.btn-new:hover{background:var(--blue-dark)}
.sb-bottom{padding:0 0 4px}
.sb-link{
    display:flex;align-items:center;gap:10px;
    padding:9px 20px;font-size:13px;color:var(--muted);
    text-decoration:none;transition:color .15s;
}
.sb-link:hover{color:var(--navy)}

.main{margin-left:var(--sw);margin-top:var(--nh);padding:28px 28px 60px}

.page-head{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px;gap:20px}
.page-head h1{font-size:32px;font-weight:800;color:var(--navy);letter-spacing:-.8px;margin-bottom:6px}
.page-head p{font-size:13.5px;color:var(--muted);line-height:1.55;max-width:340px}
.kpi-inline{
    display:flex;align-items:stretch;gap:0;
    background:var(--white);border:1px solid var(--border);border-radius:14px;
    box-shadow:var(--shadow);overflow:hidden;
}
.kpi-item{
    padding:16px 28px;
    display:flex;flex-direction:column;align-items:center;
    border-right:1px solid var(--border);
}
.kpi-item:last-child{border-right:none}
.kpi-lbl{font-size:10px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted);margin-bottom:6px;white-space:nowrap}
.kpi-val{font-size:26px;font-weight:800;letter-spacing:-1px}
.kpi-green{color:var(--green)}
.kpi-navy{color:var(--navy)}
.kpi-red{color:var(--red)}

.charts-row{display:grid;grid-template-columns:1fr 260px;gap:16px;margin-bottom:24px}
@media(max-width:800px){.charts-row{grid-template-columns:1fr}}

.chart-card{
    background:var(--white);border:1px solid var(--border);border-radius:14px;
    padding:22px 24px;box-shadow:var(--shadow);
}
.card-title{font-size:16px;font-weight:700;color:var(--navy);margin-bottom:4px}
.card-sub{font-size:12.5px;color:var(--muted);margin-bottom:18px}
.toggle-row{display:flex;align-items:center;gap:10px}
.toggle-btn{
    font-size:12px;font-weight:600;letter-spacing:.3px;
    border:none;background:transparent;cursor:pointer;
    padding:4px 0;color:var(--muted);transition:color .15s;
}
.toggle-btn.active{color:var(--navy);border-bottom:2px solid var(--navy)}

.bar-chart-svg{width:100%;height:200px;display:block}

.status-card{
    background:var(--blue);border-radius:14px;
    padding:24px;box-shadow:var(--shadow-md);
    color:#fff;display:flex;flex-direction:column;gap:0;
}
.status-title{font-size:15px;font-weight:700;color:#fff;margin-bottom:18px}
.status-row{margin-bottom:18px}
.status-row:last-of-type{margin-bottom:20px}
.status-label-row{display:flex;align-items:center;justify-content:space-between;margin-bottom:6px}
.status-name{font-size:13px;font-weight:500;color:rgba(255,255,255,.85)}
.status-count{font-size:14px;font-weight:700;color:#fff}
.status-bar-bg{height:5px;border-radius:10px;background:rgba(255,255,255,.15);overflow:hidden}
.status-bar{height:100%;border-radius:10px}
.bar-green{background:#4ade80}
.bar-orange{background:#fb923c}
.bar-red{background:#f87171}
.view-logs{
    margin-top:auto;
    display:inline-flex;align-items:center;gap:6px;
    font-size:13px;font-weight:600;color:rgba(255,255,255,.85);
    text-decoration:none;border-top:1px solid rgba(255,255,255,.15);
    padding-top:18px;transition:color .15s;
}
.view-logs:hover{color:#fff}

.section-head{margin-bottom:14px}
.section-title{font-size:18px;font-weight:700;color:var(--navy);margin-bottom:3px}
.section-sub{font-size:13px;color:var(--muted)}
.tickets-list{display:flex;flex-direction:column;gap:10px;margin-bottom:24px}

.ticket-card{
    background:var(--white);border:1px solid var(--border);border-radius:12px;
    padding:18px 20px;box-shadow:var(--shadow);
    display:flex;align-items:center;justify-content:space-between;gap:16px;
    position:relative;overflow:hidden;
}
.ticket-card::before{
    content:'';position:absolute;left:0;top:0;bottom:0;width:4px;
}
.ticket-left{flex:1;min-width:0}
.ticket-meta{display:flex;align-items:center;gap:8px;margin-bottom:6px}
.priority-badge{
    font-size:10px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;
    padding:3px 8px;border-radius:4px;
}
.ticket-id{font-size:12px;font-weight:500;color:var(--muted)}
.ticket-title{font-size:14px;font-weight:600;color:var(--navy);margin-bottom:4px}
.ticket-desc{font-size:12.5px;color:var(--muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:460px}
.ticket-right{display:flex;align-items:center;gap:16px;flex-shrink:0}
.time-block{text-align:right}
.time-lbl{font-size:10px;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:var(--muted);margin-bottom:4px}
.time-val{display:flex;align-items:center;gap:5px;font-size:14px;font-weight:700}
.more-btn{
    width:30px;height:30px;border-radius:8px;border:1px solid var(--border);
    background:var(--white);cursor:pointer;display:flex;align-items:center;justify-content:center;
    color:var(--muted);transition:background .15s;flex-shrink:0;
}
.more-btn:hover{background:#f5f6fa}

.bottom-row{display:grid;grid-template-columns:1fr 1fr;gap:16px}
@media(max-width:700px){.bottom-row{grid-template-columns:1fr}}

.breakdown-card,.hist-card{
    background:var(--white);border:1px solid var(--border);border-radius:14px;
    padding:22px 24px;box-shadow:var(--shadow);
}
.breakdown-title,.hist-title{font-size:16px;font-weight:700;color:var(--navy);margin-bottom:18px}
.breakdown-item{
    display:flex;align-items:center;gap:14px;
    padding:12px 0;border-bottom:1px solid #f0f2f5;
}
.breakdown-item:last-child{border-bottom:none}
.bk-icon{
    width:36px;height:36px;border-radius:9px;
    display:flex;align-items:center;justify-content:center;flex-shrink:0;
}
.bk-body{flex:1}
.bk-name{font-size:13.5px;font-weight:600;color:var(--navy)}
.bk-target{font-size:11.5px;color:var(--muted);margin-top:1px}
.bk-right{text-align:right}
.bk-val{font-size:16px;font-weight:800;color:var(--orange)}
.bk-avg{font-size:10.5px;font-weight:600;letter-spacing:.8px;text-transform:uppercase;color:var(--muted)}

.hist-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:6px}
.hist-sub{font-size:12.5px;color:var(--muted);margin-bottom:20px}
.avatars-stack{display:flex;align-items:center}
.av-sm{
    width:26px;height:26px;border-radius:50%;border:2px solid var(--white);
    margin-left:-8px;first:margin-left:0;
    background:linear-gradient(135deg,#94a3b8,#64748b);
    display:flex;align-items:center;justify-content:center;
    font-size:9px;font-weight:700;color:#fff;
}
.av-sm:first-child{margin-left:0}
.av-plus{
    width:26px;height:26px;border-radius:50%;border:2px solid var(--white);
    margin-left:-8px;background:#1e3a6e;
    display:flex;align-items:center;justify-content:center;
    font-size:9px;font-weight:700;color:#fff;
}
.quarters-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:10px}
.quarter-col{display:flex;flex-direction:column;align-items:center;gap:8px}
.quarter-lbl{font-size:11px;font-weight:600;color:var(--muted);text-align:center}
.quarter-box{
    width:100%;border-radius:10px;
    display:flex;align-items:center;justify-content:center;
    font-size:20px;font-weight:800;
    padding:22px 0;
}
</style>
</head>
<body>

<header class="tnav">
    <div class="tnav-brand">
        <div class="brand-name">Helpdesk Pro</div>
        <div class="brand-sub">Architectural Concierge</div>
    </div>
    <div class="tnav-search">
        <svg width="14" height="14" fill="none" stroke="#a0aab4" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" placeholder="Search SLA metrics or ticket IDs...">
        <div class="tnav-shortcut">CMD + K</div>
    </div>
    <div class="tnav-right">
        <button class="icon-btn">
            <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            <span class="notif-dot"></span>
        </button>
        <button class="icon-btn">
            <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
        </button>
        <div class="avatar">
            <svg width="18" height="18" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </div>
    </div>
</header>

<aside class="sidebar">
    <nav>
        <a href="#" class="nav-item">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
            Dashboard
        </a>
        <a href="#" class="nav-item">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/></svg>
            Tickets
        </a>
        <a href="#" class="nav-item">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
            Tasks
        </a>
        <a href="#" class="nav-item active">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            SLA Monitor
        </a>
        <a href="#" class="nav-item">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            Notifications
        </a>
    </nav>

    <div class="sb-spacer"></div>

    <a href="#" class="btn-new">
        <svg width="14" height="14" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        New Ticket
    </a>

    <div class="sb-bottom">
        <a href="#" class="sb-link">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
            Settings
        </a>
        <a href="#" class="sb-link">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            Help
        </a>
    </div>
</aside>

<main class="main">
    <div class="page-head">
        <div>
            <h1>SLA Performance</h1>
            <p>Monitoring real-time compliance across architectural and structural support tiers.</p>
        </div>
        <div class="kpi-inline">
            <div class="kpi-item">
                <div class="kpi-lbl">Global Compliance</div>
                <div class="kpi-val kpi-green">98.4%</div>
            </div>
            <div class="kpi-item">
                <div class="kpi-lbl">Avg Resolution</div>
                <div class="kpi-val kpi-navy">2.4h</div>
            </div>
            <div class="kpi-item">
                <div class="kpi-lbl">Active Breaches</div>
                <div class="kpi-val kpi-red">02</div>
            </div>
        </div>
    </div>

    <div class="charts-row">

        <div class="chart-card">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:4px">
                <div>
                    <div class="card-title">Compliance Trends</div>
                    <div class="card-sub">Resolution efficiency over the last 30 days</div>
                </div>
                <div class="toggle-row">
                    <button class="toggle-btn active">WEEKLY</button>
                    <button class="toggle-btn">MONTHLY</button>
                </div>
            </div>

            <svg class="bar-chart-svg" viewBox="0 0 520 200" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                <line x1="0" y1="10"  x2="520" y2="10"  stroke="#f0f2f5" stroke-width="1"/>
                <line x1="0" y1="55"  x2="520" y2="55"  stroke="#f0f2f5" stroke-width="1"/>
                <line x1="0" y1="100" x2="520" y2="100" stroke="#f0f2f5" stroke-width="1"/>
                <line x1="0" y1="145" x2="520" y2="145" stroke="#f0f2f5" stroke-width="1"/>
                <line x1="0" y1="190" x2="520" y2="190" stroke="#f0f2f5" stroke-width="1"/>

                <rect x="7.5"   y="94"  width="11" height="96"  rx="3" fill="rgba(191,219,254,0.85)"/>
                <rect x="59.5"  y="85"  width="11" height="105" rx="3" fill="rgba(191,219,254,0.85)"/>
                <rect x="85.5"  y="100" width="11" height="90"  rx="3" fill="rgba(191,219,254,0.85)"/>
                <rect x="111.5" y="115" width="11" height="75"  rx="3" fill="rgba(191,219,254,0.85)"/>
                <rect x="137.5" y="76"  width="11" height="114" rx="3" fill="rgba(191,219,254,0.85)"/>
                <rect x="163.5" y="70"  width="11" height="120" rx="3" fill="rgba(191,219,254,0.85)"/>
                <rect x="189.5" y="88"  width="11" height="102" rx="3" fill="rgba(191,219,254,0.85)"/>
                <rect x="215.5" y="103" width="11" height="87"  rx="3" fill="rgba(191,219,254,0.85)"/>
                <rect x="241.5" y="91"  width="11" height="99"  rx="3" fill="rgba(191,219,254,0.85)"/>
                <rect x="267.5" y="79"  width="11" height="111" rx="3" fill="rgba(191,219,254,0.85)"/>
                <rect x="293.5" y="64"  width="11" height="126" rx="3" fill="rgba(191,219,254,0.85)"/>
                <rect x="319.5" y="73"  width="11" height="117" rx="3" fill="rgba(191,219,254,0.85)"/>
                <rect x="371.5" y="94"  width="11" height="96"  rx="3" fill="rgba(191,219,254,0.85)"/>
                <rect x="397.5" y="55"  width="11" height="135" rx="3" fill="rgba(191,219,254,0.85)"/>
                <rect x="449.5" y="82"  width="11" height="108" rx="3" fill="rgba(191,219,254,0.85)"/>
                <rect x="475.5" y="88"  width="11" height="102" rx="3" fill="rgba(191,219,254,0.85)"/>
                <rect x="501.5" y="40"  width="11" height="150" rx="3" fill="rgba(191,219,254,0.85)"/>

                <rect x="345.5" y="106" width="11" height="84"  rx="3" fill="rgba(239,68,68,0.6)"/>

                <rect x="423.5" y="46"  width="11" height="144" rx="3" fill="rgba(249,115,22,0.6)"/>
            </svg>

            <div style="display:flex;justify-content:space-between;padding-top:8px">
                <span style="font-size:11px;color:var(--muted)">DAY 01</span>
                <span style="font-size:11px;color:var(--muted)">DAY 15</span>
                <span style="font-size:11px;color:var(--muted)">DAY 30</span>
            </div>
        </div>

        <div class="status-card">
            <div class="status-title">Status Distribution</div>

            <div class="status-row">
                <div class="status-label-row">
                    <span class="status-name">On Track</span>
                    <span class="status-count">242</span>
                </div>
                <div class="status-bar-bg">
                    <div class="status-bar bar-green" style="width:95%"></div>
                </div>
            </div>

            <div class="status-row">
                <div class="status-label-row">
                    <span class="status-name">Nearing Breach</span>
                    <span class="status-count">12</span>
                </div>
                <div class="status-bar-bg">
                    <div class="status-bar bar-orange" style="width:22%"></div>
                </div>
            </div>

            <div class="status-row">
                <div class="status-label-row">
                    <span class="status-name">Breached</span>
                    <span class="status-count">02</span>
                </div>
                <div class="status-bar-bg">
                    <div class="status-bar bar-red" style="width:5%"></div>
                </div>
            </div>

            <a href="#" class="view-logs">View detailed logs →</a>
        </div>
    </div>

    <div class="section-head">
        <div class="section-title">Urgent Attention Required</div>
        <div class="section-sub">Tickets nearing resolution deadlines within 4 hours</div>
    </div>

    <div class="tickets-list">
        <?php foreach ($tickets as $t): ?>
        <div class="ticket-card" style="border-left:4px solid <?= $t['border'] ?>">
            <div class="ticket-left">
                <div class="ticket-meta">
                    <span class="priority-badge" style="color:<?= $t['priority_color'] ?>;background:<?= $t['priority_bg'] ?>"><?= $t['priority'] ?></span>
                    <span class="ticket-id"><?= $t['id'] ?></span>
                </div>
                <div class="ticket-title"><?= htmlspecialchars($t['title']) ?></div>
                <div class="ticket-desc"><?= htmlspecialchars($t['desc']) ?></div>
            </div>
            <div class="ticket-right">
                <div class="time-block">
                    <div class="time-lbl">Time Remaining</div>
                    <div class="time-val" style="color:<?= $t['time_color'] ?>">
                        <svg width="10" height="10" viewBox="0 0 10 10" fill="currentColor"><circle cx="5" cy="5" r="5"/></svg>
                        <?= $t['time'] ?>
                    </div>
                </div>
                <button class="more-btn">
                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/></svg>
                </button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="bottom-row">

        <div class="breakdown-card">
            <div class="breakdown-title">Resolution Breakdown</div>

            <div class="breakdown-item">
                <div class="bk-icon" style="background:#fff7ed">
                    <svg width="16" height="16" fill="none" stroke="#f97316" stroke-width="2.5" viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                </div>
                <div class="bk-body">
                    <div class="bk-name">First Response</div>
                    <div class="bk-target">Target: 30 mins</div>
                </div>
                <div class="bk-right">
                    <div class="bk-val">18m</div>
                    <div class="bk-avg">AVG</div>
                </div>
            </div>

            <div class="breakdown-item">
                <div class="bk-icon" style="background:#eff4ff">
                    <svg width="16" height="16" fill="none" stroke="#2563eb" stroke-width="2.5" viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                </div>
                <div class="bk-body">
                    <div class="bk-name">Work Started</div>
                    <div class="bk-target">Target: 2 hours</div>
                </div>
                <div class="bk-right">
                    <div class="bk-val">1.2h</div>
                    <div class="bk-avg">AVG</div>
                </div>
            </div>

            <div class="breakdown-item">
                <div class="bk-icon" style="background:#f0fdf4">
                    <svg width="16" height="16" fill="none" stroke="#16a34a" stroke-width="2.5" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <div class="bk-body">
                    <div class="bk-name">Resolution</div>
                    <div class="bk-target">Target: 24 hours</div>
                </div>
                <div class="bk-right">
                    <div class="bk-val" style="color:var(--red)">21.4h</div>
                    <div class="bk-avg">AVG</div>
                </div>
            </div>
        </div>

        <div class="hist-card">
            <div class="hist-header">
                <div>
                    <div class="hist-title">Historical Comparison</div>
                    <div class="hist-sub">Quarterly performance against benchmark targets</div>
                </div>
                <div class="avatars-stack">
                    <?php for($i=0;$i<3;$i++): ?>
                    <div class="av-sm" style="background:<?= ['#3b82f6','#8b5cf6','#10b981'][$i] ?>"></div>
                    <?php endfor; ?>
                    <div class="av-plus">+12</div>
                </div>
            </div>
            <div class="quarters-grid">
                <?php foreach ($quarters as $q): ?>
                <div class="quarter-col">
                    <div class="quarter-lbl"><?= $q['label'] ?></div>
                    <div class="quarter-box" style="background:<?= $q['bg'] ?>;color:<?= $q['text'] ?>">
                        <?= $q['pct'] ?>%
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>

</main>

</body>
</html>