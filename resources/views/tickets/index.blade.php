@extends('layouts.app')
@section('title', 'Tickets – Fixtora')

@section('styles')
<style>
.page-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px}
.page-header h1{font-size:22px;font-weight:800;letter-spacing:-.5px;color:var(--navy)}
.page-header p{font-size:13px;color:var(--muted);margin-top:4px}
.hdr-btns{display:flex;gap:8px;align-items:center}
.btn-sm{padding:8px 14px;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:6px;border:1px solid var(--border);background:var(--surface);color:var(--text-sub);font-family:inherit;transition:all .15s;text-decoration:none}
.btn-sm:hover{background:var(--bg)}
.btn-primary{background:var(--blue);color:#fff;border-color:var(--blue)}
.btn-primary:hover{background:#1a42c4;color:#fff}

/* FILTER TABS */
.filter-row{display:flex;align-items:center;gap:6px;margin-bottom:16px;flex-wrap:wrap}
.filter-tab{padding:7px 14px;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;border:1px solid var(--border);background:var(--surface);color:var(--muted);font-family:inherit;transition:all .15s;text-decoration:none}
.filter-tab:hover{background:var(--bg);color:var(--text)}
.filter-tab.active{background:var(--navy);color:#fff;border-color:var(--navy)}

/* TABLE */
.ticket-table{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow)}
.tt-header{display:grid;grid-template-columns:90px 1fr 150px 110px 100px 40px;gap:12px;padding:11px 18px;background:var(--bg);border-bottom:1px solid var(--border);font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--muted)}
.tt-row{display:grid;grid-template-columns:90px 1fr 150px 110px 100px 40px;gap:12px;padding:14px 18px;border-bottom:1px solid var(--border);align-items:center;transition:background .12s;text-decoration:none;color:inherit}
.tt-row:last-child{border-bottom:none}
.tt-row:hover{background:#fafbff}
.tt-id{font-family:'DM Mono',monospace;font-size:11.5px;font-weight:700;color:var(--muted)}
.tt-name{font-size:13px;font-weight:600;color:var(--text);margin-bottom:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.tt-sub{font-size:11.5px;color:var(--muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.tt-time{font-size:12px;color:var(--muted-lt);font-weight:500}
.more-btn{border:none;background:none;cursor:pointer;color:var(--muted-lt);font-size:16px;padding:4px 8px;border-radius:6px}
.more-btn:hover{background:var(--bg);color:var(--text)}

/* PILLS */
.pill{display:inline-block;padding:3px 10px;border-radius:20px;font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.4px}
.pill-critical{background:#fee2e2;color:var(--red);border:1px solid #fecaca}
.pill-high{background:#fff7ed;color:var(--orange);border:1px solid #fed7aa}
.pill-medium{background:var(--blue-bg);color:var(--blue);border:1px solid var(--blue-lt)}
.pill-low{background:#f0fdf4;color:var(--green);border:1px solid #bbf7d0}
.pill-open{background:#fff7ed;color:var(--orange);border:1px solid #fed7aa}
.pill-resolved{background:#f0fdf4;color:var(--green);border:1px solid #bbf7d0}
.pill-in_progress{background:var(--blue-bg);color:var(--blue);border:1px solid var(--blue-lt)}
.pill-closed{background:var(--bg);color:var(--muted);border:1px solid var(--border)}

/* PAGINATION */
.pagination-wrap{display:flex;justify-content:flex-end;padding:14px 18px;border-top:1px solid var(--border);gap:4px}
.page-btn{padding:6px 12px;border-radius:7px;font-size:12px;font-weight:600;border:1px solid var(--border);background:var(--surface);color:var(--text-sub);cursor:pointer;text-decoration:none;font-family:inherit}
.page-btn:hover{background:var(--bg)}
.page-btn.active-pg{background:var(--navy);color:#fff;border-color:var(--navy)}

/* EMPTY */
.empty-state{text-align:center;padding:60px 20px;color:var(--muted)}
.empty-state svg{margin-bottom:14px;opacity:.25}
.empty-state h3{font-size:16px;font-weight:700;color:var(--text-sub);margin-bottom:6px}
.empty-state p{font-size:13px;margin-bottom:18px}
</style>
@endsection

@section('content')
<div class="page-header">
  <div>
    <h1>Ticket Management</h1>
    <p>Track and resolve all architectural support tickets.</p>
  </div>
  <div class="hdr-btns">
    <a href="{{ route('tickets.index') }}" class="btn-sm">
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
      Filter
    </a>
    <a href="{{ route('tickets.create') }}" class="btn-sm btn-primary">
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      New Ticket
    </a>
  </div>
</div>

<!-- FILTER TABS -->
<div class="filter-row">
  <a href="{{ route('tickets.index') }}" class="filter-tab {{ !request('status') ? 'active' : '' }}">All Tickets</a>
  <a href="{{ route('tickets.index', ['status' => 'open']) }}" class="filter-tab {{ request('status') === 'open' ? 'active' : '' }}">Open</a>
  <a href="{{ route('tickets.index', ['status' => 'in_progress']) }}" class="filter-tab {{ request('status') === 'in_progress' ? 'active' : '' }}">In Progress</a>
  <a href="{{ route('tickets.index', ['status' => 'resolved']) }}" class="filter-tab {{ request('status') === 'resolved' ? 'active' : '' }}">Resolved</a>
  <a href="{{ route('tickets.index', ['status' => 'closed']) }}" class="filter-tab {{ request('status') === 'closed' ? 'active' : '' }}">Closed</a>
</div>

<!-- TICKET TABLE -->
<div class="ticket-table">
  <div class="tt-header">
    <span>Ticket ID</span>
    <span>Title</span>
    <span>Priority</span>
    <span>Status</span>
    <span>Created</span>
    <span></span>
  </div>

  @forelse($tickets as $ticket)
  <a href="{{ route('tickets.show', $ticket) }}" class="tt-row">
    <div class="tt-id">#TK-{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}</div>
    <div class="tt-title-wrap">
      <div class="tt-name">{{ $ticket->title }}</div>
      <div class="tt-sub">{{ $ticket->system ?? 'No system specified' }}</div>
    </div>
    <div><span class="pill pill-{{ $ticket->priority }}">{{ ucfirst($ticket->priority) }}</span></div>
    <div><span class="pill pill-{{ $ticket->status }}">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span></div>
    <div class="tt-time">{{ $ticket->created_at->format('M d') }}</div>
    <div><button class="more-btn" onclick="event.preventDefault()">⋯</button></div>
  </a>
  @empty
  <div class="empty-state">
    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
    <h3>No tickets found</h3>
    <p>{{ request('status') ? 'No ' . request('status') . ' tickets.' : 'You have not created any tickets yet.' }}</p>
    <a href="{{ route('tickets.create') }}" class="btn-sm btn-primary" style="display:inline-flex;margin:0 auto">+ Create First Ticket</a>
  </div>
  @endforelse

  @if($tickets->hasPages())
  <div class="pagination-wrap">
    @if($tickets->onFirstPage())
      <span class="page-btn" style="opacity:.4">← Prev</span>
    @else
      <a href="{{ $tickets->previousPageUrl() }}" class="page-btn">← Prev</a>
    @endif
    @foreach($tickets->getUrlRange(1, $tickets->lastPage()) as $page => $url)
      <a href="{{ $url }}" class="page-btn {{ $page == $tickets->currentPage() ? 'active-pg' : '' }}">{{ $page }}</a>
    @endforeach
    @if($tickets->hasMorePages())
      <a href="{{ $tickets->nextPageUrl() }}" class="page-btn">Next →</a>
    @else
      <span class="page-btn" style="opacity:.4">Next →</span>
    @endif
  </div>
  @endif
</div>
@endsection