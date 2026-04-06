@extends('layouts.app')
@section('title', 'Tickets – Fixtora')

@section('styles')
<style>
.page-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px}
.page-header h1{font-size:22px;font-weight:800;letter-spacing:-.5px;color:var(--navy)}
.page-header p{font-size:13px;color:var(--muted);margin-top:4px}
.hdr-btns{display:flex;gap:8px;align-items:center}
.btn-sm{padding:8px 14px;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:6px;border:1px solid var(--border);background:var(--surface);color:#475569;font-family:inherit;transition:all .15s;text-decoration:none}
.btn-sm:hover{background:var(--bg)}
.btn-primary{background:var(--blue);color:#fff;border-color:var(--blue)}
.btn-primary:hover{background:#1a42c4;color:#fff}
.btn-danger{background:#ef4444;color:#fff;border-color:#ef4444}
.btn-danger:hover{background:#dc2626;color:#fff}
/* Filter Panel */
.filter-panel{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);box-shadow:0 8px 24px rgba(0,0,0,.08);margin-bottom:18px;overflow:hidden;display:none}
.filter-panel.open{display:block}
.filter-panel-head{display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid var(--border)}
.filter-panel-head h3{font-size:13px;font-weight:800;color:var(--navy);display:flex;align-items:center;gap:7px}
.filter-panel-body{padding:20px;display:grid;grid-template-columns:repeat(4,1fr);gap:24px}
.filter-group .group-label{display:block;font-size:10px;font-weight:800;letter-spacing:.8px;text-transform:uppercase;color:var(--muted);margin-bottom:10px}
.filter-check{display:flex;align-items:center;gap:8px;margin-bottom:7px;cursor:pointer}
.filter-check input{width:14px;height:14px;accent-color:var(--blue)}
.filter-check span{font-size:12.5px;font-weight:500;color:var(--text)}
.filter-date-lbl{display:block;font-size:11px;color:var(--muted);font-weight:600;margin-bottom:4px}
.filter-date-input{width:100%;padding:7px 10px;border:1px solid var(--border);border-radius:7px;font-size:12px;font-family:inherit;outline:none;margin-bottom:8px;background:var(--bg)}
.filter-foot{display:flex;justify-content:flex-end;gap:8px;padding:12px 20px;border-top:1px solid var(--border);background:var(--bg)}
.link-btn{background:none;border:none;font-size:12px;font-weight:700;color:var(--muted);cursor:pointer;font-family:inherit;padding:4px 8px;border-radius:6px}
.link-btn:hover{color:var(--blue)}
/* Tabs */
.filter-row{display:flex;align-items:center;gap:6px;margin-bottom:16px;flex-wrap:wrap}
.filter-tab{padding:7px 14px;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;border:1px solid var(--border);background:var(--surface);color:var(--muted);font-family:inherit;transition:all .15s;text-decoration:none;display:inline-flex;align-items:center;gap:5px}
.filter-tab:hover{background:var(--bg);color:var(--text)}
.filter-tab.active{background:var(--navy);color:#fff;border-color:var(--navy)}
.tab-count{font-size:10px;font-weight:800;padding:1px 7px;border-radius:20px;background:rgba(255,255,255,.2)}
.filter-tab:not(.active) .tab-count{background:var(--bg);color:var(--muted)}
/* Table */
.ticket-table{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);overflow:visible;box-shadow:var(--shadow)}
@if(Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('select_system'))
.tt-header{display:grid;grid-template-columns:90px 1fr 160px 120px 130px 90px 44px;gap:12px;padding:11px 18px;background:var(--bg);border-bottom:1px solid var(--border);font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);border-radius:var(--radius) var(--radius) 0 0;}
.tt-row{display:grid;grid-template-columns:90px 1fr 160px 120px 130px 90px 44px;gap:12px;padding:14px 18px;border-bottom:1px solid var(--border);align-items:center;transition:background .12s;text-decoration:none;color:inherit;cursor:pointer}
@else
.tt-header{display:grid;grid-template-columns:90px 1fr 120px 130px 90px 44px;gap:12px;padding:11px 18px;background:var(--bg);border-bottom:1px solid var(--border);font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);border-radius:var(--radius) var(--radius) 0 0;}
.tt-row{display:grid;grid-template-columns:90px 1fr 120px 130px 90px 44px;gap:12px;padding:14px 18px;border-bottom:1px solid var(--border);align-items:center;transition:background .12s;text-decoration:none;color:inherit;cursor:pointer}
@endif
.tt-row:last-child{border-bottom:none}
.tt-row:hover{background:#f7f9ff}
.tt-id{font-size:11.5px;font-weight:700;color:var(--muted);font-variant-numeric:tabular-nums}
.tt-name{font-size:13px;font-weight:600;color:var(--text);margin-bottom:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.tt-sub{font-size:11.5px;color:var(--muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.tt-sys{font-size:12.5px;font-weight:600;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.tt-time{font-size:12px;color:var(--muted-lt);font-weight:500}
/* Pills */
.pill{display:inline-block;padding:3px 10px;border-radius:20px;font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.4px}
.pill-critical{background:#fee2e2;color:#dc2626;border:1px solid #fecaca}
.pill-high{background:#fff7ed;color:#f97316;border:1px solid #fed7aa}
.pill-medium{background:#eff6ff;color:#2563eb;border:1px solid #dbeafe}
.pill-low{background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0}
.pill-open{background:#fff7ed;color:#f97316;border:1px solid #fed7aa}
.pill-resolved{background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0}
.pill-in_progress,.pill-in-progress{background:#eff6ff;color:#2563eb;border:1px solid #dbeafe}
.pill-in_review{background:#fdf4ff;color:#c026d3;border:1px solid #fae8ff}
.pill-closed{background:var(--bg);color:var(--muted);border:1px solid var(--border)}
/* 3-dot */
.more-wrap{position:relative;display:flex;justify-content:center}
.more-btn{border:none;background:none;cursor:pointer;color:var(--muted-lt);padding:5px 7px;border-radius:7px;font-size:18px;line-height:1;opacity:0;transition:opacity .12s,background .12s}
.tt-row:hover .more-btn,.more-btn:focus,.more-btn.active{opacity:1}
.more-btn:hover{background:var(--bg);color:var(--text)}
.dropdown-menu{display:none;position:absolute;right:0;top:calc(100% + 4px);width:170px;background:var(--surface);border:1px solid var(--border);border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.14);z-index:999;overflow:hidden;padding:4px}
.dropdown-menu.open{display:block;animation:dropFade .15s ease}
@keyframes dropFade{from{opacity:0;transform:translateY(-4px)}to{opacity:1;transform:translateY(0)}}
.dd-item{display:flex;align-items:center;gap:9px;padding:9px 12px;font-size:12.5px;font-weight:600;color:var(--text);text-decoration:none;border-radius:7px;cursor:pointer;border:none;background:none;font-family:inherit;width:100%;text-align:left;transition:background .1s}
.dd-item:hover{background:var(--bg)}
.dd-item svg{color:var(--muted);flex-shrink:0}
.dd-item.danger{color:#ef4444}
.dd-item.danger:hover{background:#fff5f5}
.dd-item.danger svg{color:#ef4444}
.dd-sep{height:1px;background:var(--border);margin:4px 0}
/* Pagination */
.pagination-wrap{display:flex;justify-content:flex-end;padding:14px 18px;border-top:1px solid var(--border);gap:4px}
.page-btn{padding:6px 12px;border-radius:7px;font-size:12px;font-weight:600;border:1px solid var(--border);background:var(--surface);color:var(--text);cursor:pointer;text-decoration:none;font-family:inherit;transition:all .12s}
.page-btn:hover{background:var(--bg)}
.page-btn.active-pg{background:var(--navy);color:#fff;border-color:var(--navy)}
.page-btn.disabled{opacity:.4;pointer-events:none;cursor:default}
/* Empty */
.empty-state{text-align:center;padding:60px 20px;color:var(--muted)}
.empty-state h3{font-size:16px;font-weight:700;color:var(--text);margin-bottom:6px;margin-top:14px}
.empty-state p{font-size:13px;margin-bottom:18px}
/* Modal */
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);backdrop-filter:blur(3px);z-index:700;align-items:center;justify-content:center;padding:20px}
.modal-overlay.open{display:flex;animation:fadeIn .2s ease}
@keyframes fadeIn{from{opacity:0}to{opacity:1}}
.modal-box{background:var(--surface);border-radius:14px;width:100%;max-width:520px;box-shadow:0 20px 60px rgba(0,0,0,.18);overflow:hidden;animation:slideUp .2s ease}
@keyframes slideUp{from{transform:translateY(12px);opacity:0}to{transform:translateY(0);opacity:1}}
.modal-head{display:flex;align-items:center;justify-content:space-between;padding:18px 22px;border-bottom:1px solid var(--border)}
.modal-head h3{font-size:16px;font-weight:800;color:var(--navy)}
.modal-close{width:30px;height:30px;border:none;background:none;cursor:pointer;border-radius:7px;display:flex;align-items:center;justify-content:center;color:var(--muted);transition:all .12s;font-size:18px;line-height:1}
.modal-close:hover{background:var(--bg);color:var(--text)}
.modal-body{padding:22px;display:flex;flex-direction:column;gap:16px}
.form-group label{display:block;font-size:10.5px;font-weight:800;letter-spacing:.6px;text-transform:uppercase;color:var(--muted);margin-bottom:6px}
.form-control{width:100%;padding:10px 13px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-family:inherit;outline:none;background:var(--surface);color:var(--text);transition:all .15s}
.form-control:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(37,99,235,.08)}
.form-control.has-error{border-color:#ef4444;box-shadow:0 0 0 3px rgba(239,68,68,.08)}
textarea.form-control{resize:vertical;min-height:80px}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.modal-foot{display:flex;align-items:center;justify-content:flex-end;gap:10px;padding:16px 22px;border-top:1px solid var(--border);background:var(--bg)}
/* Delete modal */
.del-modal-box{background:var(--surface);border-radius:14px;width:100%;max-width:380px;box-shadow:0 20px 60px rgba(0,0,0,.18);overflow:hidden;animation:slideUp .2s ease}
.del-icon{width:56px;height:56px;border-radius:50%;background:#fee2e2;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;color:#ef4444}
.del-modal-body{padding:28px 24px 10px;text-align:center}
.del-modal-body h3{font-size:17px;font-weight:800;color:var(--navy);margin-bottom:8px}
.del-modal-body p{font-size:13px;color:var(--muted);line-height:1.6}
.del-modal-foot{display:flex;gap:10px;padding:18px 24px}
/* Toast */
#toast{position:fixed;bottom:24px;right:24px;z-index:900;background:var(--navy);color:#fff;padding:12px 18px;border-radius:10px;font-size:13px;font-weight:700;display:flex;align-items:center;gap:10px;box-shadow:0 8px 24px rgba(0,0,0,.2);transform:translateY(16px);opacity:0;pointer-events:none;transition:all .25s ease}
#toast.show{transform:translateY(0);opacity:1}
</style>
@endsection

@section('content')
<div class="page-header">
  <div>
    <h1>Ticket Management</h1>
    <p>Track and resolve all architectural support tickets.</p>
  </div>
  <div class="hdr-btns">
    <button onclick="toggleFilter()" id="filterBtn" class="btn-sm">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
      Filter
    </button>
    @if(Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('create_tickets'))
    <button onclick="openModal('new')" class="btn-sm btn-primary">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      New Ticket
    </button>
    @endif
  </div>
</div>

<!-- Filter Panel -->
<div class="filter-panel" id="filterPanel">
  <div class="filter-panel-head">
    <h3>
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="11" y1="18" x2="13" y2="18"/></svg>
      Filter Tickets
    </h3>
    <button class="link-btn" onclick="clearFilters()">Clear all</button>
  </div>
  <div class="filter-panel-body">
    <div class="filter-group">
      <span class="group-label">Status</span>
      <label class="filter-check"><input type="checkbox" class="filter-status" value="all" checked><span>All</span></label>
      <label class="filter-check"><input type="checkbox" class="filter-status" value="open"><span>Open</span></label>
      <label class="filter-check"><input type="checkbox" class="filter-status" value="in_progress"><span>In Progress</span></label>
      <label class="filter-check"><input type="checkbox" class="filter-status" value="in_review"><span>In Review</span></label>
      <label class="filter-check"><input type="checkbox" class="filter-status" value="resolved"><span>Resolved</span></label>
      <label class="filter-check"><input type="checkbox" class="filter-status" value="closed"><span>Closed</span></label>
    </div>
    <div class="filter-group">
      <span class="group-label">Priority</span>
      <label class="filter-check"><input type="checkbox" class="filter-priority" value="all" checked><span>All</span></label>
      <label class="filter-check"><input type="checkbox" class="filter-priority" value="low"><span>Low</span></label>
      <label class="filter-check"><input type="checkbox" class="filter-priority" value="medium"><span>Medium</span></label>
      <label class="filter-check"><input type="checkbox" class="filter-priority" value="high"><span>High</span></label>
      <label class="filter-check"><input type="checkbox" class="filter-priority" value="critical"><span>Critical</span></label>
    </div>
    <div class="filter-group">
      <span class="group-label">System (Company)</span>
      <label class="filter-check"><input type="checkbox" class="filter-system" value="all" checked><span>All Systems</span></label>
      @php $filterCompanies = \App\Models\Company::where('is_active', true)->orderBy('name')->pluck('name'); @endphp
      @foreach($filterCompanies as $fc)
      <label class="filter-check"><input type="checkbox" class="filter-system" value="{{ $fc }}"><span>{{ $fc }}</span></label>
      @endforeach
    </div>
    <div class="filter-group">
      <span class="group-label">Date Created</span>
      <label class="filter-date-lbl">From</label>
      <input type="date" class="filter-date-input">
      <label class="filter-date-lbl">To</label>
      <input type="date" class="filter-date-input">
    </div>
  </div>
  <div class="filter-foot">
    <button class="link-btn" onclick="toggleFilter()">Cancel</button>
    <button class="btn-sm btn-primary" onclick="applyFilters()">Apply Filters</button>
  </div>
</div>

<!-- Status Tabs -->
<div class="filter-row">
  <a href="{{ route('tickets.index') }}" class="filter-tab {{ !request('status') && !request('status[]') ? 'active' : '' }}">
    All Tickets <span class="tab-count">{{ $tickets->total() }}</span>
  </a>
  @foreach(['open'=>'Open','in_progress'=>'In Progress','in_review'=>'In Review','resolved'=>'Resolved','closed'=>'Closed'] as $val=>$label)
  <a href="{{ route('tickets.index', ['status[]' => [$val]]) }}" class="filter-tab {{ (request('status[]') === [$val] || request('status') === $val) ? 'active' : '' }}">{{ $label }}</a>
  @endforeach
</div>

<!-- Ticket Table -->
<div class="ticket-table">
  <div class="tt-header">
    <span>Ticket ID</span><span>Title</span>@if(Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('select_system'))<span>Company Name</span>@endif<span>Priority</span><span>Status</span><span>Created</span><span></span>
  </div>

  @forelse($tickets as $ticket)
  <div class="tt-row" onclick="window.location='{{ route('tickets.show', $ticket) }}'">
    <div class="tt-id">#TK-{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}</div>
    <div style="min-width:0">
      <div class="tt-name" style="margin-bottom:0">{{ $ticket->title }}</div>
    </div>
    @if(Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('select_system'))
    <div class="tt-sys" title="{{ $ticket->system }}">{{ $ticket->system ?? '—' }}</div>
    @endif
    <div><span class="pill pill-{{ $ticket->priority }}">{{ ucfirst($ticket->priority) }}</span></div>
    <div><span class="pill pill-{{ $ticket->status }}">{{ ucfirst(str_replace('_',' ',$ticket->status)) }}</span></div>
    <div class="tt-time">{{ $ticket->created_at->format('M d') }}</div>
    <div class="more-wrap" onclick="event.stopPropagation()">
      <button class="more-btn" onclick="toggleDropdown('dd-{{ $ticket->id }}',this)">⋯</button>
      <div class="dropdown-menu" id="dd-{{ $ticket->id }}">
        @if(Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('view_tickets'))
        <a href="{{ route('tickets.show', $ticket) }}" class="dd-item">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
          View Details
        </a>
        @endif
        @if(Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('edit_tickets'))
        <button class="dd-item" onclick="openModal('edit',{{ $ticket->id }},'{{ addslashes($ticket->title) }}','{{ addslashes($ticket->description) }}','{{ $ticket->system }}','{{ $ticket->priority }}','{{ $ticket->impact }}','{{ $ticket->status }}','{{ $ticket->due_date ? \Carbon\Carbon::parse($ticket->due_date)->format("Y-m-d") : "" }}')">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
          Edit Ticket
        </button>
        @endif
        @if(Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('delete_tickets'))
        <div class="dd-sep"></div>
        <button class="dd-item danger" onclick="confirmDelete({{ $ticket->id }},'#TK-{{ str_pad($ticket->id,4,'0',STR_PAD_LEFT) }}')">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
          Delete Ticket
        </button>
        @endif
      </div>
    </div>
  </div>
  @empty
  <div class="empty-state">
    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" style="margin:0 auto;display:block;opacity:.18"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
    <h3>No tickets found</h3>
    @php
      $_activeStatuses = array_filter((array)(request('status[]') ?? request('status') ?? []), fn($s) => $s !== 'all');
    @endphp
    <p>{{ count($_activeStatuses) ? 'No '.implode(', ', array_map(fn($s) => str_replace('_',' ',$s), $_activeStatuses)).' tickets found.' : 'You have not created any tickets yet.' }}</p>
    @if(Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('create_tickets'))
    <button onclick="openModal('new')" class="btn-sm btn-primary" style="display:inline-flex;margin:0 auto">+ Create First Ticket</button>
    @endif
  </div>
  @endforelse

  @if($tickets->hasPages())
  <div class="pagination-wrap">
    @if($tickets->onFirstPage())
      <span class="page-btn disabled">← Prev</span>
    @else
      <a href="{{ $tickets->previousPageUrl() }}" class="page-btn">← Prev</a>
    @endif
    @foreach($tickets->getUrlRange(1,$tickets->lastPage()) as $page=>$url)
      <a href="{{ $url }}" class="page-btn {{ $page==$tickets->currentPage()?'active-pg':'' }}">{{ $page }}</a>
    @endforeach
    @if($tickets->hasMorePages())
      <a href="{{ $tickets->nextPageUrl() }}" class="page-btn">Next →</a>
    @else
      <span class="page-btn disabled">Next →</span>
    @endif
  </div>
  @endif
</div>

<!-- Create / Edit Modal -->
<div class="modal-overlay" id="ticketModal" onclick="closeModal()">
  <div class="modal-box" onclick="event.stopPropagation()">
    <div class="modal-head">
      <h3 id="modalHeadTitle">New Ticket</h3>
      <button class="modal-close" onclick="closeModal()">✕</button>
    </div>
    <form id="ticketForm" method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="_method" id="formMethod" value="POST">
      <input type="hidden" id="formTicketId" value="">
      <div class="modal-body">
        <div class="form-group">
          <label>Title *</label>
          <input type="text" name="title" id="fTitle" class="form-control" placeholder="Brief description of the issue" required>
        </div>
        <div class="form-group">
          <label>Description *</label>
          <textarea name="description" id="fDesc" class="form-control" placeholder="Detailed description..." required></textarea>
        </div>
        <div class="form-row">
          @if(auth()->user()->isSuperAdmin())
          @php $modalCompanies = \App\Models\Company::where('is_active', true)->orderBy('name')->pluck('name'); @endphp
          <div class="form-group">
            <label>System (Company)</label>
            <select name="system" id="fSystem" class="form-control">
              <option value="">Select Company</option>
              @foreach($modalCompanies as $sys)
              <option value="{{ $sys }}">{{ $sys }}</option>
              @endforeach
            </select>
          </div>
          @else
          <div class="form-group">
            <label>System</label>
            @if(!empty($companySystems))
              <select name="system" id="fSystem" class="form-control">
                <option value="">Select System</option>
                @foreach($companySystems as $sys)
                  <option value="{{ $sys }}">{{ $sys }}</option>
                @endforeach
              </select>
            @else
              <input type="text" id="fSystemText" class="form-control" value="{{ auth()->user()->company->name ?? 'Unknown Company' }}" disabled />
              <input type="hidden" name="system" id="fSystem" value="{{ auth()->user()->company->name ?? 'Unknown Company' }}" />
            @endif
          </div>
          @endif
          <div class="form-group">
            <label>Priority</label>
            <select name="priority" id="fPriority" class="form-control">
              <option value="low">Low</option><option value="medium" selected>Medium</option><option value="high">High</option><option value="critical">Critical</option>
            </select>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Impact</label>
            <select name="impact" id="fImpact" class="form-control">
              <option value="low" selected>Low</option><option value="medium">Medium</option><option value="high">High</option><option value="critical">Critical</option>
            </select>
          </div>
          <div class="form-group" id="statusGroup">
            <label>Status</label>
            <select name="status" id="fStatus" class="form-control">
              <option value="open">Open</option><option value="in_progress">In Progress</option><option value="in_review">In Review</option><option value="resolved">Resolved</option><option value="closed">Closed</option>
            </select>
          </div>
        </div>
        <div class="form-group" style="margin-top: 14px;">
          <label>Due Date</label>
          <input type="date" name="due_date" id="fDueDate" class="form-control">
        </div>
        <div class="form-group" id="attachmentGroup">
          <label>Attachments <span style="font-weight:500;text-transform:none;letter-spacing:0;color:var(--muted)">(optional · JPG, PNG, LOG, JSON, ZIP · max 25MB)</span></label>
          <label for="modal_file_upload" style="display:block;cursor:pointer;">
            <div id="modal_dropzone" style="border:2px dashed var(--border);border-radius:8px;padding:14px 16px;text-align:center;transition:all .15s;">
              <div style="font-size:18px;margin-bottom:4px;">📎</div>
              <div style="font-size:12px;font-weight:600;color:var(--text-sub);margin-bottom:2px;" id="modal_dz_title">Drag &amp; drop files or <span style="color:var(--blue)">browse</span></div>
              <div style="font-size:11px;color:var(--muted);">JPG, PNG, LOG, JSON, ZIP · max 25MB each</div>
            </div>
          </label>
          <input type="file" id="modal_file_upload" name="attachments[]" multiple
                 accept=".jpg,.jpeg,.png,.log,.json,.zip" style="display:none;"
                 onchange="handleModalFiles(this.files); this.value='';">
          <div id="modal_file_list" style="margin-top:6px;font-size:11px;line-height:1.8;"></div>
        </div>
      </div>
      <div class="modal-foot">
        <button type="button" onclick="closeModal()" class="btn-sm">Cancel</button>
        <button type="button" id="modalSaveBtn" class="btn-sm btn-primary" onclick="submitTicketForm()">Create Ticket</button>
      </div>
    </form>
  </div>
</div>

<!-- Delete Confirm Modal -->
<div class="modal-overlay" id="deleteModal" onclick="closeDeleteModal()">
  <div class="del-modal-box" onclick="event.stopPropagation()">
    <div class="del-modal-body">
      <div class="del-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
      </div>
      <h3>Delete Ticket?</h3>
      <p>Ticket <strong id="deleteLabel"></strong> will be permanently deleted.<br>This action cannot be undone.</p>
    </div>
    <div class="del-modal-foot">
      <button onclick="closeDeleteModal()" class="btn-sm" style="flex:1;justify-content:center">Cancel</button>
      <button id="deleteConfirmBtn" onclick="doDeleteTicket()" class="btn-sm btn-danger" style="flex:1;justify-content:center">Delete</button>
    </div>
  </div>
</div>

<meta name="csrf-token" content="{{ csrf_token() }}">
<div id="toast">
  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
  <span id="toastMsg"></span>
</div>

<script>
// Dropdown
function toggleDropdown(id,btn){
  document.querySelectorAll('.dropdown-menu').forEach(m=>{if(m.id!==id){m.classList.remove('open');}});
  document.querySelectorAll('.more-btn').forEach(b=>b.classList.remove('active'));
  const menu=document.getElementById(id);
  menu.classList.toggle('open');
  if(menu.classList.contains('open'))btn.classList.add('active');
}
document.addEventListener('click',()=>{
  document.querySelectorAll('.dropdown-menu').forEach(m=>m.classList.remove('open'));
  document.querySelectorAll('.more-btn').forEach(b=>b.classList.remove('active'));
});

// Filter
function toggleFilter(){document.getElementById('filterPanel').classList.toggle('open');}

function clearFilters(){
  document.querySelectorAll('.filter-status').forEach(el=>{ el.checked = (el.value==='all'); });
  document.querySelectorAll('.filter-priority').forEach(el=>{ el.checked = (el.value==='all'); });
  document.querySelectorAll('.filter-system').forEach(el=>{ el.checked = false; });
  document.querySelectorAll('.filter-date-input').forEach(el=>{ el.value=''; });
}

function applyFilters(){
  const params = new URLSearchParams();

  // Status — collect checked values, skip "all"
  const statuses = [...document.querySelectorAll('.filter-status:checked')].map(el=>el.value).filter(v=>v!=='all');
  statuses.forEach(s => params.append('status[]', s));

  // Priority
  const priorities = [...document.querySelectorAll('.filter-priority:checked')].map(el=>el.value).filter(v=>v!=='all');
  priorities.forEach(p => params.append('priority[]', p));

  // System (company)
  const systems = [...document.querySelectorAll('.filter-system:checked')].map(el=>el.value).filter(v=>v!=='all');
  systems.forEach(s => params.append('system[]', s));

  // Date range
  const dates = document.querySelectorAll('.filter-date-input');
  if(dates[0]?.value) params.set('date_from', dates[0].value);
  if(dates[1]?.value) params.set('date_to',   dates[1].value);

  window.location.href = '{{ route("tickets.index") }}' + (params.toString() ? '?'+params.toString() : '');
}

// Restore filter panel checkbox/date state from current URL on page load
document.addEventListener('DOMContentLoaded', () => {
  const p = new URLSearchParams(window.location.search);
  const hasFilters = p.has('status[]') || p.has('priority[]') || p.has('system[]') || p.has('date_from') || p.has('date_to');

  if(hasFilters){
    // Status
    const activeStatuses = p.getAll('status[]');
    document.querySelectorAll('.filter-status').forEach(el=>{
      el.checked = el.value === 'all' ? activeStatuses.length === 0 : activeStatuses.includes(el.value);
    });

    // Priority
    const activePriorities = p.getAll('priority[]');
    document.querySelectorAll('.filter-priority').forEach(el=>{
      el.checked = el.value === 'all' ? activePriorities.length === 0 : activePriorities.includes(el.value);
    });

    // System
    const activeSystems = p.getAll('system[]');
    document.querySelectorAll('.filter-system').forEach(el=>{
      el.checked = el.value !== 'all' && activeSystems.includes(el.value);
    });

    // Dates
    const dates = document.querySelectorAll('.filter-date-input');
    if(dates[0] && p.get('date_from')) dates[0].value = p.get('date_from');
    if(dates[1] && p.get('date_to'))   dates[1].value = p.get('date_to');
  }
});

// Modal
let currentMode='new';
function openModal(mode,id,title,desc,system,priority,impact,status,due_date){
  currentMode=mode;
  document.querySelectorAll('.dropdown-menu').forEach(m=>m.classList.remove('open'));
  document.querySelectorAll('.more-btn').forEach(b=>b.classList.remove('active'));
  const form=document.getElementById('ticketForm');
  if(mode==='new'){
    document.getElementById('modalHeadTitle').textContent='New Ticket';
    document.getElementById('modalSaveBtn').textContent='Create Ticket';
    form.action='{{ route("tickets.store") }}';
    document.getElementById('formMethod').value='POST';
    document.getElementById('fTitle').value='';
    document.getElementById('fDesc').value='';
    if(document.getElementById('fSystem')) {
      if(document.getElementById('fSystem').tagName === 'SELECT') {
        document.getElementById('fSystem').value='';
      } else {
        document.getElementById('fSystem').value='{{ auth()->user()->company->name ?? "Unknown Company" }}';
      }
    }
    if(document.getElementById('fSystemText')) {
      document.getElementById('fSystemText').value='{{ auth()->user()->company->name ?? "Unknown Company" }}';
    }
    document.getElementById('fPriority').value='medium';
    document.getElementById('fImpact').value='low';
    document.getElementById('fStatus').value='open';
    document.getElementById('fDueDate').value='';
    document.getElementById('modal_file_upload').value='';
    document.getElementById('modal_file_list').innerHTML='';
    document.getElementById('modal_dz_title').innerHTML='Drag &amp; drop files or <span style="color:var(--blue)">browse</span>';
    document.getElementById('attachmentGroup').style.display='block';
    modalFiles = [];
  } else {
    document.getElementById('modalHeadTitle').textContent='Edit Ticket';
    document.getElementById('modalSaveBtn').textContent='Save Changes';
    form.action='/tickets/'+id;
    document.getElementById('formMethod').value='PATCH';
    document.getElementById('formTicketId').value=id;
    document.getElementById('fTitle').value=title||'';
    document.getElementById('fDesc').value=desc||'';
    if(system){
      if(document.getElementById('fSystem')) document.getElementById('fSystem').value=system;
      if(document.getElementById('fSystemText')) document.getElementById('fSystemText').value=system;
    }
    if(priority)document.getElementById('fPriority').value=priority;
    if(impact)document.getElementById('fImpact').value=impact;
    if(status)document.getElementById('fStatus').value=status;
    document.getElementById('fDueDate').value=due_date||'';
    document.getElementById('attachmentGroup').style.display='none';
  }
  document.getElementById('ticketModal').classList.add('open');
}
function closeModal(){document.getElementById('ticketModal').classList.remove('open');}

// Delete
let _deleteTicketId = null;
function confirmDelete(id,label){
  _deleteTicketId = id;
  document.querySelectorAll('.dropdown-menu').forEach(m=>m.classList.remove('open'));
  document.querySelectorAll('.more-btn').forEach(b=>b.classList.remove('active'));
  document.getElementById('deleteLabel').textContent=label;
  document.getElementById('deleteModal').classList.add('open');
}
function closeDeleteModal(){
  document.getElementById('deleteModal').classList.remove('open');
  _deleteTicketId = null;
}
function doDeleteTicket(){
  if(!_deleteTicketId) return;
  const btn = document.getElementById('deleteConfirmBtn');
  btn.disabled = true; btn.textContent = 'Deleting…';
  const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  fetch('/tickets/' + _deleteTicketId, {
    method: 'POST',
    headers: {'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
    body: '_method=DELETE'
  }).then(r => {
    if(r.ok || r.redirected){
      closeDeleteModal();
      showToast('Ticket deleted');
      setTimeout(() => window.location.reload(), 700);
    } else {
      showToast('Delete failed', false);
      btn.disabled = false; btn.textContent = 'Delete';
    }
  }).catch(() => { showToast('Delete failed', false); btn.disabled = false; btn.textContent = 'Delete'; });
}

function showToast(msg,ok=true){
  document.getElementById('toastMsg').textContent=msg;
  const t=document.getElementById('toast');
  t.classList.add('show');
  clearTimeout(t._t);
  t._t=setTimeout(()=>t.classList.remove('show'),3000);
}

// Submit via FormData+fetch so files from modalFiles array are reliably sent
function submitTicketForm() {
  const form = document.getElementById('ticketForm');

  // Basic HTML5 validation
  if (!form.checkValidity()) { form.reportValidity(); return; }

  const btn = document.getElementById('modalSaveBtn');
  btn.disabled = true;
  btn.textContent = currentMode === 'new' ? 'Creating…' : 'Saving…';

  const fd = new FormData(form);
  const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  if (currentMode === 'new') {
    // Remove any file input value that was set via DataTransfer (unreliable)
    fd.delete('attachments[]');
    // Append files directly from the modalFiles JS array (reliable)
    modalFiles.forEach(f => {
      const ext = f.name.split('.').pop().toLowerCase();
      if (modalAllowed.includes(ext) && f.size / 1024 / 1024 <= modalMaxMB) {
        fd.append('attachments[]', f);
      }
    });
    fd.set('_method', 'POST');
    var action = '{{ route("tickets.store") }}';
  } else {
    fd.set('_method', 'PATCH');
    var action = '/tickets/' + document.getElementById('formTicketId').value;
  }

  fetch(action, {
    method: 'POST',
    body: fd,
    headers: {
      'X-CSRF-TOKEN': csrf,
      'X-Requested-With': 'XMLHttpRequest'
    }
  }).then(r => {
    if (r.redirected) {
      window.location.href = r.url;
    } else if (r.ok) {
      window.location.reload();
    } else {
      return r.text().then(text => {
        showToast('Error: please check all fields', false);
        btn.disabled = false;
        btn.textContent = currentMode === 'new' ? 'Create Ticket' : 'Save Changes';
      });
    }
  }).catch(() => {
    showToast('Upload failed — please try again', false);
    btn.disabled = false;
    btn.textContent = currentMode === 'new' ? 'Create Ticket' : 'Save Changes';
  });
}

// Modal file upload — with removable pills
let modalFiles = [];
const modalMaxMB = 25;
const modalAllowed = ['jpg','jpeg','png','log','json','zip'];

function handleModalFiles(incoming) {
  [...incoming].forEach(f => {
    if (!modalFiles.find(x => x.name === f.name)) modalFiles.push(f);
  });
  syncModalFiles();
}

function removeModalFile(name) {
  modalFiles = modalFiles.filter(f => f.name !== name);
  syncModalFiles();
}

function syncModalFiles() {
  const input = document.getElementById('modal_file_upload');
  const list  = document.getElementById('modal_file_list');
  const title = document.getElementById('modal_dz_title');
  const dt    = new DataTransfer();

  modalFiles.forEach(f => {
    const ext = f.name.split('.').pop().toLowerCase();
    if (modalAllowed.includes(ext) && f.size/1024/1024 <= modalMaxMB) dt.items.add(f);
  });
  input.files = dt.files;

  const validCount = dt.files.length;
  title.innerHTML = validCount
    ? `${validCount} file(s) ready to upload`
    : 'Drag &amp; drop files or <span style="color:var(--blue)">browse</span>';

  if (!modalFiles.length) { list.innerHTML = ''; return; }

  list.innerHTML = modalFiles.map(f => {
    const ext    = f.name.split('.').pop().toLowerCase();
    const sizeMB = f.size / 1024 / 1024;
    const valid  = modalAllowed.includes(ext) && sizeMB <= modalMaxMB;
    const errMsg = !modalAllowed.includes(ext) ? ' — unsupported' : ` — exceeds 25MB`;
    return `<div style="display:flex;align-items:center;gap:7px;background:${valid?'#f0fdf4':'#fef2f2'};border:1px solid ${valid?'#bbf7d0':'#fecaca'};border-radius:7px;padding:5px 9px;margin-bottom:4px;font-size:11px;font-weight:600;color:${valid?'#15803d':'#dc2626'};">
      <span>${valid?'✅':'❌'}</span>
      <span style="flex:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="${f.name}">${f.name}${!valid?errMsg:''}</span>
      ${valid?`<span style="color:#94a3b8;flex-shrink:0;">${sizeMB.toFixed(2)}MB</span>`:''}
      <button type="button" onclick="removeModalFile('${f.name.replace(/'/g,"\\'")}')"
        style="background:none;border:none;cursor:pointer;color:#94a3b8;font-size:15px;line-height:1;padding:0 2px;flex-shrink:0;transition:color .12s;"
        onmouseover="this.style.color='#dc2626'" onmouseout="this.style.color='#94a3b8'"
        title="Remove">✕</button>
    </div>`;
  }).join('');
}

document.addEventListener('DOMContentLoaded', () => {
  const mdz = document.getElementById('modal_dropzone');
  mdz.addEventListener('dragover', e => { e.preventDefault(); mdz.style.borderColor='#2563eb'; mdz.style.background='var(--blue-bg)'; });
  mdz.addEventListener('dragleave', () => { mdz.style.borderColor=''; mdz.style.background=''; });
  mdz.addEventListener('drop', e => {
    e.preventDefault();
    mdz.style.borderColor=''; mdz.style.background='';
    handleModalFiles(e.dataTransfer.files);
  });
});

@if(session('success'))
document.addEventListener('DOMContentLoaded',()=>showToast('{{ session('success') }}'));
@endif
</script>
@endsection