@extends('layouts.app')
@section('title', 'Profile – Fixtora')

@section('styles')
<style>
.page-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px}
.page-header h1{font-size:22px;font-weight:800;letter-spacing:-.5px;color:var(--navy)}
.page-header p{font-size:13px;color:var(--muted);margin-top:4px}

/* TABS */
.prof-tabs{display:flex;gap:2px;background:var(--bg);border:1px solid var(--border);border-radius:8px;padding:3px;width:fit-content;margin-bottom:20px}
.pt-btn{padding:7px 16px;border-radius:6px;border:none;background:transparent;font-size:12.5px;font-weight:600;color:var(--muted);cursor:pointer;font-family:inherit;transition:all .15s}
.pt-btn.active{background:var(--surface);color:var(--text);box-shadow:var(--shadow)}
.tab-panel{display:none}.tab-panel.active{display:block}

/* GRID */
.profile-grid{display:grid;grid-template-columns:260px 1fr;gap:18px;align-items:start}

/* CARD */
.card-box{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:20px;box-shadow:var(--shadow);margin-bottom:14px}
.card-box:last-child{margin-bottom:0}
.card-title{font-size:15px;font-weight:700;color:var(--navy);margin-bottom:4px}
.card-sub{font-size:12px;color:var(--muted);margin-bottom:16px}

/* AVATAR */
.avatar-circle{width:80px;height:80px;border-radius:16px;background:linear-gradient(135deg,#7c3aed,#2563eb);display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:800;color:#fff;margin:0 auto 14px;position:relative}
.avatar-cam{position:absolute;bottom:-5px;right:-5px;width:24px;height:24px;border-radius:50%;background:#fff;border:2px solid var(--border);display:flex;align-items:center;justify-content:center;cursor:pointer}
.prof-name{font-size:15px;font-weight:800;letter-spacing:-.3px;text-align:center}
.prof-role{font-size:10px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:var(--muted);margin-top:2px;text-align:center}
.prof-divider{height:1px;background:var(--border);margin:14px 0}
.prof-stat-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:8px}
.prof-stat-row:last-child{margin-bottom:0}
.prof-stat-key{font-size:10.5px;font-weight:700;color:var(--muted-lt);text-transform:uppercase;letter-spacing:.5px}
.prof-stat-val{font-size:12px;font-weight:600;color:var(--text)}

/* STORAGE */
.storage-bar-bg{height:6px;background:rgba(29,78,216,.15);border-radius:20px;overflow:hidden;margin-bottom:8px}
.storage-bar-fill{height:100%;background:var(--blue);border-radius:20px}

/* FORM */
.pf-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px}
.pf-group{display:flex;flex-direction:column;gap:6px}
.pf-group.full{grid-column:1/-1}
.lbl{font-size:10px;font-weight:700;letter-spacing:.6px;text-transform:uppercase;color:var(--muted)}
.pf-input{width:100%;padding:10px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;font-family:inherit;outline:none;background:var(--bg);color:var(--text);transition:border-color .12s}
.pf-input:focus{border-color:var(--blue);background:#fff;box-shadow:0 0 0 3px rgba(29,78,216,.08)}
.pf-input:disabled{opacity:.6;cursor:not-allowed}
.pf-input::placeholder{color:var(--muted-lt)}
.card-footer{border-top:1px solid var(--border);padding-top:14px;margin-top:6px;display:flex;gap:8px}
.btn-save{padding:9px 18px;background:var(--blue);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;transition:all .15s}
.btn-save:hover{background:#1a42c4}
.btn-ghost{padding:9px 16px;background:transparent;color:var(--muted);border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;transition:all .15s}
.btn-ghost:hover{background:var(--bg);color:var(--text)}

/* ACTIVITY */
.act-item{display:flex;align-items:flex-start;gap:12px;padding:13px 0;border-bottom:1px solid var(--border)}
.act-item:last-child{border-bottom:none}
.act-dot{width:8px;height:8px;border-radius:50%;margin-top:5px;flex-shrink:0}
.dot-blue{background:var(--blue)}.dot-green{background:var(--green)}.dot-orange{background:var(--orange)}.dot-red{background:var(--red)}
.act-text{font-size:13px;font-weight:500;color:var(--text);line-height:1.4;flex:1}
.act-time{font-size:11px;color:var(--muted-lt);white-space:nowrap;margin-top:2px}

/* SECURITY */
.sec-item{display:flex;align-items:center;justify-content:space-between;padding:14px 0;border-bottom:1px solid var(--border)}
.sec-item:last-child{border-bottom:none}
.sec-icon{width:38px;height:38px;background:linear-gradient(135deg,#7c3aed,#2563eb);border-radius:10px;display:flex;align-items:center;justify-content:center;color:#fff;flex-shrink:0}
.sec-name{font-size:13px;font-weight:700}
.sec-desc{font-size:11px;color:var(--muted);margin-top:2px}
.toggle-wrap{position:relative;display:inline-block;width:42px;height:24px}
.toggle-wrap input{opacity:0;width:0;height:0}
.toggle-slider{position:absolute;cursor:pointer;top:0;left:0;right:0;bottom:0;background:var(--border-dark);border-radius:24px;transition:.3s}
.toggle-slider::before{position:absolute;content:'';height:18px;width:18px;left:3px;bottom:3px;background:#fff;border-radius:50%;transition:.3s}
.toggle-wrap input:checked + .toggle-slider{background:var(--blue)}
.toggle-wrap input:checked + .toggle-slider::before{transform:translateX(18px)}

.session-row{display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border)}
.session-row:last-child{border-bottom:none}
.session-icon{width:36px;height:36px;background:var(--bg);border:1px solid var(--border);border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0}
.session-name{font-size:12.5px;font-weight:700}
.session-sub{font-size:11px;color:var(--muted);margin-top:1px}
.badge-current{font-size:10px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px}
.badge-online{font-size:10px;font-weight:700;padding:2px 8px;border-radius:20px;background:#dcfce7;color:#15803d}
.btn-revoke{font-size:10px;font-weight:700;color:var(--red);background:transparent;border:none;cursor:pointer;padding:4px 8px;border-radius:6px}
.btn-revoke:hover{background:#fee2e2}

/* NOTIFICATIONS */
.notif-table{width:100%;border-collapse:collapse}
.notif-table th{text-align:left;font-size:10px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;color:var(--muted);padding-bottom:10px;border-bottom:1px solid var(--border)}
.notif-table th:not(:first-child){text-align:center;width:65px}
.notif-table td{padding:12px 0;border-bottom:1px solid var(--bg);vertical-align:middle}
.notif-table tr:last-child td{border-bottom:none}
.notif-name{font-size:12.5px;font-weight:700;color:var(--text)}
.notif-desc{font-size:11px;color:var(--muted);margin-top:2px}
.notif-chk{display:block;margin:0 auto;width:15px;height:15px;accent-color:var(--blue);cursor:pointer}
</style>
@endsection

@section('styles')
<style>
.page-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 28px;
}
.page-header h1 {
    font-size: 22px;
    font-weight: 800;
    color: var(--text);
    letter-spacing: -0.5px;
    margin-bottom: 3px;
}
.page-header .subtitle {
    font-size: 13px;
    font-weight: 400;
    color: var(--muted);
}

/* profile layout */
.profile-grid {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 20px;
    align-items: start;
}

/* card base */
.pcard {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    overflow: hidden;
}

/* avatar card */
.avatar-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 32px 24px;
    text-align: center;
}

.avatar-wrap {
    position: relative;
    margin-bottom: 16px;
}

.avatar-circle {
    width: 88px;
    height: 88px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--blue), #7c3aed);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    font-weight: 800;
    color: #fff;
    box-shadow: 0 4px 20px rgba(37,99,235,0.3);
}

.avatar-online {
    position: absolute;
    bottom: 4px; right: 4px;
    width: 14px; height: 14px;
    border-radius: 50%;
    background: var(--green);
    border: 2px solid var(--surface);
}

.avatar-name {
    font-size: 17px;
    font-weight: 800;
    color: var(--text);
    letter-spacing: -0.3px;
    margin-bottom: 3px;
}

.avatar-role {
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    color: var(--muted);
    margin-bottom: 16px;
}

.avatar-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: var(--blue-bg);
    border: 1px solid rgba(37,99,235,0.15);
    color: var(--blue);
    font-size: 11px;
    font-weight: 700;
    padding: 4px 12px;
    border-radius: 100px;
    margin-bottom: 24px;
}

.avatar-divider {
    width: 100%;
    height: 1px;
    background: var(--border);
    margin-bottom: 20px;
}

.avatar-stat {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.a-stat-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.a-stat-label {
    font-size: 12px;
    font-weight: 500;
    color: var(--muted);
}

.a-stat-val {
    font-size: 12px;
    font-weight: 700;
    color: var(--text);
}

.a-stat-val.green { color: var(--green); }
.a-stat-val.blue  { color: var(--blue); }

/* info card */
.pcard-header {
    padding: 18px 24px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.pcard-title {
    font-size: 14px;
    font-weight: 700;
    color: var(--text);
    letter-spacing: -0.2px;
}

.pcard-title-sub {
    font-size: 12px;
    font-weight: 400;
    color: var(--muted);
    margin-top: 2px;
}

.pcard-body { padding: 24px; }

/* form fields */
.pf-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px;
    margin-bottom: 18px;
}

.pf-group { display: flex; flex-direction: column; gap: 6px; }
.pf-group.full { grid-column: 1 / -1; }

.pf-label {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.6px;
    text-transform: uppercase;
    color: var(--text-2);
}

.pf-value {
    height: 44px;
    background: var(--bg);
    border: 1.5px solid var(--border);
    border-radius: 8px;
    padding: 0 14px;
    font-family: 'Montserrat', sans-serif;
    font-size: 13px;
    font-weight: 500;
    color: var(--text);
    display: flex;
    align-items: center;
}

.pf-input {
    height: 44px;
    background: var(--bg);
    border: 1.5px solid var(--border);
    border-radius: 8px;
    padding: 0 14px;
    font-family: 'Montserrat', sans-serif;
    font-size: 13px;
    font-weight: 500;
    color: var(--text);
    outline: none;
    transition: all 0.15s;
    width: 100%;
}
.pf-input:focus {
    background: #fff;
    border-color: var(--blue);
    box-shadow: 0 0 0 3px rgba(37,99,235,0.08);
}
.pf-input::placeholder { color: var(--muted-lt); font-weight: 400; }

/* readonly tag */
.pf-readonly {
    font-size: 10px; font-weight: 700;
    letter-spacing: 1px; text-transform: uppercase;
    color: var(--muted); background: var(--border);
    padding: 2px 8px; border-radius: 4px;
}

/* activity list */
.activity-list { display: flex; flex-direction: column; gap: 0; }

.activity-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 14px 0;
    border-bottom: 1px solid var(--border);
}
.activity-item:last-child { border-bottom: none; }

.activity-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    margin-top: 5px;
    flex-shrink: 0;
}
.dot-blue   { background: var(--blue); }
.dot-green  { background: var(--green); }
.dot-orange { background: var(--orange); }
.dot-red    { background: var(--red); }

.activity-text {
    font-size: 13px;
    font-weight: 500;
    color: var(--text);
    line-height: 1.4;
    flex: 1;
}
.activity-text span { color: var(--blue); font-weight: 600; }

.activity-time {
    font-size: 11px;
    font-weight: 500;
    color: var(--muted-lt);
    white-space: nowrap;
    margin-top: 1px;
}

/* btn */
.btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 9px 18px;
    background: var(--blue);
    color: #fff;
    border: none;
    border-radius: 8px;
    font-family: 'Montserrat', sans-serif;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.15s;
    text-decoration: none;
    box-shadow: 0 2px 8px rgba(37,99,235,0.3);
}
.btn-primary:hover { background: var(--blue-2); transform: translateY(-1px); box-shadow: 0 4px 14px rgba(37,99,235,0.4); color: #fff; }
.btn-primary:active { transform: translateY(0); }

.btn-ghost {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 9px 18px;
    background: transparent;
    color: var(--muted);
    border: 1.5px solid var(--border);
    border-radius: 8px;
    font-family: 'Montserrat', sans-serif;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s;
}
.btn-ghost:hover { background: var(--bg); color: var(--text); border-color: var(--border-2); }

.pcard-footer {
    padding: 16px 24px;
    border-top: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: 10px;
    background: var(--bg);
}

/* security items */
.sec-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 0;
    border-bottom: 1px solid var(--border);
}
.sec-item:last-child { border-bottom: none; }

.sec-left { display: flex; align-items: center; gap: 12px; }

.sec-icon {
    width: 36px; height: 36px;
    border-radius: 9px;
    background: var(--blue-bg);
    display: flex; align-items: center; justify-content: center;
    color: var(--blue); flex-shrink: 0;
}

.sec-name { font-size: 13px; font-weight: 700; color: var(--text); }
.sec-desc { font-size: 12px; font-weight: 400; color: var(--muted); margin-top: 2px; }

.sec-status {
    font-size: 11px; font-weight: 700;
    padding: 4px 10px; border-radius: 100px;
}
.sec-status.enabled  { background: var(--green-bg); color: var(--green); }
.sec-status.disabled { background: var(--bg); color: var(--muted); border: 1px solid var(--border); }
</style>
@endsection

@section('content')
<<<<<<< HEAD
@php
  $user = Auth::user();
  $ticketCount = \App\Models\Ticket::where('user_id', $user->id)->count();
  $resolvedCount = \App\Models\Ticket::where('user_id', $user->id)->where('status','resolved')->count();
@endphp

<div class="page-header">
  <div>
    <h1>User Profile</h1>
    <p>Manage your account, security, and notification preferences.</p>
  </div>
  <button class="btn-save" onclick="document.getElementById('saveForm').submit()">Save Changes</button>
</div>

<!-- TABS -->
<div class="prof-tabs">
  <button class="pt-btn active" onclick="switchTab('general',this)">General</button>
  <button class="pt-btn" onclick="switchTab('security',this)">Security</button>
  <button class="pt-btn" onclick="switchTab('notifications',this)">Notifications</button>
</div>

<div class="profile-grid">

  <!-- LEFT COLUMN -->
  <div>
    <!-- Avatar Card -->
    <div class="card-box" style="text-align:center">
      <div class="avatar-circle">
        {{ strtoupper(substr($user->name, 0, 2)) }}
        <div class="avatar-cam">
          <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="var(--blue)" stroke-width="2.5"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
        </div>
      </div>
      <div class="prof-name">{{ $user->name }}</div>
      <div class="prof-role">Senior Architect</div>
      <div class="prof-divider"></div>
      <div class="prof-stat-row">
        <span class="prof-stat-key">Status</span>
        <span style="font-size:10.5px;font-weight:700;background:#dcfce7;color:#15803d;padding:2px 8px;border-radius:20px">Verified</span>
      </div>
      <div class="prof-stat-row">
        <span class="prof-stat-key">Member Since</span>
        <span class="prof-stat-val">{{ $user->created_at->format('M Y') }}</span>
      </div>
      <div class="prof-stat-row">
        <span class="prof-stat-key">Tickets</span>
        <span class="prof-stat-val" style="color:var(--blue)">{{ $ticketCount }}</span>
      </div>
      <div class="prof-stat-row">
        <span class="prof-stat-key">Resolved</span>
        <span class="prof-stat-val" style="color:var(--green)">{{ $resolvedCount }}</span>
      </div>
    </div>

    <!-- Storage Card -->
    <div class="card-box" style="background:var(--blue-bg);border-color:#bfdbfe">
      <div style="font-size:10px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:var(--blue);margin-bottom:8px">Storage Usage</div>
      <div class="storage-bar-bg">
        <div class="storage-bar-fill" style="width:72%"></div>
      </div>
      <div style="font-size:11.5px;color:var(--muted)">7.2 GB of 10 GB used (72%)</div>
    </div>
  </div>

  <!-- RIGHT COLUMN -->
  <div>

    <!-- GENERAL TAB -->
    <div id="tab-general" class="tab-panel active">
      <form id="saveForm" action="#" method="POST">
        @csrf
        <div class="card-box">
          <div class="card-title">General Information</div>
          <div class="card-sub">Update your professional identity and contact details.</div>
          <div class="pf-grid">
            <div class="pf-group">
              <label class="lbl">Full Name</label>
              <input type="text" name="name" class="pf-input" value="{{ $user->name }}"/>
            </div>
            <div class="pf-group">
              <label class="lbl">Role</label>
              <input type="text" class="pf-input" value="Senior Architect" disabled/>
            </div>
            <div class="pf-group">
              <label class="lbl">Email Address</label>
              <input type="email" class="pf-input" value="{{ $user->email }}" disabled/>
            </div>
            <div class="pf-group">
              <label class="lbl">Department</label>
              <input type="text" class="pf-input" value="Architecture & Design" disabled/>
            </div>
            <div class="pf-group full">
              <label class="lbl">Phone Number</label>
              <input type="tel" class="pf-input" placeholder="+1 (555) 000-0000"/>
            </div>
          </div>
          <div class="card-footer">
            <button type="submit" class="btn-save">Save Changes</button>
            <button type="button" class="btn-ghost">Cancel</button>
          </div>
        </div>
      </form>

      <!-- Recent Activity -->
      <div class="card-box">
        <div class="card-title">Recent Activity</div>
        <div class="card-sub">Your latest actions on the platform.</div>
        <div class="act-item">
          <div class="act-dot dot-blue"></div>
          <div style="flex:1">
            <div class="act-text">Logged in to Fixtora dashboard</div>
            <div class="act-time">Just now</div>
          </div>
        </div>
        @foreach(\App\Models\Ticket::where('user_id',$user->id)->latest()->take(3)->get() as $t)
        <div class="act-item">
          <div class="act-dot dot-{{ $t->priority === 'critical' ? 'red' : ($t->priority === 'high' ? 'orange' : 'blue') }}"></div>
          <div style="flex:1">
            <div class="act-text">Created ticket — <span style="color:var(--blue)">{{ Str::limit($t->title,40) }}</span></div>
            <div class="act-time">{{ $t->created_at->diffForHumans() }}</div>
          </div>
        </div>
        @endforeach
        <div class="act-item">
          <div class="act-dot dot-green"></div>
          <div style="flex:1">
            <div class="act-text">Account created successfully</div>
            <div class="act-time">{{ $user->created_at->diffForHumans() }}</div>
          </div>
        </div>
      </div>
    </div>

    <!-- SECURITY TAB -->
    <div id="tab-security" class="tab-panel">
      <div class="card-box">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:4px">
          <div>
            <div class="card-title">Security & Privacy</div>
            <div class="card-sub" style="margin-bottom:0">Manage your access and active session logs.</div>
          </div>
          <button class="btn-ghost" style="font-size:12px;padding:6px 12px">Manage</button>
        </div>

        <!-- 2FA -->
        <div style="background:var(--bg);border:1px solid var(--border);border-radius:9px;padding:14px;margin-top:14px;display:flex;align-items:center;justify-content:space-between">
          <div style="display:flex;align-items:center;gap:12px">
            <div class="sec-icon">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
            </div>
            <div>
              <div class="sec-name">Two-Factor Authentication</div>
              <div class="sec-desc">Extra layer of security via mobile authenticator.</div>
            </div>
          </div>
          <label class="toggle-wrap">
            <input type="checkbox" checked>
            <span class="toggle-slider"></span>
          </label>
        </div>

        <!-- Password -->
        <div class="sec-item" style="margin-top:6px">
          <div style="display:flex;align-items:center;gap:12px">
            <div style="width:38px;height:38px;background:var(--blue-bg);border-radius:10px;display:flex;align-items:center;justify-content:center;color:var(--blue)">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            </div>
            <div>
              <div class="sec-name">Password</div>
              <div class="sec-desc">Last changed — never</div>
            </div>
          </div>
          <a href="{{ route('password.request') }}" class="btn-ghost" style="font-size:12px;padding:7px 14px;text-decoration:none">Change</a>
        </div>
      </div>

      <!-- Active Sessions -->
      <div class="card-box">
        <div class="card-title">Active Sessions</div>
        <div class="card-sub">Devices currently signed in to your account.</div>
        <div class="session-row">
          <div style="display:flex;align-items:center;gap:10px">
            <div class="session-icon">💻</div>
            <div>
              <div class="session-name">Windows · {{ request()->getClientIp() }}</div>
              <div class="session-sub">Chrome Browser · Active Now</div>
            </div>
          </div>
          <span class="badge-online">Current</span>
        </div>
        <div class="session-row">
          <div style="display:flex;align-items:center;gap:10px">
            <div class="session-icon">📱</div>
            <div>
              <div class="session-name">Mobile Device</div>
              <div class="session-sub">Fixtora App · 2 hours ago</div>
            </div>
          </div>
          <button class="btn-revoke">LOGOUT</button>
        </div>
      </div>
    </div>

    <!-- NOTIFICATIONS TAB -->
    <div id="tab-notifications" class="tab-panel">
      <div class="card-box">
        <div class="card-title">Notification Preferences</div>
        <div class="card-sub">Control how you stay informed about helpdesk activities.</div>
        <table class="notif-table">
          <thead>
            <tr>
              <th>Event Type</th>
              <th>Email</th>
              <th>Push</th>
              <th>Desktop</th>
            </tr>
          </thead>
          <tbody>
            @foreach([
              ['New Ticket Assignment', 'When a new ticket is assigned to you.', true, true, false],
              ['SLA Breach Alert', 'Critical alerts for tickets nearing SLA deadlines.', true, true, true],
              ['Ticket Status Update', 'When a ticket you created changes status.', true, false, false],
              ['System Update', 'Platform maintenance and feature releases.', false, false, true],
              ['Weekly Report', 'Summary of your team performance each week.', true, false, false],
            ] as [$name, $desc, $email, $push, $desktop])
            <tr>
              <td>
                <div class="notif-name">{{ $name }}</div>
                <div class="notif-desc">{{ $desc }}</div>
              </td>
              <td style="text-align:center"><input type="checkbox" class="notif-chk" {{ $email ? 'checked' : '' }}/></td>
              <td style="text-align:center"><input type="checkbox" class="notif-chk" {{ $push ? 'checked' : '' }}/></td>
              <td style="text-align:center"><input type="checkbox" class="notif-chk" {{ $desktop ? 'checked' : '' }}/></td>
            </tr>
            @endforeach
          </tbody>
        </table>
        <div class="card-footer" style="margin-top:14px">
          <button class="btn-save">Save Preferences</button>
          <button class="btn-ghost">Reset to Default</button>
        </div>
      </div>
    </div>

  </div>
</div>

<script>
function switchTab(name, btn) {
  document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.pt-btn').forEach(b => b.classList.remove('active'));
  document.getElementById('tab-' + name).classList.add('active');
  btn.classList.add('active');
}
</script>
@endsection
=======

<div class="page-header">
    <div>
        <h1>My Profile</h1>
        <p class="subtitle">Manage your account information and preferences</p>
    </div>
</div>

<div class="profile-grid">

    <!-- LEFT: Avatar Card -->
    <div>
        <div class="pcard avatar-card">
            <div class="avatar-wrap">
                <div class="avatar-circle">{{ strtoupper(substr(Auth::user()->name ?? 'AC', 0, 2)) }}</div>
                <div class="avatar-online"></div>
            </div>
            <div class="avatar-name">{{ Auth::user()->name ?? 'Alex Chen' }}</div>
            <div class="avatar-role">Senior Architect</div>
            <div class="avatar-badge">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                Active Account
            </div>
            <div class="avatar-divider"></div>
            <div class="avatar-stat">
                <div class="a-stat-row">
                    <span class="a-stat-label">Email</span>
                    <span class="a-stat-val">{{ Auth::user()->email ?? 'alex@fixtora.com' }}</span>
                </div>
                <div class="a-stat-row">
                    <span class="a-stat-label">Member since</span>
                    <span class="a-stat-val">{{ Auth::user()->created_at ? Auth::user()->created_at->format('M Y') : 'Jan 2024' }}</span>
                </div>
                <div class="a-stat-row">
                    <span class="a-stat-label">Tickets raised</span>
                    <span class="a-stat-val blue">{{ \App\Models\Ticket::where('user_id', Auth::id())->count() }}</span>
                </div>
                <div class="a-stat-row">
                    <span class="a-stat-label">Status</span>
                    <span class="a-stat-val green">● Online</span>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT: Info + Activity + Security -->
    <div style="display:flex;flex-direction:column;gap:20px;">

        <!-- Account Info -->
        <div class="pcard">
            <div class="pcard-header">
                <div>
                    <div class="pcard-title">Account Information</div>
                    <div class="pcard-title-sub">Your personal details</div>
                </div>
            </div>
            <div class="pcard-body">
                <div class="pf-grid">
                    <div class="pf-group">
                        <div class="pf-label">Full Name</div>
                        <div class="pf-value">{{ Auth::user()->name ?? 'Alex Chen' }}</div>
                    </div>
                    <div class="pf-group">
                        <div class="pf-label" style="display:flex;align-items:center;justify-content:space-between;">
                            Email Address
                            <span class="pf-readonly">Readonly</span>
                        </div>
                        <div class="pf-value">{{ Auth::user()->email ?? 'alex@fixtora.com' }}</div>
                    </div>
                    <div class="pf-group">
                        <div class="pf-label">Role</div>
                        <div class="pf-value">Senior Architect</div>
                    </div>
                    <div class="pf-group">
                        <div class="pf-label">Department</div>
                        <div class="pf-value">Architecture & Design</div>
                    </div>
                </div>
                <p style="font-size:12px;color:var(--muted);font-weight:400;">
                    Profile editing is coming soon. Contact your administrator to update your details.
                </p>
            </div>
            <div class="pcard-footer">
                <button class="btn-primary" disabled style="opacity:0.5;cursor:not-allowed;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                    Save Changes
                </button>
                <button class="btn-ghost">Cancel</button>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="pcard">
            <div class="pcard-header">
                <div>
                    <div class="pcard-title">Recent Activity</div>
                    <div class="pcard-title-sub">Your latest actions on the platform</div>
                </div>
            </div>
            <div class="pcard-body">
                <div class="activity-list">
                    <div class="activity-item">
                        <div class="activity-dot dot-blue"></div>
                        <div style="flex:1">
                            <div class="activity-text">Logged in to Fixtora dashboard</div>
                            <div class="activity-time">Just now</div>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-dot dot-green"></div>
                        <div style="flex:1">
                            <div class="activity-text">Account created successfully</div>
                            <div class="activity-time">{{ Auth::user()->created_at ? Auth::user()->created_at->diffForHumans() : '2 days ago' }}</div>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-dot dot-orange"></div>
                        <div style="flex:1">
                            <div class="activity-text">Viewing profile settings</div>
                            <div class="activity-time">A moment ago</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security -->
        <div class="pcard">
            <div class="pcard-header">
                <div>
                    <div class="pcard-title">Security</div>
                    <div class="pcard-title-sub">Manage your account security settings</div>
                </div>
            </div>
            <div class="pcard-body">
                <div class="sec-item">
                    <div class="sec-left">
                        <div class="sec-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                <rect x="3" y="11" width="18" height="11" rx="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                        </div>
                        <div>
                            <div class="sec-name">Password</div>
                            <div class="sec-desc">Last changed — never</div>
                        </div>
                    </div>
                    <a href="{{ route('password.request') }}" class="btn-ghost" style="font-size:12px;padding:7px 14px;">Change</a>
                </div>
                <div class="sec-item">
                    <div class="sec-left">
                        <div class="sec-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="sec-name">Two-Factor Auth</div>
                            <div class="sec-desc">Add an extra layer of security</div>
                        </div>
                    </div>
                    <span class="sec-status disabled">Not enabled</span>
                </div>
                <div class="sec-item">
                    <div class="sec-left">
                        <div class="sec-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                <path d="M21 2H3v16h5v4l4-4h5l4-4V2zM11 11V7m0 8v.01"/>
                            </svg>
                        </div>
                        <div>
                            <div class="sec-name">Active Sessions</div>
                            <div class="sec-desc">1 active session on this device</div>
                        </div>
                    </div>
                    <span class="sec-status enabled">Active</span>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
>>>>>>> 266f96ddbebd898a32043aa49660953512618759
