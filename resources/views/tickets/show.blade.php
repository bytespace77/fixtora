@extends('layouts.app')
@section('title', 'Ticket #TK-'.str_pad($ticket->id,4,'0',STR_PAD_LEFT).' – Fixtora')

@section('styles')
<style>
.breadcrumb{display:flex;align-items:center;gap:6px;font-size:11.5px;font-weight:600;color:var(--muted);margin-bottom:12px}
.breadcrumb a{color:var(--muted);text-decoration:none;transition:color .12s}.breadcrumb a:hover{color:var(--blue)}
.bc-sep{color:var(--border-2);font-size:14px}
.ticket-hdr{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px}
.ticket-hdr h1{font-size:22px;font-weight:800;letter-spacing:-.4px;color:var(--navy);margin-bottom:8px}
.ticket-meta{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.tk-id-badge{font-size:11.5px;font-weight:700;color:var(--muted-lt);background:var(--bg);padding:3px 10px;border-radius:6px;border:1px solid var(--border)}
.hdr-btns{display:flex;gap:8px;flex-shrink:0}
.btn-sm{padding:8px 14px;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:6px;border:1px solid var(--border);background:var(--surface);color:#475569;font-family:inherit;transition:all .15s;text-decoration:none}
.btn-sm:hover{background:var(--bg)}
.btn-primary{background:var(--blue);color:#fff;border-color:var(--blue)}.btn-primary:hover{background:#1a42c4;color:#fff}
.btn-danger-outline{border-color:#fecaca;color:#ef4444}.btn-danger-outline:hover{background:#fff5f5}
.btn-danger{background:#ef4444;color:#fff;border-color:#ef4444}.btn-danger:hover{background:#dc2626;color:#fff}
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
.detail-grid{display:grid;grid-template-columns:1fr 300px;gap:18px}
.detail-col{display:flex;flex-direction:column;gap:14px}
.card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow)}
.card-head{display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid var(--border)}
.card-head-left{display:flex;align-items:center;gap:8px;font-size:13px;font-weight:700;color:var(--navy)}
.card-head-left svg{color:var(--muted-lt)}
.card-body{padding:20px}
.desc-body{font-size:13.5px;color:var(--text);line-height:1.7;white-space:pre-wrap;background:var(--bg);padding:16px;border-radius:8px;border:1px solid var(--border);min-height:60px}
/* Ticket attachments below description */
.tk-attach-header{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin:14px 0 6px;display:flex;align-items:center;gap:6px}
.tk-attach-list{display:flex;flex-direction:column;gap:6px}
.tk-attach-item{display:flex;align-items:center;justify-content:space-between;padding:8px 12px;background:var(--bg);border:1px solid var(--border);border-radius:8px;transition:border-color .12s}
.tk-attach-item:hover{border-color:var(--blue)}
.tk-attach-left{display:flex;align-items:center;gap:9px;min-width:0;flex:1}
.tk-attach-left svg{color:var(--blue);flex-shrink:0}
.tk-attach-name{font-size:12px;font-weight:700;color:var(--navy);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.tk-attach-sz{font-size:10.5px;color:var(--muted-lt)}
.tk-attach-actions{display:flex;gap:4px;margin-left:8px;flex-shrink:0}
.icon-act{width:26px;height:26px;border:none;background:none;cursor:pointer;border-radius:6px;display:flex;align-items:center;justify-content:center;color:var(--muted-lt);transition:all .12s;text-decoration:none}
.icon-act:hover{background:var(--bg);color:var(--blue)}.icon-act.del-act:hover{color:#ef4444}

/* Timeline */
.timeline{display:flex;flex-direction:column;gap:0;position:relative}
.tl-item{display:flex;gap:14px;padding-bottom:18px;position:relative}
.tl-item:last-child{padding-bottom:0}
.tl-item:not(:last-child)::before{content:'';position:absolute;left:14px;top:30px;bottom:0;width:2px;background:var(--border);border-radius:2px}
.tl-dot{width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:11px;font-weight:800;color:#fff}
.tl-dot.blue{background:var(--blue)}.tl-dot.green{background:var(--green)}.tl-dot.orange{background:var(--orange)}.tl-dot.gray{background:var(--muted-lt)}
.tl-title{font-size:13px;font-weight:600;color:var(--text);margin-bottom:2px}
.tl-time{font-size:11px;color:var(--muted-lt);font-weight:600}
.tl-desc{font-size:12px;color:var(--muted);margin-top:3px}
/* Comments */
.comment-list{display:flex;flex-direction:column;gap:14px;padding-right:4px}
.comment-item{display:flex;gap:10px;animation:cmtIn .25s ease}
@keyframes cmtIn{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
.cmt-av{width:32px;height:32px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;color:#fff;flex-shrink:0;margin-top:2px}
.cmt-bubble{flex:1;min-width:0;background:var(--bg);border:1px solid var(--border);border-radius:12px;border-top-left-radius:3px;padding:12px 14px}
.cmt-meta{display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;gap:8px}
.cmt-name{font-size:12.5px;font-weight:800;color:var(--navy)}
.cmt-role-badge{font-size:9.5px;font-weight:800;letter-spacing:.4px;text-transform:uppercase;padding:2px 7px;border-radius:4px}
.badge-admin{background:var(--navy);color:#fff}.badge-user{background:var(--bg);color:var(--muted);border:1px solid var(--border)}
.cmt-time{font-size:10.5px;color:var(--muted-lt);font-weight:600;margin-left:auto}
.cmt-text{font-size:13px;color:var(--text);line-height:1.6}
/* Comment attached files displayed in bubble */
.cmt-attach-row{display:flex;flex-wrap:wrap;gap:6px;margin-top:10px;padding-top:8px;border-top:1px dashed var(--border)}
.cmt-fchip{display:inline-flex;align-items:center;gap:5px;background:var(--surface);border:1px solid var(--border);border-radius:6px;padding:4px 9px;font-size:11px;font-weight:600;color:var(--text);text-decoration:none;transition:border-color .12s}
.cmt-fchip:hover{border-color:var(--blue);color:var(--blue)}
.cmt-fchip-sz{color:var(--muted-lt);font-size:10px}
.cmt-empty{text-align:center;padding:32px 20px;color:var(--muted)}
.cmt-empty svg{margin:0 auto 10px;display:block;opacity:.18}
.cmt-empty p{font-size:13px;font-weight:600}
.cmt-empty span{font-size:12px;color:var(--muted-lt)}
/* Comment input */
.cmt-input-wrap{border-top:1px solid var(--border);padding:16px 20px}
.cmt-sender{display:flex;align-items:center;gap:8px;margin-bottom:10px}
.cmt-sender-av{width:28px;height:28px;border-radius:8px;background:var(--navy);display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;color:#fff}
.cmt-sender-info .name{font-size:12px;font-weight:800;color:var(--navy)}
.cmt-sender-info .role-lbl{font-size:10.5px;color:var(--muted);margin-left:6px}
.cmt-editor{border:1.5px solid var(--border);border-radius:10px;overflow:hidden;transition:border-color .15s,box-shadow .15s}
.cmt-editor:focus-within{border-color:var(--blue);box-shadow:0 0 0 3px rgba(37,99,235,.08)}
.cmt-textarea{width:100%;padding:12px 14px;font-size:13px;font-family:inherit;border:none;outline:none;resize:none;color:var(--text);background:var(--surface)}
.cmt-textarea::placeholder{color:var(--muted-lt)}
/* file preview strip inside comment editor */
.cmt-file-preview{display:flex;flex-wrap:wrap;gap:5px;padding:8px 12px;background:var(--bg);border-top:1px solid var(--border)}
.cmt-file-preview:empty{display:none}
.cmt-fpill{display:inline-flex;align-items:center;gap:5px;background:var(--surface);border:1px solid var(--border);border-radius:6px;padding:3px 8px;font-size:11px;font-weight:600;color:var(--text)}
.cmt-fpill button{background:none;border:none;cursor:pointer;color:#94a3b8;font-size:13px;line-height:1;padding:0 1px;transition:color .12s}
.cmt-fpill button:hover{color:#ef4444}
.cmt-toolbar{display:flex;align-items:center;justify-content:space-between;padding:8px 10px;background:var(--bg);border-top:1px solid var(--border)}
.toolbar-left{display:flex;align-items:center;gap:2px}
.tool-btn{border:none;background:none;cursor:pointer;color:var(--muted);padding:5px 7px;border-radius:6px;transition:all .12s;font-family:inherit;font-size:12px;font-weight:700;display:flex;align-items:center;gap:5px}
.tool-btn:hover{background:var(--surface);color:var(--text)}
.toolbar-right{display:flex;align-items:center;gap:8px}
.role-toggle{display:flex;align-items:center;gap:6px;background:var(--surface);border:1px solid var(--border);border-radius:7px;padding:5px 10px}
.role-toggle span{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--muted-lt)}
.role-toggle select{font-size:11px;font-weight:800;color:var(--navy);border:none;outline:none;background:transparent;cursor:pointer;font-family:inherit}
.send-btn{padding:7px 14px;background:var(--navy);color:#fff;border:none;border-radius:7px;font-size:12px;font-weight:800;cursor:pointer;font-family:inherit;display:flex;align-items:center;gap:6px;transition:all .15s}
.send-btn:hover{background:var(--blue)}.send-btn:active{transform:scale(.97)}
.cmt-del-pill{display:inline-flex;align-items:center;gap:4px;margin-left:auto;padding:3px 9px;background:#fee2e2;color:#dc2626;border:1px solid #fecaca;border-radius:20px;font-size:10px;font-weight:700;cursor:pointer;font-family:inherit;transition:all .15s;white-space:nowrap}
.cmt-del-pill:hover{background:#dc2626;color:#fff;border-color:#dc2626}
/* Delete comment modal */
.cmt-del-modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);backdrop-filter:blur(3px);z-index:800;align-items:center;justify-content:center;padding:20px}
.cmt-del-modal-overlay.open{display:flex;animation:fadeIn .2s ease}
.cmt-del-modal-box{background:var(--surface);border-radius:16px;width:100%;max-width:400px;box-shadow:0 24px 64px rgba(0,0,0,.22);overflow:hidden;animation:slideUp .2s ease}
.cmt-del-modal-icon{width:60px;height:60px;border-radius:50%;background:#fee2e2;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;color:#dc2626}
.cmt-del-modal-body{padding:32px 28px 16px;text-align:center}
.cmt-del-modal-body h3{font-size:18px;font-weight:800;color:var(--navy);margin-bottom:10px}
.cmt-del-modal-body p{font-size:13px;color:var(--muted);line-height:1.6}
.cmt-del-preview{background:var(--bg);border:1px solid var(--border);border-radius:8px;padding:10px 14px;font-size:12.5px;color:var(--text);font-style:italic;margin-top:12px;text-align:left;line-height:1.5}
.cmt-del-modal-foot{display:flex;gap:10px;padding:16px 24px 24px}
.cmt-drop-zone{border:2px dashed var(--border);border-radius:8px;padding:10px 14px;display:flex;align-items:center;gap:8px;cursor:pointer;transition:all .15s;background:var(--bg);}
.cmt-drop-zone:hover,.cmt-drop-zone.drag-over{border-color:var(--blue);background:#f0f6ff;}
.cmt-drop-zone svg{color:var(--muted-lt);flex-shrink:0;}
#cmtDropLabel{font-size:12px;font-weight:700;color:var(--muted);flex:1;}
.cmt-drop-hint{font-size:10.5px;color:var(--muted-lt);white-space:nowrap;}
/* Right col */
.meta-list{display:flex;flex-direction:column}
.meta-row{display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border);font-size:12.5px}
.meta-row:last-child{border-bottom:none}
.meta-key{font-weight:700;color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:.4px}
.meta-val{font-weight:600;color:var(--text)}
.status-select{width:100%;padding:10px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-family:inherit;outline:none;background:var(--surface);color:var(--text);margin-bottom:10px;transition:border-color .15s}
.status-select:focus{border-color:var(--blue)}
.btn-full{width:100%;padding:10px;border-radius:8px;font-size:13px;font-weight:800;font-family:inherit;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:7px;transition:all .15s;border:none;background:var(--navy);color:#fff}
.btn-full:hover{background:var(--blue)}
.reporter-row{display:flex;align-items:center;gap:12px}
.rep-av{width:40px;height:40px;border-radius:12px;background:linear-gradient(135deg,#2563eb,#7c3aed);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;color:#fff;flex-shrink:0}
.rep-name{font-size:13.5px;font-weight:800;color:var(--navy)}
.rep-email{font-size:11.5px;color:var(--muted);margin-top:2px}
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);backdrop-filter:blur(3px);z-index:700;align-items:center;justify-content:center;padding:20px}
.modal-overlay.open{display:flex;animation:fadeIn .2s ease}
@keyframes fadeIn{from{opacity:0}to{opacity:1}}
.modal-box{background:var(--surface);border-radius:14px;width:100%;max-width:520px;box-shadow:0 20px 60px rgba(0,0,0,.18);overflow:hidden;animation:slideUp .2s ease}
@keyframes slideUp{from{transform:translateY(12px);opacity:0}to{transform:translateY(0);opacity:1}}
.modal-head{display:flex;align-items:center;justify-content:space-between;padding:18px 22px;border-bottom:1px solid var(--border)}
.modal-head h3{font-size:16px;font-weight:800;color:var(--navy)}
.modal-close{width:30px;height:30px;border:none;background:none;cursor:pointer;border-radius:7px;display:flex;align-items:center;justify-content:center;color:var(--muted);font-size:18px;line-height:1;transition:all .12s}
.modal-close:hover{background:var(--bg);color:var(--text)}
.modal-body{padding:22px;display:flex;flex-direction:column;gap:16px}
.form-group label{display:block;font-size:10.5px;font-weight:800;letter-spacing:.6px;text-transform:uppercase;color:var(--muted);margin-bottom:6px}
.form-control{width:100%;padding:10px 13px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;font-family:inherit;outline:none;background:var(--surface);color:var(--text);transition:all .15s}
.form-control:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(37,99,235,.08)}
textarea.form-control{resize:vertical;min-height:80px}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.ticket-side-form{display:flex;flex-direction:column;gap:14px}
.ticket-side-form .btn-full{margin-top:4px}
.side-muted{font-size:12px;color:var(--muted);line-height:1.55}
.side-section{margin-top:16px;padding-top:14px;border-top:1px solid var(--border)}
.modal-foot{display:flex;align-items:center;justify-content:flex-end;gap:10px;padding:16px 22px;border-top:1px solid var(--border);background:var(--bg)}
.del-modal-box{background:var(--surface);border-radius:14px;width:100%;max-width:380px;box-shadow:0 20px 60px rgba(0,0,0,.18);overflow:hidden;animation:slideUp .2s ease}
.del-icon{width:56px;height:56px;border-radius:50%;background:#fee2e2;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;color:#ef4444}
.del-modal-body{padding:28px 24px 10px;text-align:center}
.del-modal-body h3{font-size:17px;font-weight:800;color:var(--navy);margin-bottom:8px}
.del-modal-body p{font-size:13px;color:var(--muted);line-height:1.6}
.del-modal-foot{display:flex;gap:10px;padding:18px 24px}
#toast{position:fixed;bottom:24px;right:24px;z-index:900;background:var(--navy);color:#fff;padding:12px 18px;border-radius:10px;font-size:13px;font-weight:700;display:flex;align-items:center;gap:10px;box-shadow:0 8px 24px rgba(0,0,0,.2);transform:translateY(16px);opacity:0;pointer-events:none;transition:all .25s ease}
#toast.show{transform:translateY(0);opacity:1}
</style>
@endsection

@section('content')
@if($errors->any())
<div style="margin-bottom:12px;padding:10px 12px;border:1px solid #fecaca;background:#fff1f2;color:#b91c1c;border-radius:8px;font-size:12px;font-weight:600">
  {{ $errors->first() }}
</div>
@endif
<div class="breadcrumb">
  <a href="{{ route('home') }}">Dashboard</a><span class="bc-sep">/</span>
  <a href="{{ route('tickets.index') }}">Tickets</a><span class="bc-sep">/</span>
  <span>#TK-{{ str_pad($ticket->id,4,'0',STR_PAD_LEFT) }}</span>
</div>

<div class="ticket-hdr">
  <div>
    <h1>{{ $ticket->title }}</h1>
    <div class="ticket-meta">
      <span class="tk-id-badge">#TK-{{ str_pad($ticket->id,4,'0',STR_PAD_LEFT) }}</span>
      <span class="pill pill-{{ $ticket->priority }}">{{ ucfirst($ticket->priority) }}</span>
      <span class="pill pill-{{ $ticket->status }}">{{ ucfirst(str_replace('_',' ',$ticket->status)) }}</span>
      <span style="font-size:12px;color:var(--muted)">Opened {{ $ticket->created_at->diffForHumans() }}</span>
    </div>
  </div>
  <div class="hdr-btns">
    @if(auth()->user()->hasPermission('edit_tickets'))
    <button onclick="openEditModal()" class="btn-sm">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>Edit
    </button>
    @endif
    @if(auth()->user()->hasPermission('delete_tickets'))
    <button onclick="openDeleteModal()" class="btn-sm btn-danger-outline">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>Delete
    </button>
    @endif
    <a href="{{ route('tickets.index') }}" class="btn-sm">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>Back
    </a>
  </div>
</div>

<div class="detail-grid">
  <div class="detail-col">

    {{-- ── Description + Attachments ── --}}
    <div class="card">
      <div class="card-head">
        <div class="card-head-left">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
          Description
        </div>
      </div>
      <div class="card-body">
        <div class="desc-body">{{ $ticket->description }}</div>

        {{-- Ticket-level attachments shown right below description --}}
        @if($ticket->attachments->count())
          <div class="tk-attach-header">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
            Attachments ({{ $ticket->attachments->count() }})
          </div>
          <div class="tk-attach-list">
            @foreach($ticket->attachments as $att)
              <div class="tk-attach-item">
                <div class="tk-attach-left">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                  <div>
                    <div class="tk-attach-name" title="{{ $att->original_name }}">{{ $att->original_name }}</div>
                    <div class="tk-attach-sz">{{ $att->formatted_size }}</div>
                  </div>
                </div>
                <div class="tk-attach-actions">
                  <a href="{{ $att->url }}" download="{{ $att->original_name }}" class="icon-act" title="Download">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                  </a>

                </div>
              </div>
            @endforeach
          </div>
        @endif


      </div>
    </div>

    {{-- ── Activity Timeline ── --}}
    <div class="card">
      <div class="card-head">
        <div class="card-head-left">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>Activity Timeline
        </div>
      </div>
      <div class="card-body">
        <div class="timeline">
          <div class="tl-item">
            <div class="tl-dot blue">{{ strtoupper(substr($ticket->user->name??'U',0,1)) }}</div>
            <div><div class="tl-title">Ticket Created</div><div class="tl-time">{{ $ticket->created_at->format('M d, Y · H:i') }}</div><div class="tl-desc">Submitted by {{ $ticket->user->name??'Unknown' }}</div></div>
          </div>
          @if($ticket->tasks && $ticket->tasks->count() > 0)
          @foreach($ticket->tasks as $task)
            @if($task->assigned_to)
            <div class="tl-item">
              <div class="tl-dot orange">{{ strtoupper(substr($task->assignee->name??'A',0,1)) }}</div>
              <div>
                <div class="tl-title">Task "{{ Str::limit($task->title, 20) }}" Assigned</div>
                <div class="tl-time">{{ $task->assigned_date ? $task->assigned_date->format('M d, Y · H:i') : '—' }}</div>
                <div class="tl-desc">{{ $task->assignee->name ?? 'Developer' }} · SLA {{ $task->sla_level ?? '—' }}</div>
              </div>
            </div>
            @endif
          @endforeach
          @endif
          @if($ticket->status!=='open')
          <div class="tl-item">
            <div class="tl-dot orange">↔</div>
            <div><div class="tl-title">Status changed to {{ ucfirst(str_replace('_',' ',$ticket->status)) }}</div><div class="tl-time">{{ $ticket->updated_at->format('M d, Y · H:i') }}</div></div>
          </div>
          @endif
          @if($ticket->status==='resolved')
          <div class="tl-item">
            <div class="tl-dot green">✓</div>
            <div><div class="tl-title">Ticket Resolved</div><div class="tl-time">{{ $ticket->updated_at->format('M d, Y · H:i') }}</div></div>
          </div>
          @endif
        </div>
      </div>
    </div>

    {{-- ── Comments ── --}}
    <div class="card">
      <div class="card-head">
        <div class="card-head-left">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
          Comments
          <span style="background:var(--navy);color:#fff;font-size:10px;font-weight:800;padding:2px 7px;border-radius:20px;margin-left:2px">{{ $ticket->comments->count() }}</span>
        </div>
        <span style="font-size:11px;color:var(--muted-lt);font-weight:600">Admin & user communication</span>
      </div>

      <div class="card-body" style="padding-bottom:0">
        <div class="comment-list">
          @forelse($ticket->comments as $comment)
            <div class="comment-item">
              <div class="cmt-av" style="background:{{ $comment->role==='superadmin'?'#0f1f38':'#2563eb' }}">
                {{ strtoupper(substr($comment->user->name??'U',0,2)) }}
              </div>
              <div class="cmt-bubble">
                <div class="cmt-meta">
                  <span class="cmt-name">{{ $comment->user->name??($comment->role==='superadmin'?'Superadmin':'User') }}</span>
                  <span class="cmt-role-badge {{ $comment->role==='superadmin'?'badge-admin':'badge-user' }}">{{ $comment->role==='superadmin'?'Admin':'User' }}</span>
                  <span class="cmt-time">{{ $comment->created_at->diffForHumans() }}</span>
                  @if($comment->user_id === auth()->id() && auth()->user()->hasPermission('delete_comments'))
                  <button class="cmt-del-pill" onclick="confirmDeleteComment({{ $comment->id }},'{{ addslashes(Str::limit($comment->body,40)) }}')" title="Delete comment">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                    Delete
                  </button>
                  @endif
                </div>
                <div class="cmt-text">{{ $comment->body }}</div>

                {{-- Files attached to this specific comment --}}
                @if($comment->attachments->count())
                  <div class="cmt-attach-row">
                    @foreach($comment->attachments as $cf)
                      <div style="display:inline-flex;align-items:center;gap:4px;">
                        <a href="{{ $cf->url }}" download="{{ $cf->original_name }}" class="cmt-fchip" title="Download">
                          <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                          {{ $cf->original_name }}
                          <span class="cmt-fchip-sz">{{ $cf->formatted_size }}</span>
                        </a>

                      </div>
                    @endforeach
                  </div>
                @endif
              </div>
            </div>
          @empty
            <div class="cmt-empty">
              <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
              <p>No comments yet</p><span>Start the conversation below</span>
            </div>
          @endforelse
        </div>
      </div>

      {{-- Comment input with file attach --}}
      @if(auth()->user()->hasPermission('add_comments'))
      <form id="cmtForm" action="{{ route('tickets.comments.store', $ticket) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="cmt-input-wrap">
          <div class="cmt-sender">
            <div class="cmt-sender-av">{{ strtoupper(substr(auth()->user()->name??'SA',0,2)) }}</div>
            <div class="cmt-sender-info">
              <span class="name">{{ auth()->user()->name??'Superadmin' }}</span>

            </div>
          </div>
          <div class="cmt-editor">
            <textarea name="body" id="cmtInput" class="cmt-textarea" rows="3"
              placeholder="{{ auth()->user()->hasPermission('upload_attachments') ? 'Write a comment… attach files with the 📎 button or drag & drop below' : 'Write a comment…' }}"
              required onkeydown="handleCmtKey(event)"></textarea>
            <div id="cmtFilePreview" class="cmt-file-preview"></div>
            {{-- Drag-and-drop zone for comment attachments --}}
            @if(auth()->user()->hasPermission('upload_attachments'))
            <div id="cmtDropZone" class="cmt-drop-zone"
                 ondrop="handleCmtDrop(event)" ondragover="handleCmtDragOver(event)" ondragleave="handleCmtDragLeave(event)"
                 onclick="document.getElementById('cmtFileInput').click()">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
              <span id="cmtDropLabel">Attach files — click or drag &amp; drop here</span>
              <span class="cmt-drop-hint">JPG, PNG, LOG, JSON, ZIP · max 25MB</span>
            </div>
            @endif
            <div class="cmt-toolbar">
              <div class="toolbar-left" style="font-size:11px;color:var(--muted-lt)">
                <kbd style="background:var(--bg);border:1px solid var(--border);padding:1px 5px;border-radius:4px;font-size:10px">Ctrl+Enter</kbd> to send quickly
              </div>
              <div class="toolbar-right">
                <input type="hidden" name="role" value="superadmin">
                <button type="submit" class="send-btn" id="cmtSendBtn">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>Send
                </button>
              </div>
            </div>
          </div>
          @if(auth()->user()->hasPermission('upload_attachments'))
          <input type="file" id="cmtFileInput" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.log,.json,.zip" style="display:none" onchange="handleCmtFileSelect(this.files);">
          @endif
        </div>
      </form>
      @endif
    </div>

  </div>

  {{-- RIGHT COLUMN --}}
  <div class="detail-col">

    <div class="card">
      <div class="card-head"><div class="card-head-left"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>Related Tasks (SLA & Delivery)</div></div>
      <div class="card-body" style="padding:0">
        @forelse($ticket->tasks as $t)
          <div style="padding:16px;border-bottom:1px solid var(--border)">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
              <div style="font-size:13.5px;font-weight:800;color:var(--navy)">{{ $t->title }}</div>
              <span class="pill pill-{{ $t->status }}">{{ ucfirst(str_replace('_',' ',$t->status)) }}</span>
            </div>
            <div class="meta-list" style="margin-top:10px">
              <div class="meta-row"><span class="meta-key">Assigned To</span><span class="meta-val" style="display:flex;align-items:center;gap:6px">@if($t->assignee)<div style="width:18px;height:18px;border-radius:4px;background:#2563eb;color:#fff;display:flex;align-items:center;justify-content:center;font-size:8px;font-weight:700">{{ strtoupper(substr($t->assignee->name,0,2)) }}</div>{{ $t->assignee->name }}@else <span style="color:var(--muted-lt)">Unassigned</span> @endif</span></div>
              <div class="meta-row"><span class="meta-key">SLA</span><span class="meta-val">{{ $t->sla_level ?? '—' }}</span></div>
              <div class="meta-row"><span class="meta-key">Estimated Delivery</span><span class="meta-val">{{ $t->estimated_delivery_date ? $t->estimated_delivery_date->format('M d, Y · H:i') : '—' }}</span></div>
              <div class="meta-row"><span class="meta-key">Actual Delivery</span><span class="meta-val">{{ $t->actual_delivery_date ? $t->actual_delivery_date->format('M d, Y · H:i') : '—' }}</span></div>
              <div class="meta-row"><span class="meta-key">QC Test Date</span><span class="meta-val">{{ $t->qc_test_date ? $t->qc_test_date->format('M d, Y · H:i') : '—' }}</span></div>
            </div>
          </div>
        @empty
          <div style="padding:24px 20px;text-align:center;color:var(--muted)">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 10px;opacity:0.3;display:block"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
            <div style="font-size:12.5px;font-weight:600">No tasks linked</div>
            <div style="font-size:11.5px;color:var(--muted-lt);margin-top:2px">SLA & Delivery are now managed in Tasks.</div>
          </div>
        @endforelse
      </div>
    </div>

    <div class="card">
      <div class="card-head"><div class="card-head-left"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>Update Status</div></div>
      <div class="card-body">
        @if(auth()->user()->hasPermission('edit_tickets'))
        <form action="{{ route('tickets.update', $ticket) }}" method="POST">
          @csrf @method('PATCH')
          <select name="status" class="status-select">
            <option value="open" {{ $ticket->status=='open'?'selected':'' }}>Open</option>
            <option value="in_progress" {{ $ticket->status=='in_progress'?'selected':'' }}>In Progress</option>
            <option value="in_review" {{ $ticket->status=='in_review'?'selected':'' }}>In Review</option>
            <option value="resolved" {{ $ticket->status=='resolved'?'selected':'' }}>Resolved</option>
            <option value="closed" {{ $ticket->status=='closed'?'selected':'' }}>Closed</option>
          </select>
          <button type="submit" class="btn-full"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>Save Status</button>
        </form>
        @else
        <p style="font-size:13px;color:var(--muted)">You do not have permission to update the status.</p>
        @endif
      </div>
    </div>

    <div class="card">
      <div class="card-head"><div class="card-head-left"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>Ticket Details</div></div>
      <div class="card-body" style="padding-top:6px;padding-bottom:6px">
        <div class="meta-list">
          <div class="meta-row"><span class="meta-key">System</span><span class="meta-val">{{ $ticket->system??'—' }}</span></div>
          <div class="meta-row"><span class="meta-key">Priority</span><span class="pill pill-{{ $ticket->priority }}">{{ ucfirst($ticket->priority) }}</span></div>
          <div class="meta-row"><span class="meta-key">Impact</span><span class="pill pill-{{ $ticket->impact??'low' }}">{{ ucfirst($ticket->impact??'low') }}</span></div>
          <div class="meta-row"><span class="meta-key">Status</span><span class="pill pill-{{ $ticket->status }}">{{ ucfirst(str_replace('_',' ',$ticket->status)) }}</span></div>
          <div class="meta-row"><span class="meta-key">Due Date</span><span class="meta-val">{{ $ticket->due_date ? \Carbon\Carbon::parse($ticket->due_date)->format('M d, Y') : '—' }}</span></div>
          <div class="meta-row"><span class="meta-key">Date of Issue</span><span class="meta-val">{{ $ticket->created_at->format('M d, Y · H:i') }}</span></div>
          <!-- Delivery dates have been moved to individual Tasks -->
          <div class="meta-row"><span class="meta-key">Updated</span><span class="meta-val">{{ $ticket->updated_at->diffForHumans() }}</span></div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-head"><div class="card-head-left"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>Reported By</div></div>
      <div class="card-body">
        <div class="reporter-row">
          <div class="rep-av">{{ strtoupper(substr($ticket->user->name??'U',0,2)) }}</div>
          <div><div class="rep-name">{{ $ticket->user->name??'Unknown' }}</div><div class="rep-email">{{ $ticket->user->email??'' }}</div></div>
        </div>
      </div>
    </div>

  </div>
</div>

{{-- Edit Modal --}}
<div class="modal-overlay" id="editModal" onclick="closeEditModal()">
  <div class="modal-box" onclick="event.stopPropagation()">
    <div class="modal-head"><h3>Edit Ticket</h3><button class="modal-close" onclick="closeEditModal()">✕</button></div>
    <form action="{{ route('tickets.update', $ticket) }}" method="POST">
      @csrf @method('PATCH')
      <div class="modal-body">
        <div class="form-group"><label>Title *</label><input type="text" name="title" class="form-control" value="{{ $ticket->title }}" required></div>
        <div class="form-group"><label>Description</label><textarea name="description" class="form-control">{{ $ticket->description }}</textarea></div>
        <div class="form-row">
          @if(auth()->user()->hasPermission('select_system'))
          @php $companies = \App\Models\Company::orderBy('name')->pluck('name'); @endphp
          <div class="form-group"><label>System (Company)</label><select name="system" class="form-control">@foreach($companies as $sys)<option value="{{ $sys }}" {{ $ticket->system===$sys?'selected':'' }}>{{ $sys }}</option>@endforeach</select></div>
          @else
          <div class="form-group"><label>System (Company)</label><input type="text" class="form-control" value="{{ $ticket->system }}" disabled /></div>
          @endif
          <div class="form-group"><label>Priority</label><select name="priority" class="form-control">@foreach(['low','medium','high','critical'] as $p)<option value="{{ $p }}" {{ $ticket->priority===$p?'selected':'' }}>{{ ucfirst($p) }}</option>@endforeach</select></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Impact</label><select name="impact" class="form-control">@foreach(['low','medium','high','critical'] as $imp)<option value="{{ $imp }}" {{ ($ticket->impact??'low')===$imp?'selected':'' }}>{{ ucfirst($imp) }}</option>@endforeach</select></div>
          <div class="form-group"><label>Status</label><select name="status" class="form-control"><option value="open" {{ $ticket->status==='open'?'selected':'' }}>Open</option><option value="in_progress" {{ $ticket->status==='in_progress'?'selected':'' }}>In Progress</option><option value="in_review" {{ $ticket->status==='in_review'?'selected':'' }}>In Review</option><option value="resolved" {{ $ticket->status==='resolved'?'selected':'' }}>Resolved</option><option value="closed" {{ $ticket->status==='closed'?'selected':'' }}>Closed</option></select></div>
        </div>
        <div class="form-group" style="margin-top:14px"><label>Due Date</label><input type="date" name="due_date" class="form-control" value="{{ $ticket->due_date?\Carbon\Carbon::parse($ticket->due_date)->format('Y-m-d'):'' }}"></div>
      </div>
      <div class="modal-foot"><button type="button" onclick="closeEditModal()" class="btn-sm">Cancel</button><button type="submit" class="btn-sm btn-primary">Save Changes</button></div>
    </form>
  </div>
</div>

{{-- Delete Ticket Modal --}}
<div class="modal-overlay" id="deleteModal" onclick="closeDeleteModal()">
  <div class="del-modal-box" onclick="event.stopPropagation()">
    <div class="del-modal-body">
      <div class="del-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg></div>
      <h3>Delete Ticket?</h3>
      <p>Ticket <strong>#TK-{{ str_pad($ticket->id,4,'0',STR_PAD_LEFT) }}</strong> will be permanently deleted.<br>This action cannot be undone.</p>
    </div>
    <div class="del-modal-foot">
      <button onclick="closeDeleteModal()" class="btn-sm" style="flex:1;justify-content:center">Cancel</button>
      <button id="deleteTicketConfirmBtn" onclick="doDeleteTicket()" class="btn-sm btn-danger" style="flex:1;justify-content:center">Delete</button>
    </div>
  </div>
</div>

{{-- Delete Attachment Modal --}}
<div class="modal-overlay" id="deleteAttachModal" onclick="closeDeleteAttachModal()">
  <div class="del-modal-box" onclick="event.stopPropagation()">
    <div class="del-modal-body">
      <div class="del-icon"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg></div>
      <h3>Delete Attachment?</h3>
      <p><strong id="deleteAttachName"></strong> will be permanently removed.</p>
    </div>
    <div class="del-modal-foot">
      <button onclick="closeDeleteAttachModal()" class="btn-sm" style="flex:1;justify-content:center">Cancel</button>
      <button id="deleteAttachConfirmBtn" onclick="doDeleteAttachment()" class="btn-sm btn-danger" style="flex:1;justify-content:center">Delete</button>
    </div>
  </div>
</div>
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- Delete Comment Modal --}}
<div class="cmt-del-modal-overlay" id="deleteCmtModal" onclick="closeDeleteCmtModal()">
  <div class="cmt-del-modal-box" onclick="event.stopPropagation()">
    <div class="cmt-del-modal-body">
      <div class="cmt-del-modal-icon">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      </div>
      <h3>Delete this comment?</h3>
      <p>This message will be permanently removed and cannot be recovered.</p>
      <div class="cmt-del-preview" id="deleteCmtPreview"></div>
    </div>
    <div class="cmt-del-modal-foot">
      <button onclick="closeDeleteCmtModal()" class="btn-sm" style="flex:1;justify-content:center">Cancel</button>
      <button id="deleteCmtConfirmBtn" onclick="doDeleteComment()" class="btn-sm btn-danger" style="flex:1;justify-content:center">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
        Yes, Delete
      </button>
    </div>
  </div>
</div>

<div id="toast"><svg id="toastIcon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg><span id="toastMsg"></span></div>

<script>
function openEditModal(){document.getElementById('editModal').classList.add('open');}
function closeEditModal(){document.getElementById('editModal').classList.remove('open');}
function openDeleteModal(){document.getElementById('deleteModal').classList.add('open');}
function closeDeleteModal(){document.getElementById('deleteModal').classList.remove('open');}

function doDeleteTicket(){
  const btn=document.getElementById('deleteTicketConfirmBtn');
  btn.disabled=true; btn.textContent='Deleting…';
  const csrfToken=document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  fetch('{{ route('tickets.destroy', $ticket) }}',{
    method:'POST',
    headers:{'X-CSRF-TOKEN':csrfToken,'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},
    body:'_method=DELETE'
  }).then(r=>{
    if(r.ok||r.redirected){
      window.location.href='{{ route('tickets.index') }}';
    } else {
      showToast('Delete failed',false);
      btn.disabled=false; btn.textContent='Delete';
      closeDeleteModal();
    }
  }).catch(()=>{showToast('Delete failed',false);btn.disabled=false;btn.textContent='Delete';closeDeleteModal();});
}
function handleCmtKey(e){if((e.ctrlKey||e.metaKey)&&e.key==='Enter'){e.preventDefault();submitCmtForm();}}

// ── Comment file attachments (fixed) ──
let cmtFiles=[];
const cmtAllowed=['jpg','jpeg','png','log','json','zip'];
const cmtMaxMB=25;

function handleCmtFileSelect(incoming){
  [...incoming].forEach(f=>{if(!cmtFiles.find(x=>x.name===f.name))cmtFiles.push(f);});
  // Reset input so same file can be re-added after removal
  document.getElementById('cmtFileInput').value='';
  renderCmtPreview();
}
function handleCmtDrop(e){
  e.preventDefault();
  document.getElementById('cmtDropZone').classList.remove('drag-over');
  handleCmtFileSelect(e.dataTransfer.files);
}
function handleCmtDragOver(e){e.preventDefault();document.getElementById('cmtDropZone').classList.add('drag-over');}
function handleCmtDragLeave(){document.getElementById('cmtDropZone').classList.remove('drag-over');}

function removeCmtFile(name){cmtFiles=cmtFiles.filter(f=>f.name!==name);renderCmtPreview();}

function renderCmtPreview(){
  const preview=document.getElementById('cmtFilePreview');
  const label=document.getElementById('cmtDropLabel');
  const validCount=cmtFiles.filter(f=>{
    const ext=f.name.split('.').pop().toLowerCase();
    return cmtAllowed.includes(ext)&&f.size/1024/1024<=cmtMaxMB;
  }).length;

  preview.innerHTML=cmtFiles.map(f=>{
    const ext=f.name.split('.').pop().toLowerCase();
    const valid=cmtAllowed.includes(ext)&&f.size/1024/1024<=cmtMaxMB;
    return `<span class="cmt-fpill" style="${valid?'':'border-color:#fecaca;color:#dc2626;background:#fef2f2'}">
      ${valid?'📎':'❌'} ${f.name}
      <button type="button" onclick="removeCmtFile('${f.name.replace(/'/g,"\\'")}')">✕</button>
    </span>`;
  }).join('');

  label.textContent=validCount
    ? `${validCount} file(s) attached — click or drag to add more`
    : 'Attach files — click or drag & drop here';
}

// Submit comment form via FormData so files are reliably included
function submitCmtForm(){
  const form=document.getElementById('cmtForm');
  const body=document.getElementById('cmtInput').value.trim();
  if(!body){document.getElementById('cmtInput').focus();return;}

  const fd=new FormData(form);
  // Remove any stale attachments[] from the form's own hidden input
  fd.delete('attachments[]');
  // Append valid files from our JS array
  cmtFiles.forEach(f=>{
    const ext=f.name.split('.').pop().toLowerCase();
    if(cmtAllowed.includes(ext)&&f.size/1024/1024<=cmtMaxMB) fd.append('attachments[]',f);
  });

  const btn=document.getElementById('cmtSendBtn');
  btn.disabled=true;btn.innerHTML='Sending…';

  fetch(form.action,{method:'POST',body:fd,headers:{'X-Requested-With':'XMLHttpRequest'}})
    .then(r=>{if(r.redirected){window.location.href=r.url;}else{window.location.reload();}})
    .catch(()=>{btn.disabled=false;btn.innerHTML='<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>Send';showToast('Failed to send',false);});
}

document.addEventListener('DOMContentLoaded',function(){
  document.getElementById('cmtForm').addEventListener('submit',function(e){
    e.preventDefault();
    submitCmtForm();
  });
});


// ── Delete comment ──
let _deleteCmtId=null;
function confirmDeleteComment(id, preview){
  _deleteCmtId=id;
  document.getElementById('deleteCmtPreview').textContent='"'+preview+(preview.length>=40?'…':'')+'"';
  document.getElementById('deleteCmtModal').classList.add('open');
}
function closeDeleteCmtModal(){
  document.getElementById('deleteCmtModal').classList.remove('open');
  _deleteCmtId=null;
}
function doDeleteComment(){
  if(!_deleteCmtId) return;
  const btn=document.getElementById('deleteCmtConfirmBtn');
  btn.disabled=true; btn.innerHTML='Deleting…';
  const csrfToken=document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  fetch(`/tickets/{{ $ticket->id }}/comments/${_deleteCmtId}`,{
    method:'POST',
    headers:{'X-CSRF-TOKEN':csrfToken,'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},
    body:'_method=DELETE'
  }).then(r=>{
    if(r.ok){
      closeDeleteCmtModal();
      showToast('Comment deleted');
      setTimeout(()=>window.location.reload(),600);
    } else {
      showToast('Delete failed',false);
      btn.disabled=false;
      btn.innerHTML='<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg> Yes, Delete';
    }
  }).catch(()=>{showToast('Delete failed',false);btn.disabled=false;});
}

// ── Delete attachment ──
let _deleteAttachId=null;
function confirmDeleteAttachment(id,name){
  _deleteAttachId=id;
  document.getElementById('deleteAttachName').textContent=name;
  document.getElementById('deleteAttachModal').classList.add('open');
}
function closeDeleteAttachModal(){
  document.getElementById('deleteAttachModal').classList.remove('open');
  _deleteAttachId=null;
}
function doDeleteAttachment(){
  if(!_deleteAttachId) return;
  const btn=document.getElementById('deleteAttachConfirmBtn');
  btn.disabled=true; btn.textContent='Deleting…';
  const csrfToken=document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  fetch(`/tickets/{{ $ticket->id }}/attachments/${_deleteAttachId}`,{
    method:'POST',
    headers:{'X-CSRF-TOKEN':csrfToken,'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},
    body:'_method=DELETE'
  }).then(r=>{
    if(r.ok||r.redirected){
      closeDeleteAttachModal();
      showToast('Attachment deleted');
      setTimeout(()=>window.location.reload(),600);
    } else {
      showToast('Delete failed',false);
      btn.disabled=false; btn.textContent='Delete';
    }
  }).catch(()=>{showToast('Delete failed',false);btn.disabled=false;btn.textContent='Delete';});
}

function showToast(msg,ok=true){
  document.getElementById('toastMsg').textContent=msg;
  document.getElementById('toastIcon').setAttribute('stroke',ok?'#4ade80':'#f87171');
  const t=document.getElementById('toast');t.classList.add('show');clearTimeout(t._t);t._t=setTimeout(()=>t.classList.remove('show'),3000);
}

@if(session('success'))
document.addEventListener('DOMContentLoaded',()=>showToast('{{ session('success') }}'));
@endif
</script>
@endsection