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

    public function index()
    {
        $month = now()->startOfMonth();

        $monthStart = $month->copy()->startOfMonth();
        $monthEnd = $month->copy()->endOfMonth();

        $tasksInMonth = Task::with(['ticket', 'assignee'])
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->get();

        $tasksByDay = $tasksInMonth->groupBy(fn (Task $t) => $t->due_date->format('Y-m-d'));

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
                'tasks' => $tasksByDay->get($key, collect()),
            ];
        }

        $weeks = array_chunk($cells, 7);

        $totalScheduled = Task::whereNotNull('due_date')
            ->where('status', '!=', 'done')
            ->count();

        $overdue = Task::whereNotNull('due_date')
            ->where('status', '!=', 'done')
            ->whereDate('due_date', '<', now()->toDateString())
            ->count();

        $thisWeek = Task::whereNotNull('due_date')
            ->whereBetween('due_date', [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()])
            ->count();

        $upcoming = Task::with(['ticket', 'assignee'])
            ->whereNotNull('due_date')
            ->where('status', '!=', 'done')
            ->whereDate('due_date', '>=', now()->toDateString())
            ->orderBy('due_date')
            ->orderBy('priority', 'desc')
            ->limit(12)
            ->get();

        return view('scheduling.index', [
            'monthLabel' => $month->translatedFormat('F Y'),
            'weeks' => $weeks,
            'totalScheduled' => $totalScheduled,
            'overdue' => $overdue,
            'thisWeek' => $thisWeek,
            'upcoming' => $upcoming,
        ]);
    }
}
