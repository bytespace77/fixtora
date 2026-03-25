<?php

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Helpdesk Pro – Architectural Concierge</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --sidebar-w: 200px;
    --navy: #1a2744;
    --navy-light: #223060;
    --accent: #3b5bdb;
    --accent-hover: #364fc7;
    --green: #2f9e44;
    --green-bg: #ebfbee;
    --green-text: #2f9e44;
    --red: #e03131;
    --orange: #f76707;
    --gray-50: #f8f9fa;
    --gray-100: #f1f3f5;
    --gray-200: #e9ecef;
    --gray-300: #dee2e6;
    --gray-400: #ced4da;
    --gray-500: #adb5bd;
    --gray-600: #868e96;
    --gray-700: #495057;
    --gray-800: #343a40;
    --gray-900: #212529;
    --card-shadow: 0 1px 3px rgba(0,0,0,.08), 0 1px 2px rgba(0,0,0,.05);
    --radius: 10px;
    --radius-sm: 6px;
  }

  body {
    font-family: 'Inter', sans-serif;
    background: var(--gray-50);
    color: var(--gray-900);
    display: flex;
    min-height: 100vh;
    font-size: 14px;
  }

  .sidebar {
    width: var(--sidebar-w);
    background: #fff;
    border-right: 1px solid var(--gray-200);
    display: flex;
    flex-direction: column;
    position: fixed;
    top: 0; left: 0; bottom: 0;
    z-index: 100;
  }

  .sidebar-brand {
    padding: 22px 20px 16px;
    border-bottom: 1px solid var(--gray-100);
  }

  .sidebar-brand .brand-name {
    font-size: 15px;
    font-weight: 700;
    color: var(--gray-900);
    letter-spacing: -.3px;
  }

  .sidebar-brand .brand-sub {
    font-size: 9px;
    font-weight: 600;
    color: var(--gray-500);
    letter-spacing: 1.2px;
    text-transform: uppercase;
    margin-top: 2px;
  }

  .sidebar-nav {
    flex: 1;
    padding: 12px 0;
  }

  .nav-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 20px;
    font-size: 13.5px;
    font-weight: 500;
    color: var(--gray-600);
    cursor: pointer;
    border-radius: 0;
    transition: background .15s, color .15s;
    text-decoration: none;
    position: relative;
  }

  .nav-item:hover { background: var(--gray-50); color: var(--gray-900); }

  .nav-item.active {
    color: var(--accent);
    background: #eef2ff;
  }

  .nav-item.active::before {
    content: '';
    position: absolute;
    left: 0; top: 4px; bottom: 4px;
    width: 3px;
    background: var(--accent);
    border-radius: 0 2px 2px 0;
  }

  .nav-icon {
    width: 18px;
    height: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .nav-icon svg { width: 16px; height: 16px; stroke-width: 1.8; }

  .sidebar-bottom {
    padding: 14px 20px;
    border-top: 1px solid var(--gray-100);
  }

  .sidebar-bottom .nav-item { padding: 8px 0; border-radius: var(--radius-sm); }

  .sidebar-user {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 20px 10px;
  }

  .user-avatar {
    width: 34px; height: 34px;
    border-radius: 50%;
    background: var(--navy);
    color: #fff;
    font-size: 12px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .user-info .user-name { font-size: 13px; font-weight: 600; color: var(--gray-800); }
  .user-info .user-role { font-size: 11px; color: var(--gray-500); margin-top: 1px; }

  .new-ticket-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    background: var(--navy);
    color: #fff;
    border: none;
    border-radius: var(--radius);
    padding: 10px 16px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    width: calc(100% - 32px);
    margin: 6px 16px 14px;
    transition: background .15s;
  }

  .new-ticket-btn:hover { background: var(--navy-light); }

  .main {
    margin-left: var(--sidebar-w);
    flex: 1;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
  }

  .topbar {
    background: #fff;
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    align-items: center;
    padding: 0 28px;
    height: 56px;
    gap: 12px;
    position: sticky;
    top: 0;
    z-index: 90;
  }

  .search-box {
    display: flex;
    align-items: center;
    gap: 8px;
    background: var(--gray-100);
    border-radius: var(--radius-sm);
    padding: 7px 14px;
    flex: 1;
    max-width: 380px;
    cursor: text;
  }

  .search-box svg { color: var(--gray-500); flex-shrink: 0; }
  .search-box span { font-size: 13px; color: var(--gray-500); }

  .topbar-right {
    margin-left: auto;
    display: flex;
    align-items: center;
    gap: 14px;
  }

  .status-pill {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 500;
    color: var(--green-text);
  }

  .status-dot {
    width: 7px; height: 7px;
    border-radius: 50%;
    background: var(--green);
    box-shadow: 0 0 0 2px #d3f9d8;
  }

  .icon-btn {
    width: 32px; height: 32px;
    border-radius: 6px;
    border: none;
    background: transparent;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: var(--gray-600);
    transition: background .15s;
  }

  .icon-btn:hover { background: var(--gray-100); }

  .content {
    padding: 28px;
    flex: 1;
  }

  .breadcrumb {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: var(--gray-500);
    margin-bottom: 20px;
  }

  .breadcrumb span { cursor: pointer; }
  .breadcrumb span:hover { color: var(--accent); }
  .breadcrumb .sep { color: var(--gray-400); }
  .breadcrumb .current { color: var(--gray-800); font-weight: 500; }

  .board-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 20px;
  }

  .board-title { font-size: 22px; font-weight: 700; color: var(--gray-900); letter-spacing: -.4px; }
  .board-desc { font-size: 13px; color: var(--gray-500); margin-top: 4px; }

  .board-actions {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .avatar-stack {
    display: flex;
    align-items: center;
  }

  .avatar-stack .av {
    width: 28px; height: 28px;
    border-radius: 50%;
    border: 2px solid #fff;
    margin-left: -8px;
    background: var(--navy);
    color: #fff;
    font-size: 10px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
  }

  .avatar-stack .av:first-child { margin-left: 0; }
  .avatar-stack .av.more { background: var(--gray-700); font-size: 9px; }

  .view-toggle {
    display: flex;
    background: var(--gray-100);
    border-radius: var(--radius-sm);
    padding: 3px;
    gap: 2px;
  }

  .vt-btn {
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 5px 11px;
    border-radius: 5px;
    border: none;
    background: transparent;
    font-size: 12.5px;
    font-weight: 500;
    color: var(--gray-600);
    cursor: pointer;
    transition: all .15s;
  }

  .vt-btn.active { background: #fff; color: var(--gray-900); box-shadow: 0 1px 2px rgba(0,0,0,.08); }

  .filter-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: var(--radius-sm);
    border: 1px solid var(--gray-300);
    background: #fff;
    font-size: 13px;
    font-weight: 500;
    color: var(--gray-700);
    cursor: pointer;
    transition: background .15s;
  }

  .filter-btn:hover { background: var(--gray-50); }

  .kanban {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 16px;
    margin-bottom: 28px;
  }

  .column-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
    padding: 0 2px;
  }

  .column-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13.5px;
    font-weight: 600;
    color: var(--gray-800);
  }

  .column-indicator {
    width: 3px;
    height: 14px;
    border-radius: 2px;
  }

  .col-todo .column-indicator { background: var(--gray-400); }
  .col-doing .column-indicator { background: var(--accent); }
  .col-done .column-indicator { background: var(--green); }

  .column-count {
    font-size: 12px;
    font-weight: 500;
    color: var(--gray-500);
    background: var(--gray-100);
    border-radius: 10px;
    padding: 1px 7px;
  }

  .col-more-btn {
    border: none;
    background: transparent;
    color: var(--gray-500);
    cursor: pointer;
    font-size: 18px;
    line-height: 1;
    padding: 0 4px;
  }

  .card {
    background: #fff;
    border-radius: var(--radius);
    border: 1px solid var(--gray-200);
    padding: 14px;
    margin-bottom: 10px;
    box-shadow: var(--card-shadow);
    transition: box-shadow .15s, border-color .15s;
    cursor: pointer;
  }

  .card:hover { box-shadow: 0 4px 12px rgba(0,0,0,.1); border-color: var(--gray-300); }

  .card-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
  }

  .ticket-id { font-size: 11px; color: var(--gray-500); font-weight: 500; }

  .priority-badge {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .5px;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    gap: 4px;
  }

  .priority-badge::before {
    content: '●';
    font-size: 8px;
  }

  .priority-high { color: var(--red); }
  .priority-medium { color: var(--orange); }
  .priority-urgent { color: var(--red); }
  .priority-resolved { color: var(--green-text); }

  .card-title {
    font-size: 13.5px;
    font-weight: 600;
    color: var(--gray-900);
    line-height: 1.4;
    margin-bottom: 6px;
  }

  .card-desc {
    font-size: 12px;
    color: var(--gray-500);
    line-height: 1.5;
    margin-bottom: 12px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .card-assignee {
    display: flex;
    align-items: center;
    gap: 7px;
  }

  .assignee-av {
    width: 24px; height: 24px;
    border-radius: 50%;
    background: var(--navy);
    color: #fff;
    font-size: 9px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .assignee-av.unassigned {
    background: var(--gray-200);
    color: var(--gray-500);
  }

  .assignee-name { font-size: 12px; color: var(--gray-600); font-weight: 500; }

  .card-stats {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 11.5px;
    color: var(--gray-500);
  }

  .stat-item { display: flex; align-items: center; gap: 3px; }
  .stat-item svg { width: 12px; height: 12px; }

  .progress-wrap { margin: 8px 0 10px; }
  .progress-bar-bg {
    height: 6px;
    border-radius: 3px;
    background: var(--gray-200);
    overflow: hidden;
  }
  .progress-bar-fill {
    height: 100%;
    border-radius: 3px;
    background: var(--accent);
    transition: width .3s;
  }

  .card.resolved {
    opacity: .85;
  }

  .card.resolved .card-title {
    text-decoration: line-through;
    color: var(--gray-500);
  }

  .resolved-date { font-size: 11px; color: var(--gray-400); }

  .add-task-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    width: 100%;
    padding: 10px;
    border: 1.5px dashed var(--gray-300);
    border-radius: var(--radius);
    background: transparent;
    color: var(--gray-400);
    font-size: 13px;
    cursor: pointer;
    transition: border-color .15s, color .15s;
    margin-top: 4px;
  }

  .add-task-btn:hover { border-color: var(--accent); color: var(--accent); }

  .bottom-row {
    display: grid;
    grid-template-columns: 1fr 280px;
    gap: 16px;
    align-items: start;
  }

  .chart-card {
    background: #fff;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius);
    padding: 20px 22px;
    box-shadow: var(--card-shadow);
  }

  .chart-title { font-size: 15px; font-weight: 700; color: var(--gray-900); margin-bottom: 18px; letter-spacing: -.3px; }

  .bar-chart {
    display: flex;
    align-items: flex-end;
    gap: 10px;
    height: 180px;
    padding-bottom: 2px;
  }

  .bar-wrap {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    height: 100%;
    justify-content: flex-end;
  }

  .bar {
    width: 100%;
    border-radius: 4px 4px 0 0;
    background: var(--gray-200);
    transition: background .15s;
    min-height: 4px;
  }

  .bar.blue { background: #c5d0f5; }
  .bar.active { background: var(--navy); }
  .bar:hover { background: var(--accent); }

  .bar-label { font-size: 10.5px; color: var(--gray-400); font-weight: 500; }

  .right-col { display: flex; flex-direction: column; gap: 14px; }

  .sla-card {
    background: var(--navy);
    border-radius: var(--radius);
    padding: 20px;
    color: #fff;
    position: relative;
    overflow: hidden;
  }

  .sla-card::before {
    content: '';
    position: absolute;
    top: -30px; right: -30px;
    width: 120px; height: 120px;
    border-radius: 50%;
    background: rgba(255,255,255,.05);
  }

  .sla-icon {
    width: 36px; height: 36px;
    border-radius: 8px;
    background: rgba(255,255,255,.12);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 14px;
  }

  .sla-percent { font-size: 34px; font-weight: 800; letter-spacing: -1px; margin-bottom: 2px; }
  .sla-label { font-size: 13px; font-weight: 600; opacity: .9; margin-bottom: 8px; }
  .sla-desc { font-size: 12px; opacity: .65; line-height: 1.5; }

  .add-fab {
    position: absolute;
    top: 16px; right: 16px;
    width: 32px; height: 32px;
    border-radius: 50%;
    background: var(--navy);
    color: #fff;
    border: none;
    font-size: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(0,0,0,.2);
  }

  .deadlines-card {
    background: #fff;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius);
    padding: 18px 20px;
    box-shadow: var(--card-shadow);
  }

  .deadlines-label {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: var(--gray-500);
    margin-bottom: 14px;
  }

  .deadline-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 7px 0;
    border-bottom: 1px solid var(--gray-100);
    font-size: 13px;
    font-weight: 500;
    color: var(--gray-800);
  }

  .deadline-dot {
    width: 7px; height: 7px;
    border-radius: 50%;
    background: var(--red);
    flex-shrink: 0;
  }

  .view-schedule {
    display: flex;
    align-items: center;
    gap: 5px;
    margin-top: 12px;
    font-size: 13px;
    font-weight: 500;
    color: var(--gray-700);
    cursor: pointer;
    text-decoration: none;
  }

  .view-schedule:hover { color: var(--accent); }
</style>
</head>
<body>

<?php

$todo_cards = [
  [
    'id' => '#TK-8821',
    'priority' => 'HIGH',
    'title' => 'Audit server logs for latency in Node-East API gateway',
    'desc' => 'Investigate reports of 500ms+ response times during peak hours on...',
    'assignee' => 'Sarah M.',
    'initials' => 'SM',
    'comments' => 3,
  ],
  [
    'id' => '#TK-8845',
    'priority' => 'MEDIUM',
    'title' => 'Update documentation for v2.4 core security patch',
    'desc' => 'Ensure all internal architectural diagrams reflect the new firewall rule...',
    'assignee' => null,
    'initials' => null,
    'comments' => 1,
  ],
];

$doing_cards = [
  [
    'id' => '#TK-8790',
    'priority' => 'URGENT',
    'title' => 'Cloudflare WAF policy reconfiguration for portal.helpdesk.pro',
    'desc' => null,
    'progress' => 65,
    'assignee' => 'David K.',
    'initials' => 'DK',
    'comments' => 12,
    'time' => '6d',
  ],
  [
    'id' => '#TK-8801',
    'priority' => 'MEDIUM',
    'title' => 'Analyze SQL query performance for Ticket Search module',
    'desc' => 'Slow joins identified on the meta_tags table. Need to re-index the productio...',
    'assignee' => 'Elena R.',
    'initials' => 'ER',
    'comments' => null,
    'time' => '2d rem.',
    'progress' => null,
  ],
];

$done_cards = [
  [
    'id' => '#TK-8712',
    'title' => 'Draft Architectural Guideline for 2024 VDI upgrade',
    'assignee' => 'Alex Chen',
    'initials' => 'AC',
    'date' => 'Oct 12',
  ],
  [
    'id' => '#TK-8744',
    'title' => 'Security Patch deployment for Sandbox-4 Environment',
    'assignee' => 'Sarah M.',
    'initials' => 'SM',
    'date' => 'Oct 10',
  ],
];

$bar_data = [
  'MON' => ['h' => 42,  'blue' => true],
  'TUE' => ['h' => 62,  'blue' => true],
  'WED' => ['h' => 100, 'blue' => true],
  'THU' => ['h' => 68,  'blue' => true],
  'FRI' => ['h' => 82,  'blue' => true],
  'SAT' => ['h' => 44,  'blue' => false],
  'SUN' => ['h' => 32,  'blue' => false],
];

function priority_class($p) {
  $map = ['HIGH' => 'priority-high', 'MEDIUM' => 'priority-medium', 'URGENT' => 'priority-urgent'];
  return $map[$p] ?? '';
}
?>

<aside class="sidebar">
  <div class="sidebar-brand">
    <div class="brand-name">Helpdesk Pro</div>
    <div class="brand-sub">Architectural Concierge</div>
  </div>

  <nav class="sidebar-nav">
    <a href="#" class="nav-item">
      <span class="nav-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
      </span>
      Dashboard
    </a>
    <a href="#" class="nav-item">
      <span class="nav-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20 12V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h6"/><path d="M8 10h8M8 14h4"/><circle cx="18" cy="18" r="3"/><path d="m21 21-1.5-1.5"/></svg>
      </span>
      Tickets
    </a>
    <a href="#" class="nav-item active">
      <span class="nav-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 12h6M9 16h4"/></svg>
      </span>
      Tasks
    </a>
    <a href="#" class="nav-item">
      <span class="nav-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
      </span>
      SLA Monitor
    </a>
    <a href="#" class="nav-item">
      <span class="nav-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
      </span>
      Notifications
    </a>
  </nav>

  <button class="new-ticket-btn">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
    New Ticket
  </button>

  <div class="sidebar-bottom">
    <a href="#" class="nav-item">
      <span class="nav-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93A10 10 0 0 0 2.93 19.07M4.93 4.93a10 10 0 1 1 14.14 14.14"/></svg>
      </span>
      Settings
    </a>
    <a href="#" class="nav-item">
      <span class="nav-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3M12 17h.01"/></svg>
      </span>
      Help
    </a>
  </div>

  <div class="sidebar-user">
    <div class="user-avatar">AC</div>
    <div class="user-info">
      <div class="user-name">Alex Chen</div>
      <div class="user-role">Senior Architect</div>
    </div>
  </div>
</aside>

<main class="main">

  <header class="topbar">
    <div class="search-box">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
      <span>Search tasks, tickets, or documentation...</span>
    </div>
    <div class="topbar-right">
      <div class="status-pill">
        <div class="status-dot"></div>
        SYSTEM ONLINE
      </div>
      <button class="icon-btn">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
      </button>
      <button class="icon-btn">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93A10 10 0 0 0 2.93 19.07M4.93 4.93a10 10 0 1 1 14.14 14.14"/></svg>
      </button>
    </div>
  </header>

  <div class="content">

    <div class="breadcrumb">
      <span>PATH</span>
      <span class="sep">/</span>
      <span>Projects</span>
      <span class="sep">/</span>
      <span>Internal Support</span>
      <span class="sep">/</span>
      <span class="current">Task Board</span>

      <div style="margin-left:auto; display:flex; align-items:center; gap:10px;">
        <div class="avatar-stack">
          <div class="av" style="background:#2d6a4f;">JL</div>
          <div class="av" style="background:#7b2d8b;">RK</div>
          <div class="av" style="background:#c05621;">SM</div>
          <div class="av more">+12</div>
        </div>
        <button class="filter-btn">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
          Filter
        </button>
      </div>
    </div>

    <div class="board-header">
      <div>
        <div class="board-title">Team Sprint: Q3 Maintenance</div>
        <div class="board-desc">Managing internal architectural support tickets and resource allocation.</div>
      </div>
      <div class="view-toggle">
        <button class="vt-btn active">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
          Board View
        </button>
        <button class="vt-btn">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
          List View
        </button>
      </div>
    </div>

    <div class="kanban">

      <div class="col-todo">
        <div class="column-header">
          <div class="column-label">
            <div class="column-indicator"></div>
            To Do
            <span class="column-count"><?= count($todo_cards) ?></span>
          </div>
          <button class="col-more-btn">···</button>
        </div>

        <?php foreach ($todo_cards as $card): ?>
        <div class="card">
          <div class="card-meta">
            <span class="ticket-id"><?= htmlspecialchars($card['id']) ?></span>
            <span class="priority-badge <?= priority_class($card['priority']) ?>">
              <?= htmlspecialchars($card['priority']) ?>
            </span>
          </div>
          <div class="card-title"><?= htmlspecialchars($card['title']) ?></div>
          <?php if ($card['desc']): ?>
          <div class="card-desc"><?= htmlspecialchars($card['desc']) ?></div>
          <?php endif; ?>
          <div class="card-footer">
            <div class="card-assignee">
              <?php if ($card['assignee']): ?>
                <div class="assignee-av"><?= htmlspecialchars($card['initials']) ?></div>
                <span class="assignee-name"><?= htmlspecialchars($card['assignee']) ?></span>
              <?php else: ?>
                <div class="assignee-av unassigned">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                </div>
                <span class="assignee-name" style="color:var(--gray-400)">Unassigned</span>
              <?php endif; ?>
            </div>
            <div class="card-stats">
              <span class="stat-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                <?= $card['comments'] ?>
              </span>
            </div>
          </div>
        </div>
        <?php endforeach; ?>

        <button class="add-task-btn">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
          Add Task
        </button>
      </div>

      <div class="col-doing">
        <div class="column-header">
          <div class="column-label">
            <div class="column-indicator"></div>
            Doing
            <span class="column-count"><?= count($doing_cards) ?></span>
          </div>
          <button class="col-more-btn">···</button>
        </div>

        <?php foreach ($doing_cards as $card): ?>
        <div class="card">
          <div class="card-meta">
            <span class="ticket-id"><?= htmlspecialchars($card['id']) ?></span>
            <span class="priority-badge <?= priority_class($card['priority']) ?>">
              <?= htmlspecialchars($card['priority']) ?>
            </span>
          </div>
          <div class="card-title"><?= htmlspecialchars($card['title']) ?></div>

          <?php if (!empty($card['progress'])): ?>
          <div class="progress-wrap">
            <div class="progress-bar-bg">
              <div class="progress-bar-fill" style="width:<?= $card['progress'] ?>%"></div>
            </div>
          </div>
          <?php endif; ?>

          <?php if (!empty($card['desc'])): ?>
          <div class="card-desc"><?= htmlspecialchars($card['desc']) ?></div>
          <?php endif; ?>

          <div class="card-footer">
            <div class="card-assignee">
              <div class="assignee-av"><?= htmlspecialchars($card['initials']) ?></div>
              <span class="assignee-name"><?= htmlspecialchars($card['assignee']) ?></span>
            </div>
            <div class="card-stats">
              <?php if (!empty($card['comments'])): ?>
              <span class="stat-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                <?= $card['comments'] ?>
              </span>
              <?php endif; ?>
              <?php if (!empty($card['time'])): ?>
              <span class="stat-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <?= htmlspecialchars($card['time']) ?>
              </span>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <div class="col-done">
        <div class="column-header">
          <div class="column-label">
            <div class="column-indicator"></div>
            Done
            <span class="column-count"><?= count($done_cards) ?></span>
          </div>
          <button class="col-more-btn" style="color:var(--gray-400)">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.34"/></svg>
          </button>
        </div>

        <?php foreach ($done_cards as $card): ?>
        <div class="card resolved">
          <div class="card-meta">
            <span class="ticket-id"><?= htmlspecialchars($card['id']) ?></span>
            <span class="priority-badge priority-resolved">RESOLVED</span>
          </div>
          <div class="card-title"><?= htmlspecialchars($card['title']) ?></div>
          <div class="card-footer" style="margin-top:10px;">
            <div class="card-assignee">
              <div class="assignee-av"><?= htmlspecialchars($card['initials']) ?></div>
              <span class="assignee-name"><?= htmlspecialchars($card['assignee']) ?></span>
            </div>
            <span class="resolved-date"><?= htmlspecialchars($card['date']) ?></span>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

    </div>

    <div class="bottom-row">

      <div class="chart-card">
        <div class="chart-title">Workload Velocity</div>
        <div class="bar-chart">
          <?php foreach ($bar_data as $day => $info): 
            $cls = 'bar';
            if ($day === 'WED') $cls .= ' active';
            elseif ($info['blue']) $cls .= ' blue';
          ?>
          <div class="bar-wrap">
            <div class="<?= $cls ?>" style="height:<?= $info['h'] ?>%"></div>
            <span class="bar-label"><?= $day ?></span>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="right-col">

        <div class="sla-card" style="position:relative;">
          <button class="add-fab">+</button>
          <div class="sla-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
          </div>
          <div class="sla-percent">94%</div>
          <div class="sla-label">SLA Success Rate</div>
          <div class="sla-desc">Architectural tasks are being resolved 12% faster than last quarter.</div>
        </div>

        <div class="deadlines-card">
          <div class="deadlines-label">Upcoming Deadlines</div>
          <div class="deadline-item">
            <div class="deadline-dot"></div>
            Core API Refresh
          </div>
          <div class="deadline-item">
            <div class="deadline-dot"></div>
            Node 14 Migration
          </div>
          <a href="#" class="view-schedule">
            View Schedule
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </a>
        </div>

      </div>
    </div>

  </div>
</main>

</body>
</html>