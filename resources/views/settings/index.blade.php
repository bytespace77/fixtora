@extends('layouts.app')

@section('title', 'Integrations – Fixtora')

@section('styles')
<style>
.int-page { max-width: 1240px; }
.int-breadcrumb {
  display:flex; align-items:center; gap:8px;
  color: var(--muted);
  margin-bottom:18px;
  font-size:13px;
}
.int-breadcrumb a { color: var(--muted); text-decoration:none; }
.int-breadcrumb a:hover { color: var(--navy); }
.int-sep { color: rgba(100,116,139,1); }
.int-current { color: var(--navy); font-weight:800; }

.int-hero { margin-bottom: 18px; }
.int-hero h1 { font-size:28px; font-weight:800; letter-spacing:-.5px; color: var(--navy); margin-bottom:6px; }
.int-hero p { font-size:13px; color: var(--muted); line-height:1.6; margin:0; max-width: 720px; }

.int-section-label {
  display:flex; align-items:center; gap:12px;
  font-size:11px; font-weight:800; letter-spacing:.9px;
  text-transform:uppercase; color: var(--muted-lt);
  margin: 16px 0 12px;
}
.int-section-label::before{
  content:'';
  width:28px; height:2px;
  background: var(--navy-3);
  border-radius: 2px;
}

.int-conn-grid { display:grid; grid-template-columns:repeat(2, minmax(0,1fr)); gap:16px; }
.int-conn-card {
  background: var(--surface);
  border:1px solid var(--border);
  border-radius: var(--radius);
  box-shadow: var(--shadow);
  padding:18px;
  transition: transform .15s, box-shadow .15s;
}
.int-conn-card:hover { transform: translateY(-1px); box-shadow: var(--shadow-md); }
.int-conn-top { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; }
.int-conn-left { display:flex; gap:12px; align-items:flex-start; min-width:0; }
.int-conn-logo {
  width:42px; height:42px; border-radius:12px;
  background: var(--navy-2);
  display:flex; align-items:center; justify-content:center;
  color:#fff; flex-shrink:0;
  font-weight:900;
}
.int-conn-name { font-size:15px; font-weight:800; color: var(--text); margin-bottom:4px; }
.int-conn-desc { font-size:12.5px; color: var(--muted); line-height:1.5; }
.int-pill {
  font-size:10px;
  font-weight:800;
  letter-spacing:.8px;
  text-transform:uppercase;
  padding:4px 10px;
  border-radius:999px;
  border:1px solid rgba(22,163,74,.25);
  background: var(--green-bg);
  color: var(--green);
  white-space:nowrap;
}

.int-catalog-head {
  display:flex; gap:12px; justify-content:space-between; align-items:flex-start; flex-wrap:wrap;
  margin-bottom:14px;
}
.int-tabs { display:flex; gap:6px; flex-wrap:wrap; }
.int-tab {
  padding:8px 14px;
  border-radius:20px;
  border:1px solid var(--border);
  background: var(--surface);
  color: var(--muted);
  font-size:12.5px;
  font-weight:700;
  cursor:pointer;
  text-decoration:none;
}
.int-tab:hover { background: var(--bg); color: var(--navy); }
.int-tab.active { background: var(--navy); color:#fff; border-color: var(--navy); }

.int-search {
  display:flex; align-items:center; gap:10px;
  background: var(--surface);
  border:1px solid var(--border);
  border-radius: 10px;
  padding: 0 12px;
  height: 40px;
  min-width: 260px;
}
.int-search input {
  border:none; outline:none; background: transparent;
  width: 100%;
  font-size:13px;
  color: var(--text);
  font-family: inherit;
}
.int-search input::placeholder { color: var(--muted-lt); }

.int-catalog-grid {
  display:grid;
  grid-template-columns:repeat(3, minmax(0,1fr));
  gap:16px;
  margin-bottom: 10px;
}
.int-card {
  background: var(--surface);
  border:1px solid var(--border);
  border-radius: var(--radius);
  box-shadow: var(--shadow);
  padding:16px;
  transition: transform .15s, box-shadow .15s;
  display:flex;
  flex-direction:column;
  min-height: 210px;
}
.int-card:hover { transform: translateY(-1px); box-shadow: var(--shadow-md); }
.int-tool-logo {
  width:56px; height:56px; border-radius:14px;
  background: var(--navy-2);
  display:flex; align-items:center; justify-content:center;
  color:#fff;
  font-weight:900;
  margin-bottom:14px;
}
.int-tool-name { font-size:16px; font-weight:800; color: var(--navy); margin-bottom:6px; }
.int-tool-desc { font-size:12.5px; color: var(--muted); line-height:1.55; flex:1; }
.int-card .int-connect {
  width: 100%;
  margin-top: 14px;
  height: 38px;
  border-radius: 10px;
  border:1px solid var(--border);
  background: var(--navy);
  color:#fff;
  font-size:13px;
  font-weight:800;
  cursor:pointer;
}
.int-card .int-connect:hover { background: #162234; }
.int-card .int-connect:disabled { background: rgba(37,99,235,.25); cursor:not-allowed; }

.int-custom {
  background: var(--bg);
  border:1px dashed var(--border);
  align-items:center;
  text-align:center;
}
.int-custom .int-tool-logo { background: var(--blue-bg); color: var(--blue-2); border:1px solid #bfdbfe; margin-bottom:12px; }
.int-custom .int-tool-name { margin-bottom:8px; }
.int-custom .int-tool-desc { max-width: 320px; }

@media (max-width: 1100px) {
  .int-conn-grid { grid-template-columns: 1fr; }
  .int-catalog-grid { grid-template-columns:repeat(2, minmax(0,1fr)); }
}
@media (max-width: 640px) {
  .int-catalog-grid { grid-template-columns:1fr; }
  .int-search { min-width: 100%; }
}

.int-empty-msg {
  grid-column: span 3;
  text-align:center;
  color: var(--muted);
  font-size:13px;
  font-weight:600;
  padding:18px 0;
}
</style>
@endsection

@section('content')
@php
  $activeTab = request('tab', 'all');
  $filter = (string) request('filter', '');

  $tabs = [
    'all' => 'All Tools',
    'communication' => 'Communication',
    'developer' => 'Developer Tools',
    'analytics' => 'Analytics',
  ];

  // No demo connections/tools yet. When backend is ready, these will come from DB.
  $active_connections = [];
  $connectedNames = [];
  $catalog = [];

  $filtered = collect($catalog)->filter(function ($t) use ($activeTab, $filter) {
    $tabOk = $activeTab === 'all' || $t['category'] === $activeTab;
    $nameOk = $filter === '' || stripos($t['name'], $filter) !== false;
    return $tabOk && $nameOk;
  })->values();
@endphp

<div class="int-page">

  <div class="int-hero">
    <h1>Connected Ecosystem</h1>
    <p>Centralize your operations by bridging the gap between your favorite tools and our orchestration engine.</p>
  </div>

  <div class="int-section-label">Active Connections</div>
  <div class="int-conn-grid">
    @if(empty($active_connections))
      <div class="int-empty-msg" style="grid-column: span 2;">No active connections yet.</div>
    @else
      @foreach($active_connections as $c)
        <div class="int-conn-card">
          <div class="int-conn-top">
            <div class="int-conn-left">
              <div class="int-conn-logo" style="background: {{ $c['color'] }}">{{ strtoupper(substr($c['name'], 0, 1)) }}</div>
              <div>
                <div class="int-conn-name">{{ $c['name'] }}</div>
                <div class="int-conn-desc">{{ $c['desc'] }}</div>
              </div>
            </div>
            <span class="int-pill">Connected</span>
          </div>
        </div>
      @endforeach
    @endif
  </div>

  <div class="int-section-label" style="margin-top:22px">Integration Catalog</div>

  <div class="int-catalog-head">
    <div class="int-tabs">
      @foreach($tabs as $key => $label)
        <a
          href="{{ route('settings.index', ['tab' => $key, 'filter' => $filter]) }}"
          class="int-tab {{ $activeTab === $key ? 'active' : '' }}"
        >
          {{ $label }}
        </a>
      @endforeach
    </div>

    <form class="int-search" method="GET" action="{{ route('settings.index') }}">
      <svg width="14" height="14" fill="none" stroke="#a0aab4" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
        <circle cx="11" cy="11" r="8"></circle>
        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
      </svg>
      <input
        type="text"
        name="filter"
        value="{{ $filter }}"
        placeholder="Filter by name..."
        oninput="this.form.submit()"
      />
      <input type="hidden" name="tab" value="{{ $activeTab }}">
    </form>
  </div>

  <div class="int-catalog-grid">
    @if($filtered->isEmpty())
      <div class="int-empty-msg">No integration tools available yet.</div>

      <div class="int-card int-custom" style="grid-column: span 3;">
        <div class="int-tool-logo">+</div>
        <div class="int-tool-name">Request Custom Integration</div>
        <div class="int-tool-desc">Don’t see your internal tools? Submit a request and our engineering concierge team will review it.</div>

        <a
          class="int-connect"
          href="{{ route('integrations.custom-request.create') }}"
          style="display:block; text-align:center; text-decoration:none; line-height:38px;"
        >
          Create Request →
        </a>
      </div>
    @else
      @foreach($filtered as $tool)
        @php
          $isConnected = in_array($tool['name'], $connectedNames, true);
        @endphp
        <div class="int-card">
          <div class="int-tool-logo" style="background: {{ $tool['color'] }}">{{ strtoupper(substr($tool['name'], 0, 1)) }}</div>
          <div class="int-tool-name">{{ $tool['name'] }}</div>
          <div class="int-tool-desc">{{ $tool['desc'] }}</div>
          <button class="int-connect" type="button" {{ $isConnected ? 'disabled' : '' }}>
            {{ $isConnected ? 'Connected' : 'Connect Tool' }}
          </button>
        </div>
      @endforeach
    @endif
  </div>
</div>
@endsection