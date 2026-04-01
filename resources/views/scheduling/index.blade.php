@extends('layouts.app')

@section('title', 'Scheduling – Fixtora')

@section('styles')
<style>
.page-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px}
.page-header h1{font-size:22px;font-weight:800;letter-spacing:-.5px;color:var(--navy)}
.page-header p{font-size:13px;color:var(--muted);margin-top:4px}
.hdr-btns{display:flex;gap:8px}
.btn-sm{padding:8px 14px;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;border:1px solid var(--border);background:var(--surface);color:var(--text-2);font-family:inherit;text-decoration:none}
.btn-sm:hover{background:var(--bg)}
.btn-primary{background:var(--blue);color:#fff;border-color:var(--blue)}
.btn-primary:hover{background:#1a42c4;color:#fff}

.sch-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:20px}
.sch-stat{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:20px 22px;box-shadow:var(--shadow);text-align:center}
.sch-stat-val{font-size:34px;font-weight:800;letter-spacing:-1px;margin-bottom:4px;color:var(--navy-3)}
.sch-stat-lbl{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:var(--muted)}
.sch-stat-hint{font-size:11.5px;font-weight:600;margin-top:6px;color:var(--muted-lt)}
.val-warn{color:var(--orange)}
.val-danger{color:var(--red)}

.sch-mid{display:grid;grid-template-columns:1.35fr 1fr;gap:16px;margin-bottom:20px}
.card-box{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:0;box-shadow:var(--shadow);overflow:hidden}
.cal-head{display:grid;grid-template-columns:1fr auto 1fr;align-items:center;padding:14px 18px;border-bottom:1px solid var(--border);background:var(--bg)}
.cal-title{font-size:16px;font-weight:800;color:var(--navy);margin:0 16px;min-width:130px;text-align:center}
.cal-nav{display:flex;align-items:center;justify-content:center}
.cal-nav a.btn-nav{display:flex;align-items:center;justify-content:center;width:32px;height:32px;border:1px solid var(--border);background:var(--surface);border-radius:8px;cursor:pointer;color:var(--muted);font-size:16px;line-height:1;text-decoration:none;transition:background 0.12s}
.cal-nav a.btn-nav:hover{background:var(--blue-bg);color:var(--blue-2)}
.cal-legend{display:flex;gap:12px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--muted)}
.cal-legend span{display:flex;align-items:center;gap:5px}
.dot{width:7px;height:7px;border-radius:50%}
.dot.ticket{background:#c026d3}
.dot.todo{background:var(--blue-2)}
.dot.doing{background:var(--orange)}
.dot.done{background:var(--green)}

.cal-dow{display:grid;grid-template-columns:repeat(7,1fr);border-bottom:1px solid var(--border);background:var(--bg)}
.cal-dow div{padding:10px;text-align:center;font-size:10px;font-weight:800;color:var(--muted-lt);letter-spacing:.6px;text-transform:uppercase}
.cal-weeks{display:flex;flex-direction:column}
.cal-row{display:grid;grid-template-columns:repeat(7,1fr);min-height:88px}
.cal-cell{border-right:1px solid var(--border);border-bottom:1px solid var(--border);padding:8px;font-size:11px;vertical-align:top}
.cal-row .cal-cell:last-child{border-right:none}
.cal-cell.muted{background:#fafbfd;color:var(--muted-lt)}
.cal-cell.today{background:var(--blue-bg);box-shadow:inset 0 0 0 1px rgba(37,99,235,.15)}
.cal-day-num{font-weight:800;color:var(--text);font-size:12px;margin-bottom:4px}
.cal-cell.muted .cal-day-num{color:var(--muted-lt);font-weight:600}
.cal-pill{display:flex;align-items:center;gap:4px;font-size:9px;font-weight:700;padding:4px 6px;border-radius:6px;margin-top:4px;line-height:1.25;border:1px solid transparent;text-decoration:none;transition:transform 0.15s, opacity 0.15s;}
.cal-pill:hover{transform:translateY(-1px);opacity:0.9;}
.cal-pill-text{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}

/* Ticket Statuses (Solid) */
.cal-pill.tkt-open { background: var(--orange); color: #fff; box-shadow: 0 2px 4px rgba(234, 88, 12, 0.2); }
.cal-pill.tkt-in_progress { background: var(--blue); color: #fff; box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2); }
.cal-pill.tkt-in_review { background: #9333ea; color: #fff; box-shadow: 0 2px 4px rgba(147, 51, 234, 0.2); }
.cal-pill.tkt-resolved { background: var(--green); color: #fff; box-shadow: 0 2px 4px rgba(22, 163, 74, 0.2); }
.cal-pill.tkt-closed { background: var(--muted); color: #fff; }

/* Task Statuses (Pale - Based on Priority) */
.cal-pill.tsk-low { background: var(--green-bg); color: var(--green); border-color: #bbf7d0; }
.cal-pill.tsk-medium { background: var(--orange-bg); color: var(--orange); border-color: #fed7aa; }
.cal-pill.tsk-high { background: #fee2e2; color: var(--red); border-color: #fca5a5; }
.cal-pill.tsk-urgent { background: #fce7f3; color: #be185d; border-color: #fbcfe8; }

.side-hdr{padding:16px 18px;border-bottom:1px solid var(--border)}
.side-title{font-size:14px;font-weight:700;color:var(--navy)}
.side-sub{font-size:11.5px;color:var(--muted);margin-top:3px}
.side-body{padding:12px 14px;max-height:420px;overflow-y:auto}
.sch-row{display:flex;gap:10px;padding:12px 10px;border-radius:8px;border:1px solid var(--border);margin-bottom:8px;background:var(--surface);text-decoration:none;color:inherit;transition:background .12s}
.sch-row:hover{background:var(--bg)}
.sch-row:last-child{margin-bottom:0}
.sch-date{flex-shrink:0;width:44px;text-align:center;padding:6px 4px;border-radius:8px;background:var(--navy);color:#fff;font-size:10px;font-weight:800;line-height:1.2}
.sch-date span{display:block;font-size:15px;font-weight:800;line-height:1.1}
.sch-main{flex:1;min-width:0}
.sch-t{font-size:12.5px;font-weight:700;color:var(--text);line-height:1.3}
.sch-meta{font-size:11px;color:var(--muted);margin-top:4px}
.pill-s{display:inline-flex;align-items:center;gap:4px;font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:.4px;padding:3px 8px;border-radius:999px;margin-top:6px}
/* Side pills Ticket */
.pill-s.tkt-open { background: var(--orange); color: #fff; }
.pill-s.tkt-in_progress { background: var(--blue); color: #fff; }
.pill-s.tkt-in_review { background: #9333ea; color: #fff; }
.pill-s.tkt-resolved { background: var(--green); color: #fff; }
.pill-s.tkt-closed { background: var(--muted); color: #fff; }
/* Side pills Task */
.pill-s.tsk-low { background: var(--green-bg); color: var(--green); border: 1px solid #bbf7d0; }
.pill-s.tsk-medium { background: var(--orange-bg); color: var(--orange); border: 1px solid #fed7aa; }
.pill-s.tsk-high { background: #fee2e2; color: var(--red); border: 1px solid #fca5a5; }
.pill-s.tsk-urgent { background: #fce7f3; color: #be185d; border: 1px solid #fbcfe8; }

.empty-msg{text-align:center;padding:28px 16px;color:var(--muted);font-size:13px;font-weight:600}

@media (max-width:1100px){
  .sch-mid{grid-template-columns:1fr}
  .sch-grid{grid-template-columns:repeat(2,1fr)}
}
@media (max-width:520px){
  .sch-grid{grid-template-columns:1fr}
}
</style>
@endsection

@section('content')
<div class="page-header">
  <div>
    <h1>Scheduling</h1>
    <p>Plan work by due dates — tasks with deadlines appear on the calendar and in upcoming work.</p>
  </div>
  <div class="hdr-btns">
    <a href="{{ route('tasks.index') }}" class="btn-sm">Open Tasks</a>
    <a href="{{ route('tickets.create') }}" class="btn-sm btn-primary">New Ticket</a>
  </div>
</div>

<div class="sch-grid">
  <div class="sch-stat">
    <div class="sch-stat-val">{{ $totalScheduled }}</div>
    <div class="sch-stat-lbl">Open with due date</div>
    <div class="sch-stat-hint">Not completed</div>
  </div>
  <div class="sch-stat">
    <div class="sch-stat-val val-warn">{{ $thisWeek }}</div>
    <div class="sch-stat-lbl">This week</div>
    <div class="sch-stat-hint">Due Mon–Sun</div>
  </div>
  <div class="sch-stat">
    <div class="sch-stat-val {{ $overdue > 0 ? 'val-danger' : '' }}">{{ $overdue }}</div>
    <div class="sch-stat-lbl">Overdue</div>
    <div class="sch-stat-hint">Past due, still open</div>
  </div>
  <div class="sch-stat">
    <div class="sch-stat-val">{{ $upcoming->count() }}</div>
    <div class="sch-stat-lbl">Upcoming list</div>
    <div class="sch-stat-hint">Next items shown →</div>
  </div>
</div>

<div class="sch-mid">
  <div class="card-box">
    <div class="cal-head">
      <div class="cal-legend" style="justify-content:flex-start; gap: 16px;">
        <span><span class="dot" style="background:var(--orange)"></span>Ticket (Solid Pill)</span>
        <span><span class="dot" style="background:var(--blue-bg); border:1px solid #bfdbfe;"></span>Task (Pale Pill)</span>
      </div>
      <div class="cal-nav">
        <a href="{{ route('scheduling.index', ['month' => $prevMonth]) }}" class="btn-nav">‹</a>
        <div class="cal-title">{{ $monthLabel }}</div>
        <a href="{{ route('scheduling.index', ['month' => $nextMonth]) }}" class="btn-nav">›</a>
      </div>
      <div></div>
    </div>
    <div class="cal-dow">
      <div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div><div>Sun</div>
    </div>
    <div class="cal-weeks">
      @foreach($weeks as $row)
        <div class="cal-row">
          @foreach($row as $cell)
            <div class="cal-cell {{ !$cell['inMonth'] ? 'muted' : '' }} {{ !empty($cell['isToday']) ? 'today' : '' }}">
              <div class="cal-day-num">{{ $cell['day'] }}</div>
              @foreach($cell['items']->take(3) as $item)
                @php
                  $st = $item->status ?? '';
                  $pr = $item->priority ?? 'medium';
                  $isTicket = $item->type === 'ticket';
                  $cls = $isTicket ? 'tkt-' . $st : 'tsk-' . $pr;
                  // Icons
                  $icon = $isTicket 
                    ? '<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="7.5 4.21 12 6.81 16.5 4.21"></polyline><polyline points="7.5 19.79 7.5 14.6 3 12"></polyline><polyline points="21 12 16.5 14.6 16.5 19.79"></polyline><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>'
                    : '<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>';
                  
                  $titleText = $isTicket ? preg_replace('/^Tkt:\s*/', '', $item->title) : $item->title;
                @endphp
                <a href="{{ $item->link }}" class="cal-pill {{ $cls }}" title="{{ $item->title }}">
                  {!! $icon !!} <span class="cal-pill-text">{{ Str::limit($titleText, 20) }}</span>
                </a>
              @endforeach
              @if($cell['items']->count() > 3)
                <div style="font-size:9px;font-weight:700;color:var(--muted);margin-top:4px">+{{ $cell['items']->count() - 3 }} more</div>
              @endif
            </div>
          @endforeach
        </div>
      @endforeach
    </div>
  </div>

  <div class="card-box">
    <div class="side-hdr">
      <div class="side-title">Upcoming work</div>
      <div class="side-sub">Tasks with a due date from today onward</div>
    </div>
    <div class="side-body">
      @forelse($upcoming as $item)
        @php
          $st = $item->status ?? 'todo';
          
          if ($item->type === 'ticket') {
              $pill = 'todo'; // Default fallback for pill style logic
          } else {
              $pill = $st === 'doing' ? 'doing' : ($st === 'done' ? 'done' : 'todo');
          }
          
          $d = $item->due_date;
        @endphp
        <a href="{{ $item->link }}" class="sch-row">
          <div class="sch-date">
            {{ strtoupper($d->format('M')) }}
            <span>{{ $d->format('d') }}</span>
          </div>
          <div class="sch-main">
            <div class="sch-t">
              @if($item->type === 'ticket')
                <span style="color:#c026d3;margin-right:2px">[TKT]</span>
              @endif
              {{ Str::limit($item->title, 44) }}
            </div>
            <div class="sch-meta">
              {{ $item->meta1 }}
              @if($item->meta2)
                · {{ $item->meta2 }}
              @endif
            </div>
            @if($item->type === 'ticket')
              <span class="pill-s tkt-{{ $item->status ?? 'open' }}">
                 <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="7.5 4.21 12 6.81 16.5 4.21"></polyline><polyline points="7.5 19.79 7.5 14.6 3 12"></polyline><polyline points="21 12 16.5 14.6 16.5 19.79"></polyline><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                 {{ ucfirst(str_replace('_',' ',$item->status ?? 'open')) }}
              </span>
            @else
              <span class="pill-s tsk-{{ $item->priority ?? 'medium' }}">
                 <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>
                 {{ ucfirst($item->priority ?? 'medium') }} Task
              </span>
            @endif
          </div>
        </a>
      @empty
        <div class="empty-msg">No scheduled tasks yet. Add due dates on tasks to see them here.</div>
      @endforelse
    </div>
  </div>
</div>
@endsection
