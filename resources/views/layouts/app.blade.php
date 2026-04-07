<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<meta name="csrf-token" content="{{ csrf_token() }}"/>
<title>@yield('title', 'Fixtora – Architectural Concierge')</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
    /* layout */
    --sidebar-w: 220px;
    --topbar-h:  58px;

    /* colors */
    --navy:      #0f1f38;
    --navy-2:    #162844;
    --navy-3:    #1e3a5f;
    --blue:      #2563eb;
    --blue-2:    #1d4ed8;
    --blue-lt:   #dbeafe;
    --blue-bg:   #eff6ff;
    --green:     #16a34a;
    --green-bg:  #f0fdf4;
    --red:       #dc2626;
    --red-bg:    #fef2f2;
    --orange:    #f97316;
    --orange-bg: #fff7ed;

    /* surfaces */
    --bg:        #f1f5f9;
    --surface:   #ffffff;
    --border:    #e2e8f0;
    --border-2:  #cbd5e1;

    /* text */
    --text:      #0f172a;
    --text-2:    #334155;
    --muted:     #64748b;
    --muted-lt:  #94a3b8;

    /* misc */
    --radius:    10px;
    --radius-sm: 7px;
    --shadow-sm: 0 1px 2px rgba(0,0,0,.05);
    --shadow:    0 1px 3px rgba(0,0,0,.06), 0 4px 12px rgba(0,0,0,.04);
    --shadow-md: 0 4px 20px rgba(0,0,0,.08);
}

html, body {
    height: 100%;
    font-family: 'Monsterra', 'Montserrat', sans-serif;
    font-size: 14px;
    color: var(--text);
    background: var(--bg);
    -webkit-font-smoothing: antialiased;
}

/* ═══════════════════════════════
   SHELL
═══════════════════════════════ */
.shell { display: flex; height: 100vh; overflow: hidden; }

/* ═══════════════════════════════
   SIDEBAR
═══════════════════════════════ */
.sidebar {
    width: var(--sidebar-w);
    min-width: var(--sidebar-w);
    background: var(--navy);
    display: flex;
    flex-direction: column;
    height: 100vh;
    overflow: hidden;
    z-index: 300;
    flex-shrink: 0;
    border-right: 1px solid rgba(255,255,255,0.05);
}

/* scrollbar (nav scrolls, not whole sidebar — avoids last links sitting under sb-bottom) */
.sb-nav::-webkit-scrollbar { width: 4px; }
.sb-nav::-webkit-scrollbar-track { background: transparent; }
.sb-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 4px; }

/* brand */
.sb-brand {
    flex-shrink: 0;
    padding: 20px 16px 18px;
    border-bottom: 1px solid rgba(255,255,255,0.07);
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
}

.sb-logo {
    width: 36px; height: 36px;
    border-radius: 10px;
    background: var(--blue);
    display: flex; align-items: center; justify-content: center;
    color: #fff; flex-shrink: 0;
    box-shadow: 0 0 0 1px rgba(255,255,255,0.1), 0 4px 12px rgba(37,99,235,0.4);
}

.sb-brand-text .brand-name {
    font-size: 15px; font-weight: 800;
    color: #fff; letter-spacing: -0.3px;
}
.sb-brand-text .brand-sub {
    font-size: 9px; font-weight: 600;
    letter-spacing: 1.5px; text-transform: uppercase;
    color: rgba(255,255,255,0.3); margin-top: 2px;
}

/* nav */
.sb-section-label {
    font-size: 9px; font-weight: 700;
    letter-spacing: 1.8px; text-transform: uppercase;
    color: rgba(255,255,255,0.2);
    padding: 16px 16px 6px;
}

.sb-nav {
    flex: 1;
    min-height: 0;
    overflow-x: hidden;
    overflow-y: auto;
    padding: 8px 10px;
}

.nav-item {
    display: flex; align-items: center; gap: 9px;
    padding: 9px 12px;
    border-radius: 8px;
    font-size: 13px; font-weight: 500;
    color: rgba(255,255,255,0.5);
    text-decoration: none; cursor: pointer;
    transition: all 0.15s;
    margin-bottom: 2px;
    position: relative;
    border: none; background: transparent;
    width: 100%; text-align: left;
    font-family: 'Monsterra', 'Montserrat', sans-serif;
}
.nav-item svg { flex-shrink: 0; opacity: 0.65; transition: opacity 0.15s; }
.nav-item:hover { color: rgba(255,255,255,0.85); background: rgba(255,255,255,0.07); }
.nav-item:hover svg { opacity: 0.9; }
.nav-item.active {
    color: #fff; background: rgba(255,255,255,0.11);
    font-weight: 600;
}
.nav-item.active svg { opacity: 1; }
.nav-item.active::before {
    content: '';
    position: absolute; left: 0; top: 7px; bottom: 7px;
    width: 3px; border-radius: 0 3px 3px 0;
    background: #60a5fa;
}

.nav-badge {
    margin-left: auto;
    font-size: 10px; font-weight: 700;
    background: rgba(239,68,68,0.85);
    color: #fff; padding: 1px 7px;
    border-radius: 20px;
}

/* bottom */
.sb-bottom {
    flex-shrink: 0;
    padding: 10px;
    border-top: 1px solid rgba(255,255,255,0.07);
}

.new-ticket-btn {
    width: 100%; padding: 10px 14px;
    background: var(--blue);
    color: #fff; border: none;
    border-radius: 8px;
    font-size: 13px; font-weight: 700;
    cursor: pointer; font-family: 'Monsterra', 'Montserrat', sans-serif;
    display: flex; align-items: center; justify-content: center; gap: 7px;
    margin-bottom: 8px; text-decoration: none;
    transition: all 0.15s;
    box-shadow: 0 2px 8px rgba(37,99,235,0.35);
}
.new-ticket-btn:hover {
    background: var(--blue-2);
    transform: translateY(-1px);
    box-shadow: 0 4px 16px rgba(37,99,235,0.5);
    color: #fff;
}

.sb-util-links { margin-bottom: 6px; }

.sb-util-link {
    display: flex; align-items: center; gap: 8px;
    padding: 7px 12px; border-radius: 7px;
    font-size: 12px; font-weight: 500;
    color: rgba(255,255,255,0.38);
    text-decoration: none; transition: all 0.15s;
    border: none; background: transparent;
    cursor: pointer; width: 100%; text-align: left;
    font-family: 'Monsterra', 'Montserrat', sans-serif;
}
.sb-util-link:hover { color: rgba(255,255,255,0.7); background: rgba(255,255,255,0.06); }

/* user */
.sb-user {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 12px; border-radius: 8px;
    cursor: pointer; margin-top: 4px;
    text-decoration: none; transition: all 0.15s;
}
.sb-user:hover { background: rgba(255,255,255,0.07); }

.sb-avatar {
    width: 32px; height: 32px; border-radius: 50%;
    background: linear-gradient(135deg, var(--blue), #7c3aed);
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 800; color: #fff; flex-shrink: 0;
}

.sb-user-name { font-size: 12.5px; font-weight: 700; color: rgba(255,255,255,0.85); }
.sb-user-role { font-size: 10px; color: rgba(255,255,255,0.3); margin-top: 1px; font-weight: 500; }

/* ═══════════════════════════════
   MAIN
═══════════════════════════════ */
.main { flex: 1; display: flex; flex-direction: column; overflow: hidden; }

/* topbar */
.topbar {
    height: var(--topbar-h);
    min-height: var(--topbar-h);
    background: var(--surface);
    border-bottom: 1px solid var(--border);
    display: flex; align-items: center; gap: 12px;
    padding: 0 24px; z-index: 100;
}

.topbar-search { flex: 1; max-width: 340px; position: relative; }
.topbar-search svg {
    position: absolute; left: 11px; top: 50%;
    transform: translateY(-50%); color: var(--muted-lt);
}
.topbar-search input {
    width: 100%; padding: 8px 12px 8px 34px;
    border: 1.5px solid var(--border); border-radius: 8px;
    font-size: 13px; font-family: 'Monsterra', 'Montserrat', sans-serif;
    background: var(--bg); color: var(--text); outline: none;
    transition: all 0.15s; font-weight: 500;
}
.topbar-search input:focus { border-color: var(--blue); background: #fff; box-shadow: 0 0 0 3px rgba(37,99,235,0.08); }
.topbar-search input::placeholder { color: var(--muted-lt); font-weight: 400; }

.topbar-right { margin-left: auto; display: flex; align-items: center; gap: 6px; }

.sys-status {
    display: flex; align-items: center; gap: 6px;
    font-size: 10.5px; font-weight: 700;
    letter-spacing: 0.8px; color: var(--green);
    text-transform: uppercase; margin-right: 8px;
    padding: 5px 10px;
    background: var(--green-bg);
    border: 1px solid rgba(22,163,74,0.2);
    border-radius: 100px;
}
.sys-dot {
    width: 6px; height: 6px; border-radius: 50%;
    background: var(--green); animation: blink 2s infinite;
}
@keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.4} }

.icon-btn {
    width: 36px; height: 36px;
    border: 1.5px solid var(--border);
    background: var(--surface); color: var(--muted);
    cursor: pointer; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    transition: all 0.15s; text-decoration: none; font-size: 12px; font-weight: 600;
    position: relative;
}
.icon-btn:hover { background: var(--bg); color: var(--text); border-color: var(--border-2); }

/* notification bell wrapper */
.noti-wrap { position: relative; }

/* unread badge on bell */
.noti-badge {
    position: absolute;
    top: -5px; right: -5px;
    min-width: 17px; height: 17px;
    background: var(--red);
    color: #fff;
    font-size: 9px; font-weight: 800;
    border-radius: 100px;
    display: flex; align-items: center; justify-content: center;
    padding: 0 4px;
    border: 2px solid var(--surface);
    pointer-events: none;
    line-height: 1;
}
.noti-badge.hidden { display: none; }

/* dropdown panel */
.noti-dropdown {
    position: absolute;
    top: calc(100% + 10px);
    right: 0;
    width: 360px;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 12px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.12), 0 2px 8px rgba(0,0,0,0.06);
    z-index: 500;
    overflow: hidden;
    display: none;
    animation: dropIn 0.18s ease;
}
.noti-dropdown.open { display: block; }

@keyframes dropIn {
    from { opacity: 0; transform: translateY(-6px); }
    to   { opacity: 1; transform: translateY(0); }
}

.noti-dd-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 16px 12px;
    border-bottom: 1px solid var(--border);
}
.noti-dd-title {
    font-size: 13px; font-weight: 800;
    color: var(--text); letter-spacing: -0.2px;
}
.noti-dd-clear {
    font-size: 11px; font-weight: 700;
    color: var(--blue); cursor: pointer;
    background: none; border: none;
    font-family: 'Monsterra', 'Montserrat', sans-serif;
    transition: color 0.15s;
}
.noti-dd-clear:hover { color: var(--blue-2); }

.noti-list { max-height: 320px; overflow-y: auto; }
.noti-list::-webkit-scrollbar { width: 4px; }
.noti-list::-webkit-scrollbar-thumb { background: var(--border-2); border-radius: 4px; }

.noti-item {
    display: flex; align-items: flex-start; gap: 11px;
    padding: 13px 16px;
    border-bottom: 1px solid var(--border);
    cursor: pointer;
    transition: background 0.12s;
    position: relative;
    text-decoration: none;
}
.noti-item:last-child { border-bottom: none; }
.noti-item:hover { background: var(--bg); }
.noti-item.unread { background: var(--blue-bg); }
.noti-item.unread:hover { background: #e0ecff; }

/* unread dot */
.noti-item.unread::before {
    content: '';
    position: absolute;
    left: 6px; top: 50%;
    transform: translateY(-50%);
    width: 5px; height: 5px;
    border-radius: 50%;
    background: var(--blue);
}

.noti-icon {
    width: 34px; height: 34px;
    border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; margin-top: 1px;
}
.noti-icon.green  { background: var(--green-bg); color: var(--green); }
.noti-icon.red    { background: var(--red-bg);   color: var(--red); }
.noti-icon.blue   { background: var(--blue-bg);  color: var(--blue); }
.noti-icon.orange { background: var(--orange-bg);color: var(--orange); }

.noti-text { flex: 1; min-width: 0; }
.noti-title {
    font-size: 12.5px; font-weight: 700;
    color: var(--text); margin-bottom: 2px;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.noti-desc {
    font-size: 11.5px; font-weight: 400;
    color: var(--muted); line-height: 1.4;
}
.noti-time {
    font-size: 10px; font-weight: 600;
    color: var(--muted-lt); margin-top: 4px;
    letter-spacing: 0.3px; text-transform: uppercase;
}

.noti-dd-footer {
    padding: 11px 16px;
    border-top: 1px solid var(--border);
    text-align: center;
    background: var(--bg);
}
.noti-dd-footer a {
    font-size: 12px; font-weight: 700;
    color: var(--blue); text-decoration: none;
    transition: color 0.15s;
}
.noti-dd-footer a:hover { color: var(--blue-2); }

/* empty state in dropdown */
.noti-empty {
    text-align: center;
    padding: 32px 20px;
    color: var(--muted);
}
.noti-empty svg { margin: 0 auto 10px; display: block; opacity: 0.25; }
.noti-empty p { font-size: 12px; font-weight: 600; }

/* page */
.page-wrap {
    flex: 1; overflow-y: auto;
    padding: 28px 28px 60px;
    background: var(--bg);
}

/* scrollbar */
.page-wrap::-webkit-scrollbar { width: 6px; }
.page-wrap::-webkit-scrollbar-track { background: transparent; }
.page-wrap::-webkit-scrollbar-thumb { background: var(--border-2); border-radius: 6px; }

/* ═══════════════════════════════
   ALERTS
═══════════════════════════════ */
.alert {
    display: flex; align-items: flex-start; gap: 10px;
    padding: 13px 16px; border-radius: var(--radius);
    margin-bottom: 20px;
    font-size: 13px; font-weight: 500; line-height: 1.5;
    border: 1px solid transparent;
}
.alert svg { flex-shrink: 0; margin-top: 1px; }
.alert-success { background: var(--green-bg); color: #15803d; border-color: rgba(22,163,74,0.2); }
.alert-danger  { background: var(--red-bg);   color: var(--red); border-color: rgba(220,38,38,0.2); }

</style>
@yield('styles')
</head>
<body>
@includeWhen(str_contains(request()->getHost(), 'ngrok'), 'partials.ngrok-client-fix')
<div class="shell">

  <!-- SIDEBAR -->
  <aside class="sidebar">
    <a href="{{ route('home') }}" class="sb-brand">
      <div class="sb-logo">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
          <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
          <polyline points="9 22 9 12 15 12 15 22"/>
        </svg>
      </div>
      <div class="sb-brand-text">
        <div class="brand-name">Fixtora</div>
        <div class="brand-sub">Architectural Concierge</div>
      </div>
    </a>

    <nav class="sb-nav">
      {{-- Dashboard --}}
      @if((Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('view_dashboard')) && !Auth::user()->isDeveloper())
      <a href="{{ route('home') }}" class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
          <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
          <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
        </svg>
        Dashboard
      </a>
      @endif

      {{-- Tickets --}}
      @if(Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('list_tickets') || Auth::user()->hasPermission('create_tickets'))
      <a href="{{ route('tickets.index') }}" class="nav-item {{ request()->routeIs('tickets.*') ? 'active' : '' }}">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
          <polyline points="14 2 14 8 20 8"/>
          <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
        </svg>
        Tickets
        @php $unreadCount = \App\Models\Ticket::where('is_read', false)->count(); @endphp
        @if($unreadCount > 0)<span class="nav-badge">{{ $unreadCount }}</span>@endif
      </a>
      @endif

      {{-- Tasks --}}
      @if(Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('list_tasks') || Auth::user()->hasPermission('create_tasks'))
      <a href="{{ route('tasks.index') }}" class="nav-item {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
          <polyline points="9 11 12 14 22 4"/>
          <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
        </svg>
        Tasks
      </a>
      @endif



      {{-- SLA Monitor --}}
      @if((Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('view_sla_monitor')) && !Auth::user()->isDeveloper())
      <a href="{{ route('sla-monitor.index') }}" class="nav-item {{ request()->routeIs('sla-monitor.*') ? 'active' : '' }}">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
          <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
        </svg>
        SLA Monitor
      </a>
      @endif

      {{-- Reports --}}
      @if((Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('view_reports')) && !Auth::user()->isDeveloper())
      <a href="{{ route('reports.index') }}" class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
          <line x1="18" y1="20" x2="18" y2="10"/>
          <line x1="12" y1="20" x2="12" y2="4"/>
          <line x1="6" y1="20" x2="6" y2="14"/>
        </svg>
        Reports
      </a>
      @endif

      {{-- Notifications: always visible --}}
      <a href="{{ route('notifications.index') }}" class="nav-item {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
          <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
          <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
        </svg>
        Notifications
      </a>

      {{-- Scheduling --}}
      @if(Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('view_scheduling'))
      <a href="{{ url('/scheduling') }}" class="nav-item {{ request()->routeIs('scheduling.*') ? 'active' : '' }}">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
          <rect x="3" y="4" width="18" height="18" rx="2"/>
          <line x1="16" y1="2" x2="16" y2="6"/>
          <line x1="8" y1="2" x2="8" y2="6"/>
          <line x1="3" y1="10" x2="21" y2="10"/>
        </svg>
        Scheduling
      </a>
      @endif

      {{-- User Roles: superadmin only --}}
      @if((Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('view_roles')) && !Auth::user()->isDeveloper())
      <a href="{{ route('roles.index') }}" class="nav-item {{ request()->routeIs('roles.*') ? 'active' : '' }}">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
          <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
          <circle cx="9" cy="7" r="4"/>
          <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
          <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
        </svg>
        User Roles
      </a>
      @endif

      {{-- ✅ Task 33 & 34: Super Admin section (superadmin only) --}}
      @if(Auth::user()->isSuperAdmin())
      <a href="{{ route('superadmin.dashboard') }}" class="nav-item {{ request()->routeIs('superadmin.*') ? 'active' : '' }}">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
          <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
        </svg>
        Super Admin
      </a>
      @endif
    </nav>

    <div class="sb-bottom">
      @if(Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('create_tickets'))
      <a href="{{ route('tickets.create') }}" class="new-ticket-btn">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
          <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        New Ticket
      </a>
      @endif

      <div class="sb-util-links">
        <a href="{{ route('profile.show') }}" class="sb-util-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
          </svg>
          Profile
        </a>
        <a href="{{ route('logout') }}" class="sb-util-link"
           onclick="event.preventDefault();document.getElementById('logout-form').submit();">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
            <polyline points="16 17 21 12 16 7"/>
            <line x1="21" y1="12" x2="9" y2="12"/>
          </svg>
          Logout
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none">@csrf</form>
      </div>

      <a href="{{ route('profile.show') }}" class="sb-user" style="color:inherit">
        <div class="sb-avatar" style="{{ Auth::user()->avatar ? 'background:none;padding:0;overflow:hidden' : '' }}">
          @if(Auth::user()->avatar)
            <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar"
                 style="width:100%;height:100%;object-fit:cover;border-radius:50%">
          @else
            {{ strtoupper(substr(Auth::user()->name ?? 'AC', 0, 2)) }}
          @endif
        </div>
        <div>
          <div class="sb-user-name">{{ Auth::user()->name ?? 'Alex Chen' }}</div>
          <div class="sb-user-role">{{ Auth::user()->company?->name ?? Auth::user()->role ?? '' }}</div>
        </div>
      </a>
    </div>
  </aside>

  <!-- MAIN -->
  <div class="main">
    <header class="topbar">
      <div class="topbar-search">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
          <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input type="text" placeholder="Search tickets, agents, or knowledge…"/>
      </div>
      <div class="topbar-right">
        <span class="sys-status">
          <span class="sys-dot"></span>System Online
        </span>

        <!-- Notification Bell -->
        <div class="noti-wrap" id="notiWrap">
          <button class="icon-btn" id="notiBtn" onclick="toggleNoti(event)" title="Notifications">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
              <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
              <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
            </svg>
            <span class="noti-badge {{ ($newNotificationsCount ?? 0) > 0 ? '' : 'hidden' }}" id="notiBadge">{{ $newNotificationsCount ?? 0 }}</span>
          </button>

          <!-- Dropdown -->
          <div class="noti-dropdown" id="notiDropdown">
            <div class="noti-dd-head">
              <div class="noti-dd-title">Notifications</div>
            </div>
            @if(($topNotifications ?? collect())->count() > 0)
              <div class="noti-list">
                @foreach($topNotifications as $notification)
                  <a href="{{ $notification['url'] ?? route('notifications.index') }}" data-uid="{{ $notification['unique_id'] ?? '' }}" class="noti-item {{ !empty($notification['is_new']) ? 'unread' : '' }}">
                    <div class="noti-icon {{ $notification['type'] ?? 'blue' }}">
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M12 2v3m0 14v3M4.93 4.93l2.12 2.12m9.9 9.9l2.12 2.12M2 12h3m14 0h3M4.93 19.07l2.12-2.12m9.9-9.9l2.12-2.12"></path>
                      </svg>
                    </div>
                    <div class="noti-text">
                      <div class="noti-title">{{ $notification['title'] ?? 'Notification' }}</div>
                      <div class="noti-desc">{{ $notification['description'] ?? '' }}</div>
                      <div class="noti-time">{{ $notification['time_human'] ?? 'just now' }}</div>
                    </div>
                  </a>
                @endforeach
              </div>
            @else
              <div class="noti-empty">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                  <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                  <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                </svg>
                <p>No notifications yet</p>
              </div>
            @endif

            <div class="noti-dd-footer">
              <a href="{{ route('notifications.index') }}">View all notifications →</a>
            </div>
          </div>
        </div>

        <button id="notiTextBtn" class="icon-btn" style="font-size:11px;font-weight:700;letter-spacing:0.3px;width:auto;padding:0 12px;">
          ({{ $newNotificationsCount ?? 0 }})
        </button>
      </div>
    </header>

    <div class="page-wrap">
      @if(session('success'))
        <div class="alert alert-success">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
          {{ session('success') }}
        </div>
      @endif
      @if(session('error'))
        <div class="alert alert-danger">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
          {{ session('error') }}
        </div>
      @endif
      @yield('content')
    </div>
  </div>

</div>
<script>
function toggleNoti(e) {
    e.stopPropagation();
    document.getElementById('notiDropdown').classList.toggle('open');
}

document.addEventListener('DOMContentLoaded', () => {
    const wrap = document.getElementById('notiWrap');
    document.addEventListener('click', function(e) {
        if (wrap && !wrap.contains(e.target)) {
            document.getElementById('notiDropdown').classList.remove('open');
        }
    });

    // Handle LocalStorage Client-Side read states
    let readNotis = JSON.parse(localStorage.getItem('fixtora_read_notifications') || '[]');
    let items = document.querySelectorAll('.noti-item');
    let unreadCount = 0;

    items.forEach(item => {
        let titleBlock = item.querySelector('.noti-title') ? item.querySelector('.noti-title').innerText : '';
        let descBlock = item.querySelector('.noti-desc') ? item.querySelector('.noti-desc').innerText : '';
        let uid = item.getAttribute('data-uid');
        let key = uid ? uid : (item.getAttribute('href') + '|' + titleBlock + '|' + descBlock);
        
        if (readNotis.includes(key)) {
            item.classList.remove('unread');
        } else if (item.classList.contains('unread')) {
            unreadCount++;
        }

        item.addEventListener('click', () => {
            if (!readNotis.includes(key)) {
                readNotis.push(key);
                // Keep array size reasonable
                if(readNotis.length > 100) readNotis.shift();
                localStorage.setItem('fixtora_read_notifications', JSON.stringify(readNotis));
            }
            // Immediately hide visual cues before navigation completes
            item.classList.remove('unread');
            unreadCount = Math.max(0, unreadCount - 1);
            updateNotiVisuals(unreadCount);
        });
    });

    updateNotiVisuals(unreadCount);

    let markAllBtn = document.getElementById('markAllReadBtn');
    if (markAllBtn) {
        markAllBtn.addEventListener('click', () => {
            items.forEach(item => {
                let titleBlock = item.querySelector('.noti-title') ? item.querySelector('.noti-title').innerText : '';
                let descBlock = item.querySelector('.noti-desc') ? item.querySelector('.noti-desc').innerText : '';
                let uid = item.getAttribute('data-uid');
                let key = uid ? uid : (item.getAttribute('href') + '|' + titleBlock + '|' + descBlock);
                if (!readNotis.includes(key)) {
                    readNotis.push(key);
                }
                item.classList.remove('unread');
            });
            if(readNotis.length > 200) readNotis = readNotis.slice(-200);
            localStorage.setItem('fixtora_read_notifications', JSON.stringify(readNotis));
            
            unreadCount = 0;
            updateNotiVisuals(unreadCount);
        });
    }

    function updateNotiVisuals(count) {
        let badge = document.getElementById('notiBadge');
        let textBtn = document.getElementById('notiTextBtn');
        let ovUnread = document.getElementById('ovUnreadCount');
        let currentTitle = document.title.replace(/^\(\d+\)\s/, '');
        
        if (ovUnread) ovUnread.textContent = count;
        
        if (count > 0) {
            if(badge) {
                badge.textContent = count;
                badge.classList.remove('hidden');
            }
            if(textBtn) textBtn.textContent = '(' + count + ')';
            document.title = '(' + count + ') ' + currentTitle;
        } else {
            if(badge) {
                badge.textContent = '';
                badge.classList.add('hidden');
            }
            if(textBtn) textBtn.textContent = '(0)';
            document.title = currentTitle;
        }
    }
});
</script>
@yield('scripts')
</body>
</html>