@extends('layouts.app')

@section('title', 'User role – {{ $role->name }} – Fixtora')

@section('content')
<div style="padding: 28px 32px;">

  {{-- Breadcrumb --}}
  <div style="display:flex; align-items:center; gap:8px; margin-bottom:20px; font-size:13px; color:var(--muted);">
    <a href="{{ route('roles.index') }}" style="color:var(--blue); text-decoration:none;">User Roles</a>
    <span>›</span>
    <span>{{ $role->name }}</span>
  </div>

  <h1 style="font-size:24px; font-weight:800; letter-spacing:-.6px; color:var(--navy); margin-bottom:24px;">User role – {{ $role->name }}</h1>

  {{-- Flash --}}
  @if(session('success'))
    <div style="background:var(--green-bg); border:1px solid #bbf7d0; color:var(--green); padding:10px 14px; border-radius:var(--radius-sm); font-size:13px; margin-bottom:18px;">
      {{ session('success') }}
    </div>
  @endif

  @if($errors->any())
    <div style="background:var(--red-bg); border:1px solid #fecaca; color:var(--red); padding:10px 14px; border-radius:var(--radius-sm); font-size:13px; margin-bottom:18px;">
      @foreach($errors->all() as $error) {{ $error }}<br> @endforeach
    </div>
  @endif

  <div style="display:grid; grid-template-columns:1fr 320px; gap:20px; align-items:start;">

    {{-- Left: Users assignment + Permissions --}}
    <div>

      {{-- Users in role --}}
      <div style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); padding:24px; margin-bottom:20px; box-shadow:var(--shadow-sm);">
        <h2 style="font-size:14px; font-weight:700; margin-bottom:16px; color:var(--text);">Users in role</h2>

        @if(auth()->user()->hasPermission('assign_users_to_role'))
        <form action="{{ route('roles.association', $role) }}" method="POST">
          @csrf

          <div style="display:grid; grid-template-columns:1fr 40px 1fr; gap:12px; align-items:start; margin-bottom:14px;">

            {{-- Unassigned --}}
            <div>
              <label style="display:block; font-size:12px; font-weight:600; color:var(--muted); margin-bottom:6px;">Unassigned users</label>
              <select id="unassigned" multiple
                style="width:100%; height:180px; border:1px solid var(--border); border-radius:var(--radius-sm); font-size:12.5px; padding:4px; font-family:inherit; color:var(--text);">
                @foreach($unassignedUsers as $user)
                  <option value="{{ $user->id }}" ondblclick="moveToAssigned(this)">
                    {{ $user->name }} ({{ $user->email }})
                  </option>
                @endforeach
              </select>
            </div>

            {{-- Arrow --}}
            <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; gap:6px; padding-top:26px;">
              <button type="button" onclick="moveSelected()" title="Move selected to assigned"
                style="background:var(--navy); border:none; border-radius:5px; padding:5px 8px; cursor:pointer; color:#fff; font-size:14px;">›</button>
              <button type="button" onclick="moveBack()" title="Move back to unassigned"
                style="background:var(--border); border:none; border-radius:5px; padding:5px 8px; cursor:pointer; color:var(--text-2); font-size:14px;">‹</button>
            </div>

            {{-- Assigned --}}
            <div>
              <label style="display:block; font-size:12px; font-weight:600; color:var(--muted); margin-bottom:6px;">Users assigned to role</label>
              <select id="assigned" name="assigned_users[]" multiple
                style="width:100%; height:180px; border:1px solid var(--border); border-radius:var(--radius-sm); font-size:12.5px; padding:4px; font-family:inherit; color:var(--text);">
                @foreach($assignedUsers as $user)
                  <option value="{{ $user->id }}" ondblclick="moveBack(this)">
                    {{ $user->name }} ({{ $user->email }})
                  </option>
                @endforeach
              </select>
            </div>
          </div>

          <p style="font-size:12px; color:var(--muted); margin-bottom:14px; text-align:center;">
            Double click the user's name to assign or unassign
          </p>

          <div style="display:flex; gap:10px;">
            <button type="submit"
              style="background:var(--navy); color:#fff; padding:9px 18px; border-radius:var(--radius-sm); font-size:13px; font-weight:600; border:none; cursor:pointer;">
              Save association
            </button>
            <a href="{{ route('roles.index') }}"
              style="padding:9px 18px; border-radius:var(--radius-sm); font-size:13px; font-weight:600; border:1px solid var(--border); color:var(--text-2); text-decoration:none; background:var(--surface);">
              Go back
            </a>
          </div>
        </form>
        @endif
      </div>

      {{-- Permissions --}}
      <div style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); padding:24px; box-shadow:var(--shadow-sm);">
        <h2 style="font-size:14px; font-weight:700; margin-bottom:16px; color:var(--text);">Permissions</h2>

        {{-- Tabs --}}
        <div style="display:flex; flex-wrap:wrap; gap:4px; margin-bottom:20px; border-bottom:1px solid var(--border); padding-bottom:12px;">
          @foreach($permissions as $group => $perms)
            <button type="button" onclick="showTab('{{ Str::slug($group) }}')" id="tab-{{ Str::slug($group) }}"
              style="padding:5px 12px; border-radius:20px; font-size:12.5px; font-weight:600; border:none; cursor:pointer; transition:all 0.15s;"
              class="perm-tab {{ $loop->first ? 'active-tab' : '' }}">
              {{ $group }}
            </button>
          @endforeach
        </div>

        @if(auth()->user()->hasPermission('assign_permissions'))
        <form action="{{ route('roles.permissions', $role) }}" method="POST">
          @csrf

          @foreach($permissions as $group => $perms)
            <div id="panel-{{ Str::slug($group) }}" class="perm-panel" style="{{ $loop->first ? '' : 'display:none;' }}">
              <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                <span style="font-size:13px; font-weight:700; color:var(--text-2);">{{ $group }}</span>
                <button type="button" onclick="toggleGroup('{{ Str::slug($group) }}', true)"
                  style="font-size:11px; color:var(--blue); background:none; border:none; cursor:pointer; font-weight:600; display:flex; align-items:center; gap:3px;">
                  <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 5"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                  Select all
                </button>
                <button type="button" onclick="toggleGroup('{{ Str::slug($group) }}', false)"
                  style="font-size:11px; color:var(--muted); background:none; border:none; cursor:pointer; font-weight:600; display:flex; align-items:center; gap:3px;">
                  <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/></svg>
                  Select none
                </button>
              </div>
              <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:8px;">
                @foreach($perms as $perm)
                  <label style="display:flex; align-items:center; gap:7px; font-size:12px; color:var(--text-2); cursor:pointer;">
                    <input type="checkbox" name="permissions[]" value="{{ $perm }}"
                      {{ in_array($perm, $role->permissions ?? []) ? 'checked' : '' }}
                      class="perm-cb-{{ Str::slug($group) }}"
                      style="width:13px; height:13px; accent-color:var(--blue); cursor:pointer;">
                    {{ $perm }}
                  </label>
                @endforeach
              </div>
            </div>
          @endforeach

          <div style="display:flex; gap:10px; margin-top:20px; padding-top:16px; border-top:1px solid var(--border);">
            <button type="submit"
              style="background:var(--navy); color:#fff; padding:9px 18px; border-radius:var(--radius-sm); font-size:13px; font-weight:600; border:none; cursor:pointer;">
              Save permissions
            </button>
            <a href="{{ route('roles.index') }}"
              style="padding:9px 18px; border-radius:var(--radius-sm); font-size:13px; font-weight:600; border:1px solid var(--border); color:var(--text-2); text-decoration:none; background:var(--surface);">
              Go back
            </a>
          </div>
        </form>
        @endif
      </div>

    </div>{{-- end left --}}

    {{-- Right: Edit role name/description --}}
    <div style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); padding:24px; box-shadow:var(--shadow-sm);">
      <h2 style="font-size:14px; font-weight:700; margin-bottom:16px; color:var(--text);">Edit user role</h2>

      @if(auth()->user()->hasPermission('edit_roles'))
      <form action="{{ route('roles.update', $role) }}" method="POST">
        @csrf @method('PATCH')

        <div style="margin-bottom:14px;">
          <label style="display:block; font-size:12px; font-weight:600; color:var(--muted); margin-bottom:5px;">Role name</label>
          <input type="text" name="name" value="{{ old('name', $role->name) }}"
            style="width:100%; padding:9px 12px; border:1px solid var(--border); border-radius:var(--radius-sm); font-size:13px; font-family:inherit; color:var(--text); outline:none;"
            onfocus="this.style.borderColor='var(--blue)'" onblur="this.style.borderColor='var(--border)'">
        </div>

        <div style="margin-bottom:16px;">
          <label style="display:block; font-size:12px; font-weight:600; color:var(--muted); margin-bottom:5px;">Description</label>
          <textarea name="description" rows="4"
            style="width:100%; padding:9px 12px; border:1px solid var(--border); border-radius:var(--radius-sm); font-size:13px; font-family:inherit; color:var(--text); resize:vertical; outline:none;"
            onfocus="this.style.borderColor='var(--blue)'" onblur="this.style.borderColor='var(--border)'">{{ old('description', $role->description) }}</textarea>
        </div>

        <button type="submit"
          style="width:100%; background:var(--navy); color:#fff; padding:10px; border-radius:var(--radius-sm); font-size:13px; font-weight:600; border:none; cursor:pointer;">
          Save changes
        </button>
      </form>
      @endif
    </div>

  </div>
</div>

<style>
.perm-tab { background: #f1f5f9; color: var(--muted); }
.perm-tab.active-tab { background: var(--navy); color: #fff; }
</style>

<script>
// Tab switching
function showTab(group) {
  document.querySelectorAll('.perm-panel').forEach(p => p.style.display = 'none');
  document.querySelectorAll('.perm-tab').forEach(t => t.classList.remove('active-tab'));
  document.getElementById('panel-' + group).style.display = '';
  document.getElementById('tab-' + group).classList.add('active-tab');
}

// Toggle all checkboxes in a group
function toggleGroup(group, selectAll) {
  document.querySelectorAll('.perm-cb-' + group).forEach(cb => cb.checked = selectAll);
}

// Move selected from unassigned → assigned
function moveSelected() {
  const from = document.getElementById('unassigned');
  const to   = document.getElementById('assigned');
  Array.from(from.selectedOptions).forEach(opt => to.appendChild(opt));
}

// Move selected from assigned → unassigned
function moveBack(opt) {
  const from = document.getElementById('assigned');
  const to   = document.getElementById('unassigned');
  if (opt) {
    to.appendChild(opt);
  } else {
    Array.from(from.selectedOptions).forEach(o => to.appendChild(o));
  }
}

// On double-click: move to assigned
function moveToAssigned(opt) {
  document.getElementById('assigned').appendChild(opt);
}

// Auto select all options in assigned list before any form submit
document.addEventListener('DOMContentLoaded', function () {
  const forms = document.querySelectorAll('form');
  forms.forEach(function(form) {
    form.addEventListener('submit', function () {
      const assigned = document.getElementById('assigned');
      if (assigned) {
        Array.from(assigned.options).forEach(o => o.selected = true);
      }
    });
  });
});
</script>
@endsection