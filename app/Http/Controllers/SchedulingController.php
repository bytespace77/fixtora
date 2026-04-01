<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Carbon\Carbon;

class SchedulingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(\Illuminate\Http\Request $request)
    {
        abort_unless(auth()->user()->hasPermission('view_scheduling'), 403, 'You do not have permission to view scheduling.');

        $monthParam = $request->input('month');
        if ($monthParam && preg_match('/^\d{4}-\d{2}$/', $monthParam)) {
            $month = Carbon::createFromFormat('Y-m-d', $monthParam . '-01')->startOfDay();
        } else {
            $month = now()->startOfMonth();
        }

        $monthStart = $month->copy()->startOfMonth();
        $monthEnd = $month->copy()->endOfMonth();

        $prevMonth = $month->copy()->subMonth()->format('Y-m');
        $nextMonth = $month->copy()->addMonth()->format('Y-m');

        $tasksInMonth = Task::with(['ticket', 'assignee'])
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->get();

        $ticketsInMonth = \App\Models\Ticket::whereNotNull('due_date')
            ->whereNotIn('status', ['resolved', 'closed'])
            ->whereBetween('due_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->get();

        $items = collect();

        foreach ($tasksInMonth as $t) {
            $items->push((object)[
                'type' => 'task',
                'id' => $t->id,
                'title' => $t->title,
                'date' => Carbon::parse($t->due_date)->format('Y-m-d'),
                'sort_date' => Carbon::parse($t->due_date)->startOfDay(),
                'status' => $t->status ?? 'todo',
                'priority' => $t->priority ?? 'medium',
                'link' => route('tasks.index'),
            ]);
        }

        foreach ($ticketsInMonth as $t) {
            $items->push((object)[
                'type' => 'ticket',
                'id' => $t->id,
                'title' => 'Tkt: ' . $t->title,
                'date' => Carbon::parse($t->due_date)->format('Y-m-d'),
                'sort_date' => Carbon::parse($t->due_date)->startOfDay(),
                'status' => $t->status ?? 'open',
                'priority' => $t->priority ?? 'medium',
                'link' => route('tickets.show', $t->id),
            ]);
        }

        $itemsByDay = $items->groupBy('date');

        $calendarStart = $monthStart->copy()->startOfWeek(Carbon::MONDAY);
        $calendarEnd = $monthEnd->copy()->endOfWeek(Carbon::SUNDAY);

        $cells = [];
        for ($d = $calendarStart->copy(); $d->lte($calendarEnd); $d->addDay()) {
            $key = $d->format('Y-m-d');
            $cells[] = [
                'day' => $d->day,
                'date' => $d->copy(),
                'inMonth' => $d->month === $month->month,
                'isToday' => $d->isToday(),
                'items' => $itemsByDay->get($key, collect()),
            ];
        }

        $weeks = array_chunk($cells, 7);

        // Stats
        $taskScheduled = Task::whereNotNull('due_date')->where('status', '!=', 'done')->count();
        $ticketScheduled = \App\Models\Ticket::whereNotNull('due_date')->whereNotIn('status', ['resolved', 'closed'])->count();
        $totalScheduled = $taskScheduled + $ticketScheduled;

        $taskOverdue = Task::whereNotNull('due_date')->where('status', '!=', 'done')->whereDate('due_date', '<', now()->toDateString())->count();
        $ticketOverdue = \App\Models\Ticket::whereNotNull('due_date')->whereNotIn('status', ['resolved', 'closed'])->whereDate('due_date', '<', now()->toDateString())->count();
        $overdue = $taskOverdue + $ticketOverdue;

        $taskThisWeek = Task::whereNotNull('due_date')
            ->whereBetween('due_date', [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()])
            ->where('status', '!=', 'done')->count();
        $ticketThisWeek = \App\Models\Ticket::whereNotNull('due_date')
            ->whereBetween('due_date', [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()])
            ->whereNotIn('status', ['resolved', 'closed'])->count();
        $thisWeek = $taskThisWeek + $ticketThisWeek;

        // Upcoming
        $upcomingTasks = Task::with(['ticket', 'assignee'])
            ->whereNotNull('due_date')
            ->where('status', '!=', 'done')
            ->whereDate('due_date', '>=', now()->toDateString())
            ->get()->map(function($t) {
                return (object)[
                    'type' => 'task',
                    'id' => $t->id,
                    'title' => $t->title,
                    'due_date' => Carbon::parse($t->due_date),
                    'status' => $t->status ?? 'todo',
                    'link' => route('tasks.index'),
                    'priority' => $t->priority ?? 'medium',
                    'meta1' => $t->ticket ? 'Ticket #'.str_pad($t->ticket->id, 4, '0', STR_PAD_LEFT) : 'No linked ticket',
                    'meta2' => $t->assignee ? $t->assignee->name : null,
                ];
            });

        $upcomingTickets = \App\Models\Ticket::with(['user'])
            ->whereNotNull('due_date')
            ->whereNotIn('status', ['resolved', 'closed'])
            ->whereDate('due_date', '>=', now()->toDateString())
            ->get()->map(function($t) {
                return (object)[
                    'type' => 'ticket',
                    'id' => $t->id,
                    'title' => $t->title,
                    'due_date' => Carbon::parse($t->due_date),
                    'status' => $t->status ?? 'open',
                    'link' => route('tickets.show', $t->id),
                    'priority' => $t->priority ?? 'medium',
                    'meta1' => 'Ticket',
                    'meta2' => $t->user ? 'Reporter: '.$t->user->name : null,
                ];
            });

        $upcoming = $upcomingTasks->concat($upcomingTickets)
            ->sortBy('due_date')
            ->take(12)
            ->values();

        return view('scheduling.index', [
            'monthLabel' => $month->translatedFormat('F Y'),
            'prevMonth' => $prevMonth,
            'nextMonth' => $nextMonth,
            'weeks' => $weeks,
            'totalScheduled' => $totalScheduled,
            'overdue' => $overdue,
            'thisWeek' => $thisWeek,
            'upcoming' => $upcoming,
        ]);
    }
}