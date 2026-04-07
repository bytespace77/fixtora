@extends('layouts.app')
@section('title', 'Profile – Fixtora')

@section('styles')
<style>
/* ── Page Header ──────────────────────────────────────────────── */
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

/* SUCCESS FLASH */
.flash-success{background:#dcfce7;border:1px solid #bbf7d0;color:#15803d;padding:10px 16px;border-radius:8px;font-size:13px;font-weight:600;margin-bottom:16px;display:flex;align-items:center;gap:8px}
</style>
@endsection

@section('content')


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
</div>

<div class="profile-grid">

  <!-- LEFT COLUMN -->
  <div>
    <!-- Avatar Card -->
    <div class="card-box" style="text-align:center">
      {{-- Avatar upload form (hidden) --}}
      <form id="avatarForm" action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data" style="display:none">
        @csrf
        <input type="file" id="avatarInput" name="avatar" accept="image/*" onchange="document.getElementById('avatarForm').submit()">
      </form>

      <div class="avatar-circle" style="{{ $user->avatar ? 'background:none;padding:0;overflow:hidden' : '' }}">
        @if($user->avatar)
          <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar"
               style="width:100%;height:100%;object-fit:cover;border-radius:16px">
        @else
          {{ strtoupper(substr($user->name, 0, 2)) }}
        @endif
        <div class="avatar-cam" onclick="document.getElementById('avatarInput').click()" title="Change photo">
          <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="var(--blue)" stroke-width="2.5"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
        </div>
      </div>
      <div class="prof-name">{{ $user->name }}</div>
      <div class="prof-role">{{ $user->company?->name ?? 'No Company' }}</div>
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
        <div class="storage-bar-fill" style="width:{{ $usedPercent }}%"></div>
      </div>
      <div style="font-size:11.5px;color:var(--muted)">{{ $usedGB }} GB of 10 GB used ({{ $usedPercent }}%)</div>
    </div>
  </div>

  <!-- RIGHT COLUMN -->
  <div>

    <!-- GENERAL TAB -->
    <div id="tab-general" class="tab-panel active">

      {{-- ✅ Step 17: Form now posts to profile.update route --}}
      <form id="saveForm" action="{{ route('profile.update') }}" method="POST">
        @csrf
        <div class="card-box">
          <div class="card-title">General Information</div>
          <div class="card-sub">Update your professional identity and contact details.</div>
          <div class="pf-grid">
            <div class="pf-group">
              <label class="lbl">Full Name</label>
              <input type="text" name="name" class="pf-input" value="{{ old('name', $user->name) }}" required/>
              @error('name')<div style="font-size:11px;color:var(--red);margin-top:3px">{{ $message }}</div>@enderror
            </div>
            <div class="pf-group">
              <label class="lbl">Phone Number</label>
              <input type="text" name="phone" class="pf-input" value="{{ old('phone', $user->phone) }}" pattern="[0-9]+" title="Numbers only"/>
              @error('phone')<div style="font-size:11px;color:var(--red);margin-top:3px">{{ $message }}</div>@enderror
            </div>
            <div class="pf-group">
              <label class="lbl">Company</label>
              {{-- ✅ Step 17: Show real company name (read-only) --}}
              <input type="text" class="pf-input" value="{{ $user->company?->name ?? '—' }}" disabled/>
            </div>
            <div class="pf-group">
              <label class="lbl">Email Address</label>
              <input type="email" class="pf-input" value="{{ $user->email }}" disabled/>
            </div>
            <div class="pf-group">
              <label class="lbl">Member Since</label>
              <input type="text" class="pf-input" value="{{ $user->created_at->format('d M Y') }}" disabled/>
            </div>
          </div>
          <div class="card-footer">
            <button type="submit" class="btn-save">Save Changes</button>
            <a href="{{ route('profile.show') }}" class="btn-ghost" style="text-decoration:none">Cancel</a>
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

        <!-- Password -->
        <div class="sec-item" style="margin-top:14px">
          <div style="display:flex;align-items:center;gap:12px">
            <div style="width:38px;height:38px;background:var(--blue-bg);border-radius:10px;display:flex;align-items:center;justify-content:center;color:var(--blue)">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            </div>
            <div>
              <div class="sec-name">Password</div>
              <div class="sec-desc">Last changed — {{ $user->password_changed_at ? $user->password_changed_at->format('d M Y') : 'never' }}</div>
            </div>
          </div>
          <button type="button" class="btn-ghost" style="font-size:12px;padding:7px 14px" onclick="openPwModal()">Change</button>
        </div>
      </div>

      <!-- Active Sessions -->
      <div class="card-box">
        <div class="card-title">Active Sessions</div>
        <div class="card-sub">Devices currently signed in to your account.</div>
        @forelse($activeSessions as $session)
        <div class="session-row">
          <div style="display:flex;align-items:center;gap:10px">
            <div class="session-icon">{{ in_array($session->os, ['Android','iOS']) ? '📱' : '💻' }}</div>
            <div>
              <div class="session-name">{{ $session->os }} · {{ $session->ip_address }}</div>
              <div class="session-sub">{{ $session->browser }} · {{ $session->is_current ? 'Active Now' : $session->last_active }}</div>
            </div>
          </div>
          @if($session->is_current)
            <span class="badge-online">Current</span>
          @else
            <form method="POST" action="{{ route('profile.session.destroy') }}" style="margin:0">
              @csrf
              <input type="hidden" name="session_id" value="{{ $session->id }}">
              <button type="submit" class="btn-revoke">LOGOUT</button>
            </form>
          @endif
        </div>
        @empty
        <div style="font-size:13px;color:var(--muted);padding:10px 0">No active sessions found.</div>
        @endforelse
      </div>
    </div>


  </div>
</div>

<!-- PASSWORD CHANGE MODAL -->
<div id="pwModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:9999;align-items:center;justify-content:center">
  <div style="background:var(--surface);border-radius:14px;padding:28px;width:420px;max-width:95vw;box-shadow:0 20px 60px rgba(0,0,0,.2)">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
      <div>
        <div style="font-size:16px;font-weight:800;color:var(--navy)">Change Password</div>
        <div style="font-size:12px;color:var(--muted);margin-top:2px">Choose a strong password with at least 8 characters.</div>
      </div>
      <button onclick="closePwModal()" style="background:none;border:none;cursor:pointer;color:var(--muted);font-size:20px;line-height:1">&times;</button>
    </div>

    <form action="{{ route('profile.password') }}" method="POST">
      @csrf
      <div style="display:flex;flex-direction:column;gap:14px">
        <div class="pf-group">
          <label class="lbl">Current Password</label>
          <input type="password" name="current_password" class="pf-input" placeholder="Enter current password" required/>
          @error('current_password')<div style="font-size:11px;color:var(--red);margin-top:3px">{{ $message }}</div>@enderror
        </div>
        <div class="pf-group">
          <label class="lbl">New Password</label>
          <input type="password" name="password" class="pf-input" placeholder="Min 8 characters" required/>
        </div>
        <div class="pf-group">
          <label class="lbl">Confirm New Password</label>
          <input type="password" name="password_confirmation" class="pf-input" placeholder="Re-enter new password" required/>
          @error('password')<div style="font-size:11px;color:var(--red);margin-top:3px">{{ $message }}</div>@enderror
        </div>
      </div>
      <div style="display:flex;gap:8px;margin-top:20px">
        <button type="submit" class="btn-save" style="flex:1">Update Password</button>
        <button type="button" onclick="closePwModal()" class="btn-ghost">Cancel</button>
      </div>
    </form>
  </div>
</div>

<script>
function switchTab(name, btn) {
  document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.pt-btn').forEach(b => b.classList.remove('active'));
  document.getElementById('tab-' + name).classList.add('active');
  btn.classList.add('active');
}

function openPwModal() {
  const m = document.getElementById('pwModal');
  m.style.display = 'flex';
}

function closePwModal() {
  document.getElementById('pwModal').style.display = 'none';
}

// Auto-open Security tab if returning from password change or if there are errors
document.addEventListener('DOMContentLoaded', function() {
  @if(session('open_tab') === 'security' || $errors->has('current_password') || $errors->has('password'))
    const secBtn = document.querySelectorAll('.pt-btn')[1];
    switchTab('security', secBtn);
    @if($errors->has('current_password') || $errors->has('password'))
      openPwModal();
    @endif
  @endif
});
</script>
@endsection