@extends('layouts.app')

@section('title', 'Create Role – Fixtora')

@section('content')
<div style="padding: 28px 32px; max-width: 800px;">

  {{-- Breadcrumb --}}
  <div style="display:flex; align-items:center; gap:8px; margin-bottom:20px; font-size:13px; color:var(--muted);">
    <a href="{{ route('roles.index') }}" style="color:var(--blue); text-decoration:none;">User Roles</a>
    <span>›</span>
    <span>New Role</span>
  </div>

  <h1 style="font-size:20px; font-weight:700; color:var(--text); margin-bottom:24px;">Create New Role</h1>

  @if($errors->any())
    <div style="background:var(--red-bg); border:1px solid #fecaca; color:var(--red); padding:10px 14px; border-radius:var(--radius-sm); font-size:13px; margin-bottom:18px;">
      @foreach($errors->all() as $error) {{ $error }}<br> @endforeach
    </div>
  @endif

  <form action="{{ route('roles.store') }}" method="POST">
    @csrf

    {{-- Role Details --}}
    <div style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); padding:24px; margin-bottom:20px; box-shadow:var(--shadow-sm);">
      <h2 style="font-size:14px; font-weight:700; margin-bottom:16px; color:var(--text);">Role Details</h2>

      <div style="margin-bottom:14px;">
        <label style="display:block; font-size:12.5px; font-weight:600; color:var(--text-2); margin-bottom:5px;">Role Name *</label>
        <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Administrator, Support Agent"
          style="width:100%; padding:9px 12px; border:1px solid var(--border); border-radius:var(--radius-sm); font-size:13px; font-family:inherit; color:var(--text); outline:none;"
          onfocus="this.style.borderColor='var(--blue)'" onblur="this.style.borderColor='var(--border)'">
      </div>

      <div>
        <label style="display:block; font-size:12.5px; font-weight:600; color:var(--text-2); margin-bottom:5px;">Description</label>
        <textarea name="description" rows="2" placeholder="Optional description of this role..."
          style="width:100%; padding:9px 12px; border:1px solid var(--border); border-radius:var(--radius-sm); font-size:13px; font-family:inherit; color:var(--text); resize:vertical; outline:none;"
          onfocus="this.style.borderColor='var(--blue)'" onblur="this.style.borderColor='var(--border)'">{{ old('description') }}</textarea>
      </div>
    </div>

    {{-- Permissions --}}
    <div style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); padding:24px; margin-bottom:20px; box-shadow:var(--shadow-sm);">
      <h2 style="font-size:14px; font-weight:700; margin-bottom:16px; color:var(--text);">Permissions</h2>

      @foreach($permissions as $group => $perms)
        <div style="margin-bottom:20px;">
          <div style="display:flex; align-items:center; gap:12px; margin-bottom:10px;">
            <span style="font-size:13px; font-weight:700; color:var(--text-2);">{{ $group }}</span>
            <button type="button" onclick="toggleGroup(this, '{{ Str::slug($group) }}')"
              style="font-size:11px; color:var(--blue); background:none; border:none; cursor:pointer; font-weight:600;">Select all</button>
            <button type="button" onclick="toggleGroup(this, '{{ Str::slug($group) }}', false)"
              style="font-size:11px; color:var(--muted); background:none; border:none; cursor:pointer; font-weight:600;">Select none</button>
          </div>
          <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:8px;" class="perm-group-{{ Str::slug($group) }}">
            @foreach($perms as $perm)
              <label style="display:flex; align-items:center; gap:7px; font-size:12.5px; color:var(--text-2); cursor:pointer;">
                <input type="checkbox" name="permissions[]" value="{{ $perm }}"
                  {{ in_array($perm, old('permissions', [])) ? 'checked' : '' }}
                  class="perm-{{ Str::slug($group) }}"
                  style="width:14px; height:14px; accent-color:var(--blue); cursor:pointer;">
                {{ $perm }}
              </label>
            @endforeach
          </div>
        </div>
        @if(!$loop->last)
          <hr style="border:none; border-top:1px solid var(--border); margin-bottom:20px;">
        @endif
      @endforeach
    </div>

    {{-- Buttons --}}
    <div style="display:flex; gap:10px;">
      <button type="submit" style="background:var(--navy); color:#fff; padding:10px 20px; border-radius:var(--radius-sm); font-size:13px; font-weight:600; border:none; cursor:pointer;">
        Create Role
      </button>
      <a href="{{ route('roles.index') }}" style="padding:10px 20px; border-radius:var(--radius-sm); font-size:13px; font-weight:600; border:1px solid var(--border); color:var(--text-2); text-decoration:none; background:var(--surface);">
        Go back
      </a>
    </div>
  </form>
</div>

<script>
function toggleGroup(btn, group, selectAll) {
  const checkboxes = document.querySelectorAll('.perm-' + group);
  const doSelect = selectAll !== undefined ? selectAll : btn.textContent.includes('Select all');
  checkboxes.forEach(cb => cb.checked = doSelect);
}
</script>
@endsection