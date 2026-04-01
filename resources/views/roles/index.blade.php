@extends('layouts.app')

@section('title', 'User Roles – Fixtora')

@section('content')
<div style="padding: 28px 32px; max-width: 900px;">

  {{-- Header --}}
  <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px;">
    <div>
      <h1 style="font-size:20px; font-weight:700; color:var(--text); margin-bottom:3px;">User Roles</h1>
      <p style="font-size:13px; color:var(--muted);">Create roles and assign permissions to control access.</p>
    </div>
    <a href="{{ route('roles.create') }}" style="display:inline-flex; align-items:center; gap:6px; background:var(--navy); color:#fff; padding:9px 16px; border-radius:var(--radius-sm); font-size:13px; font-weight:600; text-decoration:none;">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      New Role
    </a>
  </div>

  {{-- Flash --}}
  @if(session('success'))
    <div style="background:var(--green-bg); border:1px solid #bbf7d0; color:var(--green); padding:10px 14px; border-radius:var(--radius-sm); font-size:13px; margin-bottom:18px;">
      {{ session('success') }}
    </div>
  @endif

  {{-- Roles Table --}}
  <div style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); overflow:hidden; box-shadow:var(--shadow-sm);">
    @if($roles->isEmpty())
      <div style="padding:40px; text-align:center; color:var(--muted); font-size:13px;">
        No roles yet. <a href="{{ route('roles.create') }}" style="color:var(--blue);">Create your first role</a>.
      </div>
    @else
      <table style="width:100%; border-collapse:collapse;">
        <thead>
          <tr style="border-bottom:1px solid var(--border); background:#f8fafc;">
            <th style="padding:11px 16px; text-align:left; font-size:11.5px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.04em;">Role Name</th>
            <th style="padding:11px 16px; text-align:left; font-size:11.5px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.04em;">Description</th>
            <th style="padding:11px 16px; text-align:center; font-size:11.5px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.04em;">Users</th>
            <th style="padding:11px 16px; text-align:right; font-size:11.5px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.04em;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($roles as $role)
          <tr style="border-bottom:1px solid var(--border);">
            <td style="padding:13px 16px; font-weight:600; font-size:13.5px; color:var(--text);">{{ $role->name }}</td>
            <td style="padding:13px 16px; font-size:13px; color:var(--muted);">{{ $role->description ?: '–' }}</td>
            <td style="padding:13px 16px; text-align:center;">
              <span style="background:var(--blue-bg); color:var(--blue); font-size:12px; font-weight:600; padding:3px 10px; border-radius:20px;">{{ $role->users_count }}</span>
            </td>
            <td style="padding:13px 16px; text-align:right;">
              <a href="{{ route('roles.edit', $role) }}" style="display:inline-flex; align-items:center; gap:5px; font-size:12.5px; color:var(--blue); font-weight:600; text-decoration:none; padding:5px 12px; border:1px solid var(--border); border-radius:var(--radius-sm);">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Edit
              </a>
              <form action="{{ route('roles.destroy', $role) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete role {{ $role->name }}? Users will be unassigned.')">
                @csrf @method('DELETE')
                <button type="submit" style="display:inline-flex; align-items:center; gap:5px; font-size:12.5px; color:var(--red); font-weight:600; background:none; border:1px solid var(--border); border-radius:var(--radius-sm); padding:5px 12px; cursor:pointer; margin-left:6px;">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                  Delete
                </button>
              </form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>
</div>
@endsection