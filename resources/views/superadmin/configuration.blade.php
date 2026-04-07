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
.grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(300px, 1fr)); gap:18px; max-width:900px; }
.hub-card { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); padding:22px 24px; text-decoration:none; color:inherit; display:block; transition:box-shadow .15s, border-color .15s; }
.hub-card:hover { box-shadow:var(--shadow-md); border-color:var(--blue-lt); }
.hub-card h2 { font-size:15px; font-weight:800; color:var(--navy); margin-bottom:8px; display:flex; align-items:center; gap:8px; }
.hub-card p { font-size:13px; color:var(--muted); line-height:1.55; margin-bottom:14px; }
.hub-card .hub-meta { font-size:11px; font-weight:700; color:var(--blue); text-transform:uppercase; letter-spacing:.5px; }
.hub-card svg.icon { color:var(--blue); flex-shrink:0; }
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

<div class="grid">
  <a href="{{ route('superadmin.companies.index') }}" class="hub-card">
    <h2>
      <svg class="icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 21h18"/><path d="M9 8h1"/><path d="M14 8h1"/><path d="M7 16h10"/><path d="M6 10h12a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1v-7a1 1 0 0 1 1-1z"/><path d="M8 10V6a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v4"/></svg>
      Companies &amp; system names
    </h2>
    <p>Open the company list. When you create or edit a company, add each <strong>system name</strong> (e.g. CRM Portal, Payment GW). Those names appear in ticket forms and on the task board.</p>
    <span class="hub-meta">{{ $companyCount }} {{ $companyCount === 1 ? 'company' : 'companies' }} on file →</span>
  </a>
</div>
@endsection
