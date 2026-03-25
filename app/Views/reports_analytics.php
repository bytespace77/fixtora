<?php

$agents = [
    ['name'=>'Marcus Thorne',   'role'=>'Senior Technical Support','resolved'=>342,'avg_response'=>'12m','load'=>85,'csat'=>'4.9/5.0','status'=>'online', 'initials'=>'MT','color'=>'#3b6ea8'],
    ['name'=>'Elena Rodriguez', 'role'=>'Product Specialist',       'resolved'=>289,'avg_response'=>'18m','load'=>65,'csat'=>'4.7/5.0','status'=>'online', 'initials'=>'ER','color'=>'#2a7a5e'],
    ['name'=>'Siddharth Varma', 'role'=>'API Specialist',           'resolved'=>215,'avg_response'=>'9m', 'load'=>40,'csat'=>'5.0/5.0','status'=>'away',   'initials'=>'SV','color'=>'#5a3e8a'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Reports & Analytics — Helpdesk Analytics</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
    --sw:210px;
    --nh:56px;
    --bg:#f2f4f8;
    --white:#fff;
    --navy:#0d1b2e;
    --navy2:#1a2e4a;
    --blue:#1e3a6e;
    --blue-act:#1d4ed8;
    --border:#e2e6ed;
    --muted:#6b7a8d;
    --text:#1a2335;
    --green:#16a34a;
    --orange:#f59e0b;
    --red:#ef4444;
    --shadow:0 1px 3px rgba(0,0,0,.07),0 1px 2px rgba(0,0,0,.05);
    --shadow-md:0 4px 14px rgba(0,0,0,.09);
}
html,body{height:100%;font-family:'Inter',sans-serif;font-size:14px;color:var(--text);background:var(--bg);overflow-x:hidden}

.tnav{
    position:fixed;top:0;left:0;right:0;height:var(--nh);
    background:var(--white);border-bottom:1px solid var(--border);
    display:flex;align-items:center;z-index:200;
    padding:0 20px 0 0;
}
.tnav-brand{
    width:var(--sw);flex-shrink:0;padding:0 20px;
    font-size:15px;font-weight:700;color:var(--navy);
    letter-spacing:-.2px;
}
.tnav-links{display:flex;align-items:center;gap:2px;flex:1;padding-left:8px}
.tlink{
    padding:7px 14px;font-size:13.5px;font-weight:500;color:var(--muted);
    text-decoration:none;border-radius:6px;transition:color .15s;
    position:relative;
}
.tlink:hover{color:var(--navy)}
.tlink.active{color:var(--blue-act);font-weight:600}
.tlink.active::after{
    content:'';position:absolute;bottom:-17px;left:14px;right:14px;
    height:2px;background:var(--blue-act);border-radius:2px;
}
.tnav-right{display:flex;align-items:center;gap:6px;margin-left:auto}
.tnav-search{
    display:flex;align-items:center;gap:8px;
    background:#f4f5f8;border:1px solid var(--border);border-radius:8px;
    padding:0 12px;height:34px;width:220px;
}
.tnav-search input{border:none;background:transparent;outline:none;font-size:13px;color:var(--text);width:100%}
.tnav-search input::placeholder{color:#a0aab4}
.icon-btn{
    width:34px;height:34px;border-radius:8px;border:none;background:transparent;
    cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--muted);
    transition:background .15s;
}
.icon-btn:hover{background:#f0f2f5}
.avatar{width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#94a3b8,#64748b);display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;font-weight:600;cursor:pointer}

.sidebar{
    position:fixed;top:var(--nh);left:0;bottom:0;width:var(--sw);
    background:var(--white);border-right:1px solid var(--border);
    display:flex;flex-direction:column;z-index:100;padding:16px 0;
}
.sb-brand{
    display:flex;align-items:center;gap:12px;
    padding:10px 16px 18px;margin-bottom:4px;
    border-bottom:1px solid var(--border);
}
.sb-icon{
    width:38px;height:38px;border-radius:10px;background:var(--navy2);
    display:flex;align-items:center;justify-content:center;color:#fff;flex-shrink:0;
}
.sb-label{font-size:12.5px;font-weight:700;color:var(--navy)}
.sb-sub{font-size:10px;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted);margin-top:1px}
.nav-item{
    display:flex;align-items:center;gap:10px;
    padding:10px 18px;
    font-size:12px;font-weight:600;letter-spacing:.8px;text-transform:uppercase;
    color:var(--muted);text-decoration:none;
    border-left:3px solid transparent;
    transition:background .15s,color .15s;
}
.nav-item:hover{background:#f5f6fa;color:var(--navy)}
.nav-item.active{background:#eff4ff;color:var(--blue-act);border-left-color:var(--blue-act)}
.sb-footer{margin-top:auto;border-top:1px solid var(--border);padding:16px}
.btn-export{
    width:100%;padding:11px;background:var(--navy2);color:#fff;
    border:none;border-radius:9px;font-family:'Inter',sans-serif;
    font-size:12px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;
    cursor:pointer;transition:background .15s;
}
.btn-export:hover{background:#162234}
.sb-links{margin-top:14px;display:flex;flex-direction:column;gap:2px}
.sb-link{
    display:flex;align-items:center;gap:8px;padding:7px 0;
    font-size:12.5px;color:var(--muted);text-decoration:none;
    transition:color .15s;
}
.sb-link:hover{color:var(--navy)}

.main{margin-left:var(--sw);margin-top:var(--nh);padding:28px 28px 60px;min-height:calc(100vh - var(--nh))}

.page-head{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:14px}
.page-head h1{font-size:28px;font-weight:800;color:var(--navy);letter-spacing:-.5px}
.page-head p{font-size:13.5px;color:var(--muted);margin-top:4px}
.head-controls{display:flex;align-items:center;gap:8px}
.ctrl-btn{
    display:flex;align-items:center;gap:7px;
    padding:8px 14px;background:var(--white);border:1px solid var(--border);border-radius:8px;
    font-size:13px;font-weight:500;color:var(--text);cursor:pointer;
    transition:background .15s;white-space:nowrap;
}
.ctrl-btn:hover{background:#f5f6fa}
.btn-apply{
    padding:9px 20px;background:var(--navy2);color:#fff;border:none;
    border-radius:8px;font-family:'Inter',sans-serif;font-size:13px;font-weight:600;
    cursor:pointer;transition:background .15s;
}
.btn-apply:hover{background:#162234}

.kpi-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px}
@media(max-width:900px){.kpi-grid{grid-template-columns:repeat(2,1fr)}}
.kpi-card{
    background:var(--white);border:1px solid var(--border);border-radius:14px;
    padding:20px 20px 18px;box-shadow:var(--shadow);
}
.kpi-top{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:14px}
.kpi-icon{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.kpi-badge{font-size:11px;font-weight:600;padding:3px 8px;border-radius:20px}
.badge-up{color:#16a34a;background:#f0fdf4}
.badge-down{color:#ef4444;background:#fef2f2}
.badge-target{color:#f59e0b;background:#fffbeb}
.badge-neutral{color:#6b7a8d;background:#f4f5f8}
.kpi-label{font-size:11.5px;font-weight:600;letter-spacing:.8px;text-transform:uppercase;color:var(--muted);margin-bottom:6px}
.kpi-value{font-size:28px;font-weight:800;color:var(--navy);letter-spacing:-1px}

.charts-row{display:grid;grid-template-columns:1fr 320px;gap:16px;margin-bottom:24px}
@media(max-width:900px){.charts-row{grid-template-columns:1fr}}
.chart-card{
    background:var(--white);border:1px solid var(--border);border-radius:14px;
    padding:22px 24px;box-shadow:var(--shadow);
}
.card-head{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:6px}
.card-title{font-size:15px;font-weight:700;color:var(--navy)}
.card-sub{font-size:12.5px;color:var(--muted);margin-bottom:16px}
.legend{display:flex;align-items:center;gap:14px}
.leg-item{display:flex;align-items:center;gap:5px;font-size:12px;font-weight:500;color:var(--muted)}
.leg-dot{width:8px;height:8px;border-radius:50%}

.line-chart-svg{width:100%;height:240px;display:block;overflow:visible}

.donut-wrap{display:flex;justify-content:center;margin:8px 0 20px;position:relative}
canvas#donutChart{max-width:180px;max-height:180px}
.donut-center{
    position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);
    text-align:center;pointer-events:none;
}
.donut-val{font-size:22px;font-weight:800;color:var(--navy)}
.donut-lbl{font-size:10px;font-weight:600;letter-spacing:1.2px;text-transform:uppercase;color:var(--muted)}
.dist-list{display:flex;flex-direction:column;gap:10px;margin-top:4px}
.dist-row{display:flex;align-items:center;justify-content:space-between}
.dist-left{display:flex;align-items:center;gap:8px;font-size:13px;color:var(--text)}
.dist-dot{width:10px;height:10px;border-radius:50%;flex-shrink:0}
.dist-pct{font-size:13px;font-weight:600;color:var(--navy)}

.team-card{
    background:var(--white);border:1px solid var(--border);border-radius:14px;
    padding:22px 24px;box-shadow:var(--shadow);
}
.team-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:4px}
.view-all{font-size:13px;font-weight:500;color:var(--blue-act);text-decoration:none;display:flex;align-items:center;gap:6px}
.view-all:hover{text-decoration:underline}
table{width:100%;border-collapse:collapse;margin-top:16px}
thead th{
    font-size:11px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;
    color:var(--muted);padding:0 12px 12px;text-align:left;border-bottom:1px solid var(--border);
}
tbody tr{border-bottom:1px solid #f0f2f5;transition:background .15s}
tbody tr:last-child{border-bottom:none}
tbody tr:hover{background:#fafbfd}
td{padding:14px 12px;vertical-align:middle}
.agent-cell{display:flex;align-items:center;gap:12px}
.agent-av{
    width:38px;height:38px;border-radius:50%;
    display:flex;align-items:center;justify-content:center;
    color:#fff;font-size:12px;font-weight:700;flex-shrink:0;
}
.agent-name{font-size:13.5px;font-weight:600;color:var(--navy)}
.agent-role{font-size:11.5px;color:var(--muted);margin-top:1px}
.load-bar-wrap{width:100px;height:6px;background:#eef0f4;border-radius:10px;overflow:hidden}
.load-bar{height:100%;border-radius:10px;background:var(--navy2)}
.csat-val{font-size:13.5px;font-weight:600;color:var(--green)}
.status-badge{
    display:inline-flex;align-items:center;gap:5px;
    font-size:11px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;
}
.dot-online{width:7px;height:7px;border-radius:50%;background:var(--green)}
.dot-away  {width:7px;height:7px;border-radius:50%;background:var(--orange)}
.online-txt{color:var(--green)}
.away-txt  {color:var(--orange)}

.fab{
    position:fixed;bottom:32px;right:32px;
    width:52px;height:52px;border-radius:50%;
    background:var(--navy2);color:#fff;border:none;
    display:flex;align-items:center;justify-content:center;
    cursor:pointer;box-shadow:0 4px 16px rgba(30,58,110,.30);
    font-size:24px;transition:background .15s,transform .1s;z-index:300;
}
.fab:hover{background:#162234}
.fab:active{transform:scale(.95)}
</style>
</head>
<body>

<header class="tnav">
    <div class="tnav-brand">Helpdesk Analytics</div>
    <nav class="tnav-links">
        <a href="#" class="tlink">Dashboards</a>
        <a href="#" class="tlink active">Reports</a>
        <a href="#" class="tlink">SLA Status</a>
        <a href="#" class="tlink">Team Performance</a>
    </nav>
    <div class="tnav-right">
        <div class="tnav-search">
            <svg width="14" height="14" fill="none" stroke="#a0aab4" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" placeholder="Search reports...">
        </div>
        <button class="icon-btn">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
        </button>
        <button class="icon-btn">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
        </button>
        <div class="avatar">
            <svg width="16" height="16" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </div>
    </div>
</header>

<aside class="sidebar">
    <div class="sb-brand">
        <div class="sb-icon">
            <svg width="18" height="18" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
        </div>
        <div>
            <div class="sb-label">Global Metrics</div>
            <div class="sb-sub">Q3 Performance</div>
        </div>
    </div>

    <nav style="margin-top:8px">
        <a href="#" class="nav-item">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
            Overview
        </a>
        <a href="#" class="nav-item">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            Ticket Volume
        </a>
        <a href="#" class="nav-item active">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            Resolution Rate
        </a>
        <a href="#" class="nav-item">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            Customer CSAT
        </a>
        <a href="#" class="nav-item">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Agent Heatmap
        </a>
    </nav>

    <div class="sb-footer">
        <button class="btn-export">Export Report</button>
        <div class="sb-links">
            <a href="#" class="sb-link">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                Help Center
            </a>
            <a href="#" class="sb-link">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7z"/><polyline points="14 2 14 8 20 8"/></svg>
                Documentation
            </a>
        </div>
    </div>
</aside>

<main class="main">

    <div class="page-head">
        <div>
            <h1>Reports &amp; Analytics</h1>
            <p>Real-time performance overview for Q3 period.</p>
        </div>
        <div class="head-controls">
            <button class="ctrl-btn">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Last 30 Days
            </button>
            <button class="ctrl-btn">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="4" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="10" y1="18" x2="14" y2="18"/></svg>
                All Systems
            </button>
            <button class="btn-apply">Apply</button>
        </div>
    </div>

    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon" style="background:#eff4ff">
                    <svg width="20" height="20" fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/></svg>
                </div>
                <span class="kpi-badge badge-up">+12.5%</span>
            </div>
            <div class="kpi-label">Total Tickets</div>
            <div class="kpi-value">1,284</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon" style="background:#f0f9ff">
                    <svg width="20" height="20" fill="none" stroke="#0284c7" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <span class="kpi-badge badge-down">-4.2%</span>
            </div>
            <div class="kpi-label">Avg. Resolution</div>
            <div class="kpi-value">4h 12m</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon" style="background:#fffbeb">
                    <svg width="20" height="20" fill="none" stroke="#d97706" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <span class="kpi-badge badge-target">↑ Target</span>
            </div>
            <div class="kpi-label">SLA Compliance</div>
            <div class="kpi-value">94.8%</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-icon" style="background:#f0fdf4">
                    <svg width="20" height="20" fill="none" stroke="#16a34a" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
                </div>
                <span class="kpi-badge badge-neutral">+0.4</span>
            </div>
            <div class="kpi-label">Customer CSAT</div>
            <div class="kpi-value">4.8/5</div>
        </div>
    </div>

    <div class="charts-row">
        <div class="chart-card">
            <div class="card-head">
                <div>
                    <div class="card-title">Ticket Volume Trends</div>
                    <div class="card-sub">Active resolution spikes over the last 30 days</div>
                </div>
                <div class="legend">
                    <div class="leg-item"><div class="leg-dot" style="background:#1e3a6e"></div>NEW</div>
                    <div class="leg-item"><div class="leg-dot" style="background:#93c5fd"></div>CLOSED</div>
                </div>
            </div>

            <svg class="line-chart-svg" viewBox="0 0 520 220" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="fillGrad" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%"   stop-color="#1e3a6e" stop-opacity="0.12"/>
                        <stop offset="100%" stop-color="#1e3a6e" stop-opacity="0.01"/>
                    </linearGradient>
                    <clipPath id="chartClip">
                        <rect x="0" y="0" width="520" height="215"/>
                    </clipPath>
                </defs>

                <line x1="0" y1="10"  x2="520" y2="10"  stroke="#f0f2f5" stroke-width="1"/>
                <line x1="0" y1="60"  x2="520" y2="60"  stroke="#f0f2f5" stroke-width="1"/>
                <line x1="0" y1="110" x2="520" y2="110" stroke="#f0f2f5" stroke-width="1"/>
                <line x1="0" y1="160" x2="520" y2="160" stroke="#f0f2f5" stroke-width="1"/>
                <line x1="0" y1="210" x2="520" y2="210" stroke="#f0f2f5" stroke-width="1"/>

                <text x="2"  y="14"  font-family="Inter,sans-serif" font-size="10" fill="#a0aab4">80</text>
                <text x="2"  y="64"  font-family="Inter,sans-serif" font-size="10" fill="#a0aab4">60</text>
                <text x="2"  y="114" font-family="Inter,sans-serif" font-size="10" fill="#a0aab4">40</text>
                <text x="2"  y="164" font-family="Inter,sans-serif" font-size="10" fill="#a0aab4">20</text>

                <g clip-path="url(#chartClip)">
                    <path fill="url(#fillGrad)"
                          d="M0,210
                             C6,204 12,200 17.9,197
                             C23,194 30,210 35.9,207
                             C41,204 48,165 53.8,155
                             C59,146 66,186 71.7,177
                             C77,168 84,142 89.7,132
                             C95,122 102,153 107.6,145
                             C113,137 120,196 125.5,187
                             C131,178 137,181 143.4,171
                             C149,161 155,133 161.4,123
                             C167,113 173,109 179.3,100
                             C185,91  191,147 197.2,139
                             C203,131 209,153 215.2,145
                             C221,137 227,97  233.1,87
                             C239,77  245,119 251.0,110
                             C257,101 263,75  269.0,65
                             C275,55  281,87  286.9,78
                             C292,69  298,141 304.8,132
                             C310,123 317,116 322.8,106
                             C328,96  334,68  340.7,58
                             C346,48  352,45  358.6,35
                             C364,25  370,57  376.6,48
                             C382,39  388,96  394.5,87
                             C400,78  406,81  412.4,71
                             C418,61  424,33  430.3,23
                             C436,13  442,20  448.3,10
                             C454,0   460,51  466.2,42
                             C472,33  478,67  484.1,58
                             C490,49  496,87  502.1,78
                             C508,69  514,109 520,100
                             L520,215 L0,215 Z"/>

                    <path fill="none" stroke="#1e3a6e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                          d="M0,210
                             C6,204 12,200 17.9,197
                             C23,194 30,210 35.9,207
                             C41,204 48,165 53.8,155
                             C59,146 66,186 71.7,177
                             C77,168 84,142 89.7,132
                             C95,122 102,153 107.6,145
                             C113,137 120,196 125.5,187
                             C131,178 137,181 143.4,171
                             C149,161 155,133 161.4,123
                             C167,113 173,109 179.3,100
                             C185,91  191,147 197.2,139
                             C203,131 209,153 215.2,145
                             C221,137 227,97  233.1,87
                             C239,77  245,119 251.0,110
                             C257,101 263,75  269.0,65
                             C275,55  281,87  286.9,78
                             C292,69  298,141 304.8,132
                             C310,123 317,116 322.8,106
                             C328,96  334,68  340.7,58
                             C346,48  352,45  358.6,35
                             C364,25  370,57  376.6,48
                             C382,39  388,96  394.5,87
                             C400,78  406,81  412.4,71
                             C418,61  424,33  430.3,23
                             C436,13  442,20  448.3,10
                             C454,0   460,51  466.2,42
                             C472,33  478,67  484.1,58
                             C490,49  496,87  502.1,78
                             C508,69  514,109 520,100"/>

                    <path fill="none" stroke="#93c5fd" stroke-width="1.8" stroke-dasharray="4 4" stroke-linecap="round" stroke-linejoin="round"
                          d="M0,210
                             C6,207 12,204 17.9,200
                             C23,196 30,193 35.9,190
                             C41,187 48,181 53.8,177
                             C59,173 66,170 71.7,167
                             C77,163 84,156 89.7,150
                             C95,144 102,160 107.6,157
                             C113,153 120,147 125.5,143
                             C131,139 137,136 143.4,133
                             C149,129 155,122 161.4,117
                             C167,112 173,126 179.3,123
                             C185,119 191,113 197.2,110
                             C203,107 209,103 215.2,100
                             C221,97  227,126 233.1,123
                             C239,119 245,106 251.0,103
                             C257,99  263,83  269.0,77
                             C275,70  281,97  286.9,90
                             C292,83  298,70  304.8,63
                             C310,56  317,89  322.8,83
                             C328,77  334,63  340.7,57
                             C346,51  352,46  358.6,43
                             C364,40  370,54  376.6,50
                             C382,46  388,73  394.5,67
                             C400,60  406,36  412.4,33
                             C418,27  424,26  430.3,23
                             C436,19  442,14  448.3,10
                             C454,6   460,39  466.2,33
                             C472,27  478,21  484.1,17
                             C490,13  496,50  502.1,43
                             C508,37  514,50  520,57"/>

                    <circle cx="358.6" cy="35" r="4.5" fill="white" stroke="#1e3a6e" stroke-width="2"/>
                    <text x="358.6" y="23" font-family="Inter,sans-serif" font-size="10.5" font-weight="600" fill="#1e3a6e" text-anchor="middle">Peak:42</text>
                </g>
            </svg>

            <div style="display:flex;justify-content:space-between;padding-top:10px">
                <span style="font-size:11.5px;color:var(--muted)">SEP 01</span>
                <span style="font-size:11.5px;color:var(--muted)">SEP 10</span>
                <span style="font-size:11.5px;color:var(--muted)">SEP 20</span>
                <span style="font-size:11.5px;color:var(--muted)">SEP 30</span>
            </div>
        </div>

        <div class="chart-card">
            <div class="card-title">Issue Distribution</div>
            <div class="card-sub">Resolution by infrastructure type</div>
            <div class="donut-wrap">
                <canvas id="donutChart" width="180" height="180"></canvas>
                <div class="donut-center">
                    <div class="donut-val">1.2k</div>
                    <div class="donut-lbl">Tickets</div>
                </div>
            </div>
            <div class="dist-list">
                <div class="dist-row">
                    <div class="dist-left"><div class="dist-dot" style="background:#1e3a6e"></div>Backend Infrastructure</div>
                    <div class="dist-pct">45%</div>
                </div>
                <div class="dist-row">
                    <div class="dist-left"><div class="dist-dot" style="background:#3b6ea8"></div>Frontend / UI Issues</div>
                    <div class="dist-pct">30%</div>
                </div>
                <div class="dist-row">
                    <div class="dist-left"><div class="dist-dot" style="background:#bfdbfe"></div>API Integrations</div>
                    <div class="dist-pct">25%</div>
                </div>
            </div>
        </div>
    </div>

    <div class="team-card">
        <div class="team-head">
            <div>
                <div class="card-title">Team Performance</div>
                <div class="card-sub" style="margin-bottom:0">Live metrics for active support agents</div>
            </div>
            <div style="display:flex;align-items:center;gap:12px">
                <a href="#" class="view-all">
                    View All Agents
                </a>
                <button class="icon-btn" title="Export">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                </button>
            </div>
        </div>
        <table>
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
                <?php foreach ($agents as $a): ?>
                <tr>
                    <td>
                        <div class="agent-cell">
                            <div class="agent-av" style="background:<?= $a['color'] ?>"><?= $a['initials'] ?></div>
                            <div>
                                <div class="agent-name"><?= htmlspecialchars($a['name']) ?></div>
                                <div class="agent-role"><?= htmlspecialchars($a['role']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:14px;font-weight:600;color:var(--navy)"><?= $a['resolved'] ?></td>
                    <td style="font-size:13.5px;color:var(--text)"><?= $a['avg_response'] ?></td>
                    <td>
                        <div class="load-bar-wrap">
                            <div class="load-bar" style="width:<?= $a['load'] ?>%"></div>
                        </div>
                    </td>
                    <td><span class="csat-val"><?= $a['csat'] ?></span></td>
                    <td>
                        <?php if ($a['status']==='online'): ?>
                            <span class="status-badge"><span class="dot-online"></span><span class="online-txt">ONLINE</span></span>
                        <?php else: ?>
                            <span class="status-badge"><span class="dot-away"></span><span class="away-txt">AWAY</span></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</main>

<button class="fab" title="New">
    <svg width="22" height="22" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
</button>

<script>
(function(){
    const ctx = document.getElementById('donutChart').getContext('2d');
    new Chart(ctx,{
        type:'doughnut',
        data:{
            datasets:[{
                data:[45,30,25],
                backgroundColor:['#1e3a6e','#3b6ea8','#bfdbfe'],
                borderWidth:0,
                hoverOffset:6,
            }]
        },
        options:{
            cutout:'72%',
            responsive:false,
            plugins:{legend:{display:false},tooltip:{
                backgroundColor:'#0d1b2e',
                titleColor:'#fff',
                bodyColor:'#93c5fd',
                padding:10,
                cornerRadius:8,
                callbacks:{label:function(c){return ' '+c.label+': '+c.parsed+'%'}}
            }},
        }
    });
})();
</script>
</body>
</html>