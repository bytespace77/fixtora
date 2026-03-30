@extends('layouts.app')

@section('title', 'Scheduling – Fixtora')

@section('styles')
<style>
.page-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px}
.page-header h1{font-size:22px;font-weight:800;letter-spacing:-.5px;color:var(--navy)}
.page-header p{font-size:13px;color:var(--muted);margin-top:4px}
.hdr-btns{display:flex;gap:8px}
.btn-sm{padding:8px 14px;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;border:1px solid var(--border);background:var(--surface);color:var(--text-2);font-family:inherit}
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
.cal-head{display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid var(--border);background:var(--bg)}
.cal-title{font-size:14px;font-weight:700;color:var(--navy)}
.cal-nav{display:flex;gap:4px}
.cal-nav button{width:32px;height:32px;border:1px solid var(--border);background:var(--surface);border-radius:8px;cursor:pointer;color:var(--muted);font-size:16px;line-height:1}
.cal-nav button:hover{background:var(--blue-bg);color:var(--blue-2)}
.cal-legend{display:flex;gap:12px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:var(--muted)}
.cal-legend span{display:flex;align-items:center;gap:5px}
.dot{width:7px;height:7px;border-radius:50%}
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
.cal-pill{font-size:9px;font-weight:700;padding:3px 6px;border-radius:6px;margin-top:3px;line-height:1.25;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;display:block;border:1px solid transparent}
.cal-pill.todo{background:var(--blue-bg);color:var(--blue-2);border-color:#bfdbfe}
.cal-pill.doing{background:var(--orange-bg);color:var(--orange);border-color:#fed7aa}
.cal-pill.done{background:var(--green-bg);color:var(--green);border-color:#bbf7d0}

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
.pill-s{display:inline-block;font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:.4px;padding:2px 7px;border-radius:999px;margin-top:6px}
.pill-s.todo{background:var(--blue-bg);color:var(--blue-2)}
.pill-s.doing{background:var(--orange-bg);color:var(--orange)}
.pill-s.done{background:var(--green-bg);color:var(--green)}

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
      <div class="cal-title">{{ $monthLabel }}</div>
      <div class="cal-legend">
        <span><span class="dot todo"></span>To do</span>
        <span><span class="dot doing"></span>Doing</span>
        <span><span class="dot done"></span>Done</span>
      </div>
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
              @foreach($cell['tasks']->take(3) as $task)
                @php
                  $st = $task->status ?? 'todo';
                  $cls = $st === 'doing' ? 'doing' : ($st === 'done' ? 'done' : 'todo');
                @endphp
                <a href="{{ route('tasks.index') }}" class="cal-pill {{ $cls }}" title="{{ $task->title }}">{{ Str::limit($task->title, 22) }}</a>
              @endforeach
              @if($cell['tasks']->count() > 3)
                <div style="font-size:9px;font-weight:700;color:var(--muted);margin-top:4px">+{{ $cell['tasks']->count() - 3 }} more</div>
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
      @forelse($upcoming as $task)
        @php
          $st = $task->status ?? 'todo';
          $pill = $st === 'doing' ? 'doing' : ($st === 'done' ? 'done' : 'todo');
          $d = $task->due_date;
        @endphp
        <a href="{{ route('tasks.index') }}" class="sch-row">
          <div class="sch-date">
            {{ strtoupper($d->format('M')) }}
            <span>{{ $d->format('d') }}</span>
          </div>
          <div class="sch-main">
            <div class="sch-t">{{ Str::limit($task->title, 48) }}</div>
            <div class="sch-meta">
              @if($task->ticket)
                Ticket #{{ str_pad((string) $task->ticket->id, 4, '0', STR_PAD_LEFT) }}
              @else
                No linked ticket
              @endif
              @if($task->assignee)
                · {{ $task->assignee->name }}
              @endif
            </div>
            <span class="pill-s {{ $pill }}">{{ ucfirst($st) }}</span>
          </div>
        </a>
      @empty
        <div class="empty-msg">No scheduled tasks yet. Add due dates on tasks to see them here.</div>
      @endforelse
    </div>
  </div>
</div>
@endsection
