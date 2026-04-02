<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\Ticket;
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
        abort_unless(auth()->user()->hasPermission('list_tasks'), 403, 'You do not have permission to view tasks.');

        $todo  = Task::with(['assignee', 'ticket'])->todo()->latest()->get();
        $doing = Task::with(['assignee', 'ticket'])->doing()->latest()->get();
        $done  = Task::with(['assignee', 'ticket'])->done()->latest()->get();

        // ✅ Step 14: Only show users from the same company in the assignee dropdown (Filtered by Developer role)
        $usersQuery = User::whereHas('userRole', function ($q) {
                         $q->whereRaw('LOWER(TRIM(name)) = ?', ['developer']);
                     })->orderBy('name');

        if (!auth()->user()->isSuperAdmin()) {
            $usersQuery->where('company_id', auth()->user()->company_id);
        }

        $users = $usersQuery->get();

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
        $withDue = Task::whereNotNull('due_date')->count();
        $onTime  = Task::whereNotNull('due_date')
                       ->where('status', 'done')
                       ->whereColumn('updated_at', '<=', 'due_date')
                       ->count();
        $slaRate = $withDue > 0 ? round(($onTime / $withDue) * 100) : 0;

        // Upcoming deadlines (next 7 days, not done)
        $deadlines = Task::whereNotNull('due_date')
                        ->where('status', '!=', 'done')
                        ->whereBetween('due_date', [now()->toDateString(), now()->addDays(7)->toDateString()])
                        ->orderBy('due_date')
                        ->limit(5)
                        ->get();

        // Fetch tickets to link to tasks
        $tickets = Ticket::orderByDesc('id')->get(['id', 'title']);

        return view('tasks.index', compact(
            'todo', 'doing', 'done', 'users', 'velocity', 'slaRate', 'deadlines', 'tickets'
        ));
    }

    /**
     * Store a new task (AJAX or normal POST).
     */
    public function store(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('create_tasks'), 403, 'You do not have permission to create tasks.');

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority'    => 'required|in:low,medium,high,urgent',
            'status'      => 'required|in:todo,doing,done',
            'assigned_to' => 'nullable|exists:users,id',
            'ticket_id'   => 'nullable|exists:tickets,id',
            'due_date'    => 'nullable|date',
            'progress'    => 'nullable|integer|min:0|max:100',
            'sla_level'               => 'nullable|string',
            'estimated_delivery_date' => 'nullable|date',
            'actual_delivery_date'    => 'nullable|date',
            'qc_test_date'            => 'nullable|date',
        ]);

        $validated['user_id'] = auth()->id();
        // ✅ Step 14: company_id is auto-set by the Task model's boot() method

        if (!empty($validated['assigned_to'])) {
            $validated['assigned_date'] = now();
            $validated['assigned_by'] = auth()->id();
        }

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

        abort_unless(auth()->user()->hasPermission('edit_tasks'), 403, 'You do not have permission to edit tasks.');

        $validated = $request->validate([
            'title'       => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'priority'    => 'sometimes|required|in:low,medium,high,urgent',
            'status'      => 'sometimes|required|in:todo,doing,done',
            'assigned_to' => 'nullable|exists:users,id',
            'ticket_id'   => 'nullable|exists:tickets,id',
            'due_date'    => 'nullable|date',
            'progress'    => 'nullable|integer|min:0|max:100',
            'sla_level'               => 'nullable|string',
            'estimated_delivery_date' => 'nullable|date',
            'actual_delivery_date'    => 'nullable|date',
            'qc_test_date'            => 'nullable|date',
        ]);

        // Convert empty string to null for nullable fields
        foreach (['assigned_to', 'due_date', 'ticket_id', 'estimated_delivery_date', 'actual_delivery_date', 'qc_test_date'] as $field) {
            if (array_key_exists($field, $validated) && $validated[$field] === '') {
                $validated[$field] = null;
            }
        }

        if (array_key_exists('assigned_to', $validated)) {
            if ($validated['assigned_to'] && !$task->assigned_to) {
                $validated['assigned_date'] = now();
                $validated['assigned_by'] = auth()->id();
            } elseif (!$validated['assigned_to']) {
                $validated['assigned_date'] = null;
                $validated['assigned_by'] = null;
            }
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
        abort_unless(auth()->user()->hasPermission('delete_tasks'), 403, 'You do not have permission to delete tasks.');

        $task->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('tasks.index')->with('success', 'Task deleted!');
    }
}