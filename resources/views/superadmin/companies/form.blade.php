@extends('layouts.app')
@section('title', (isset($company) ? 'Edit ' . $company->name : 'New Company') . ' – Fixtora')

@section('styles')
<style>
.page-header { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:24px; }
.page-header h1 { font-size:24px; font-weight:800; letter-spacing:-.6px; color:var(--navy); }
.page-header p  { font-size:13px; color:var(--muted); margin-top:4px; }

.breadcrumb { display:flex; align-items:center; gap:6px; font-size:12.5px; color:var(--muted); margin-bottom:16px; }
.breadcrumb a { color:var(--blue); text-decoration:none; }
.breadcrumb a:hover { text-decoration:underline; }

.card { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); max-width:720px; }
.card-header { padding:18px 24px; border-bottom:1px solid var(--border); }
.card-header h2 { font-size:15px; font-weight:700; color:var(--text); }
.card-body { padding:24px; display:flex; flex-direction:column; gap:20px; }

.field { display:flex; flex-direction:column; gap:6px; }
.field label { font-size:12px; font-weight:700; color:var(--text-2); letter-spacing:.3px; text-transform:uppercase; }
.field input[type="text"] { padding:10px 14px; border:1px solid var(--border-2); border-radius:var(--radius-sm); font-size:13.5px; font-family:inherit; color:var(--text); background:var(--surface); outline:none; transition:border-color .12s; }
.field input[type="text"]:focus { border-color:var(--blue); box-shadow:0 0 0 3px rgba(37,99,235,.08); }
.field .hint { font-size:11.5px; color:var(--muted); }
.field .error { font-size:12px; color:var(--red); }

.toggle-row { display:flex; align-items:center; justify-content:space-between; padding:14px 0; border-top:1px solid var(--border); }
.toggle-label { font-size:13.5px; font-weight:600; color:var(--text); }
.toggle-sub { font-size:12px; color:var(--muted); margin-top:2px; }
.toggle-switch { position:relative; width:44px; height:24px; }
.toggle-switch input { opacity:0; width:0; height:0; }
.toggle-slider { position:absolute; inset:0; background:var(--border-2); border-radius:24px; cursor:pointer; transition:background .2s; }
.toggle-slider:before { content:''; position:absolute; width:18px; height:18px; background:#fff; border-radius:50%; top:3px; left:3px; transition:transform .2s; box-shadow:0 1px 3px rgba(0,0,0,.2); }
.toggle-switch input:checked + .toggle-slider { background:var(--green); }
.toggle-switch input:checked + .toggle-slider:before { transform:translateX(20px); }

.footer { display:flex; gap:10px; padding-top:4px; }
.btn { display:inline-flex; align-items:center; gap:6px; padding:9px 18px; border-radius:var(--radius-sm); font-size:13px; font-weight:600; cursor:pointer; border:none; font-family:inherit; text-decoration:none; transition:all .12s; }
.btn-primary { background:var(--blue); color:#fff; }
.btn-primary:hover { background:var(--blue-2); }
.btn-outline { background:transparent; color:var(--text-2); border:1px solid var(--border-2); }
.btn-outline:hover { border-color:var(--blue); color:var(--blue); background:var(--blue-bg); }
.btn-ghost { background:var(--bg); color:var(--text-2); border:1px solid var(--border-2); font-size:12px; padding:6px 12px; border-radius:var(--radius-sm); cursor:pointer; font-family:inherit; font-weight:600; }
.btn-ghost:hover { border-color:var(--blue); color:var(--blue); }
.systems-wrap { display:flex; flex-direction:column; gap:8px; margin-top:4px; }
.system-row { display:flex; gap:8px; align-items:center; }
.system-row input { flex:1; padding:10px 14px; border:1px solid var(--border-2); border-radius:var(--radius-sm); font-size:13.5px; font-family:inherit; }
.system-row input:focus { border-color:var(--blue); outline:none; box-shadow:0 0 0 3px rgba(37,99,235,.08); }
.btn-icon-remove { flex-shrink:0; width:36px; height:36px; border:1px solid var(--border-2); background:var(--surface); border-radius:var(--radius-sm); cursor:pointer; color:var(--muted); font-size:18px; line-height:1; display:flex; align-items:center; justify-content:center; }
.btn-icon-remove:hover { color:var(--red); border-color:#fecaca; background:var(--red-bg); }
</style>
@endsection

@section('content')
<div class="breadcrumb">
  <a href="{{ route('superadmin.dashboard') }}">Super Admin</a>
  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
  <a href="{{ route('superadmin.configuration') }}">Configuration</a>
  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
  <a href="{{ route('superadmin.companies.index') }}">Companies</a>
  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
  {{ isset($company) ? 'Edit ' . $company->name : 'New Company' }}
</div>

<div class="page-header">
  <div>
    <h1>{{ isset($company) ? 'Edit Company' : 'New Company' }}</h1>
    <p>{{ isset($company) ? 'Update plan, slug, or status.' : 'Register a new company on the platform.' }}</p>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h2>{{ isset($company) ? 'Edit: ' . $company->name : 'Company Details' }}</h2>
  </div>
  <div class="card-body">
    @if ($errors->any())
      <div style="background:var(--red-bg); border:1px solid #fecaca; border-radius:var(--radius-sm); padding:12px 16px; font-size:13px; color:var(--red);">
        @foreach($errors->all() as $error)
          <div>• {{ $error }}</div>
        @endforeach
      </div>
    @endif

    <form method="POST"
      action="{{ isset($company) ? route('superadmin.companies.update', $company) : route('superadmin.companies.store') }}">
      @csrf
      @if(isset($company)) @method('PATCH') @endif

      <div class="field">
        <label for="name">Company Name</label>
        <input type="text" id="name" name="name" value="{{ old('name', $company->name ?? '') }}"
          placeholder="e.g. Acme Corp" required>
        @error('name')<div class="error">{{ $message }}</div>@enderror
      </div>

      <div class="field" style="margin-top:16px;">
        <label for="slug">Slug <span style="font-weight:400; text-transform:none; font-size:11px;">(URL-safe identifier)</span></label>
        <input type="text" id="slug" name="slug" value="{{ old('slug', $company->slug ?? '') }}"
          placeholder="e.g. acme-corp" required>
        <span class="hint">Only letters, numbers, dashes and underscores. Must be unique.</span>
        @error('slug')<div class="error">{{ $message }}</div>@enderror
      </div>

      <div class="toggle-row" style="margin-top:8px;">
        <div>
          <div class="toggle-label">Active</div>
          <div class="toggle-sub">Inactive companies cannot log in.</div>
        </div>
        <label class="toggle-switch">
          <input type="checkbox" name="is_active" value="1"
            {{ old('is_active', $company->is_active ?? true) ? 'checked' : '' }}>
          <span class="toggle-slider"></span>
        </label>
      </div>

      <div class="field" style="margin-top:20px; padding-top:20px; border-top:1px solid var(--border);">
        <label>Ticket system names</label>
        <span class="hint">These appear as the <strong>System name</strong> dropdown when creating tickets and assigning from the task board. Add one row per product/system (e.g. CRM Portal, API Gateway).</span>
        @error('systems')<div class="error">{{ $message }}</div>@enderror
        <div id="systems-container" class="systems-wrap" style="margin-top:10px;">
          @php
            $sysRows = old('systems');
            if ($sysRows === null) {
              $sysRows = isset($company) ? ($company->systems ?? []) : [];
            }
            if (!is_array($sysRows)) { $sysRows = []; }
            if (count($sysRows) === 0) { $sysRows = ['']; }
          @endphp
          @foreach($sysRows as $sysVal)
          <div class="system-row">
            <input type="text" name="systems[]" value="{{ $sysVal }}" placeholder="e.g. Payment Gateway" maxlength="255">
            <button type="button" class="btn-icon-remove js-remove-system" title="Remove">&times;</button>
          </div>
          @endforeach
        </div>
        <button type="button" class="btn-ghost" id="btn-add-system" style="margin-top:6px;">+ Add system name</button>
      </div>

      <div class="footer">
        <button type="submit" class="btn btn-primary">
          {{ isset($company) ? 'Save Changes' : 'Create Company' }}
        </button>
        <a href="{{ route('superadmin.companies.index') }}" class="btn btn-outline">Cancel</a>
      </div>
    </form>
  </div>
</div>

<script>
// Auto-generate slug from name on create
@unless(isset($company))
document.getElementById('name').addEventListener('input', function () {
  const slug = this.value.toLowerCase()
    .replace(/[^a-z0-9\s-]/g, '')
    .replace(/\s+/g, '-')
    .replace(/-+/g, '-')
    .replace(/^-|-$/g, '');
  document.getElementById('slug').value = slug;
});
@endunless

document.getElementById('btn-add-system')?.addEventListener('click', function () {
  const container = document.getElementById('systems-container');
  const row = document.createElement('div');
  row.className = 'system-row';
  row.innerHTML = '<input type="text" name="systems[]" value="" placeholder="e.g. Payment Gateway" maxlength="255">' +
    '<button type="button" class="btn-icon-remove js-remove-system" title="Remove">&times;</button>';
  container.appendChild(row);
  row.querySelector('input').focus();
});

document.getElementById('systems-container')?.addEventListener('click', function (e) {
  const btn = e.target.closest('.js-remove-system');
  if (!btn) return;
  const row = btn.closest('.system-row');
  const rows = this.querySelectorAll('.system-row');
  if (rows.length <= 1) {
    const inp = row?.querySelector('input');
    if (inp) inp.value = '';
    return;
  }
  row?.remove();
});
</script>
@endsection