@extends('layouts.app')

@section('title', 'Configure ' . $integration->name . ' – Fixtora')

@section('styles')
<style>
.cfg-page { max-width: 700px; margin: 0 auto; }
.cfg-breadcrumb { display:flex; align-items:center; gap:8px; color: var(--muted); margin-bottom:18px; font-size:13px; font-weight: 500; }
.cfg-breadcrumb a { color: var(--muted); text-decoration:none; }
.cfg-breadcrumb a:hover { color: var(--navy); }

.cfg-header {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 24px;
  display:flex;
  align-items:center;
  gap: 16px;
  margin-bottom: 24px;
  box-shadow: var(--shadow-sm);
}
.cfg-logo {
  width:64px; height:64px; border-radius:14px;
  display:flex; align-items:center; justify-content:center;
  color:#fff; font-size:24px; font-weight:900; background: {{ $integration->color }};
}
.cfg-title { font-size: 22px; font-weight: 800; color: var(--navy); margin-bottom: 4px; }
.cfg-subtitle { font-size: 13.5px; color: var(--muted); line-height: 1.5; }
.cfg-pill { font-size:10px; font-weight:800; text-transform:uppercase; padding:4px 10px; border-radius:999px; background:var(--green-bg); color:var(--green); border:1px solid rgba(22,163,74,.25); }

.cfg-form {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 24px;
  box-shadow: var(--shadow-sm);
  margin-bottom: 24px;
}
.cfg-group { margin-bottom: 20px; }
.cfg-group label { display:block; font-size:13px; font-weight:800; color:var(--text); margin-bottom:8px; }
.cfg-input { width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg); font-family:inherit; font-size:14px; color:var(--text); outline:none; transition: border-color 0.15s; }
.cfg-input:focus { border-color: var(--navy); }
.cfg-help { font-size: 11.5px; color: var(--muted-lt); margin-top: 6px; display:block; }

.cfg-toggle { display:inline-flex; align-items:center; cursor:pointer; gap: 10px; }
.cfg-toggle input { display:none; }
.cfg-toggle-slider { width:40px; height:24px; background:var(--border); border-radius:12px; position:relative; transition: background 0.2s; }
.cfg-toggle-slider::after { content:''; position:absolute; top:2px; left:2px; width:20px; height:20px; background:#fff; border-radius:10px; transition: transform 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
.cfg-toggle input:checked + .cfg-toggle-slider { background:var(--green); }
.cfg-toggle input:checked + .cfg-toggle-slider::after { transform: translateX(16px); }

.cfg-actions { display:flex; justify-content:flex-end; gap: 12px; margin-top: 30px; border-top: 1px solid var(--border); padding-top: 20px; }
.btn-save { padding: 10px 20px; background: var(--navy); color: #fff; font-size: 13.5px; font-weight: 800; border-radius: 8px; border: none; cursor:pointer; transition: opacity 0.15s; }
.btn-save:hover { opacity:0.9; }

.cfg-danger {
  background: var(--red-bg);
  border: 1px solid rgba(220,38,38,0.2);
  border-radius: var(--radius);
  padding: 24px;
  display:flex; justify-content:space-between; align-items:center;
}
.btn-danger { padding: 10px 16px; background: var(--red); color: #fff; font-size: 13px; font-weight: 800; border-radius: 8px; border: none; cursor:pointer; }
.btn-danger:hover { opacity:0.9; }

@media (max-width: 640px) {
  .cfg-header { flex-direction: column; align-items: flex-start; gap: 12px; }
  .cfg-danger { flex-direction: column; align-items: flex-start; gap: 16px; }
  div[style*="display:flex; gap: 40px;"] { flex-direction: column; gap: 20px !important; }
}
</style>
@endsection

@section('content')
<div class="cfg-page">

  <div class="cfg-breadcrumb">
    <a href="{{ route('integrations.index') }}">Connected Ecosystem</a>
    <span style="color:var(--muted-lt)">&rsaquo;</span>
    <span style="color:var(--navy); font-weight:800;">Configure {{ $integration->name }}</span>
  </div>

  @if(session('success'))
    <div style="background:var(--green-bg); color:var(--green); padding:16px; border-radius:8px; border:1px solid rgba(22,163,74,.2); font-size:13.5px; font-weight:600; margin-bottom:20px;">
      {{ session('success') }}
    </div>
  @endif

  <div class="cfg-header">
    <div class="cfg-logo">{{ strtoupper(substr($integration->name, 0, 1)) }}</div>
    <div style="flex:1;">
      <div style="display:flex; align-items:center; gap: 12px; margin-bottom: 4px;">
        <div class="cfg-title">{{ $integration->name }}</div>
        <div class="cfg-pill">Connected</div>
      </div>
      <div class="cfg-subtitle">{{ $integration->desc }}</div>
    </div>
  </div>

  <form method="POST" action="{{ route('integrations.configure.store', $integration->id) }}" class="cfg-form">
    @csrf

    <div class="cfg-group">
      <label>API Key / Bearer Token</label>
      <input type="password" name="api_key" class="cfg-input" value="{{ old('api_key', $credentials['api_key'] ?? '') }}" placeholder="sk_live_..." autocomplete="off">
      <span class="cfg-help">Securely stored and used to authenticate outgoing payload requests to {{ $integration->name }}.</span>
    </div>

    <div class="cfg-group">
      <label>Incoming Webhook URL</label>
      <input type="url" name="webhook_url" class="cfg-input" value="{{ old('webhook_url', $credentials['webhook_url'] ?? '') }}" placeholder="https://..." autocomplete="off">
      <span class="cfg-help">The location where we will dispatch event notifications.</span>
    </div>

    <div style="display:flex; gap: 40px; margin-top:28px;">
      <label class="cfg-toggle">
        <input type="hidden" name="sync_tickets" value="0">
        <input type="checkbox" name="sync_tickets" value="1" {{ old('sync_tickets', $credentials['sync_tickets'] ?? 0) ? 'checked' : '' }}>
        <div class="cfg-toggle-slider"></div>
        <span style="font-size:13px; font-weight:700; color:var(--text);">Sync Tickets Automatically</span>
      </label>

      <label class="cfg-toggle">
        <input type="hidden" name="send_notifications" value="0">
        <input type="checkbox" name="send_notifications" value="1" {{ old('send_notifications', $credentials['send_notifications'] ?? 0) ? 'checked' : '' }}>
        <div class="cfg-toggle-slider"></div>
        <span style="font-size:13px; font-weight:700; color:var(--text);">Broadcast Notifications</span>
      </label>
    </div>

    <div class="cfg-actions">
      <a href="{{ route('integrations.index') }}" style="padding:10px 16px; color:var(--muted); font-size:13.5px; font-weight:700; text-decoration:none; display:flex; align-items:center;">Cancel</a>
      <button type="submit" class="btn-save">Save Configuration</button>
    </div>
  </form>

  <div class="cfg-danger">
    <div>
      <div style="font-weight:800; color:var(--red); font-size:14px; margin-bottom:4px;">Danger Zone</div>
      <div style="font-size:12.5px; color:#555;">Disconnecting this tool will permanently wipe out its configured secrets.</div>
    </div>
    <form method="POST" action="{{ route('integrations.disconnect', $integration->id) }}" onsubmit="return confirm('Are you sure you want to disconnect this integration?');">
      @csrf
      @method('DELETE')
      <button type="submit" class="btn-danger">Disconnect {{ $integration->name }}</button>
    </form>
  </div>

</div>
@endsection
