@extends('layouts.app')
@section('title', 'Manage Users – Fixtora')

@section('styles')
<style>
.page-header { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:24px; }
.page-header h1 { font-size:24px; font-weight:800; letter-spacing:-.6px; color:var(--navy); }
.page-header p  { font-size:13px; color:var(--muted); margin-top:4px; }

.btn { display:inline-flex; align-items:center; gap:6px; padding:8px 16px; border-radius:var(--radius-sm); font-size:13px; font-weight:600; cursor:pointer; border:none; font-family:inherit; text-decoration:none; transition:all .12s; }
.btn-primary { background:var(--blue); color:#fff; }
.btn-primary:hover { background:var(--blue-2); }
.btn-sm { padding:5px 12px; font-size:12px; }
.btn-outline { background:transparent; color:var(--text-2); border:1px solid var(--border-2); }
.btn-outline:hover { border-color:var(--blue); color:var(--blue); background:var(--blue-bg); }
.btn-danger { background:transparent; color:var(--red); border:1px solid var(--border-2); }
.btn-danger:hover { background:var(--red-bg); border-color:var(--red); }
.btn-success { background:transparent; color:var(--green); border:1px solid var(--border-2); }
.btn-success:hover { background:var(--green-bg); border-color:var(--green); }

.card { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); }
.card-header { display:flex; align-items:center; justify-content:space-between; padding:18px 22px; border-bottom:1px solid var(--border); }
.card-header h2 { font-size:15px; font-weight:700; color:var(--text); }

/* Filter bar */
.filter-bar { display:flex; align-items:center; gap:10px; padding:14px 22px; border-bottom:1px solid var(--border); flex-wrap:wrap; background:var(--bg); }
.filter-bar input, .filter-bar select { padding:7px 12px; border:1px solid var(--border-2); border-radius:var(--radius-sm); font-size:13px; color:var(--text); background:var(--surface); font-family:inherit; }
.filter-bar input { width:220px; }
.filter-bar select { min-width:140px; }

table { width:100%; border-collapse:collapse; }
thead th { padding:10px 16px; text-align:left; font-size:11px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.4px; border-bottom:1px solid var(--border); background:var(--bg); }
tbody td { padding:13px 16px; font-size:13px; color:var(--text-2); border-bottom:1px solid var(--border); vertical-align:middle; }
tbody tr:last-child td { border-bottom:none; }
tbody tr:hover td { background:#fafbfd; }

.user-avatar { width:34px; height:34px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:800; color:#fff; background:linear-gradient(135deg,#2563eb,#7c3aed); flex-shrink:0; }
.user-name { font-weight:700; color:var(--text); font-size:13.5px; }
.user-email { font-size:11px; color:var(--muted); margin-top:2px; }
.user-phone { font-size:11px; color:var(--muted); margin-top:1px; }

.badge { display:inline-block; padding:3px 9px; border-radius:20px; font-size:11px; font-weight:700; }
.badge-active   { background:var(--green-bg); color:var(--green); }
.badge-inactive { background:var(--red-bg); color:var(--red); }
.badge-role { background:var(--blue-bg,#eff6ff); color:var(--blue); }
.badge-superadmin { background:#fef3c7; color:#92400e; }

.actions-cell { display:flex; gap:6px; flex-wrap:wrap; }

.alert { padding:12px 18px; border-radius:var(--radius-sm); margin-bottom:18px; font-size:13px; font-weight:500; }
.alert-success { background:var(--green-bg); color:var(--green); border:1px solid #bbf7d0; }
.alert-error   { background:var(--red-bg); color:var(--red); border:1px solid #fecaca; }

.breadcrumb { display:flex; align-items:center; gap:6px; font-size:12.5px; color:var(--muted); margin-bottom:16px; }
.breadcrumb a { color:var(--blue); text-decoration:none; }
.breadcrumb a:hover { text-decoration:underline; }

/* Modal */
.modal-backdrop { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:1000; align-items:center; justify-content:center; }
.modal-backdrop.open { display:flex; }
.modal { background:var(--surface); border-radius:var(--radius); width:100%; max-width:480px; box-shadow:0 20px 60px rgba(0,0,0,.18); padding:28px; position:relative; }
.modal h3 { font-size:17px; font-weight:800; color:var(--navy); margin-bottom:4px; }
.modal p.sub { font-size:13px; color:var(--muted); margin-bottom:20px; }
.modal-close { position:absolute; top:16px; right:16px; background:none; border:none; cursor:pointer; color:var(--muted); }
.f-group { margin-bottom:14px; }
.f-label { display:block; font-size:12px; font-weight:700; color:var(--text-2); margin-bottom:5px; letter-spacing:.3px; text-transform:uppercase; }
.f-input { width:100%; padding:9px 12px; border:1px solid var(--border-2); border-radius:var(--radius-sm); font-size:13px; color:var(--text); background:var(--surface); font-family:inherit; box-sizing:border-box; }
.f-input:focus { outline:none; border-color:var(--blue); box-shadow:0 0 0 3px rgba(37,99,235,.08); }
.f-select { width:100%; padding:9px 12px; border:1px solid var(--border-2); border-radius:var(--radius-sm); font-size:13px; color:var(--text); background:var(--surface); font-family:inherit; box-sizing:border-box; }
.modal-footer { display:flex; justify-content:flex-end; gap:8px; margin-top:20px; }
</style>
@endsection

@section('content')
<div class="breadcrumb">
  <a href="{{ route('superadmin.dashboard') }}">Super Admin</a>
  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
  Manage Users
</div>

<div class="page-header">
  <div>
    <h1>Manage Users</h1>
    <p>View contact details, reset passwords, and toggle user access across all companies.</p>
  </div>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert alert-error">{{ session('error') }}</div>
@endif

<div class="card">
  <div class="card-header">
    <h2>All Users ({{ $users->total() }})</h2>
  </div>

  {{-- Filter bar --}}
  <form method="GET" action="{{ route('superadmin.users.index') }}" class="filter-bar">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or email…">
    <select name="company_id">
      <option value="">All Companies</option>
      @foreach($companies as $company)
        <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
      @endforeach
    </select>
    <select name="status">
      <option value="">All Status</option>
      <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
      <option value="disabled" {{ request('status') === 'disabled' ? 'selected' : '' }}>Disabled</option>
    </select>
    <button type="submit" class="btn btn-outline btn-sm">Filter</button>
    @if(request()->hasAny(['search','company_id','status']))
      <a href="{{ route('superadmin.users.index') }}" class="btn btn-outline btn-sm">Clear</a>
    @endif
  </form>

  @if($users->isEmpty())
    <div style="padding:40px; text-align:center; color:var(--muted);">No users found.</div>
  @else
  <table>
    <thead>
      <tr>
        <th>User</th>
        <th>User ID</th>
        <th>Company</th>
        <th>User Role</th>
        <th>Date Joined</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      @foreach($users as $user)
      <tr>
        <td>
          <div style="display:flex;align-items:center;gap:10px">
            <div class="user-avatar">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
            <div>
              <div class="user-name">{{ $user->name }}</div>
              <div class="user-email">{{ $user->email }}</div>
              @if($user->phone)
                <div class="user-phone">📞 {{ $user->phone }}</div>
              @endif
            </div>
          </div>
        </td>
        <td style="font-family:monospace;font-size:12px;color:var(--muted)">#{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</td>
        <td>{{ $user->company->name ?? '—' }}</td>
        <td>
          @if($user->role === 'superadmin')
            <span class="badge badge-superadmin">Super Admin</span>
          @elseif($user->role === 'admin')
            <span class="badge badge-role">Admin</span>
          @else
            <span class="badge badge-role">{{ ucfirst($user->role ?? 'User') }}</span>
          @endif
        </td>
        <td>{{ $user->created_at->format('d M Y') }}</td>
        <td>
          @if(!$user->is_disabled)
            <span class="badge badge-active">Active</span>
          @else
            <span class="badge badge-inactive">Disabled</span>
          @endif
        </td>
        <td>
          <div class="actions-cell">
            {{-- View contact details --}}
            <button type="button" class="btn btn-outline btn-sm"
              onclick="openViewModal({{ json_encode(['name'=>$user->name,'email'=>$user->email,'phone'=>$user->phone ?? '—','company'=>$user->company->name ?? '—','role'=>$user->role,'joined'=>$user->created_at->format('d M Y')]) }})">
              View
            </button>

            {{-- Reset Password --}}
            <form method="POST" action="{{ route('superadmin.users.resetPassword', $user) }}" style="display:inline"
              onsubmit="return confirm('Reset password for {{ addslashes($user->name) }}? A new temporary password will be shown.')">
              @csrf
              <button type="submit" class="btn btn-outline btn-sm">Reset PW</button>
            </form>

            {{-- Toggle Disable --}}
            @if($user->role !== 'superadmin')
            <form method="POST" action="{{ route('superadmin.users.toggle', $user) }}" style="display:inline"
              onsubmit="return confirm('{{ $user->is_disabled ? 'Enable' : 'Disable' }} {{ addslashes($user->name) }}?')">
              @csrf @method('PATCH')
              <button type="submit" class="btn btn-sm {{ $user->is_disabled ? 'btn-success' : 'btn-danger' }}">
                {{ $user->is_disabled ? 'Enable' : 'Disable' }}
              </button>
            </form>
            @endif
          </div>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>

  @if($users->hasPages())
  <div style="padding:16px 22px; border-top:1px solid var(--border);">
    {{ $users->appends(request()->query())->links() }}
  </div>
  @endif
  @endif
</div>

{{-- View User Modal --}}
<div class="modal-backdrop" id="viewModal">
  <div class="modal">
    <button class="modal-close" onclick="closeViewModal()">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
    <h3 id="vm-name">User Details</h3>
    <p class="sub" id="vm-role"></p>
    <div style="display:grid;gap:12px;margin-top:16px">
      <div><div class="f-label">Email</div><div id="vm-email" style="font-size:13px;color:var(--text)"></div></div>
      <div><div class="f-label">Phone</div><div id="vm-phone" style="font-size:13px;color:var(--text)"></div></div>
      <div><div class="f-label">Company</div><div id="vm-company" style="font-size:13px;color:var(--text)"></div></div>
      <div><div class="f-label">Date Joined</div><div id="vm-joined" style="font-size:13px;color:var(--text)"></div></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="closeViewModal()">Close</button>
    </div>
  </div>
</div>

{{-- Show temp password modal --}}
@if(session('temp_password'))
<div class="modal-backdrop open" id="pwModal">
  <div class="modal">
    <h3>Password Reset</h3>
    <p class="sub">Share this temporary password with the user. It will not be shown again.</p>
    <div style="background:var(--bg);border:1px solid var(--border);border-radius:var(--radius-sm);padding:14px 18px;font-family:monospace;font-size:18px;font-weight:700;color:var(--navy);text-align:center;letter-spacing:2px">
      {{ session('temp_password') }}
    </div>
    <div class="modal-footer">
      <button class="btn btn-primary" onclick="document.getElementById('pwModal').classList.remove('open')">Done</button>
    </div>
  </div>
</div>
@endif

@endsection

@section('scripts')
<script>
function openViewModal(data) {
  document.getElementById('vm-name').textContent    = data.name;
  document.getElementById('vm-role').textContent    = data.role + ' · ' + data.company;
  document.getElementById('vm-email').textContent   = data.email;
  document.getElementById('vm-phone').textContent   = data.phone || '—';
  document.getElementById('vm-company').textContent = data.company;
  document.getElementById('vm-joined').textContent  = data.joined;
  document.getElementById('viewModal').classList.add('open');
}
function closeViewModal() {
  document.getElementById('viewModal').classList.remove('open');
}
document.getElementById('viewModal').addEventListener('click', function(e) {
  if (e.target === this) closeViewModal();
});
</script>
@endsection