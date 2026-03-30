<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the Kanban board.
     */
    public function index()
    {
        $todo  = Task::with(['assignee', 'ticket'])->todo()->latest()->get();
        $doing = Task::with(['assignee', 'ticket'])->doing()->latest()->get();
        $done  = Task::with(['assignee', 'ticket'])->done()->latest()->get();
        $users = User::orderBy('name')->get();

        // Workload velocity: tasks completed per day this week (Mon–Sun)
        $velocity = [];
        for ($i = 0; $i < 7; $i++) {
            $day = now()->startOfWeek()->addDays($i);
            $velocity[] = [
                'label' => strtoupper($day->format('D')),
                'count' => Task::whereDate('updated_at', $day)->where('status', 'done')->count(),
            ];
        }

        // SLA success rate: done tasks with due_date not overdue vs total with due_date
        $withDue     = Task::whereNotNull('due_date')->count();
        $onTime      = Task::whereNotNull('due_date')
                           ->where('status', 'done')
                           ->whereColumn('updated_at', '<=', 'due_date')
                           ->count();
        $slaRate     = $withDue > 0 ? round(($onTime / $withDue) * 100) : 0;

        // Upcoming deadlines (next 7 days, not done)
        $deadlines = Task::whereNotNull('due_date')
                        ->where('status', '!=', 'done')
                        ->whereBetween('due_date', [now()->toDateString(), now()->addDays(7)->toDateString()])
                        ->orderBy('due_date')
                        ->limit(5)
                        ->get();

        return view('tasks.index', compact(
            'todo', 'doing', 'done', 'users', 'velocity', 'slaRate', 'deadlines'
        ));
    }

    /**
     * Store a new task (AJAX or normal POST).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority'    => 'required|in:low,medium,high,urgent',
            'status'      => 'required|in:todo,doing,done',
            'assigned_to' => 'nullable|exists:users,id',
            'ticket_id'   => 'nullable|exists:tickets,id',
            'due_date'    => 'nullable|date',
            'progress'    => 'nullable|integer|min:0|max:100',
        ]);

        $validated['user_id'] = auth()->id();

        $task = Task::create($validated);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'task' => $task->load('assignee', 'ticket')]);
        }

        return redirect()->route('tasks.index')->with('success', 'Task created!');
    }

    /**
     * Update a task's status (drag-drop or status button) or full update.
     */
    public function update(Request $request, Task $task)
    {
        // Handle AJAX _method:DELETE spoofing via POST route
        if ($request->input('_method') === 'DELETE') {
            return $this->destroy($task);
        }

        $validated = $request->validate([
            'title'       => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'priority'    => 'sometimes|required|in:low,medium,high,urgent',
            'status'      => 'sometimes|required|in:todo,doing,done',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date'    => 'nullable|date',
            'progress'    => 'nullable|integer|min:0|max:100',
        ]);

        // Convert empty string to null for nullable fields
        if (isset($validated['assigned_to']) && $validated['assigned_to'] === '') {
            $validated['assigned_to'] = null;
        }
        if (isset($validated['due_date']) && $validated['due_date'] === '') {
            $validated['due_date'] = null;
        }

        $task->update($validated);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'task' => $task->fresh()->load('assignee')]);
        }

        return redirect()->route('tasks.index')->with('success', 'Task updated!');
    }

    /**
     * Delete a task.
     */
    public function destroy(Task $task)
    {
        $task->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('tasks.index')->with('success', 'Task deleted!');
    }
}