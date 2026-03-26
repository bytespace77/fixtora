@extends('layouts.app')
@section('title', 'Ticket #{{ $ticket->id }} – Fixtora')

@section('styles')
<style>
.breadcrumb{display:flex;align-items:center;gap:6px;font-size:11.5px;font-weight:600;color:var(--muted);margin-bottom:10px}
.breadcrumb a{color:var(--muted);text-decoration:none}.breadcrumb a:hover{color:var(--blue)}
.sep{color:var(--border-dark)}
.current{color:var(--text-sub)}

.detail-grid{display:grid;grid-template-columns:1fr 280px;gap:18px}
.detail-col{display:flex;flex-direction:column;gap:14px}

.card-box{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:20px;box-shadow:var(--shadow)}
.card-box-title{font-size:13px;font-weight:700;color:var(--navy);margin-bottom:14px;display:flex;align-items:center;gap:8px}

/* HEADER CARD */
.ticket-hdr{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px}
.ticket-hdr-left h1{font-size:20px;font-weight:800;letter-spacing:-.4px;color:var(--navy);margin-bottom:6px}
.ticket-meta-row{display:flex;align-items:center;gap:10px;flex-wrap:wrap}
.ticket-id-badge{font-family:'DM Mono',monospace;font-size:12px;font-weight:700;color:var(--muted-lt);background:var(--bg);padding:3px 10px;border-radius:6px;border:1px solid var(--border)}
.ticket-hdr-right{display:flex;gap:8px;flex-shrink:0}

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

/* BUTTONS */
.btn-sm{padding:8px 14px;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:6px;border:1px solid var(--border);background:var(--surface);color:var(--text-sub);font-family:inherit;transition:all .15s;text-decoration:none}
.btn-sm:hover{background:var(--bg)}
.btn-blue{background:var(--blue);color:#fff;border-color:var(--blue)}
.btn-blue:hover{background:#1a42c4;color:#fff}
.btn-green{background:var(--green);color:#fff;border-color:var(--green)}
.btn-green:hover{background:#15803d;color:#fff}

/* DESCRIPTION */
.desc-body{font-size:13.5px;color:var(--text-sub);line-height:1.7;white-space:pre-wrap;background:var(--bg);padding:16px;border-radius:8px;border:1px solid var(--border)}

/* META INFO */
.meta-list{display:flex;flex-direction:column;gap:0}
.meta-row{display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border);font-size:13px}
.meta-row:last-child{border-bottom:none}
.meta-key{font-weight:600;color:var(--muted);font-size:11.5px;text-transform:uppercase;letter-spacing:.4px}
.meta-val{font-weight:600;color:var(--text)}

/* TIMELINE */
.timeline{display:flex;flex-direction:column;gap:0}
.tl-item{display:flex;gap:12px;padding-bottom:18px;position:relative}
.tl-item:last-child{padding-bottom:0}
.tl-item:not(:last-child)::before{content:'';position:absolute;left:15px;top:32px;bottom:0;width:1px;background:var(--border)}
.tl-dot{width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:11px;font-weight:800;color:#fff}
.tl-dot.blue{background:var(--blue)}
.tl-dot.green{background:var(--green)}
.tl-dot.orange{background:var(--orange)}
.tl-dot.gray{background:var(--muted-lt)}
.tl-content{}
.tl-title{font-size:13px;font-weight:600;color:var(--text);margin-bottom:2px}
.tl-time{font-size:11px;color:var(--muted-lt);font-weight:600}
.tl-desc{font-size:12.5px;color:var(--muted);margin-top:4px}

/* STATUS CHANGE FORM */
.status-select{width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;font-family:inherit;outline:none;background:var(--surface);color:var(--text);margin-bottom:10px}
.status-select:focus{border-color:var(--blue)}
.btn-full{width:100%;padding:10px;border-radius:8px;font-size:13px;font-weight:700;font-family:inherit;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:7px;transition:all .15s;border:none;background:var(--blue);color:#fff}
.btn-full:hover{background:#1a42c4}

/* REPORTER */
.reporter-card{display:flex;align-items:center;gap:12px}
.rep-av{width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,#2563eb,#7c3aed);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;color:#fff;flex-shrink:0}
.rep-name{font-size:13px;font-weight:700;color:var(--text)}
.rep-email{font-size:12px;color:var(--muted);margin-top:2px}
</style>
@endsection

@section('content')

<!-- BREADCRUMB -->
<div class="breadcrumb">
  <a href="{{ route('home') }}">Dashboard</a>
  <span class="sep">/</span>
  <a href="{{ route('tickets.index') }}">Tickets</a>
  <span class="sep">/</span>
  <span class="current">#TK-{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}</span>
</div>

<!-- TICKET HEADER -->
<div class="ticket-hdr">
  <div class="ticket-hdr-left">
    <h1>{{ $ticket->title }}</h1>
    <div class="ticket-meta-row">
      <span class="ticket-id-badge">#TK-{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}</span>
      <span class="pill pill-{{ $ticket->priority }}">{{ ucfirst($ticket->priority) }}</span>
      <span class="pill pill-{{ $ticket->status }}">{{ ucfirst(str_replace('_',' ',$ticket->status)) }}</span>
      <span style="font-size:12px;color:var(--muted)">Opened {{ $ticket->created_at->diffForHumans() }}</span>
    </div>
  </div>
  <div class="ticket-hdr-right">
    <a href="{{ route('tickets.index') }}" class="btn-sm">← Back</a>
  </div>
</div>

<div class="detail-grid">
  <!-- LEFT -->
  <div class="detail-col">

    <!-- Description -->
    <div class="card-box">
      <div class="card-box-title">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/></svg>
        Description
      </div>
      <div class="desc-body">{{ $ticket->description }}</div>
    </div>

    <!-- Activity Timeline -->
    <div class="card-box">
      <div class="card-box-title">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        Activity Timeline
      </div>
      <div class="timeline">
        <div class="tl-item">
          <div class="tl-dot blue">{{ strtoupper(substr($ticket->user->name ?? 'U', 0, 1)) }}</div>
          <div class="tl-content">
            <div class="tl-title">Ticket Created</div>
            <div class="tl-time">{{ $ticket->created_at->format('M d, Y · H:i') }}</div>
            <div class="tl-desc">Submitted by {{ $ticket->user->name ?? 'Unknown' }}</div>
          </div>
        </div>
        @if($ticket->status !== 'open')
        <div class="tl-item">
          <div class="tl-dot orange">S</div>
          <div class="tl-content">
            <div class="tl-title">Status Updated to {{ ucfirst(str_replace('_',' ',$ticket->status)) }}</div>
            <div class="tl-time">{{ $ticket->updated_at->format('M d, Y · H:i') }}</div>
          </div>
        </div>
        @endif
        @if($ticket->status === 'resolved')
        <div class="tl-item">
          <div class="tl-dot green">✓</div>
          <div class="tl-content">
            <div class="tl-title">Ticket Resolved</div>
            <div class="tl-time">{{ $ticket->updated_at->format('M d, Y · H:i') }}</div>
          </div>
        </div>
        @endif
      </div>
    </div>

  </div>

  <!-- RIGHT -->
  <div class="detail-col">

    <!-- Update Status -->
    <div class="card-box">
      <div class="card-box-title">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
        Update Status
      </div>
      <form action="{{ route('tickets.update', $ticket) }}" method="POST">
        @csrf @method('PATCH')
        <select name="status" class="status-select">
          <option value="open"        {{ $ticket->status == 'open'        ? 'selected' : '' }}>Open</option>
          <option value="in_progress" {{ $ticket->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
          <option value="resolved"    {{ $ticket->status == 'resolved'    ? 'selected' : '' }}>Resolved</option>
          <option value="closed"      {{ $ticket->status == 'closed'      ? 'selected' : '' }}>Closed</option>
        </select>
        <button type="submit" class="btn-full">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/></svg>
          Save Status
        </button>
      </form>
    </div>

    <!-- Ticket Details -->
    <div class="card-box">
      <div class="card-box-title">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        Ticket Details
      </div>
      <div class="meta-list">
        <div class="meta-row">
          <span class="meta-key">System</span>
          <span class="meta-val">{{ $ticket->system ?? '—' }}</span>
        </div>
        <div class="meta-row">
          <span class="meta-key">Priority</span>
          <span class="pill pill-{{ $ticket->priority }}">{{ ucfirst($ticket->priority) }}</span>
        </div>
        <div class="meta-row">
          <span class="meta-key">Impact</span>
          <span class="pill pill-{{ $ticket->impact }}">{{ ucfirst($ticket->impact) }}</span>
        </div>
        <div class="meta-row">
          <span class="meta-key">Status</span>
          <span class="pill pill-{{ $ticket->status }}">{{ ucfirst(str_replace('_',' ',$ticket->status)) }}</span>
        </div>
        <div class="meta-row">
          <span class="meta-key">Created</span>
          <span class="meta-val">{{ $ticket->created_at->format('M d, Y') }}</span>
        </div>
        <div class="meta-row">
          <span class="meta-key">Last Updated</span>
          <span class="meta-val">{{ $ticket->updated_at->diffForHumans() }}</span>
        </div>
      </div>
    </div>

    <!-- Reporter -->
    <div class="card-box">
      <div class="card-box-title">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        Reported By
      </div>
      <div class="reporter-card">
        <div class="rep-av">{{ strtoupper(substr($ticket->user->name ?? 'U', 0, 2)) }}</div>
        <div>
          <div class="rep-name">{{ $ticket->user->name ?? 'Unknown' }}</div>
          <div class="rep-email">{{ $ticket->user->email ?? '' }}</div>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection