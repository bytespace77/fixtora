@php
  $cid = (int) ($ticket->company_id ?? 0);
  $devs = $developersByCompany[$cid] ?? collect();
  $isCriticalUnassigned = $ticket->priority === 'critical' && !$ticket->assigned_developer_id;
@endphp
<div class="ut-card @if($isCriticalUnassigned) ut-card-critical @endif" data-ticket-id="{{ $ticket->id }}">
  <div class="k-card-meta">
    <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap">
      <span class="ticket-id">#{{ $ticket->ticket_company_code ?? 'XX' }}-{{ str_pad((string)($ticket->ticket_company_seq ?? 0), 4, '0', STR_PAD_LEFT) }}</span>
      <a href="{{ route('tickets.show', $ticket) }}" style="font-size:10px;font-weight:700;color:var(--blue);text-decoration:none;background:#eff6ff;padding:2px 6px;border-radius:4px" target="_blank" title="Open ticket">Ticket</a>
    </div>
    @if($isCriticalUnassigned)
      <span class="priority-badge" style="background:#fee2e2;color:#b91c1c;border:1px solid #fecaca">CRITICAL · UNASSIGNED</span>
    @else
      <span class="priority-badge pb-{{ $ticket->priority }}">{{ strtoupper($ticket->priority) }}</span>
    @endif
  </div>
  <div class="k-title">{{ $ticket->title }}</div>
  @if($ticket->system_name)
    <div class="k-desc" style="font-size:11px;color:var(--muted)">System: {{ $ticket->system_name }}</div>
  @endif
  <div class="k-footer" style="margin-top:10px;flex-direction:column;align-items:stretch;gap:8px">
    @if(auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('assign_developer'))
    <form method="POST" action="{{ route('tickets.update', $ticket) }}" onclick="event.stopPropagation()">
      @csrf
      @method('PATCH')
      <input type="hidden" name="redirect_to" value="tasks">
      <label class="ut-label">Assign developer</label>
      <select name="assigned_developer_id" class="form-control" style="padding:6px 10px;font-size:12px;margin-bottom:6px;width:100%" required>
        <option value="">Select developer</option>
        @forelse($devs as $d)
          <option value="{{ $d->id }}">{{ $d->name }}</option>
        @empty
          <option value="" disabled>No developers for this company</option>
        @endforelse
      </select>
      <label class="ut-label">SLA</label>
      <select name="sla_level" class="form-control" style="padding:6px 10px;font-size:12px;margin-bottom:8px;width:100%" required>
        <option value="">SLA level</option>
        <option value="Low">Low</option>
        <option value="Medium">Medium</option>
        <option value="High">High</option>
        <option value="Critical">Critical</option>
      </select>
      <button type="submit" class="btn-primary" style="width:100%;padding:8px;font-size:12px;border:none;border-radius:8px;cursor:pointer;font-weight:700">Assign</button>
    </form>
    @else
      <a href="{{ route('tickets.show', $ticket) }}" class="btn-secondary" style="display:block;text-align:center;padding:8px;font-size:12px;border-radius:8px;text-decoration:none;border:1px solid var(--border);color:var(--text-sub)">View ticket</a>
    @endif
  </div>
</div>
