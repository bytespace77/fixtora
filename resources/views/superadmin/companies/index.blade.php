@extends('layouts.app')
@section('title', 'Manage Companies – Fixtora')

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

.card { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); }
.card-header { display:flex; align-items:center; justify-content:space-between; padding:18px 22px; border-bottom:1px solid var(--border); }
.card-header h2 { font-size:15px; font-weight:700; color:var(--text); }

table { width:100%; border-collapse:collapse; }
thead th { padding:10px 16px; text-align:left; font-size:11px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.4px; border-bottom:1px solid var(--border); background:var(--bg); }
tbody td { padding:14px 16px; font-size:13px; color:var(--text-2); border-bottom:1px solid var(--border); vertical-align:middle; }
tbody tr:last-child td { border-bottom:none; }
tbody tr:hover td { background:#fafbfd; }

.company-name { font-weight:700; color:var(--text); font-size:13.5px; }
.company-slug { font-size:11px; color:var(--muted); font-family:monospace; margin-top:2px; }
.sys-count { font-size:11px; font-weight:700; color:var(--text-2); }
.sys-preview { font-size:10px; color:var(--muted); margin-top:4px; max-width:280px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }

.badge { display:inline-block; padding:3px 9px; border-radius:20px; font-size:11px; font-weight:700; }
.badge-active   { background:var(--green-bg); color:var(--green); }
.badge-inactive { background:var(--red-bg); color:var(--red); }

.actions-cell { display:flex; gap:6px; flex-wrap:wrap; }

.alert { padding:12px 18px; border-radius:var(--radius-sm); margin-bottom:18px; font-size:13px; font-weight:500; }
.alert-success { background:var(--green-bg); color:var(--green); border:1px solid #bbf7d0; }
.alert-error   { background:var(--red-bg); color:var(--red); border:1px solid #fecaca; }

.breadcrumb { display:flex; align-items:center; gap:6px; font-size:12.5px; color:var(--muted); margin-bottom:16px; }
.breadcrumb a { color:var(--blue); text-decoration:none; }
.breadcrumb a:hover { text-decoration:underline; }
</style>
@endsection

@section('content')

<div class="page-header">
  <div>
    <h1>Manage Companies</h1>
    <p>List companies, edit details, and set <strong>ticket system names</strong> per company (Configuration → edit company).</p>
  </div>
  <a href="{{ route('superadmin.companies.create') }}" class="btn btn-primary">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    New Company
  </a>
</div>


<div class="card">
  <div class="card-header">
    <h2>All Companies ({{ $companies->total() }})</h2>
  </div>

  @if($companies->isEmpty())
    <div style="padding:40px; text-align:center; color:var(--muted);">
      No companies yet. <a href="{{ route('superadmin.companies.create') }}" style="color:var(--blue);">Create your first company →</a>
    </div>
  @else
  <table>
    <thead>
      <tr>
        <th>Company</th>
        <th>Systems</th>
        <th>Status</th>
        <th>Users</th>
        <th>Tickets</th>
        <th>Created</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      @foreach($companies as $company)
      <tr>
        <td>
          <div class="company-name">{{ $company->name }}</div>
          <div class="company-slug">{{ $company->slug }}</div>
        </td>
        <td>
          @php $syss = is_array($company->systems) ? $company->systems : []; @endphp
          <span class="sys-count">{{ count($syss) }} {{ count($syss) === 1 ? 'system' : 'systems' }}</span>
          @if(count($syss))
            <div class="sys-preview" title="{{ implode(', ', $syss) }}">{{ implode(', ', $syss) }}</div>
          @else
            <div class="sys-preview" style="color:var(--muted-lt)">None — edit company</div>
          @endif
        </td>
        <td>
          @if($company->is_active)
            <span class="badge badge-active">Active</span>
          @else
            <span class="badge badge-inactive">Inactive</span>
          @endif
        </td>
        <td><strong>{{ $company->users_count }}</strong></td>
        <td><strong>{{ $company->tickets_count }}</strong></td>
        <td>{{ $company->created_at->format('d M Y') }}</td>
        <td>
          <div class="actions-cell">
            <a href="{{ route('superadmin.companies.show', $company) }}" class="btn btn-outline btn-sm">View</a>
            <a href="{{ route('superadmin.companies.edit', $company) }}" class="btn btn-outline btn-sm">Edit</a>
            <form method="POST" action="{{ route('superadmin.companies.toggle', $company) }}" style="display:inline;">
              @csrf @method('PATCH')
              <button type="submit" class="btn btn-sm {{ $company->is_active ? 'btn-danger' : 'btn-outline' }}"
                onclick="return confirm('{{ $company->is_active ? 'Deactivate' : 'Activate' }} {{ $company->name }}?')">
                {{ $company->is_active ? 'Deactivate' : 'Activate' }}
              </button>
            </form>
          </div>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>

  @if($companies->hasPages())
  <div style="padding:16px 22px; border-top:1px solid var(--border);">
    {{ $companies->links() }}
  </div>
  @endif
  @endif
</div>
@endsection