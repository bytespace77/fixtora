<?php

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard – Helpdesk Pro</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --bg:          #f4f5f9;
      --surface:     #ffffff;
      --border:      #e5e7ef;
      --border-dark: #c5cade;
      --text:        #111827;
      --text-sub:    #374151;
      --muted:       #6b7280;
      --muted-lt:    #9ca3af;
      --blue:        #1e3a8a;
      --blue-mid:    #2563eb;
      --blue-light:  #dbeafe;
      --blue-bg:     #eff4ff;
      --navy:        #1e3a6e;
      --orange:      #f97316;
      --orange-bg:   #fff7ed;
      --green:       #16a34a;
      --red:         #dc2626;
      --urgent-bg:   #fef2f2;
      --urgent-text: #dc2626;
      --review-bg:   #eff6ff;
      --review-text: #2563eb;
      --backlog-bg:  #f3f4f6;
      --backlog-text:#6b7280;
      --radius:      10px;
      --radius-sm:   7px;
      --shadow:      0 1px 3px rgba(0,0,0,.07), 0 4px 12px rgba(0,0,0,.04);
      --shadow-card: 0 1px 4px rgba(0,0,0,.06), 0 6px 20px rgba(0,0,0,.05);
    }

    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg);
      color: var(--text);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      font-size: 14px;
    }

    .topnav {
      background: var(--surface);
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      padding: 0 24px;
      height: 58px;
      position: sticky; top: 0; z-index: 100;
      gap: 20px;
    }
    .brand-wrap { min-width: 180px; }
    .brand-name { font-weight: 800; font-size: 15px; color: var(--text); letter-spacing: -.3px; }
    .brand-sub  { font-size: 9px; font-weight: 700; letter-spacing: 1.4px; color: var(--muted); text-transform: uppercase; margin-top: 1px; }
    .search-wrap {
      flex: 1; max-width: 420px;
      position: relative;
    }
    .search-wrap svg { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--muted-lt); }
    .search-wrap input {
      width: 100%; padding: 8px 12px 8px 36px;
      border: 1px solid var(--border); border-radius: 8px;
      font-size: 13px; font-family: inherit; color: var(--text);
      background: var(--bg); outline: none;
      transition: border-color .12s, box-shadow .12s;
    }
    .search-wrap input::placeholder { color: var(--muted-lt); }
    .search-wrap input:focus { border-color: var(--blue-mid); box-shadow: 0 0 0 3px rgba(37,99,235,.12); background: #fff; }
    .topnav-right { display: flex; align-items: center; gap: 10px; margin-left: auto; }
    .icon-btn {
      width: 34px; height: 34px; border-radius: 8px;
      background: transparent; border: 1px solid var(--border);
      display: flex; align-items: center; justify-content: center;
      cursor: pointer; color: var(--muted);
      transition: background .12s;
    }
    .icon-btn:hover { background: var(--bg); }
    .avatar-top {
      width: 34px; height: 34px; border-radius: 8px;
      background: #374151; display: flex; align-items: center;
      justify-content: center; cursor: pointer; overflow: hidden;
      font-size: 12px; font-weight: 700; color: #fff;
    }

    .layout { display: flex; flex: 1; min-height: 0; }

    .sidebar {
      width: 195px; min-width: 195px;
      background: var(--surface);
      border-right: 1px solid var(--border);
      display: flex; flex-direction: column;
      position: sticky; top: 58px;
      height: calc(100vh - 58px);
      overflow-y: auto;
    }
    .sidebar-nav { flex: 1; padding: 16px 10px 10px; }
    .sidebar-nav a {
      display: flex; align-items: center; gap: 10px;
      text-decoration: none; font-size: 13px; font-weight: 500;
      color: var(--muted); padding: 8px 11px; border-radius: 7px;
      transition: all .12s; margin-bottom: 2px; position: relative;
    }
    .sidebar-nav a svg { flex-shrink: 0; }
    .sidebar-nav a:hover { color: var(--text-sub); background: var(--bg); }
    .sidebar-nav a.active {
      color: var(--blue-mid); background: var(--blue-bg);
      font-weight: 600;
    }
    .sidebar-nav a.active::before {
      content: ''; position: absolute;
      left: 0; top: 5px; bottom: 5px;
      width: 3px; background: var(--blue-mid);
      border-radius: 0 3px 3px 0;
    }
    .sidebar-bottom { padding: 10px; border-top: 1px solid var(--border); }
    .new-ticket-btn {
      display: flex; align-items: center; justify-content: center; gap: 7px;
      background: var(--navy); color: #fff;
      border: none; border-radius: 8px; padding: 11px;
      font-size: 13px; font-weight: 600; cursor: pointer;
      width: 100%; font-family: inherit;
      transition: background .12s; margin-bottom: 6px;
    }
    .new-ticket-btn:hover { background: #162c57; }
    .sidebar-link {
      display: flex; align-items: center; gap: 9px;
      text-decoration: none; font-size: 12.5px; font-weight: 500;
      color: var(--muted); padding: 7px 11px; border-radius: 7px;
      transition: all .12s; margin-bottom: 1px;
    }
    .sidebar-link:hover { color: var(--text); background: var(--bg); }
    .user-row {
      display: flex; align-items: center; gap: 9px;
      padding: 10px 11px 6px; margin-top: 4px;
    }
    .user-avatar {
      width: 32px; height: 32px; border-radius: 50%;
      background: #374151; flex-shrink: 0;
      display: flex; align-items: center; justify-content: center;
      font-size: 11px; font-weight: 700; color: #fff;
    }
    .user-name { font-size: 12.5px; font-weight: 700; color: var(--text); }
    .user-role { font-size: 11px; color: var(--muted); margin-top: 1px; }

    .main { flex: 1; padding: 30px 32px 60px; overflow-y: auto; }

    .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 24px; }
    .page-header h1 { font-size: 26px; font-weight: 800; letter-spacing: -.6px; color: var(--blue); }
    .page-header p  { font-size: 13px; color: var(--muted); margin-top: 4px; }
    .header-actions { display: flex; gap: 10px; margin-top: 4px; }
    .btn-outline {
      display: flex; align-items: center; gap: 6px;
      padding: 8px 14px; border: 1px solid var(--border-dark);
      border-radius: 7px; font-size: 12.5px; font-weight: 600;
      color: var(--text-sub); background: var(--surface); cursor: pointer;
      font-family: inherit; transition: all .12s; text-decoration: none;
    }
    .btn-outline:hover { background: var(--bg); border-color: var(--blue-mid); color: var(--blue-mid); }

    .stats-row { display: grid; grid-template-columns: 1fr 1fr 1.1fr; gap: 16px; margin-bottom: 20px; }

    .stat-card {
      background: var(--surface); border: 1px solid var(--border);
      border-radius: var(--radius); padding: 22px 22px 20px;
      box-shadow: var(--shadow-card); position: relative;
    }
    .stat-card.navy {
      background: var(--navy); border-color: var(--navy);
      color: #fff;
    }
    .stat-top { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 16px; }
    .stat-icon {
      width: 42px; height: 42px; border-radius: 10px;
      display: flex; align-items: center; justify-content: center;
      font-size: 18px;
    }
    .stat-icon.blue  { background: var(--blue-bg); color: var(--blue-mid); }
    .stat-icon.orange{ background: var(--orange-bg); color: var(--orange); }
    .stat-icon.white { background: rgba(255,255,255,.15); color: #fff; }
    .stat-badge {
      font-size: 10px; font-weight: 700; letter-spacing: .3px;
      padding: 3px 8px; border-radius: 20px;
    }
    .badge-green  { background: #dcfce7; color: #15803d; }
    .badge-blue   { background: var(--blue-light); color: var(--blue-mid); }
    .badge-white  { background: rgba(255,255,255,.18); color: #fff; font-size: 9.5px; letter-spacing: .5px; }
    .stat-label {
      font-size: 10.5px; font-weight: 700; letter-spacing: .8px;
      text-transform: uppercase; color: var(--muted); margin-bottom: 6px;
    }
    .navy .stat-label { color: rgba(255,255,255,.6); }
    .stat-value { font-size: 32px; font-weight: 800; letter-spacing: -1px; color: var(--text); }
    .navy .stat-value { color: #fff; font-size: 30px; }
    .stat-pct { font-size: 16px; font-weight: 600; }

    .sla-arc {
      position: absolute; right: 14px; bottom: 14px;
      width: 70px; height: 70px; opacity: .18;
    }

    .middle-row { display: grid; grid-template-columns: 1fr 290px; gap: 16px; margin-bottom: 20px; }

    .chart-card {
      background: var(--surface); border: 1px solid var(--border);
      border-radius: var(--radius); padding: 22px;
      box-shadow: var(--shadow-card);
    }
    .chart-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; }
    .chart-title { font-size: 15px; font-weight: 700; color: var(--text); letter-spacing: -.2px; }
    .chart-legend { display: flex; align-items: center; gap: 14px; }
    .legend-item { display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--muted); font-weight: 500; }
    .legend-dot { width: 8px; height: 8px; border-radius: 50%; }
    .dot-blue { background: var(--blue-mid); }
    .dot-gray { background: #d1d5db; }

    .chart-svg-wrap { width: 100%; }
    .chart-svg-wrap svg { width: 100%; height: 160px; }

    .chart-xaxis { display: flex; justify-content: space-between; padding: 8px 4px 0; }
    .chart-xaxis span { font-size: 10.5px; color: var(--muted-lt); font-weight: 600; letter-spacing: .3px; }

    .updates-card {
      background: var(--surface); border: 1px solid var(--border);
      border-radius: var(--radius); padding: 20px;
      box-shadow: var(--shadow-card);
    }
    .updates-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
    .updates-title { font-size: 14px; font-weight: 700; color: var(--text); letter-spacing: -.2px; }
    .view-all { font-size: 12px; font-weight: 600; color: var(--blue-mid); text-decoration: none; }
    .view-all:hover { text-decoration: underline; }

    .update-item { display: flex; gap: 12px; padding: 10px 0; position: relative; }
    .update-item:not(:last-child) { border-bottom: 1px solid var(--border); }
    .update-icon {
      width: 34px; height: 34px; border-radius: 8px;
      background: var(--bg); border: 1px solid var(--border);
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0; font-size: 13px; color: var(--muted);
    }
    .update-body { flex: 1; min-width: 0; }
    .update-title-text { font-size: 12.5px; font-weight: 700; color: var(--text); margin-bottom: 2px; }
    .update-desc  { font-size: 11px; color: var(--muted); line-height: 1.4; margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .update-time  { font-size: 10px; font-weight: 700; letter-spacing: .4px; color: var(--muted-lt); text-transform: uppercase; }
    .update-time.alert { color: var(--red); }
    .alert-dot {
      width: 8px; height: 8px; border-radius: 50%; background: var(--red);
      position: absolute; left: 25px; bottom: 9px;
      border: 2px solid var(--surface);
    }

    .queue-card {
      background: var(--surface); border: 1px solid var(--border);
      border-radius: var(--radius); padding: 22px;
      box-shadow: var(--shadow-card);
    }
    .queue-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 18px; }
    .queue-title { font-size: 16px; font-weight: 700; color: var(--text); letter-spacing: -.3px; }
    .queue-sub   { font-size: 12px; color: var(--muted); margin-top: 3px; }
    .queue-actions { display: flex; align-items: center; gap: 10px; }
    .queue-mgmt-btn {
      padding: 8px 14px; border: 1px solid var(--border-dark);
      border-radius: 7px; font-size: 12px; font-weight: 600;
      color: var(--text-sub); background: var(--surface);
      cursor: pointer; font-family: inherit; transition: all .12s;
    }
    .queue-mgmt-btn:hover { background: var(--bg); }
    .queue-chat-btn {
      width: 38px; height: 38px; border-radius: 50%;
      background: var(--navy); border: none; color: #fff;
      display: flex; align-items: center; justify-content: center;
      cursor: pointer; box-shadow: 0 4px 12px rgba(30,58,110,.35);
    }

    .ticket-row {
      display: flex; align-items: center; gap: 14px;
      padding: 14px 0; border-bottom: 1px solid var(--border);
    }
    .ticket-row:last-child { border-bottom: none; }
    .ticket-num-wrap {
      width: 50px; flex-shrink: 0;
      border-left: 3px solid var(--border-dark);
      padding-left: 10px;
    }
    .ticket-num {
      font-size: 11.5px; font-weight: 700; color: var(--muted);
      letter-spacing: -.2px;
    }
    .ticket-info { flex: 1; min-width: 0; }
    .ticket-name { font-size: 13.5px; font-weight: 700; color: var(--text); margin-bottom: 4px; letter-spacing: -.1px; }
    .ticket-meta { display: flex; align-items: center; gap: 14px; }
    .ticket-meta span { display: flex; align-items: center; gap: 4px; font-size: 11.5px; color: var(--muted); }
    .ticket-right { display: flex; align-items: center; justify-content: space-between; width: 180px; gap: 14px; flex-shrink: 0; }
    .status-pill {
      min-width: 95px; text-align: center; padding: 4px 11px; border-radius: 20px;
      font-size: 10.5px; font-weight: 700; letter-spacing: .4px; text-transform: uppercase; margin-right: 6px;
    }
    .ticket-end { display: flex; align-items: center; gap: 8px; }
    .pill-urgent  { background: var(--urgent-bg);  color: var(--urgent-text);  border: 1px solid #fecaca; }
    .pill-review  { background: var(--review-bg);  color: var(--review-text);  border: 1px solid #bfdbfe; }
    .pill-backlog { background: var(--backlog-bg);  color: var(--backlog-text); border: 1px solid #e5e7eb; }

    .ticket-avatars { display: flex; }
    .ticket-avatars .av {
      width: 26px; height: 26px; border-radius: 50%;
      border: 2px solid var(--surface);
      display: flex; align-items: center; justify-content: center;
      font-size: 10px; font-weight: 700; color: #fff;
      margin-left: -6px; flex-shrink: 0;
    }
    .ticket-avatars .av:first-child { margin-left: 0; }
    .av-teal   { background: #0d9488; }
    .av-purple { background: #7c3aed; }
    .av-orange { background: #ea580c; }
    .av-green  { background: #16a34a; }

    .more-btn {
      width: 28px; height: 28px; border-radius: 6px;
      border: none; background: transparent; cursor: pointer;
      display: flex; align-items: center; justify-content: center;
      color: var(--muted-lt); font-size: 16px; font-weight: 700;
      transition: background .12s;
    }
    .more-btn:hover { background: var(--bg); color: var(--text); }

    .ticket-row.urgent   .ticket-num-wrap { border-color: var(--red); }
    .ticket-row.review   .ticket-num-wrap { border-color: var(--blue-mid); }
    .ticket-row.backlog  .ticket-num-wrap { border-color: var(--border-dark); }
  </style>
</head>
<body>

<header class="topnav">
  <div class="brand-wrap">
    <div class="brand-name">Helpdesk Pro</div>
    <div class="brand-sub">Architectural Concierge</div>
  </div>
  <div class="search-wrap">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
    <input type="text" placeholder="Search tickets, agents, or knowledge…"/>
  </div>
  <div class="topnav-right">
    <div class="icon-btn">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
    </div>
    <div class="icon-btn">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
    </div>
    <div class="avatar-top">AS</div>
  </div>
</header>

<div class="layout">
  <aside class="sidebar">
    <nav class="sidebar-nav">
      <a href="#" class="active">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
        Dashboard
      </a>
      <a href="#">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        Tickets
      </a>
      <a href="#">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
        Tasks
      </a>
      <a href="#">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        SLA Monitor
      </a>
      <a href="#">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
        Notifications
      </a>
    </nav>
    <div class="sidebar-bottom">
      <button class="new-ticket-btn">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        New Ticket
      </button>
      <a href="#" class="sidebar-link">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
        Settings
      </a>
      <a href="#" class="sidebar-link">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        Help
      </a>
      <div class="user-row">
        <div class="user-avatar">AS</div>
        <div>
          <div class="user-name">Alex Sterling</div>
          <div class="user-role">Senior Architect</div>
        </div>
      </div>
    </div>
  </aside>

  <main class="main">

    <div class="page-header">
      <div>
        <h1>Operational Overview</h1>
        <p>Welcome back, Alex. Your concierge metrics are looking optimal today.</p>
      </div>
      <div class="header-actions">
        <a href="#" class="btn-outline">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          Last 24 Hours
        </a>
        <a href="#" class="btn-outline">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          Export Report
        </a>
      </div>
    </div>

    <div class="stats-row">
      <div class="stat-card">
        <div class="stat-top">
          <div class="stat-icon blue">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2z"/><path d="M13 5v2"/><path d="M13 17v2"/><path d="M13 11v2"/></svg>
          </div>
          <span class="stat-badge badge-green">+12% vs last week</span>
        </div>
        <div class="stat-label">Active Tickets</div>
        <div class="stat-value">1,248</div>
      </div>

      <div class="stat-card">
        <div class="stat-top">
          <div class="stat-icon orange">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
          </div>
          <span class="stat-badge badge-blue">On Target</span>
        </div>
        <div class="stat-label">Resolved (24H)</div>
        <div class="stat-value">842</div>
      </div>

      <div class="stat-card navy">
        <div class="stat-top">
          <div class="stat-icon white">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
          </div>
          <span class="stat-badge badge-white">Critical Metric</span>
        </div>
        <div class="stat-label">SLA Compliance</div>
        <div class="stat-value">99.4<span class="stat-pct">%</span></div>
        <svg class="sla-arc" viewBox="0 0 100 100" fill="none">
          <path d="M10 90 A60 60 0 0 1 90 90" stroke="white" stroke-width="8" stroke-linecap="round"/>
          <path d="M10 90 A60 60 0 0 1 85 35" stroke="white" stroke-width="8" stroke-linecap="round" opacity=".5"/>
        </svg>
      </div>
    </div>

    <div class="middle-row">

      <div class="chart-card">
        <div class="chart-header">
          <div class="chart-title">Ticket Inflow &amp; Resolution</div>
          <div class="chart-legend">
            <div class="legend-item"><span class="legend-dot dot-blue"></span> Inflow</div>
            <div class="legend-item"><span class="legend-dot dot-gray"></span> Resolution</div>
          </div>
        </div>
        <div class="chart-svg-wrap">
          <svg viewBox="0 0 600 160" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
              <linearGradient id="blueGrad" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#2563eb" stop-opacity=".18"/>
                <stop offset="100%" stop-color="#2563eb" stop-opacity="0"/>
              </linearGradient>
              <linearGradient id="grayGrad" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#9ca3af" stop-opacity=".12"/>
                <stop offset="100%" stop-color="#9ca3af" stop-opacity="0"/>
              </linearGradient>
            </defs>

            <line x1="0" y1="40"  x2="600" y2="40"  stroke="#f0f0f0" stroke-width="1"/>
            <line x1="0" y1="80"  x2="600" y2="80"  stroke="#f0f0f0" stroke-width="1"/>
            <line x1="0" y1="120" x2="600" y2="120" stroke="#f0f0f0" stroke-width="1"/>

            <path d="M0 120 C40 110,70 60,120 55 C170 50,200 90,250 70 C300 50,340 30,380 40 C420 50,460 80,500 65 C540 50,570 45,600 50 L600 160 L0 160 Z"
                  fill="url(#blueGrad)"/>

            <path d="M0 120 C40 110,70 60,120 55 C170 50,200 90,250 70 C300 50,340 30,380 40 C420 50,460 80,500 65 C540 50,570 45,600 50"
                  fill="none" stroke="#2563eb" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round"/>

            <path d="M0 135 C40 130,80 105,130 100 C180 95,210 115,260 105 C310 95,350 80,400 88 C450 96,480 110,530 100 C560 93,580 90,600 92 L600 160 L0 160 Z"
                  fill="url(#grayGrad)"/>

            <path d="M0 135 C40 130,80 105,130 100 C180 95,210 115,260 105 C310 95,350 80,400 88 C450 96,480 110,530 100 C560 93,580 90,600 92"
                  fill="none" stroke="#d1d5db" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"/>
          </svg>
        </div>
        <div class="chart-xaxis">
          <?php
          $days = ['MON','TUE','WED','THU','FRI','SAT','SUN'];
          foreach ($days as $d): ?>
            <span><?= $d ?></span>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="updates-card">
        <div class="updates-header">
          <span class="updates-title">System Updates</span>
          <a href="#" class="view-all">View All</a>
        </div>

        <?php
        $updates = [
          ['icon'=>'📋','title'=>'Infrastructure Optimized','desc'=>'Node clusters in US-East balanced successfully…','time'=>'2 MINS AGO','alert'=>false],
          ['icon'=>'!', 'title'=>'Critical Ticket Spike',   'desc'=>"Inbound volume for 'Auth Service' exceeded threshold…",'time'=>'14 MINS AGO','alert'=>true],
          ['icon'=>'👤','title'=>'New Architect Joined',    'desc'=>'Sarah Jenkins is now assigned to Level 2 Support…','time'=>'1 HOUR AGO','alert'=>false],
        ];
        foreach ($updates as $u): ?>
        <div class="update-item">
          <div class="update-icon"><?= $u['icon'] ?></div>
          <div class="update-body">
            <div class="update-title-text"><?= $u['title'] ?></div>
            <div class="update-desc"><?= $u['desc'] ?></div>
            <div class="update-time <?= $u['alert'] ? 'alert' : '' ?>"><?= $u['time'] ?></div>
          </div>
          <?php if ($u['alert']): ?>
            <span class="alert-dot"></span>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>

    </div>

    <div class="queue-card">
      <div class="queue-header">
        <div>
          <div class="queue-title">Priority Concierge Queue</div>
          <div class="queue-sub">Active issues requiring immediate structural intervention.</div>
        </div>
        <div class="queue-actions">
          <button class="queue-mgmt-btn">Queue Management</button>
          <button class="queue-chat-btn">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
          </button>
        </div>
      </div>

      <?php
      $tickets = [
        ['num'=>'#492','name'=>'Database Sharding Failure - Primary Vault','duration'=>'12m duration','assignee'=>'Marco Polo',  'status'=>'URGENT',   'class'=>'urgent',  'pill'=>'pill-urgent',  'avatars'=>[['T','av-teal'],['P','av-purple']]],
        ['num'=>'#501','name'=>'Global CSS Refactor - Component Library',  'duration'=>'45m duration','assignee'=>'Elena Ruiz', 'status'=>'IN REVIEW','class'=>'review',  'pill'=>'pill-review',  'avatars'=>[['O','av-orange']]],
        ['num'=>'#504','name'=>'API Rate Limiting - Third Party Integration','duration'=>'1h 20m duration','assignee'=>'System Bot','status'=>'BACKLOG', 'class'=>'backlog', 'pill'=>'pill-backlog', 'avatars'=>[]],
      ];
      foreach ($tickets as $t): ?>
      <div class="ticket-row <?= $t['class'] ?>">
        <div class="ticket-num-wrap">
          <div class="ticket-num"><?= $t['num'] ?></div>
        </div>
        <div class="ticket-info">
          <div class="ticket-name"><?= $t['name'] ?></div>
          <div class="ticket-meta">
            <span>
              <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
              <?= $t['duration'] ?>
            </span>
            <span>
              <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              <?= $t['assignee'] ?>
            </span>
          </div>
        </div>
        <div class="ticket-right">
          <span class="status-pill <?= $t['pill'] ?>"><?= $t['status'] ?></span>

          <div class="ticket-end">  
            <?php if (!empty($t['avatars'])): ?>
            <div class="ticket-avatars">
                <?php foreach ($t['avatars'] as $av): ?>
                <div class="av <?= $av[1] ?>"><?= $av[0] ?></div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <button class="more-btn">⋯</button>
            </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

  </main>
</div>

</body>
</html>