<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<meta name="csrf-token" content="{{ csrf_token() }}"/>
<title>@yield('title', 'Fixtora – Architectural Concierge')</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --sidebar-w:210px;--topbar-h:56px;--bg:#f1f3f7;--surface:#fff;
  --border:#e4e7ef;--border-dark:#c8cdd8;--text:#111827;--text-sub:#374151;
  --muted:#6b7280;--muted-lt:#9ca3af;--navy:#0f1f3d;--navy2:#1a3360;
  --blue:#1d4ed8;--blue-lt:#dbeafe;--blue-bg:#eef4ff;
  --orange:#f97316;--orange-bg:#fff7ed;--green:#16a34a;--red:#dc2626;
  --radius:10px;--radius-sm:7px;
  --shadow:0 1px 3px rgba(0,0,0,.06),0 4px 12px rgba(0,0,0,.04);
  --shadow-md:0 4px 20px rgba(0,0,0,.08);
}
html,body{height:100%;font-family:'Montserrat',sans-serif;font-size:14px;color:var(--text);background:var(--bg);}
.shell{display:flex;height:100vh;overflow:hidden}
/* SIDEBAR */
.sidebar{width:var(--sidebar-w);min-width:var(--sidebar-w);background:var(--navy);display:flex;flex-direction:column;height:100vh;overflow-y:auto;overflow-x:hidden;z-index:200;flex-shrink:0;}
.sb-brand{padding:20px 18px 18px;border-bottom:1px solid rgba(255,255,255,.08);display:flex;align-items:center;gap:10px;}
.sb-logo{width:34px;height:34px;border-radius:9px;background:linear-gradient(135deg,#2563eb,#7c3aed);display:flex;align-items:center;justify-content:center;color:#fff;flex-shrink:0;}
.sb-brand-text .brand-name{font-size:14px;font-weight:800;color:#fff;letter-spacing:-.3px}
.sb-brand-text .brand-sub{font-size:9px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:rgba(255,255,255,.4);margin-top:1px}
.sb-nav{flex:1;padding:8px 10px}
.nav-item{display:flex;align-items:center;gap:10px;padding:9px 10px;border-radius:8px;font-size:13px;font-weight:500;color:rgba(255,255,255,.55);text-decoration:none;cursor:pointer;transition:all .15s;margin-bottom:2px;position:relative;border:none;background:transparent;width:100%;text-align:left;font-family:inherit;}
.nav-item svg{flex-shrink:0;opacity:.7}
.nav-item:hover{color:rgba(255,255,255,.9);background:rgba(255,255,255,.07)}
.nav-item.active{color:#fff;background:rgba(255,255,255,.12);font-weight:600}
.nav-item.active svg{opacity:1}
.nav-item.active::before{content:'';position:absolute;left:0;top:6px;bottom:6px;width:3px;border-radius:0 3px 3px 0;background:#60a5fa;}
.nav-badge{margin-left:auto;font-size:10px;font-weight:700;background:rgba(239,68,68,.85);color:#fff;padding:1px 6px;border-radius:20px;}
.sb-bottom{padding:10px;border-top:1px solid rgba(255,255,255,.08);}
.new-ticket-btn{width:100%;padding:10px;background:linear-gradient(135deg,#2563eb,#1d4ed8);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;display:flex;align-items:center;justify-content:center;gap:6px;margin-bottom:8px;text-decoration:none;transition:all .15s;}
.new-ticket-btn:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(37,99,235,.5);color:#fff}
.sb-util-links{margin-bottom:8px}
.sb-util-link{display:flex;align-items:center;gap:8px;padding:7px 10px;border-radius:7px;font-size:12px;font-weight:500;color:rgba(255,255,255,.4);text-decoration:none;transition:all .15s;border:none;background:transparent;cursor:pointer;width:100%;text-align:left;font-family:inherit;}
.sb-util-link:hover{color:rgba(255,255,255,.75);background:rgba(255,255,255,.06)}
.sb-user{display:flex;align-items:center;gap:10px;padding:10px;border-radius:8px;cursor:pointer;margin-top:2px;text-decoration:none;}
.sb-user:hover{background:rgba(255,255,255,.07)}
.sb-avatar{width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#2563eb,#7c3aed);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;color:#fff;flex-shrink:0;}
.sb-user-name{font-size:12.5px;font-weight:700;color:rgba(255,255,255,.85)}
.sb-user-role{font-size:10.5px;color:rgba(255,255,255,.35);margin-top:1px}
/* MAIN */
.main{flex:1;display:flex;flex-direction:column;overflow:hidden}
.topbar{height:var(--topbar-h);min-height:var(--topbar-h);background:var(--surface);border-bottom:1px solid var(--border);display:flex;align-items:center;gap:12px;padding:0 22px;z-index:100;}
.topbar-search{flex:1;max-width:340px;position:relative;}
.topbar-search svg{position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--muted-lt);}
.topbar-search input{width:100%;padding:8px 12px 8px 34px;border:1px solid var(--border);border-radius:8px;font-size:13px;font-family:inherit;background:var(--bg);color:var(--text);outline:none;}
.topbar-search input:focus{border-color:#93c5fd;background:#fff}
.topbar-search input::placeholder{color:var(--muted-lt)}
.topbar-right{margin-left:auto;display:flex;align-items:center;gap:8px}
.sys-status{display:flex;align-items:center;gap:6px;font-size:10.5px;font-weight:700;letter-spacing:.6px;color:var(--green);text-transform:uppercase;margin-right:6px;}
.sys-dot{width:6px;height:6px;border-radius:50%;background:var(--green);animation:pulse 2s infinite}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}
.icon-btn{width:34px;height:34px;border:none;background:none;color:var(--muted);cursor:pointer;border-radius:7px;display:flex;align-items:center;justify-content:center;transition:all .15s;text-decoration:none;}
.icon-btn:hover{background:var(--bg);color:var(--text)}
.page-wrap{flex:1;overflow-y:auto;padding:28px 28px 60px}
/* Alerts */
.alert{padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:13px;font-weight:600;}
.alert-success{background:#dcfce7;color:#15803d;border:1px solid #bbf7d0}
.alert-danger{background:#fee2e2;color:#dc2626;border:1px solid #fecaca}
</style>
@yield('styles')
</head>
<body>
<div class="shell">
  <aside class="sidebar">
    <div class="sb-brand">
      <div class="sb-logo">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
      </div>
      <div class="sb-brand-text">
        <div class="brand-name">Fixtora</div>
        <div class="brand-sub">Architectural Concierge</div>
      </div>
    </div>
    <nav class="sb-nav">
      <a href="{{ route('home') }}" class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
        Dashboard
      </a>
      <a href="{{ route('tickets.index') }}" class="nav-item {{ request()->routeIs('tickets.*') ? 'active' : '' }}">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        Tickets
        @php $openCount = \App\Models\Ticket::where('status','open')->count(); @endphp
        @if($openCount > 0)<span class="nav-badge">{{ $openCount }}</span>@endif
      </a>
      <a href="{{ route('tasks.index') }}" class="nav-item {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
        Tasks
      </a>
      <a href="{{ route('sla-monitor.index') }}" class="nav-item {{ request()->routeIs('sla-monitor.*') ? 'active' : '' }}">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        SLA Monitor
      </a>
      <a href="{{ route('reports.index') }}" class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
        Reports
      </a>
      <a href="{{ route('notifications.index') }}" class="nav-item {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
        Notifications
      </a>
      <a href="#" class="nav-item">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        Scheduling
      </a>
    </nav>
    <div class="sb-bottom">
      <a href="{{ route('tickets.create') }}" class="new-ticket-btn">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        New Ticket
      </a>
      <div class="sb-util-links">
        <a href="#" class="sb-util-link">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
          Integrations
        </a>
        <a href="{{ route('profile.show') }}" class="sb-util-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          Profile
        </a>
        <a href="{{ route('logout') }}" class="sb-util-link" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
          Logout
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none">@csrf</form>
      </div>
      <a href="{{ route('profile.show') }}" class="sb-user" style="color:inherit">
        <div class="sb-avatar">{{ strtoupper(substr(Auth::user()->name ?? 'AC', 0, 2)) }}</div>
        <div>
          <div class="sb-user-name">{{ Auth::user()->name ?? 'Alex Chen' }}</div>
          <div class="sb-user-role">Senior Architect</div>
        </div>
      </a>
    </div>
  </aside>

  <div class="main">
    <header class="topbar">
      <div class="topbar-search">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" placeholder="Search tickets, agents, or knowledge…"/>
      </div>
      <div class="topbar-right">
        <span class="sys-status"><span class="sys-dot"></span>System Online</span>
        <a href="{{ route('notifications.index') }}" class="icon-btn">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
        </a>
        <button class="icon-btn">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
        </button>
      </div>
    </header>
    <div class="page-wrap">
      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif
      @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
      @endif
      @yield('content')
    </div>
  </div>
</div>
</body>
</html>