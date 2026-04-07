@extends('layouts.app')
@section('title', 'Configuration – Fixtora')

@section('styles')
<style>
.page-header { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:24px; }
.page-header h1 { font-size:24px; font-weight:800; letter-spacing:-.6px; color:var(--navy); }
.page-header p  { font-size:13px; color:var(--muted); margin-top:4px; }
.breadcrumb { display:flex; align-items:center; gap:6px; font-size:12.5px; color:var(--muted); margin-bottom:16px; }
.breadcrumb a { color:var(--blue); text-decoration:none; }
.breadcrumb a:hover { text-decoration:underline; }
.config-layout { display:flex; gap:20px; align-items:flex-start; }
.config-sidebar { width:230px; flex-shrink:0; }
.config-sidebar-title { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:var(--muted); margin-bottom:8px; }
.config-nav {
  background:var(--surface);
  border:1px solid var(--border);
  border-radius:var(--radius);
  overflow:hidden;
}

/* Section group header */
.config-nav-group { }
.config-nav-group-header {
  display:flex; align-items:center; justify-content:space-between;
  padding:10px 14px;
  font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:.5px;
  color:var(--muted);
  background:#f8fafc;
  border-bottom:1px solid var(--border);
  cursor:default;
  user-select:none;
}
.config-nav-group-header svg { opacity:.5; }

.config-nav a {
  display:flex; align-items:center; gap:9px;
  padding:9px 16px;
  font-size:13px; font-weight:500;
  color:var(--text-2); text-decoration:none;
  border-left:3px solid transparent;
  transition:background .12s;
}
.config-nav a span.icon {
  width:24px; height:24px;
  border-radius:7px;
  background:var(--blue-bg); color:var(--blue);
  display:flex; align-items:center; justify-content:center;
  font-size:11px; font-weight:700; flex-shrink:0;
}
.config-nav a span.icon.icon-roles {
  background:#f0fdf4; color:#16a34a;
}
.config-nav a.active {
  background:#eff6ff;
  border-left-color:var(--blue);
  color:var(--navy);
  font-weight:600;
}
.config-nav a:hover:not(.active) { background:#f8fafc; }
.config-nav a + a { border-top:1px solid #f1f5f9; }

.config-main { flex:1; }
.config-panel {
  background:var(--surface);
  border:1px solid var(--border);
  border-radius:var(--radius);
  padding:22px 24px;
  margin-bottom:14px;
}
.config-panel:last-child { margin-bottom:0; }
.config-panel h2 {
  font-size:15px;
  font-weight:800;
  color:var(--navy);
  margin-bottom:8px;
  display:flex;
  align-items:center;
  gap:8px;
}
.config-panel p {
  font-size:13px;
  color:var(--muted);
  line-height:1.55;
  margin-bottom:14px;
}
.config-panel p:last-child { margin-bottom:0; }
.config-panel .meta {
  font-size:11px;
  font-weight:700;
  color:var(--blue);
  text-transform:uppercase;
  letter-spacing:.5px;
}
</style>
@endsection

@section('content')
<div class="breadcrumb">
  <a href="{{ route('superadmin.dashboard') }}">Super Admin</a>
  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
  Configuration
</div>

<div class="page-header">
  <div>
    <h1>Configuration</h1>
    <p>Manage companies, system names, user roles and permissions.</p>
  </div>
</div>

<div class="config-layout">
  {{-- LEFT SIDEBAR --}}
  <div class="config-sidebar">
    <div class="config-sidebar-title">Configuration modules</div>
    <div class="config-nav">

      {{-- Tenants group --}}
      <div class="config-nav-group">
        <div class="config-nav-group-header">
          <span>Tenants</span>
          <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
        <a href="{{ route('superadmin.companies.index') }}"
           class="{{ request()->routeIs('superadmin.companies.*') ? 'active' : '' }}">
          <span class="icon">Co</span>
          <div>
            <div>Companies</div>
            <div style="font-size:11px;color:var(--muted);font-weight:400;">Tenants &amp; system names</div>
          </div>
        </a>
      </div>

      {{-- Access group --}}
      <div class="config-nav-group">
        <div class="config-nav-group-header">
          <span>Access</span>
          <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
        <a href="{{ route('roles.index') }}"
           class="{{ request()->routeIs('roles.*') ? 'active' : '' }}">
          <span class="icon icon-roles">UR</span>
          <div>
            <div>User Roles</div>
            <div style="font-size:11px;color:var(--muted);font-weight:400;">Roles &amp; permissions</div>
          </div>
        </a>
      </div>

    </div>
  </div>

  {{-- RIGHT MAIN PANELS --}}
  <div class="config-main">
    <div class="config-panel">
      <h2>
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
        Companies &amp; system names
      </h2>
      <p>Manage companies and the <strong>system names</strong> that appear in ticket forms and on the task board. Each company can define its own catalog of systems (for example, <em>CRM Portal</em>, <em>Client App</em>, or <em>Payment Gateway</em>).</p>
      <p class="meta">{{ $companyCount }} {{ $companyCount === 1 ? 'company' : 'companies' }} on file · <a href="{{ route('superadmin.companies.index') }}" style="color:var(--blue);text-decoration:none;">Open company list →</a></p>
    </div>

    <div class="config-panel">
      <h2>
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        User Roles &amp; permissions
      </h2>
      <p>Define roles (e.g. <em>Admin</em>, <em>Developer</em>, <em>QC</em>) and assign granular permissions to control what each role can see and do across tickets, tasks, reports, and more.</p>
      <p class="meta"><a href="{{ route('roles.index') }}" style="color:var(--blue);text-decoration:none;">Manage roles →</a></p>
    </div>
  </div>
</div>
@endsection