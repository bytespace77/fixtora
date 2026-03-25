<?php

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Create New Service Request – Architectural Concierge</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --bg:          #f7f8fc;
      --surface:     #ffffff;
      --border:      #dde2f0;
      --border-dark: #b8c2e0;
      --text:        #111827;
      --text-sub:    #374151;
      --muted:       #6b7280;
      --nav-active:  #2563eb;
      --sidebar-active-bar: #2563eb;
      --sidebar-active-bg:  #eff4ff;
      --accent-btn:  #2563eb;
      --accent-fg:   #ffffff;
      --draft-bg:    #ffffff;
      --draft-border:#cbd5e1;
      --blue:        #2563eb;
      --blue-light:  #dbeafe;
      --blue-mid:    #3b82f6;
      --green:       #16a34a;
      --orange:      #ea580c;
      --red:         #dc2626;
      --impact-sel-border: #2563eb;
      --impact-sel-bg:     #eff6ff;
      --radio-checked: #2563eb;
      --radius:      8px;
      --shadow:      0 1px 3px rgba(37,99,235,.06), 0 2px 10px rgba(0,0,0,.04);
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
      height: 52px;
      position: sticky;
      top: 0;
      z-index: 100;
    }
    .brand {
      font-weight: 700;
      font-size: 14.5px;
      color: var(--text);
      margin-right: 28px;
      white-space: nowrap;
      letter-spacing: -.2px;
    }
    .topnav nav { display: flex; gap: 2px; flex: 1; }
    .topnav nav a {
      text-decoration: none;
      font-size: 13.5px;
      font-weight: 500;
      color: var(--muted);
      padding: 6px 14px;
      transition: color .12s;
      position: relative;
    }
    .topnav nav a:hover { color: var(--text); }
    .topnav nav a.active {
      color: var(--nav-active);
      font-weight: 600;
    }
    .topnav nav a.active::after {
      content: '';
      position: absolute;
      bottom: -1px; left: 14px; right: 14px;
      height: 2.5px;
      background: var(--blue);
      border-radius: 2px 2px 0 0;
    }
    .topnav-right { display: flex; align-items: center; gap: 8px; margin-left: auto; }
    .icon-btn {
      width: 32px; height: 32px; border-radius: 50%;
      background: transparent; border: 1px solid var(--border);
      display: flex; align-items: center; justify-content: center;
      cursor: pointer; color: var(--muted);
      transition: background .12s, border-color .12s;
    }
    .icon-btn:hover { background: var(--bg); border-color: var(--border-dark); }
    .avatar {
      width: 32px; height: 32px; border-radius: 50%;
      background: #b5803a;
      display: flex; align-items: center;
      justify-content: center; font-size: 12px; font-weight: 700;
      color: #fff; cursor: pointer;
      letter-spacing: .3px;
    }

    .layout { display: flex; flex: 1; min-height: 0; }

    .sidebar {
      width: 195px;
      min-width: 195px;
      background: var(--surface);
      border-right: 1px solid var(--border);
      display: flex;
      flex-direction: column;
      position: sticky;
      top: 52px;
      height: calc(100vh - 52px);
      overflow-y: auto;
    }
    .sidebar-brand {
      padding: 18px 16px 16px;
      border-bottom: 1px solid var(--border);
    }
    .sidebar-brand .sb-name { font-weight: 700; font-size: 13px; color: var(--text); }
    .sidebar-brand .sb-tier {
      font-size: 9.5px; font-weight: 600; letter-spacing: 1px;
      color: var(--muted); text-transform: uppercase; margin-top: 3px;
    }
    .sidebar nav { flex: 1; padding: 10px 8px; }
    .sidebar nav a {
      display: flex; align-items: center; gap: 9px;
      text-decoration: none; font-size: 12px; font-weight: 500;
      color: var(--muted); padding: 7px 10px; border-radius: 6px;
      transition: all .12s; margin-bottom: 1px;
      position: relative;
    }
    .sidebar nav a svg { flex-shrink: 0; opacity: .7; }
    .sidebar nav a:hover { color: var(--text-sub); background: var(--bg); }
    .sidebar nav a.active {
      color: var(--blue);
      background: var(--sidebar-active-bg);
      font-weight: 600;
    }
    .sidebar nav a.active::before {
      content: '';
      position: absolute;
      left: 0; top: 4px; bottom: 4px;
      width: 3px;
      background: var(--sidebar-active-bar);
      border-radius: 0 2px 2px 0;
    }
    .sidebar nav a.active svg { opacity: 1; color: var(--blue); }
    .sidebar-bottom {
      padding: 10px 8px 14px;
      border-top: 1px solid var(--border);
    }
    .new-ticket-btn {
      display: flex; align-items: center; gap: 7px;
      background: var(--blue); color: #fff;
      border: none; border-radius: 7px; padding: 9px 13px;
      font-size: 12.5px; font-weight: 600; cursor: pointer;
      width: 100%; transition: opacity .12s;
      font-family: inherit; letter-spacing: -.1px;
    }
    .new-ticket-btn:hover { background: #1d4ed8; }
    .logout-link {
      display: flex; align-items: center; gap: 9px;
      text-decoration: none; font-size: 12px; font-weight: 500;
      color: var(--muted); padding: 7px 10px; margin-top: 6px;
      border-radius: 6px; transition: all .12s;
    }
    .logout-link:hover { color: var(--text); background: var(--bg); }

    .main { flex: 1; padding: 28px 36px 60px; overflow-y: auto; }
    .breadcrumb {
      display: flex; align-items: center; gap: 5px;
      font-size: 12.5px; color: var(--muted); margin-bottom: 14px;
    }
    .breadcrumb a { color: var(--muted); text-decoration: none; }
    .breadcrumb a:hover { color: var(--text); text-decoration: underline; }
    .breadcrumb .sep { font-size: 10px; color: var(--border-dark); }
    .breadcrumb .current { color: var(--text-sub); font-weight: 500; }
    h1 { font-size: 24px; font-weight: 700; letter-spacing: -.5px; margin-bottom: 24px; color: var(--text); }

    .content-grid {
      display: grid;
      grid-template-columns: 1fr 250px;
      gap: 20px;
      align-items: start;
    }
    .left-col { display: flex; flex-direction: column; gap: 18px; }

    .card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 22px 24px;
      box-shadow: var(--shadow);
    }
    .card-title {
      display: flex; align-items: center; gap: 8px;
      font-size: 14px; font-weight: 700; margin-bottom: 20px;
      color: var(--text); letter-spacing: -.2px;
    }
    .card-title .title-icon { color: var(--blue); display: flex; }

    .field { margin-bottom: 16px; }
    .field:last-child { margin-bottom: 0; }
    .field-label {
      display: block; font-size: 10.5px; font-weight: 600;
      letter-spacing: .7px; text-transform: uppercase;
      color: var(--muted); margin-bottom: 6px;
    }
    input[type="text"],
    select,
    textarea {
      width: 100%; padding: 9px 12px;
      border: 1px solid var(--border); border-radius: 6px;
      font-size: 13.5px; font-family: inherit; color: var(--text);
      background: var(--surface); outline: none;
      transition: border-color .12s, box-shadow .12s;
      appearance: none; -webkit-appearance: none;
    }
    input[type="text"]::placeholder,
    textarea::placeholder { color: #b0b0a8; font-size: 13px; }
    input[type="text"]:focus,
    select:focus,
    textarea:focus {
      border-color: var(--blue-mid);
      box-shadow: 0 0 0 3px rgba(37,99,235,.14);
    }
    .field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .select-wrap { position: relative; }
    .select-wrap::after {
      content: '▾'; position: absolute; right: 11px; top: 50%;
      transform: translateY(-50%); pointer-events: none;
      font-size: 11px; color: var(--muted);
    }
    .select-wrap select { padding-right: 28px; color: var(--text-sub); }
    select option[value=""] { color: #b0b0a8; }

    .rich-toolbar {
      display: flex; gap: 1px; padding: 6px 8px;
      border: 1px solid var(--border); border-bottom: none;
      border-radius: 6px 6px 0 0; background: #fafaf8;
    }
    .rich-toolbar button {
      width: 28px; height: 28px; border: none; background: transparent;
      border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: 700;
      color: var(--muted); display: flex; align-items: center;
      justify-content: center; transition: all .12s; font-family: inherit;
    }
    .rich-toolbar button:hover { background: var(--border); color: var(--text); }
    .rich-toolbar .tb-divider { width: 1px; background: var(--border); margin: 5px 3px; }
    textarea.rich-area {
      border-radius: 0 0 6px 6px; resize: vertical;
      min-height: 120px; font-size: 13.5px; line-height: 1.6;
      color: var(--text-sub);
    }

    .dropzone {
      border: 2px dashed var(--border-dark); border-radius: var(--radius);
      padding: 32px 20px; text-align: center; cursor: pointer;
      transition: border-color .12s, background .12s; background: #fafaf8;
    }
    .dropzone:hover { border-color: var(--blue); background: #eff6ff; }
    .dropzone-icon { font-size: 26px; margin-bottom: 10px; color: var(--muted); }
    .dropzone-title { font-size: 13.5px; font-weight: 600; color: var(--text-sub); margin-bottom: 4px; }
    .dropzone-sub { font-size: 11.5px; color: var(--muted); line-height: 1.5; }
    .browse-link {
      display: inline-block; margin-top: 10px;
      font-size: 11px; font-weight: 700; letter-spacing: .7px;
      text-transform: uppercase; color: var(--blue);
      border-bottom: 1.5px solid var(--blue); cursor: pointer;
    }

    .resources-header {
      display: flex; align-items: center; justify-content: space-between;
      margin-bottom: 14px;
    }
    .resources-header h2 { font-size: 15px; font-weight: 700; letter-spacing: -.3px; }
    .view-all-link {
      font-size: 11px; font-weight: 700; letter-spacing: .5px;
      text-transform: uppercase; text-decoration: none; color: var(--blue);
      transition: color .12s;
    }
    .view-all-link:hover { color: #1d4ed8; }
    .resources-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
    .resource-card {
      background: var(--surface); border: 1px solid var(--border);
      border-radius: var(--radius); padding: 16px;
      box-shadow: var(--shadow); cursor: pointer; transition: all .15s;
    }
    .resource-card:hover { border-color: var(--border-dark); transform: translateY(-1px); box-shadow: 0 4px 16px rgba(0,0,0,.07); }
    .resource-icon { font-size: 18px; margin-bottom: 9px; }
    .resource-card h4 { font-size: 12.5px; font-weight: 700; margin-bottom: 5px; color: var(--text); }
    .resource-card p { font-size: 11.5px; color: var(--muted); line-height: 1.5; }

    .right-col { display: flex; flex-direction: column; gap: 16px; }

    .impact-card {
      background: var(--surface); border: 1px solid var(--border);
      border-radius: var(--radius); padding: 20px 18px;
      box-shadow: var(--shadow);
    }
    .impact-title {
      display: flex; align-items: center; gap: 7px;
      font-size: 13.5px; font-weight: 700; margin-bottom: 14px;
      color: var(--text);
    }
    .impact-exclaim {
      width: 18px; height: 18px; border-radius: 50%;
      background: #fff3e0; display: flex; align-items: center;
      justify-content: center; font-size: 11px; font-weight: 800;
      color: #e67700; flex-shrink: 0; border: 1.5px solid #ffd08a;
    }
    .impact-option {
      border: 1.5px solid var(--border); border-radius: 7px;
      padding: 11px 12px; margin-bottom: 7px; cursor: pointer;
      transition: all .12s; display: flex; align-items: flex-start; gap: 10px;
    }
    .impact-option:last-child { margin-bottom: 0; }
    .impact-option input[type="radio"] {
      width: 15px; height: 15px; flex-shrink: 0; margin-top: 1px;
      accent-color: var(--radio-checked); cursor: pointer;
    }
    .impact-option:hover { border-color: #aab4e8; }
    .impact-option.selected {
      border-color: var(--impact-sel-border);
      background: var(--impact-sel-bg);
    }
    .impact-label { font-size: 12.5px; font-weight: 700; display: block; color: var(--text); }
    .impact-desc { font-size: 11px; color: var(--muted); line-height: 1.4; margin-top: 1px; display: block; }

    .actions-card {
      background: var(--surface); border: 1px solid var(--border);
      border-radius: var(--radius); padding: 16px 16px 14px;
      box-shadow: var(--shadow);
    }
    .actions-title {
      font-size: 10.5px; font-weight: 700; margin-bottom: 10px;
      color: var(--muted); text-transform: uppercase; letter-spacing: .7px;
    }
    .btn {
      display: flex; align-items: center; justify-content: center; gap: 7px;
      width: 100%; padding: 10px; border-radius: 7px;
      font-size: 13px; font-weight: 600; cursor: pointer;
      border: 1px solid transparent; transition: all .12s;
      font-family: inherit; margin-bottom: 7px; letter-spacing: -.1px;
    }
    .btn:last-child { margin-bottom: 0; }
    .btn-primary {
      background: var(--blue); color: var(--accent-fg);
      border-color: var(--blue);
    }
    .btn-primary:hover { background: #1d4ed8; border-color: #1d4ed8; }
    .btn-secondary {
      background: var(--surface); color: var(--text-sub);
      border-color: var(--draft-border);
    }
    .btn-secondary:hover { background: var(--bg); border-color: var(--border-dark); }
    .btn-ghost {
      background: transparent; color: var(--muted);
      border: none; font-size: 12.5px; font-weight: 500;
    }
    .btn-ghost:hover { color: var(--text); text-decoration: underline; }

    .health-card {
      background: var(--surface); border: 1px solid var(--border);
      border-radius: var(--radius); padding: 16px;
      box-shadow: var(--shadow);
    }
    .health-header {
      display: flex; align-items: center; justify-content: space-between;
      margin-bottom: 12px;
    }
    .health-title {
      font-size: 10.5px; font-weight: 700; letter-spacing: .7px;
      text-transform: uppercase; color: var(--muted);
    }
    .status-dot-live {
      width: 7px; height: 7px; border-radius: 50%;
      background: #40c057; box-shadow: 0 0 0 2px #d3f9d8;
    }
    .health-row {
      display: flex; align-items: center; justify-content: space-between;
      font-size: 12.5px; padding: 3px 0;
    }
    .health-row:not(:last-child) { margin-bottom: 3px; }
    .health-label { color: var(--text-sub); }
    .status-badge { font-size: 12px; font-weight: 700; }
    .status-stable { color: var(--green); }
    .status-degraded { color: var(--orange); }

    .resources-section { margin-top: 4px; }

    @media (max-width: 900px) {
      .content-grid { grid-template-columns: 1fr; }
      .resources-grid { grid-template-columns: 1fr 1fr; }
    }
  </style>
</head>
<body>

<header class="topnav">
  <span class="brand">Architectural Concierge</span>
  <nav>
    <a href="#">Dashboard</a>
    <a href="#" class="active">Tickets</a>
    <a href="#">Reports</a>
    <a href="#">Systems</a>
  </nav>
  <div class="topnav-right">
    <div class="icon-btn" title="Notifications">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
    </div>
    <div class="icon-btn" title="Help">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
    </div>
    <div class="avatar">AC</div>
  </div>
</header>

<div class="layout">
  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sb-name">HelpDesk Pro</div>
      <div class="sb-tier">Enterprise Tier</div>
    </div>
    <nav>
      <a href="#">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
        Overview
      </a>
      <a href="#" class="active">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        Active Tickets
      </a>
      <a href="#">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
        System Health
      </a>
      <a href="#">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        Team Chat
      </a>
      <a href="#">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
        Settings
      </a>
    </nav>
    <div class="sidebar-bottom">
      <button class="new-ticket-btn">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        New Ticket
      </button>
      <a href="#" class="logout-link">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        Logout
      </a>
    </div>
  </aside>

  <main class="main">
    <div class="breadcrumb">
      <a href="#">Dashboard</a>
      <span class="sep">›</span>
      <a href="#">Tickets</a>
      <span class="sep">›</span>
      <span class="current">New Ticket</span>
    </div>
    <h1>Create New Service Request</h1>

    <form method="post" action="#" enctype="multipart/form-data">
      <div class="content-grid">

        <div class="left-col">

          <div class="card">
            <div class="card-title">
              <span class="title-icon">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
              </span>
              Issue Identity
            </div>

            <div class="field">
              <label class="field-label" for="issue_title">Issue Title</label>
              <input type="text" id="issue_title" name="issue_title" placeholder="e.g., Performance degradation in API v2"/>
            </div>

            <div class="field field-row">
              <div>
                <label class="field-label" for="affected_system">Affected System</label>
                <div class="select-wrap">
                  <select id="affected_system" name="affected_system">
                    <option value="">Select System</option>
                    <option>CRM Portal</option>
                    <option>Payment GW</option>
                    <option>API v2</option>
                    <option>Auth Service</option>
                  </select>
                </div>
              </div>
              <div>
                <label class="field-label" for="category">Category</label>
                <div class="select-wrap">
                  <select id="category" name="category">
                    <option value="">Select Category</option>
                    <option>Performance</option>
                    <option>Security</option>
                    <option>Integration</option>
                    <option>Bug</option>
                    <option>Feature Request</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <div class="card">
            <div class="card-title">
              <span class="title-icon">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
              </span>
              Functional Details
            </div>
            <div class="field">
              <label class="field-label">Detailed Description</label>
              <div class="rich-toolbar">
                <button type="button" title="Bold"><b>B</b></button>
                <button type="button" title="Italic"><i style="font-style:italic">I</i></button>
                <button type="button" title="List" style="font-size:12px">≡</button>
                <div class="tb-divider"></div>
                <button type="button" title="Code" style="font-family:monospace;font-size:11px;letter-spacing:-.5px">&lt;/&gt;</button>
                <button type="button" title="Attach" style="font-size:13px">📎</button>
              </div>
              <textarea class="rich-area" id="description" name="description"
                placeholder="Please describe the architectural or functional issue in detail…"></textarea>
            </div>
          </div>

          <div class="card">
            <div class="card-title">
              <span class="title-icon">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
              </span>
              Evidentiary Support
            </div>
            <label for="file_upload" style="display:block;cursor:pointer;">
              <div class="dropzone" id="dropzone">
                <div class="dropzone-icon">
                  <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#b0b0a8" stroke-width="1.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                </div>
                <p class="dropzone-title">Drag and drop log files or screenshots here</p>
                <span class="dropzone-sub">Maximum file size 25MB. Supported formats: JPG, PNG, LOG, JSON.</span><br/>
                <span class="browse-link">OR BROWSE FILES</span>
              </div>
            </label>
            <input type="file" id="file_upload" name="files[]" multiple accept=".jpg,.jpeg,.png,.log,.json" style="display:none;"/>
          </div>

          <div class="resources-section">
            <div class="resources-header">
              <h2>Recommended Resources</h2>
              <a href="#" class="view-all-link">View All Docs &rarr;</a>
            </div>
            <div class="resources-grid">
              <div class="resource-card">
                <div class="resource-icon">📋</div>
                <h4>API Integration Guide</h4>
                <p>Comprehensive documentation for connecting to the v2 core API.</p>
              </div>
              <div class="resource-card">
                <div class="resource-icon">🐛</div>
                <h4>Known Issues Logs</h4>
                <p>Check if your current issue is already being tracked by our team.</p>
              </div>
              <div class="resource-card">
                <div class="resource-icon">🛡️</div>
                <h4>Security Protocols</h4>
                <p>Standard procedures for reporting vulnerabilities and concerns.</p>
              </div>
            </div>
          </div>

        </div>

        <div class="right-col">

          <div class="impact-card">
            <div class="impact-title">
              <span class="impact-exclaim">!</span>
              Impact Level
            </div>
            <?php
            $impacts = [
              ['value'=>'critical','label'=>'Critical', 'desc'=>'System-wide outage, blocking operations'],
              ['value'=>'high',    'label'=>'High',     'desc'=>'Significant impact, workarounds difficult'],
              ['value'=>'medium',  'label'=>'Medium',   'desc'=>'Partial degradation, workarounds available'],
              ['value'=>'low',     'label'=>'Low',      'desc'=>'Minor annoyance, cosmetic or low-use features'],
            ];
            $selected = $_POST['impact'] ?? 'medium';
            foreach ($impacts as $imp):
              $isSel = ($selected === $imp['value']);
            ?>
            <label class="impact-option <?= $isSel ? 'selected' : '' ?>" onclick="selectImpact(this)">
              <input type="radio" name="impact" value="<?= $imp['value'] ?>" <?= $isSel ? 'checked' : '' ?>>
              <div>
                <span class="impact-label"><?= $imp['label'] ?></span>
                <span class="impact-desc"><?= $imp['desc'] ?></span>
              </div>
            </label>
            <?php endforeach; ?>
          </div>

          <div class="actions-card">
            <div class="actions-title">Submission Actions</div>
            <button type="submit" name="action" value="submit" class="btn btn-primary">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
              Submit Ticket
            </button>
            <button type="submit" name="action" value="draft" class="btn btn-secondary">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
              Save as Draft
            </button>
            <button type="button" class="btn btn-ghost" onclick="history.back()">Cancel &amp; Discard</button>
          </div>

          <div class="health-card">
            <div class="health-header">
              <span class="health-title">System Health</span>
              <span class="status-dot-live"></span>
            </div>
            <?php
            $systems = [
              ['name'=>'CRM Portal', 'status'=>'stable'],
              ['name'=>'Payment GW', 'status'=>'stable'],
              ['name'=>'API v2',     'status'=>'degraded'],
            ];
            foreach ($systems as $sys):
              $cls   = $sys['status'] === 'stable' ? 'status-stable' : 'status-degraded';
              $label = ucfirst($sys['status']);
            ?>
            <div class="health-row">
              <span class="health-label"><?= $sys['name'] ?></span>
              <span class="status-badge <?= $cls ?>"><?= $label ?></span>
            </div>
            <?php endforeach; ?>
          </div>

        </div>
      </div>
    </form>
  </main>
</div>

<script>
  function selectImpact(el) {
    document.querySelectorAll('.impact-option').forEach(o => o.classList.remove('selected'));
    el.classList.add('selected');
  }

  const dz = document.getElementById('dropzone');
  dz.addEventListener('dragover', e => {
    e.preventDefault();
    dz.style.borderColor = '#3b5bdb';
    dz.style.background = '#f4f6ff';
  });
  dz.addEventListener('dragleave', () => {
    dz.style.borderColor = '';
    dz.style.background = '';
  });
  dz.addEventListener('drop', e => {
    e.preventDefault();
    dz.style.borderColor = '';
    dz.style.background = '';
    const files = e.dataTransfer.files;
    if (files.length) {
      dz.querySelector('.dropzone-title').textContent = `${files.length} file(s) ready to upload`;
    }
  });
</script>

</body>
</html>