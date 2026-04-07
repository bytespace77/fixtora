{{--
  Partial: tasks/_card.blade.php
--}}
@php
  $colors   = ['#2563eb','#7c3aed','#0d9488','#ea580c','#16a34a'];
  $taskCode = '#' . ($task->task_company_code ?? 'XX') . '-' . str_pad((string)($task->task_company_seq ?? 0), 4, '0', STR_PAD_LEFT);
  $isDone   = $task->status === 'done';

  $taskData = [
    'id'          => $task->id,
    'title'       => $task->title,
    'description' => $task->description ?? '',
    'priority'    => $task->priority,
    'status'      => $task->status,
    'assigned_to' => $task->assigned_to ?? '',
    'due_date'    => $task->due_date ? $task->due_date->format('Y-m-d') : '',
    'progress'    => $task->progress ?? 0,
    'ticket_id'   => $task->ticket_id ?? '',
    'task_company_code' => $task->task_company_code ?? '',
    'task_company_seq'  => $task->task_company_seq ?? 0,
    'ticket_company_code' => $task->ticket_company_code ?? '',
    'ticket_company_seq'  => $task->ticket_company_seq ?? 0,
    'sla_level'               => $task->sla_level ?? '',
    'estimated_delivery_date' => $task->estimated_delivery_date ? $task->estimated_delivery_date->format('Y-m-d\TH:i') : '',
    'actual_delivery_date'    => $task->actual_delivery_date ? $task->actual_delivery_date->format('Y-m-d\TH:i') : '',
    'qc_test_date'            => $task->qc_test_date ? $task->qc_test_date->format('Y-m-d\TH:i') : '',
    'assignee'    => $task->assignee ? [
        'id'     => $task->assignee->id,
        'name'   => $task->assignee->name,
        'avatar' => $task->assignee->avatar ?? null,
    ] : null,
  ];

@endphp

{{--
  NO inline ondragstart / onclick handlers on buttons.
  All events are wired in index.blade.php via delegated listeners on
  #boardView, which is far more reliable when SVG children are clicked.
--}}
<div class="k-card {{ $isDone ? 'done-card' : '' }}"
     data-id="{{ $task->id }}"
     data-status="{{ $task->status }}"
     data-task='@json($taskData)'>

  <div class="k-card-meta">
    <div style="display:flex;align-items:center;gap:6px;">
      <span class="ticket-id">{{ $taskCode }}</span>
      @if($task->ticket)
        <a href="{{ route('tickets.show', $task->ticket_id) }}" style="font-size:10px;font-weight:700;color:var(--blue);text-decoration:none;background:#eff6ff;padding:2px 6px;border-radius:4px" target="_blank" title="{{ $task->ticket->title }}">
          #TK-{{ str_pad($task->ticket_id, 4, '0', STR_PAD_LEFT) }}
        </a>
      @endif
    </div>
    @if($isDone)
      <span style="font-size:9.5px;font-weight:700;color:var(--green)">● RESOLVED</span>
    @else
      <span class="priority-badge pb-{{ $task->priority }}">{{ strtoupper($task->priority) }}</span>
    @endif
  </div>

  <div class="k-title">
    @php $cName = $task->company->name ?? $task->ticket->company->name ?? ''; @endphp
    @if($cName)
      <span style="font-size:10px;font-weight:800;color:#7c3aed;text-transform:uppercase;margin-right:4px;">[{{ $cName }}]</span>
    @endif
    {{ $task->title }}
  </div>

  @if($task->description)
    <div class="k-desc">{{ $task->description }}</div>
  @endif

  @if(auth()->user()->hasGlobalDataAccess() && ($task->sla_level || $task->estimated_delivery_date || $task->actual_delivery_date))
    <div style="background:#f8fafc;border:1px solid var(--border);border-radius:6px;padding:8px 10px;margin-bottom:10px;font-size:10.5px">
      @if($task->sla_level)
        <div style="display:flex;justify-content:space-between;margin-bottom:4px">
          <span style="color:var(--muted)">SLA Level</span>
          <span style="font-weight:700;color:var(--navy)">{{ $task->sla_level }}</span>
        </div>
      @endif
      @if($task->estimated_delivery_date)
        <div style="display:flex;justify-content:space-between;margin-bottom:{{ $task->actual_delivery_date ? '4px' : '0' }}">
          <span style="color:var(--muted)">Est. Delivery</span>
          <span style="font-weight:600;color:var(--text)">{{ $task->estimated_delivery_date->format('M d, g:i A') }}</span>
        </div>
      @endif
      @if($task->actual_delivery_date)
        <div style="display:flex;justify-content:space-between">
          <span style="color:var(--muted)">Actual Delivery</span>
          <span style="font-weight:600;color:var(--text)">{{ $task->actual_delivery_date->format('M d, g:i A') }}</span>
        </div>
      @endif
    </div>
  @endif

  @if($task->status === 'doing')
    <div class="k-progress">
      <div class="progress-bg">
        <div class="progress-fill" style="width:{{ $task->progress ?? 0 }}%"></div>
      </div>
    </div>
  @endif

  <div class="k-footer" style="margin-top:10px">
    <div class="k-assignee">
      @if($task->assignee)
        <div class="k-av" style="{{ ($task->assignee->avatar ?? null) ? 'background:none;padding:0;overflow:hidden' : 'background:' . $colors[$task->assignee->id % 5] }}">
          @if($task->assignee->avatar ?? null)
            <img src="{{ asset('storage/' . $task->assignee->avatar) }}" alt="{{ $task->assignee->name }}" style="width:100%;height:100%;object-fit:cover;border-radius:6px">
          @else
            {{ strtoupper(substr($task->assignee->name, 0, 2)) }}
          @endif
        </div>
        <span class="k-av-name">{{ $task->assignee->name }}</span>
      @else
        <div class="k-av unassigned">
          <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="8" r="4"/>
            <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
          </svg>
        </div>
        <span class="k-av-name" style="color:var(--muted-lt)">Unassigned</span>
      @endif
    </div>

    <div style="display:flex;align-items:center;gap:4px">
      @if($task->due_date)
        <span class="k-stat">{{ $task->due_date->format('M d') }}</span>
      @endif

      {{-- Edit button: only shown if user has edit_tasks permission --}}
      @if(auth()->user()->hasPermission('edit_tasks'))
      <button class="k-action-btn js-edit-btn" title="Edit" data-id="{{ $task->id }}">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="pointer-events:none">
          <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
          <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
        </svg>
      </button>
      @endif

      {{-- Delete button: only shown if user has delete_tasks permission --}}
      @if(auth()->user()->hasPermission('delete_tasks'))
      <button class="k-action-btn js-delete-btn" title="Delete" style="color:var(--red)" data-id="{{ $task->id }}">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="pointer-events:none">
          <polyline points="3 6 5 6 21 6"/>
          <path d="M19 6l-1 14H6L5 6"/>
          <path d="M10 11v6M14 11v6"/>
        </svg>
      </button>
      @endif
    </div>
  </div>
</div>