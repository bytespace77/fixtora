@extends('layouts.app')

@section('title', 'User Roles – Fixtora')

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

.card { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); overflow:hidden; box-shadow:var(--shadow-sm); }

table { width:100%; border-collapse:collapse; }
thead th { padding:10px 16px; text-align:left; font-size:11px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.4px; border-bottom:1px solid var(--border); background:var(--bg); }
tbody td { padding:13px 16px; font-size:13px; color:var(--text-2); border-bottom:1px solid var(--border); vertical-align:middle; }
tbody tr:last-child td { border-bottom:none; }
tbody tr:hover td { background:#fafbfd; }

.actions-cell { display:flex; gap:6px; flex-wrap:wrap; justify-content:flex-end; }

.alert { padding:12px 18px; border-radius:var(--radius-sm); margin-bottom:18px; font-size:13px; font-weight:500; }
.alert-success { background:var(--green-bg); color:var(--green); border:1px solid #bbf7d0; }

.badge-count { background:var(--blue-bg); color:var(--blue); font-size:12px; font-weight:600; padding:3px 10px; border-radius:20px; }

.role-name { font-weight:700; color:var(--text); font-size:13.5px; }

/* SVG formatting for actions */
.actions-cell button, .actions-cell a { display:inline-flex; align-items:center; gap:5px; font-size:12.5px; justify-content:center; }
.actions-cell a { color:var(--blue); }
.actions-cell button { color:var(--red); background:none; padding:5px 12px; border:1px solid var(--border); border-radius:var(--radius-sm); cursor:pointer; }
.actions-cell button:hover { background:var(--red-bg); border-color:var(--red); }
.actions-cell a { border:1px solid var(--border); padding:5px 12px; border-radius:var(--radius-sm); text-decoration:none; }
.actions-cell a:hover { border-color:var(--blue); background:var(--blue-bg); }
</style>
@endsection

@section('content')
<div style="max-width: 900px; margin: 0; padding-bottom: 40px;">

  {{-- Header --}}
  <div class="page-header">
    <div>
      <h1>User Roles</h1>
      <p>Create roles and assign permissions to control access.</p>
    </div>
    @if(auth()->user()->hasPermission('create_roles'))
    <a href="{{ route('roles.create') }}" class="btn btn-primary">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      New Role
    </a>
    @endif
  </div>

  {{-- Roles Table --}}
  <div class="card">
    @if($roles->isEmpty())
      <div style="padding:40px; text-align:center; color:var(--muted); font-size:13px;">
        No roles yet. <a href="{{ route('roles.create') }}" style="color:var(--blue);">Create your first role</a>.
      </div>
    @else
      <table>
        <thead>
          <tr>
            <th>Role Name</th>
            <th>Description</th>
            <th style="text-align:center;">Users</th>
            <th style="text-align:right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($roles as $role)
          <tr>
            <td><span class="role-name">{{ $role->name }}</span></td>
            <td>{{ $role->description ?: '–' }}</td>
            <td style="text-align:center;">
              <span class="badge-count">{{ $role->users_count }}</span>
            </td>
            <td>
              <div class="actions-cell">
                @if(auth()->user()->hasPermission('edit_roles'))
                <a href="{{ route('roles.edit', $role) }}">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                  Edit
                </a>
                @endif
                @if(auth()->user()->hasPermission('delete_roles'))
                <form action="{{ route('roles.destroy', $role) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete role {{ $role->name }}? Users will be unassigned.')">
                  @csrf @method('DELETE')
                  <button type="submit">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                    Delete
                  </button>
                </form>
                @endif
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>
</div>
@endsection