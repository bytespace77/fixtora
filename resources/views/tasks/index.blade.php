@extends('layouts.app')

@section('title', 'Tasks – Fixtora')

@section('styles')
<style>
/* ── Breadcrumb & Board Header ─────────────────────────────────── */
.breadcrumb{display:flex;align-items:center;gap:6px;font-size:11px;font-weight:700;letter-spacing:.4px;text-transform:uppercase;color:var(--muted);margin-bottom:18px}
.breadcrumb .sep{color:var(--border-dark)}
.breadcrumb .current{color:var(--text)}
.avatar-stack{display:flex}
.av-stack-item{width:26px;height:26px;border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:9px;font-weight:800;color:#fff;margin-left:-6px;border:2px solid var(--bg);background:var(--blue)}
.av-stack-item:first-child{margin-left:0}
.av-stack-item.av-more{background:var(--bg);color:var(--muted);font-size:8px;border-color:var(--border)}
.filter-btn{display:flex;align-items:center;gap:5px;padding:6px 12px;border:1px solid var(--border);border-radius:7px;background:var(--surface);font-size:12px;font-weight:600;color:var(--text-sub);cursor:pointer;font-family:inherit;transition:all .15s}
.filter-btn:hover{border-color:var(--blue);color:var(--blue)}
.board-header-row{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:18px}
.board-title{font-size:22px;font-weight:800;letter-spacing:-.5px;color:var(--navy)}
.board-desc{font-size:12.5px;color:var(--muted);margin-top:4px}
.view-toggle{display:flex;gap:4px;background:var(--bg);border:1px solid var(--border);border-radius:8px;padding:3px}
.vt-btn{display:flex;align-items:center;gap:5px;padding:5px 10px;border-radius:6px;border:none;background:transparent;font-size:11.5px;font-weight:600;color:var(--muted);cursor:pointer;font-family:inherit;transition:all .15s}
.vt-btn.active{background:var(--surface);color:var(--text);box-shadow:var(--shadow)}

/* ── Kanban Board ──────────────────────────────────────────────── */
.kanban{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:20px}
.kanban-col{min-height:200px}
.col-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px}
.col-label{display:flex;align-items:center;gap:7px;font-size:12.5px;font-weight:700;color:var(--text-sub)}
.col-indicator{width:9px;height:9px;border-radius:50%}
.ci-gray{background:var(--muted-lt)}
.ci-blue{background:var(--blue)}
.ci-green{background:var(--green)}
.col-count{font-size:11px;font-weight:700;background:var(--bg);border:1px solid var(--border);padding:1px 7px;border-radius:20px;color:var(--muted)}
.col-more{background:none;border:none;cursor:pointer;font-size:15px;color:var(--muted);padding:2px 6px;border-radius:5px}
.col-more:hover{background:var(--bg)}
.k-cards-container{min-height:80px}

/* ── Kanban Card ───────────────────────────────────────────────── */
.k-card{background:var(--surface);border:1px solid var(--border);border-radius:9px;padding:14px;margin-bottom:10px;box-shadow:var(--shadow);transition:box-shadow .15s,transform .15s;cursor:grab;user-select:none}
.k-card:hover{box-shadow:var(--shadow-md);transform:translateY(-1px)}
.k-card.done-card{opacity:.7}
.k-card.done-card .k-title{text-decoration:line-through;color:var(--muted)}
.k-card.dragging{opacity:.4;cursor:grabbing}
.k-card-meta{display:flex;align-items:center;justify-content:space-between;margin-bottom:8px}
.ticket-id{font-size:10px;font-weight:700;color:var(--muted-lt);font-family:'DM Mono',monospace}
.priority-badge{font-size:9px;font-weight:800;letter-spacing:.5px;padding:2px 7px;border-radius:4px;text-transform:uppercase}
.pb-low{background:#f0fdf4;color:#15803d}
.pb-medium{background:#fffbeb;color:#b45309}
.pb-high{background:#fff1f2;color:#be123c}
.pb-urgent{background:#fef3c7;color:#92400e;border:1px solid #fde68a}
.k-title{font-size:13px;font-weight:600;line-height:1.4;margin-bottom:6px;color:var(--text)}
.k-desc{font-size:11.5px;color:var(--muted);margin-bottom:8px;line-height:1.5;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical}
.k-progress{margin-bottom:8px}
.progress-bg{height:4px;border-radius:4px;background:var(--bg);overflow:hidden}
.progress-fill{height:100%;border-radius:4px;background:linear-gradient(90deg,var(--blue),#7c3aed);transition:width .5s}
.k-footer{display:flex;align-items:center;justify-content:space-between}
.k-assignee{display:flex;align-items:center;gap:5px}
.k-av{width:22px;height:22px;border-radius:6px;background:var(--blue);display:flex;align-items:center;justify-content:center;font-size:9px;font-weight:700;color:#fff;flex-shrink:0}
.k-av.unassigned{background:var(--bg);border:1px solid var(--border-dark);color:var(--muted-lt)}
.k-av-name{font-size:11px;font-weight:600;color:var(--muted)}
.k-stat{font-size:11px;color:var(--muted)}
.k-action-btn{background:none;border:1px solid var(--border);cursor:pointer;color:var(--muted);padding:4px 7px;border-radius:6px;font-size:11px;display:flex;align-items:center;transition:all .15s}
.k-action-btn:hover{background:var(--bg);color:var(--text);border-color:var(--border-dark)}

/* ── Drag-over highlight ───────────────────────────────────────── */
.kanban-col.drag-over .k-cards-container{background:var(--blue-bg);border-radius:9px;border:2px dashed var(--blue)}

/* ── Velocity + SLA ────────────────────────────────────────────── */
.velocity-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:22px;box-shadow:var(--shadow)}
.velocity-bars{display:flex;align-items:flex-end;gap:10px;height:100px;margin-bottom:6px}
.v-bar-wrap{display:flex;flex-direction:column;align-items:center;gap:4px;flex:1}
.v-bar{width:100%;border-radius:4px 4px 0 0;transition:height .4s}
.v-bar.blue{background:linear-gradient(180deg,var(--blue),#3b82f6)}
.v-bar.gray{background:var(--border)}
.v-bar-label{font-size:9px;font-weight:700;color:var(--muted-lt);letter-spacing:.3px}
.chart-title{font-size:13px;font-weight:700;color:var(--text)}
.sla-float{background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:14px;box-shadow:var(--shadow);text-align:center}
.sla-pct{font-size:32px;font-weight:800;color:var(--navy);letter-spacing:-1px}
.sla-label{font-size:10px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;color:var(--muted);margin-top:2px}
.sla-note{font-size:11px;color:var(--muted);margin-top:6px;line-height:1.5}

/* ── Modal ─────────────────────────────────────────────────────── */
.modal-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.35);z-index:1000;display:none;align-items:center;justify-content:center}
.modal-backdrop.open{display:flex}
.modal{background:var(--surface);border-radius:14px;width:100%;max-width:500px;box-shadow:0 20px 60px rgba(0,0,0,.2);animation:slideUp .2s ease}
@keyframes slideUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:none}}
.modal-header{display:flex;align-items:center;justify-content:space-between;padding:20px 22px 0}
.modal-title{font-size:16px;font-weight:800;color:var(--navy)}
.modal-close{background:none;border:none;cursor:pointer;color:var(--muted);font-size:20px;line-height:1;padding:2px 6px;border-radius:6px}
.modal-close:hover{background:var(--bg);color:var(--text)}
.modal-body{padding:18px 22px}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.form-group{margin-bottom:14px}
.form-group label{display:block;font-size:11.5px;font-weight:700;color:var(--text-sub);margin-bottom:5px;letter-spacing:.2px}
.form-control{width:100%;padding:9px 14px;border:1px solid var(--border);border-radius:9px;font-size:13px;font-family:inherit;color:var(--text);background:#fcfcfc;outline:none;transition:all .2s ease;box-shadow:inset 0 1px 2px rgba(0,0,0,0.02)}
.form-control:focus{border-color:var(--blue);background:#fff;box-shadow:0 0 0 3px rgba(37,99,235,0.1)}
.form-control::placeholder{color:var(--muted-lt)}
textarea.form-control{resize:vertical;min-height:80px}
.modal-footer{padding:0 22px 20px;display:flex;gap:8px;justify-content:flex-end}
.btn-primary{padding:9px 18px;background:linear-gradient(135deg,#2563eb,#1d4ed8);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;transition:all .15s}
.btn-primary:hover{opacity:.9;transform:translateY(-1px)}
.btn-secondary{padding:9px 16px;background:var(--bg);color:var(--text-sub);border:1px solid var(--border);border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;transition:all .15s}
.btn-secondary:hover{border-color:var(--muted)}

/* ── List view ──────────────────────────────────────────────────── */
.task-table{width:100%;border-collapse:collapse;background:var(--surface);border:1px solid var(--border);border-radius:10px;overflow:hidden;box-shadow:var(--shadow)}
.task-table th{padding:11px 16px;text-align:left;font-size:11px;font-weight:700;letter-spacing:.4px;text-transform:uppercase;color:var(--muted);background:var(--bg);border-bottom:1px solid var(--border)}
.task-table td{padding:12px 16px;font-size:13px;border-bottom:1px solid var(--border);vertical-align:middle}
.task-table tr:last-child td{border-bottom:none}
.task-table tr:hover td{background:var(--bg)}

/* ── Deadline pills ─────────────────────────────────────────────── */
.deadline-item{font-size:12.5px;font-weight:600;color:var(--text);display:flex;align-items:center;gap:6px;margin-bottom:6px}
</style>
@endsection

@section('content')

{{-- ── Board Header ────────────────────────────────────────────── --}}
<div class="board-header-row">
  <div>
    <div class="board-title">Team Sprint: Current Tasks</div>
    <div class="board-desc">Managing internal architectural support tickets and resource allocation.</div>
  </div>
  <div style="display:flex;align-items:center;gap:12px">
    <div class="avatar-stack">
      @foreach($users->take(3) as $u)
        <div class="av-stack-item" style="background:{{ ['#2d6a4f','#7b2d8b','#c05621','#1d4ed8','#0d9488'][($u->id % 5)] }}">
          {{ strtoupper(substr($u->name,0,2)) }}
        </div>
      @endforeach
      @if($users->count() > 3)
        <div class="av-stack-item av-more">+{{ $users->count() - 3 }}</div>
      @endif
    </div>
    <div class="view-toggle">
      <button class="vt-btn active" id="boardViewBtn" onclick="setView('board')">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
        Board View
      </button>
      <button class="vt-btn" id="listViewBtn" onclick="setView('list')">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        List View
      </button>
    </div>
    @if(auth()->user()->hasPermission('create_tasks'))
    <button class="btn-primary" style="padding:8px 16px;font-size:12.5px" onclick="openModal('todo')">
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right:4px"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      New Task
    </button>
    @endif
  </div>
</div>

{{-- ── BOARD VIEW ──────────────────────────────────────────────── --}}
<div id="boardView">
  <div class="kanban">
    {{-- TO DO --}}
    <div class="kanban-col" id="col-todo" data-status="todo">
      <div class="col-header">
        <div class="col-label">
          <div class="col-indicator ci-gray"></div>
          To Do
          <span class="col-count" id="count-todo">{{ $todo->count() }}</span>
        </div>
        @if(auth()->user()->hasPermission('create_tasks'))
        <button class="col-more" onclick="openModal('todo')">···</button>
        @endif
      </div>
      <div class="k-cards-container" id="cards-todo">
        @foreach($todo as $task)
          @include('tasks._card', ['task' => $task])
        @endforeach
      </div>
    </div>

    {{-- DOING --}}
    <div class="kanban-col" id="col-doing" data-status="doing">
      <div class="col-header">
        <div class="col-label">
          <div class="col-indicator ci-blue"></div>
          Doing
          <span class="col-count" id="count-doing">{{ $doing->count() }}</span>
        </div>
        <button class="col-more">···</button>
      </div>
      <div class="k-cards-container" id="cards-doing">
        @foreach($doing as $task)
          @include('tasks._card', ['task' => $task])
        @endforeach
      </div>
    </div>

    {{-- DONE --}}
    <div class="kanban-col" id="col-done" data-status="done">
      <div class="col-header">
        <div class="col-label">
          <div class="col-indicator ci-green"></div>
          Done
          <span class="col-count" id="count-done">{{ $done->count() }}</span>
        </div>
        <button class="col-more" style="opacity:.5">↺</button>
      </div>
      <div class="k-cards-container" id="cards-done">
        @foreach($done as $task)
          @include('tasks._card', ['task' => $task])
        @endforeach
      </div>
    </div>
  </div>
</div>

{{-- ── LIST VIEW ───────────────────────────────────────────────── --}}
<div id="listView" style="display:none;margin-bottom:20px">
  <table class="task-table">
    <thead>
      <tr>
        <th>#</th><th>Title</th><th>Priority</th><th>Status</th>
        <th>Assignee</th><th>Due Date</th><th>Actions</th>
      </tr>
    </thead>
    <tbody id="listBody">
      @foreach($todo->concat($doing)->concat($done) as $task)
      <tr id="list-row-{{ $task->id }}">
        <td style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted-lt)">#TK-{{ str_pad($task->id,4,'0',STR_PAD_LEFT) }}</td>
        <td style="font-weight:600;max-width:260px">
          {{ $task->title }}
          @if($task->ticket)
            <a href="{{ route('tickets.show', $task->ticket_id) }}" style="font-size:10px;font-weight:700;color:var(--blue);text-decoration:none;background:#eff6ff;padding:2px 6px;border-radius:4px;margin-left:6px" target="_blank" title="{{ $task->ticket->title }}">
              #TIC-{{ str_pad($task->ticket_id, 4, '0', STR_PAD_LEFT) }}
            </a>
          @endif
        </td>
        <td><span class="priority-badge pb-{{ $task->priority }}">{{ strtoupper($task->priority) }}</span></td>
        <td>
          <select class="form-control" style="padding:4px 8px;font-size:12px;width:auto"
                  onchange="quickStatusUpdate({{ $task->id }}, this.value)">
            <option value="todo" {{ $task->status=='todo'?'selected':'' }}>To Do</option>
            <option value="doing" {{ $task->status=='doing'?'selected':'' }}>Doing</option>
            <option value="done" {{ $task->status=='done'?'selected':'' }}>Done</option>
          </select>
        </td>
        <td>
          @if($task->assignee)
            <div style="display:flex;align-items:center;gap:6px">
              <div class="k-av" style="background:{{ ['#2563eb','#7c3aed','#0d9488','#ea580c','#16a34a'][$task->assignee->id % 5] }}">
                {{ strtoupper(substr($task->assignee->name,0,2)) }}
              </div>
              <span style="font-size:12px">{{ $task->assignee->name }}</span>
            </div>
          @else
            <span style="color:var(--muted-lt);font-size:12px">Unassigned</span>
          @endif
        </td>
        <td style="font-size:12px;color:var(--muted)">{{ $task->due_date ? $task->due_date->format('M d, Y') : '—' }}</td>
        <td>
          @if(auth()->user()->hasPermission('delete_tasks'))
          <button class="k-action-btn" onclick="deleteTask({{ $task->id }})" title="Delete"
                  style="color:var(--red);border:1px solid #fee2e2;padding:4px 8px">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/></svg>
          </button>
          @endif
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

{{-- ── Velocity + SLA ──────────────────────────────────────────── --}}
<div style="display:grid;grid-template-columns:1fr 220px;gap:14px">
  <div class="velocity-card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
      <div class="chart-title">Workload Velocity <span style="font-size:11px;font-weight:400;color:var(--muted)">(tasks completed this week)</span></div>
    </div>
    <div class="velocity-bars">
      @php $maxV = max(array_column($velocity,'count')) ?: 1; @endphp
      @foreach($velocity as $v)
        @php $height = max(10, round(($v['count'] / $maxV) * 100)); $isWeekend = in_array($v['label'],['SAT','SUN']); @endphp
        <div class="v-bar-wrap">
          <div class="v-bar {{ $isWeekend ? 'gray' : 'blue' }}" style="height:{{ $height }}px"></div>
          <span class="v-bar-label">{{ $v['label'] }}</span>
        </div>
      @endforeach
    </div>
  </div>
  <div>
    <div class="sla-float">
      <div class="sla-pct">{{ $slaRate }}%</div>
      <div class="sla-label">SLA Success Rate</div>
      <div class="sla-note">Architectural tasks tracked against due dates.</div>
    </div>
    <div style="margin-top:10px;background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:14px;box-shadow:var(--shadow)">
      <div style="font-size:10px;font-weight:700;letter-spacing:.6px;text-transform:uppercase;color:var(--muted);margin-bottom:8px">Upcoming Deadlines</div>
      @forelse($deadlines as $dl)
        <div class="deadline-item">
          <span style="color:{{ $dl->priority=='urgent'||$dl->priority=='high' ? 'var(--red)' : 'var(--orange)' }}">●</span>
          {{ Str::limit($dl->title, 28) }}
          <span style="margin-left:auto;font-size:10.5px;color:var(--muted-lt)">{{ $dl->due_date->format('M d') }}</span>
        </div>
      @empty
        <div style="font-size:12px;color:var(--muted-lt)">No upcoming deadlines.</div>
      @endforelse
    </div>
  </div>
</div>

{{-- ── New Task Modal ──────────────────────────────────────────── --}}
<div class="modal-backdrop" id="taskModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Create New Task</div>
      <button class="modal-close" onclick="closeModal()">×</button>
    </div>
    <form id="taskForm" onsubmit="submitTask(event)">
      <div class="modal-body">
        @csrf
        <div class="form-group">
          <label>Task Title *</label>
          <input type="text" name="title" class="form-control" placeholder="e.g. Audit server logs for latency…" required>
        </div>
        <div class="form-group">
          <label>Description</label>
          <textarea name="description" class="form-control" placeholder="Describe the task…"></textarea>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Priority *</label>
            <select name="priority" class="form-control" required>
              <option value="low">Low</option>
              <option value="medium" selected>Medium</option>
              <option value="high">High</option>
              <option value="urgent">Urgent</option>
            </select>
          </div>
          <div class="form-group">
            <label>Status *</label>
            <select name="status" id="modal-status" class="form-control" required>
              <option value="todo">To Do</option>
              <option value="doing">Doing</option>
              <option value="done">Done</option>
            </select>
          </div>
        </div>
          @if(auth()->user()->isSuperAdmin())
          <div class="form-group">
            <label>Assign To</label>
            <select name="assigned_to" class="form-control">
              <option value="">Unassigned</option>
              @foreach($users as $u)
                <option value="{{ $u->id }}">{{ $u->name }}</option>
              @endforeach
            </select>
          </div>
          @endif
          
        <div class="form-row">
          <div class="form-group">
            <label>Ticket (Optional)</label>
            <select name="ticket_id" class="form-control">
              <option value="">No Ticket</option>
              @foreach($tickets as $t)
                <option value="{{ $t->id }}">#TIC-{{ str_pad($t->id,4,'0',STR_PAD_LEFT) }} - {{ Str::limit($t->title, 30) }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label>Due Date</label>
            <input type="date" name="due_date" class="form-control">
          </div>
        </div>
        
        @if(auth()->user()->hasGlobalDataAccess())
        <div style="margin:20px 0 16px;border-top:1px dashed var(--border);padding-top:16px;display:flex;align-items:center;gap:6px">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--blue)"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          <strong style="font-size:13px;color:var(--navy)">SLA & Timeline Tracking</strong>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>SLA Level</label>
            <select name="sla_level" class="form-control">
              <option value="">Select SLA</option>
              <option value="Low">Low</option>
              <option value="Medium">Medium</option>
              <option value="High">High</option>
              <option value="Critical">Critical</option>
            </select>
          </div>
          <div class="form-group">
            <label>Estimated Delivery</label>
            <input type="datetime-local" name="estimated_delivery_date" class="form-control">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Actual Delivery</label>
            <input type="datetime-local" name="actual_delivery_date" class="form-control">
          </div>
          <div class="form-group">
            <label>QC Test Date</label>
            <input type="datetime-local" name="qc_test_date" class="form-control">
          </div>
        </div>
        @endif
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-secondary" onclick="closeModal()">Cancel</button>
        <button type="submit" class="btn-primary">Create Task</button>
      </div>
    </form>
  </div>
</div>

{{-- ── Edit Task Modal ──────────────────────────────────────────── --}}
<div class="modal-backdrop" id="editModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Edit Task</div>
      <button class="modal-close" onclick="closeEditModal()">×</button>
    </div>
    <form id="editForm" onsubmit="submitEdit(event)">
      <div class="modal-body">
        <input type="hidden" id="edit-id">
        <div class="form-group">
          <label>Task Title *</label>
          <input type="text" id="edit-title" class="form-control" required>
        </div>
        <div class="form-group">
          <label>Description</label>
          <textarea id="edit-description" class="form-control"></textarea>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Priority *</label>
            <select id="edit-priority" class="form-control">
              <option value="low">Low</option>
              <option value="medium">Medium</option>
              <option value="high">High</option>
              <option value="urgent">Urgent</option>
            </select>
          </div>
          <div class="form-group">
            <label>Status *</label>
            <select id="edit-status" class="form-control">
              <option value="todo">To Do</option>
              <option value="doing">Doing</option>
              <option value="done">Done</option>
            </select>
          </div>
        </div>
          @if(auth()->user()->isSuperAdmin())
          <div class="form-group">
            <label>Assign To</label>
            <select id="edit-assigned_to" class="form-control">
              <option value="">Unassigned</option>
              @foreach($users as $u)
                <option value="{{ $u->id }}">{{ $u->name }}</option>
              @endforeach
            </select>
          </div>
          @endif
          
        <div class="form-row">
          <div class="form-group">
            <label>Ticket (Optional)</label>
            <select id="edit-ticket_id" class="form-control">
              <option value="">No Ticket</option>
              @foreach($tickets as $t)
                <option value="{{ $t->id }}">#TIC-{{ str_pad($t->id,4,'0',STR_PAD_LEFT) }} - {{ Str::limit($t->title, 30) }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label>Due Date</label>
            <input type="date" id="edit-due_date" class="form-control">
          </div>
        </div>

        @if(auth()->user()->hasGlobalDataAccess())
        <div style="margin:20px 0 16px;border-top:1px dashed var(--border);padding-top:16px;display:flex;align-items:center;gap:6px">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--blue)"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          <strong style="font-size:13px;color:var(--navy)">SLA & Timeline Tracking</strong>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>SLA Level</label>
            <select id="edit-sla_level" class="form-control">
              <option value="">Select SLA</option>
              <option value="Low">Low</option>
              <option value="Medium">Medium</option>
              <option value="High">High</option>
              <option value="Critical">Critical</option>
            </select>
          </div>
          <div class="form-group">
            <label>Estimated Delivery</label>
            <input type="datetime-local" id="edit-estimated_delivery_date" class="form-control">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Actual Delivery</label>
            <input type="datetime-local" id="edit-actual_delivery_date" class="form-control">
          </div>
          <div class="form-group">
            <label>QC Test Date</label>
            <input type="datetime-local" id="edit-qc_test_date" class="form-control">
          </div>
        </div>
        @endif
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-secondary" onclick="closeEditModal()">Cancel</button>
        <button type="submit" class="btn-primary">Save Changes</button>
      </div>
    </form>
  </div>
</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

/* ════════════════════════════════════════════════════════════════
   DELEGATED EVENT LISTENER — single listener on #boardView handles
   ALL card interactions: drag, edit button, delete button.
   This is the only reliable way when SVG children are involved.
════════════════════════════════════════════════════════════════ */
const boardView = document.getElementById('boardView');

/* ── Click delegation: edit & delete buttons ─────────────────── */
boardView.addEventListener('click', function(e) {
  // Walk up from the actual clicked element (could be SVG path)
  const editBtn   = e.target.closest('.js-edit-btn');
  const deleteBtn = e.target.closest('.js-delete-btn');

  if (editBtn) {
    e.stopPropagation();
    // Read task JSON from the parent .k-card's data-task attribute
    const card = editBtn.closest('.k-card');
    const task = JSON.parse(card.dataset.task);
    openEditModal(task);
    return;
  }

  if (deleteBtn) {
    e.stopPropagation();
    const id = deleteBtn.dataset.id;
    deleteTask(id);
    return;
  }
});

/* ── Drag & Drop — delegated via mousedown to set draggable ──── */
// We use a mousedown approach: set draggable=true only when the mousedown
// target is NOT a button, so button clicks are never intercepted by drag.
boardView.addEventListener('mousedown', function(e) {
  const card = e.target.closest('.k-card');
  if (!card) return;
  // If the click originates from a button (or inside one), disable drag
  if (e.target.closest('button')) {
    card.setAttribute('draggable', 'false');
  } else {
    card.setAttribute('draggable', 'true');
  }
});

// Restore draggable after mouseup so cards can be dragged again later
boardView.addEventListener('mouseup', function(e) {
  const card = e.target.closest('.k-card');
  if (card) card.setAttribute('draggable', 'true');
});

/* ── dragstart / dragend on boardView ────────────────────────── */
let dragId         = null;
let dragFromStatus = null;

boardView.addEventListener('dragstart', function(e) {
  const card = e.target.closest('.k-card');
  if (!card || card.getAttribute('draggable') === 'false') {
    e.preventDefault();
    return;
  }
  dragId         = card.dataset.id;
  dragFromStatus = card.dataset.status;
  card.classList.add('dragging');
  e.dataTransfer.effectAllowed = 'move';
});

boardView.addEventListener('dragend', function(e) {
  const card = e.target.closest('.k-card');
  if (card) card.classList.remove('dragging');
});

/* ── Drop zone events on each column ────────────────────────── */
document.querySelectorAll('.kanban-col').forEach(col => {
  col.addEventListener('dragover', function(e) {
    e.preventDefault();
    this.classList.add('drag-over');
  });
  col.addEventListener('dragleave', function(e) {
    this.classList.remove('drag-over');
  });
  col.addEventListener('drop', async function(e) {
    e.preventDefault();
    this.classList.remove('drag-over');
    const newStatus = this.dataset.status;
    if (!dragId || dragFromStatus === newStatus) { dragId = null; return; }

    const card = document.querySelector(`.k-card[data-id="${dragId}"]`);
    if (card) {
      card.classList.remove('dragging');
      card.dataset.status = newStatus;
      card.classList.toggle('done-card', newStatus === 'done');

      // Update task JSON stored on card so edit modal reflects new status
      try {
        const t = JSON.parse(card.dataset.task);
        t.status = newStatus;
        card.dataset.task = JSON.stringify(t);
      } catch(err) {}

      document.getElementById(`cards-${newStatus}`).appendChild(card);
      updateCount(dragFromStatus);
      updateCount(newStatus);
    }
    
    const rowSelect = document.querySelector(`#list-row-${dragId} select`);
    if (rowSelect) rowSelect.value = newStatus;

    const id = dragId;
    dragId   = null;
    await fetch(`/tasks/${id}`, {
      method:  'POST',
      headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body:    JSON.stringify({ _method: 'PATCH', status: newStatus })
    });
  });
});

/* ── View toggle ─────────────────────────────────────────────── */
function setView(v) {
  document.getElementById('boardView').style.display = v === 'board' ? '' : 'none';
  document.getElementById('listView').style.display  = v === 'list'  ? '' : 'none';
  document.getElementById('boardViewBtn').classList.toggle('active', v === 'board');
  document.getElementById('listViewBtn').classList.toggle('active',  v === 'list');
}
function toggleView() {
  setView(document.getElementById('listView').style.display !== 'none' ? 'board' : 'list');
}

/* ── Create Modal ────────────────────────────────────────────── */
function openModal(status = 'todo') {
  document.getElementById('taskModal').classList.add('open');
  document.getElementById('modal-status').value = status;
}
function closeModal() {
  document.getElementById('taskModal').classList.remove('open');
  document.getElementById('taskForm').reset();
}
document.getElementById('taskModal').addEventListener('click', function(e) {
  if (e.target === this) closeModal();
});

async function submitTask(e) {
  e.preventDefault();
  const fd  = new FormData(e.target);
  const res = await fetch('{{ route('tasks.store') }}', {
    method:  'POST',
    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    body:    fd
  });
  const data = await res.json();
  if (data.success) { appendCard(data.task); closeModal(); }
}

/* ── Build card HTML (for newly created cards via AJAX) ──────── */
const COLORS = ['#2563eb','#7c3aed','#0d9488','#ea580c','#16a34a'];
function escHtml(s) {
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function cardHtml(t) {
  const isDone   = t.status === 'done';
  const ticketId = `#TK-${String(t.id).padStart(4,'0')}`;
  const taskJson = JSON.stringify({
    id: t.id, title: t.title, description: t.description ?? '',
    priority: t.priority, status: t.status,
    assigned_to: t.assigned_to ?? '', due_date: t.due_date ?? '',
    ticket_id: t.ticket_id ?? '',
    sla_level: t.sla_level ?? '',
    estimated_delivery_date: t.estimated_delivery_date ?? '',
    actual_delivery_date: t.actual_delivery_date ?? '',
    qc_test_date: t.qc_test_date ?? ''
  }).replace(/'/g, '&#39;');
  const badge = isDone
    ? `<span style="font-size:9.5px;font-weight:700;color:var(--green)">● RESOLVED</span>`
    : `<span class="priority-badge pb-${t.priority}">${t.priority.toUpperCase()}</span>`;
  const assigneeHtml = t.assignee
    ? `<div class="k-av" style="background:${COLORS[t.assignee.id%5]}">${t.assignee.name.split(' ').map(w=>w[0]).join('').toUpperCase().slice(0,2)}</div><span class="k-av-name">${escHtml(t.assignee.name)}</span>`
    : `<div class="k-av unassigned"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="pointer-events:none"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg></div><span class="k-av-name" style="color:var(--muted-lt)">Unassigned</span>`;

  const formatSlaDate = (dt) => {
    if (!dt) return '';
    const d = new Date(dt);
    const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    let hours = d.getHours();
    const ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12; 
    const minutes = d.getMinutes() < 10 ? '0' + d.getMinutes() : d.getMinutes();
    return `${months[d.getMonth()]} ${String(d.getDate()).padStart(2,'0')}, ${hours}:${minutes} ${ampm}`;
  };

  let slaHtml = '';
  @if(auth()->user()->hasGlobalDataAccess())
  if (t.sla_level || t.estimated_delivery_date || t.actual_delivery_date) {
    let rows = '';
    if (t.sla_level) {
      rows += `<div style="display:flex;justify-content:space-between;margin-bottom:4px"><span style="color:var(--muted)">SLA Level</span><span style="font-weight:700;color:var(--navy)">${escHtml(t.sla_level.toString())}</span></div>`;
    }
    if (t.estimated_delivery_date) {
      rows += `<div style="display:flex;justify-content:space-between;margin-bottom:${t.actual_delivery_date ? '4px' : '0'}"><span style="color:var(--muted)">Est. Delivery</span><span style="font-weight:600;color:var(--text)">${formatSlaDate(t.estimated_delivery_date)}</span></div>`;
    }
    if (t.actual_delivery_date) {
      rows += `<div style="display:flex;justify-content:space-between"><span style="color:var(--muted)">Actual Delivery</span><span style="font-weight:600;color:var(--text)">${formatSlaDate(t.actual_delivery_date)}</span></div>`;
    }
    if (rows) {
      slaHtml = `<div style="background:#f8fafc;border:1px solid var(--border);border-radius:6px;padding:8px 10px;margin-bottom:10px;font-size:10.5px">${rows}</div>`;
    }
  }
  @endif

  return `
  <div class="k-card${isDone?' done-card':''}" data-id="${t.id}" data-status="${t.status}" data-task='${taskJson}'>
    <div class="k-card-meta">
      <div style="display:flex;align-items:center;gap:6px;">
        <span class="ticket-id">${ticketId}</span>
        ${t.ticket ? `<a href="/tickets/${t.ticket_id}" style="font-size:10px;font-weight:700;color:var(--blue);text-decoration:none;background:#eff6ff;padding:2px 6px;border-radius:4px" target="_blank" title="${escHtml(t.ticket.title)}">#TIC-${String(t.ticket_id).padStart(4,'0')}</a>` : ''}
      </div>
      ${badge}
    </div>
    <div class="k-title">${escHtml(t.title)}</div>
    ${t.description ? `<div class="k-desc">${escHtml(t.description)}</div>` : ''}
    ${slaHtml}
    <div class="k-footer" style="margin-top:10px">
      <div class="k-assignee">${assigneeHtml}</div>
      <div style="display:flex;align-items:center;gap:4px">
        @if(auth()->user()->hasPermission('edit_tasks'))
        <button class="k-action-btn js-edit-btn" title="Edit" data-id="${t.id}">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="pointer-events:none">
            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
          </svg>
        </button>
        @endif
        @if(auth()->user()->hasPermission('delete_tasks'))
        <button class="k-action-btn js-delete-btn" title="Delete" style="color:var(--red)" data-id="${t.id}">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="pointer-events:none">
            <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/>
          </svg>
        </button>
        @endif
      </div>
    </div>
  </div>`;
}

function listRowHtml(t) {
  const ticketId = `#TK-${String(t.id).padStart(4,'0')}`;
  const badge = `<span class="priority-badge pb-${t.priority}">${t.priority.toUpperCase()}</span>`;
  const assigneeHtml = t.assignee
    ? `<div style="display:flex;align-items:center;gap:6px"><div class="k-av" style="background:${COLORS[t.assignee.id%5]}">${t.assignee.name.split(' ').map(w=>w[0]).join('').toUpperCase().slice(0,2)}</div><span style="font-size:12px">${escHtml(t.assignee.name)}</span></div>`
    : `<span style="color:var(--muted-lt);font-size:12px">Unassigned</span>`;
  
  let dueDateHtml = '—';
  if (t.due_date) {
      const d = new Date(t.due_date);
      const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
      dueDateHtml = `${months[d.getMonth()]} ${String(d.getDate()).padStart(2,'0')}, ${d.getFullYear()}`;
  }

  const ticketHtml = t.ticket ? `<a href="/tickets/${t.ticket_id}" style="font-size:10px;font-weight:700;color:var(--blue);text-decoration:none;background:#eff6ff;padding:2px 6px;border-radius:4px;margin-left:6px" target="_blank" title="${escHtml(t.ticket.title)}">#TIC-${String(t.ticket_id).padStart(4,'0')}</a>` : '';

  return `
      <tr id="list-row-${t.id}">
        <td style="font-family:'DM Mono',monospace;font-size:11px;color:var(--muted-lt)">${ticketId}</td>
        <td style="font-weight:600;max-width:260px">
          ${escHtml(t.title)}
          ${ticketHtml}
        </td>
        <td>${badge}</td>
        <td>
          <select class="form-control" style="padding:4px 8px;font-size:12px;width:auto" onchange="quickStatusUpdate(${t.id}, this.value)">
            <option value="todo" ${t.status==='todo'?'selected':''}>To Do</option>
            <option value="doing" ${t.status==='doing'?'selected':''}>Doing</option>
            <option value="done" ${t.status==='done'?'selected':''}>Done</option>
          </select>
        </td>
        <td>${assigneeHtml}</td>
        <td style="font-size:12px;color:var(--muted)">${dueDateHtml}</td>
        <td>
          @if(auth()->user()->hasPermission('delete_tasks'))
          <button class="k-action-btn" onclick="deleteTask(${t.id})" title="Delete" style="color:var(--red);border:1px solid #fee2e2;padding:4px 8px">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/></svg>
          </button>
          @endif
        </td>
      </tr>
  `;
}

function appendCard(task) {
  const container = document.getElementById(`cards-${task.status}`);
  if (container) container.insertAdjacentHTML('afterbegin', cardHtml(task));
  updateCount(task.status);
  
  const listBody = document.getElementById('listBody');
  if (listBody) listBody.insertAdjacentHTML('afterbegin', listRowHtml(task));
}

/* ── Edit Modal ──────────────────────────────────────────────── */
function openEditModal(task) {
  document.getElementById('edit-id').value          = task.id;
  document.getElementById('edit-title').value       = task.title;
  document.getElementById('edit-description').value = task.description ?? '';
  document.getElementById('edit-priority').value    = task.priority;
  document.getElementById('edit-status').value      = task.status;
  const assignedEl = document.getElementById('edit-assigned_to');
  if (assignedEl) assignedEl.value = task.assigned_to ?? '';
  document.getElementById('edit-ticket_id').value   = task.ticket_id ?? '';
  document.getElementById('edit-due_date').value    = task.due_date ?? '';
  const slaEl = document.getElementById('edit-sla_level');
  if (slaEl) slaEl.value = task.sla_level ?? '';
  
  const formatDT = (dt) => {
    if (!dt) return '';
    if (dt.includes('T')) return dt.slice(0, 16);
    return dt.replace(' ', 'T').slice(0, 16);
  };
  const estEl = document.getElementById('edit-estimated_delivery_date');
  if (estEl) estEl.value = formatDT(task.estimated_delivery_date);
  
  const actEl = document.getElementById('edit-actual_delivery_date');
  if (actEl) actEl.value = formatDT(task.actual_delivery_date);
  
  const qcEl = document.getElementById('edit-qc_test_date');
  if (qcEl) qcEl.value = formatDT(task.qc_test_date);

  document.getElementById('editModal').classList.add('open');
}
function closeEditModal() {
  document.getElementById('editModal').classList.remove('open');
}
document.getElementById('editModal').addEventListener('click', function(e) {
  if (e.target === this) closeEditModal();
});
async function submitEdit(e) {
  e.preventDefault();
  const id = document.getElementById('edit-id').value;
  const payload = {
    title:       document.getElementById('edit-title').value,
    description: document.getElementById('edit-description').value,
    priority:    document.getElementById('edit-priority').value,
    status:      document.getElementById('edit-status').value,
    ticket_id:   document.getElementById('edit-ticket_id').value,
    due_date:    document.getElementById('edit-due_date').value,
  };
  
  const assignedEl = document.getElementById('edit-assigned_to');
  if (assignedEl) {
    payload.assigned_to = assignedEl.value;
  }
  
  if (document.getElementById('edit-sla_level')) {
    payload.sla_level               = document.getElementById('edit-sla_level').value;
    payload.estimated_delivery_date = document.getElementById('edit-estimated_delivery_date').value;
    payload.actual_delivery_date    = document.getElementById('edit-actual_delivery_date').value;
    payload.qc_test_date            = document.getElementById('edit-qc_test_date').value;
  }
  const res  = await fetch(`/tasks/${id}`, {
    method:  'PATCH',
    headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
    body:    JSON.stringify(payload)
  });
  const data = await res.json();
  if (data.success) { closeEditModal(); window.location.reload(); }
}

/* ── Quick status (list view) ────────────────────────────────── */
async function quickStatusUpdate(id, status) {
  const card = document.querySelector(`.k-card[data-id="${id}"]`);
  if (card) {
    const oldStatus = card.dataset.status;
    if (oldStatus !== status) {
      card.dataset.status = status;
      card.classList.toggle('done-card', status === 'done');
      
      try {
        const t = JSON.parse(card.dataset.task);
        t.status = status;
        card.dataset.task = JSON.stringify(t);
      } catch(err) {}

      const container = document.getElementById(`cards-${status}`);
      if(container) container.appendChild(card);
      updateCount(oldStatus);
      updateCount(status);
    }
  }

  await fetch(`/tasks/${id}`, {
    method:  'POST',
    headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
    body:    JSON.stringify({ _method: 'PATCH', status })
  });
}

/* ── Delete task ─────────────────────────────────────────────── */
async function deleteTask(id) {
  if (!confirm('Delete this task?')) return;
  const res = await fetch(`/tasks/${id}`, {
    method:  'POST',
    headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
    body:    JSON.stringify({ _method: 'DELETE' })
  });
  const data = await res.json();
  if (data.success) {
    const card = document.querySelector(`.k-card[data-id="${id}"]`);
    if (card) { const st = card.dataset.status; card.remove(); updateCount(st); }
    const row = document.getElementById(`list-row-${id}`);
    if (row) row.remove();
  }
}

/* ── Count badges ────────────────────────────────────────────── */
function updateCount(status) {
  const container = document.getElementById(`cards-${status}`);
  const badge     = document.getElementById(`count-${status}`);
  if (container && badge) badge.textContent = container.querySelectorAll('.k-card').length;
}
</script>
@endsection