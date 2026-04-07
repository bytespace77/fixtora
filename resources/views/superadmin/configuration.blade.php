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
.config-sidebar { width:220px; flex-shrink:0; }
.config-sidebar-title { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:var(--muted); margin-bottom:8px; }
.config-nav { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); padding:10px 0; }
.config-nav a {
  display:flex; align-items:center; gap:8px;
  padding:8px 14px;
  font-size:13px; font-weight:500;
  color:var(--text-2); text-decoration:none;
  border-left:3px solid transparent;
}
.config-nav a span.icon {
  width:22px; height:22px;
  border-radius:7px;
  background:var(--blue-bg); color:var(--blue);
  display:flex; align-items:center; justify-content:center;
  font-size:12px; font-weight:700;
}
.config-nav a.active {
  background:#eff6ff;
  border-left-color:var(--blue);
  color:var(--navy);
}
.config-nav a:hover { background:#f8fafc; }
.config-main { flex:1; }
.config-panel {
  background:var(--surface);
  border:1px solid var(--border);
  border-radius:var(--radius);
  padding:22px 24px;
}
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
    <p>Set up companies and the product <strong>system names</strong> used on tickets and assignments.</p>
  </div>
</div>
<div class="config-layout">
  <div class="config-sidebar">
    <div class="config-sidebar-title">Configuration modules</div>
    <div class="config-nav">
      <a href="{{ route('superadmin.companies.index') }}" class="active">
        <span class="icon">Co</span>
        <div>
          <div style="font-weight:600;">Companies</div>
          <div style="font-size:11px;color:var(--muted);">Tenants &amp; ticket system names</div>
        </div>
      </a>
      {{-- Future modules can be added here (e.g. SLA presets, notification templates, etc.) --}}
    </div>
  </div>

  <div class="config-main">
    <div class="config-panel">
      <h2>Companies &amp; system names</h2>
      <p>Manage companies and the <strong>system names</strong> that appear in ticket forms and on the task board. Each company can define its own catalog of systems (for example, <em>CRM Portal</em>, <em>Client App</em>, or <em>Payment Gateway</em>).</p>
      <p class="meta">{{ $companyCount }} {{ $companyCount === 1 ? 'company' : 'companies' }} on file · <a href="{{ route('superadmin.companies.index') }}" style="color:var(--blue);text-decoration:none;">Open company list →</a></p>
    </div>
  </div>
</div>
@endsection
