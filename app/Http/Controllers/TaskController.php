<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Build a 2-letter company code for ticket/task display.
     * Examples:
     * - "Acme Sdn Bhd" => AC (first letters of first two words)
     * - "Bakery"       => BK (first two consonants)
     */
    private function buildCompanyCode(?string $companyName): string
    {
        if (!$companyName) return 'XX';

        $name = trim($companyName);
        if ($name === '') return 'XX';

        // Keep letters and spaces only.
        $clean = preg_replace('/[^A-Za-z\\s]/', ' ', $name);
        $words = array_values(array_filter(preg_split('/\\s+/', trim((string) $clean))));

        if (count($words) >= 2) {
            $first = strtoupper(substr((string) $words[0], 0, 1));
            $second = strtoupper(substr((string) $words[1], 0, 1));
            return $first . $second;
        }

        $word = strtoupper(substr((string) ($words[0] ?? ''), 0));
        if ($word === '') return 'XX';

        $vowels = ['A', 'E', 'I', 'O', 'U'];
        $consonants = '';
        $len = strlen($word);
        for ($i = 0; $i < $len; $i++) {
            $ch = $word[$i];
            if (ctype_alpha($ch) && !in_array($ch, $vowels, true)) {
                $consonants .= $ch;
                if (strlen($consonants) >= 2) break;
            }
        }

        if (strlen($consonants) >= 2) {
            return substr($consonants, 0, 2);
        }

        // Fallback: first 2 alphabetic characters.
        $alpha = preg_replace('/[^A-Z]/', '', $word);
        if (strlen($alpha) >= 2) return substr($alpha, 0, 2);
        if (strlen($alpha) === 1) return $alpha . 'X';
        return 'XX';
    }

    /**
     * Compute per-company ticket sequence for display (#XX-0001).
     * Sequence is based on ticket creation order within the company.
     */
    private function computeCompanyTicketSeq(Ticket $ticket): int
    {
        $companyId = (int) ($ticket->company_id ?? 0);
        if ($companyId <= 0) return 0;
        $createdAt = $ticket->created_at;
        if (!$createdAt) return 0;

        return (int) Ticket::withoutGlobalScope('company')
            ->where('company_id', $companyId)
            ->where(function ($q) use ($createdAt, $ticket) {
                $q->where('created_at', '<', $createdAt)
                  ->orWhere(function ($q2) use ($createdAt, $ticket) {
                      $q2->where('created_at', $createdAt)
                         ->where('id', '<=', (int) $ticket->id);
                  });
            })
            ->count();
    }

    /**
     * Compute per-company task sequence for display (#XX-0001).
     * Sequence is based on task creation order within the company.
     */
    private function computeCompanyTaskSeq(Task $task): int
    {
        $companyId = (int) ($task->company_id ?? $task->ticket?->company_id ?? 0);
        if ($companyId <= 0) return 0;
        $createdAt = $task->created_at;
        if (!$createdAt) return 0;

        return (int) Task::withoutGlobalScope('company')
            ->where('company_id', $companyId)
            ->where(function ($q) use ($createdAt, $task) {
                $q->where('created_at', '<', $createdAt)
                  ->orWhere(function ($q2) use ($createdAt, $task) {
                      $q2->where('created_at', $createdAt)
                         ->where('id', '<=', (int) $task->id);
                  });
            })
            ->count();
    }

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Keep ticket status in sync with its related tasks.
     *
     * Mapping (based on ALL tasks for the ticket):
     * - any task `done`  => ticket `resolved`
     * - else any task `doing` => ticket `in_progress`
     * - else => ticket `open` (tasks are only `todo`)
     */
    private function syncTicketStatusFromTasks(Task $task): void
    {
        if (empty($task->ticket_id)) {
            return;
        }

        $ticketId  = (int) $task->ticket_id;
        // Prefer company's id from ticket to handle older data where tasks.company_id might be null.
        $companyId = $task->company_id ? (int) $task->company_id : null;
        if (!$companyId) {
            $ticketForCompany = Ticket::withoutGlobalScope('company')->find($ticketId);
            $companyId = $ticketForCompany?->company_id ? (int) $ticketForCompany->company_id : null;
        }

        // Safety: all SLA/task records are multi-tenant; never update cross-company.
        if (!$companyId) return;

        // Ensure tasks for this ticket have company_id set (for older records after migration).
        Task::withoutGlobalScope('company')
            ->where('ticket_id', $ticketId)
            ->whereNull('company_id')
            ->update(['company_id' => $companyId]);

        $hasDone = Task::withoutGlobalScope('company')
            ->where('company_id', $companyId)
            ->where('ticket_id', $ticketId)
            ->where('status', 'done')
            ->exists();

        $hasDoing = Task::withoutGlobalScope('company')
            ->where('company_id', $companyId)
            ->where('ticket_id', $ticketId)
            ->where('status', 'doing')
            ->exists();

        $newStatus = $hasDone ? 'resolved' : ($hasDoing ? 'in_progress' : 'open');

        Ticket::withoutGlobalScope('company')
            ->where('company_id', $companyId)
            ->where('id', $ticketId)
            ->update(['status' => $newStatus, 'updated_at' => now()]);
    }

    /**
     * Show the Kanban board.
     */
    public function index()
    {
        abort_unless(auth()->user()->hasPermission('list_tasks'), 403, 'You do not have permission to view tasks.');

        $todo  = Task::with(['assignee', 'ticket.company', 'company'])->todo()->latest()->get();
        $doing = Task::with(['assignee', 'ticket.company', 'company'])->doing()->latest()->get();
        $done  = Task::with(['assignee', 'ticket.company', 'company'])->done()->latest()->get();

        // ✅ Step 14: Only show users from the same company in the assignee dropdown (filtered like User::isDeveloper())
        $usersQuery = User::assignableDevelopers()->orderBy('name');

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
        $deadlines = Task::with(['assignee', 'ticket.company'])
            ->whereNotNull('due_date')
            ->where('status', '!=', 'done')
            ->whereBetween('due_date', [now()->toDateString(), now()->addDays(7)->toDateString()])
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        // Fetch only tickets that are not yet linked to any task.
        // Tickets that already have a task assigned would cause confusion in the "Create Task" dropdown.
        $tickets = Ticket::with('company')
            ->whereDoesntHave('tasks')
            ->whereNotIn('status', ['resolved', 'closed'])
            ->orderByDesc('id')
            ->get(['id', 'title', 'company_id', 'created_at']);

        $unassignedTickets = Ticket::with('company')
            ->whereNull('assigned_developer_id')
            ->whereNotIn('status', ['resolved', 'closed'])
            ->latest()
            ->get();

        foreach ($unassignedTickets as $ticket) {
            $ticket->ticket_company_code = $this->buildCompanyCode($ticket->company?->name);
            $ticket->ticket_company_seq  = $this->computeCompanyTicketSeq($ticket);
        }

        $developersByCompany = [];
        $isSuperAdmin = auth()->user()->isSuperAdmin();
        foreach ($unassignedTickets->pluck('company_id')->filter()->unique() as $cid) {
            $query = User::assignableDevelopers()->orderBy('name');
            // SuperAdmin can assign any developer across all companies
            if (!$isSuperAdmin) {
                $query->where('company_id', (int) $cid);
            }
            $developersByCompany[(int) $cid] = $query->get();
        }

        // Build per-company sequences for tickets and tasks so display becomes #XX-0001.
        $ticketSeqById = [];
        $taskSeqById   = [];

        $ticketCompanyIds = $tickets->pluck('company_id')->unique()->filter()->values();
        foreach ($ticketCompanyIds as $companyId) {
            $orderedTickets = Ticket::withoutGlobalScope('company')
                ->where('company_id', (int) $companyId)
                ->orderBy('created_at', 'asc')
                ->orderBy('id', 'asc')
                ->get(['id']);

            foreach ($orderedTickets as $i => $tt) {
                $ticketSeqById[(int) $tt->id] = $i + 1;
            }
        }

        foreach ($tickets as $ticket) {
            $ticket->ticket_company_code = $this->buildCompanyCode($ticket->company?->name);
            $ticket->ticket_company_seq  = $ticketSeqById[(int) $ticket->id] ?? 0;
        }

        $allTasks = $todo->concat($doing)->concat($done);

        // Ensure tasks have company_id (legacy safety).
        foreach ($allTasks as $t) {
            if (empty($t->company_id) && $t->ticket?->company_id) {
                Task::withoutGlobalScope('company')
                    ->where('id', (int) $t->id)
                    ->update(['company_id' => (int) $t->ticket->company_id]);
                $t->company_id = (int) $t->ticket->company_id;
            }
        }

        $taskCompanyIds = $allTasks->pluck('company_id')->unique()->filter()->values();
        foreach ($taskCompanyIds as $companyId) {
            $orderedTasks = Task::withoutGlobalScope('company')
                ->where('company_id', (int) $companyId)
                ->orderBy('created_at', 'asc')
                ->orderBy('id', 'asc')
                ->get(['id']);

            foreach ($orderedTasks as $i => $tt) {
                $taskSeqById[(int) $tt->id] = $i + 1;
            }
        }

        foreach ([$todo, $doing, $done] as $bucket) {
            foreach ($bucket as $t) {
                $companyName = $t->company?->name ?? $t->ticket?->company?->name;
                $t->task_company_code = $this->buildCompanyCode($companyName);
                $t->task_company_seq  = $taskSeqById[(int) $t->id] ?? 0;

                $t->ticket_company_code = $this->buildCompanyCode($t->ticket?->company?->name);
                $t->ticket_company_seq  = isset($t->ticket_id) && $t->ticket_id ? ($ticketSeqById[(int) $t->ticket_id] ?? 0) : 0;
            }
        }

        return view('tasks.index', compact(
            'todo', 'doing', 'done', 'users', 'velocity', 'slaRate', 'deadlines', 'tickets',
            'unassignedTickets', 'developersByCompany'
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
        $this->syncTicketStatusFromTasks($task);

        if ($request->expectsJson()) {
            $task->load('assignee', 'ticket.company', 'company');

            $task->ticket_company_code = $this->buildCompanyCode($task->ticket?->company?->name);
            $task->ticket_company_seq  = $task->ticket ? $this->computeCompanyTicketSeq($task->ticket) : 0;

            $companyName = $task->company?->name ?? $task->ticket?->company?->name;
            $task->task_company_code = $this->buildCompanyCode($companyName);
            $task->task_company_seq  = $this->computeCompanyTaskSeq($task);

            return response()->json(['success' => true, 'task' => $task]);
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
        $this->syncTicketStatusFromTasks($task);

        if ($request->expectsJson()) {
            $task = $task->fresh()->load('assignee', 'ticket.company', 'company');

            $task->ticket_company_code = $this->buildCompanyCode($task->ticket?->company?->name);
            $task->ticket_company_seq  = $task->ticket ? $this->computeCompanyTicketSeq($task->ticket) : 0;

            $companyName = $task->company?->name ?? $task->ticket?->company?->name;
            $task->task_company_code = $this->buildCompanyCode($companyName);
            $task->task_company_seq  = $this->computeCompanyTaskSeq($task);

            return response()->json(['success' => true, 'task' => $task]);
        }

        return redirect()->route('tasks.index')->with('success', 'Task updated!');
    }

    /**
     * Delete a task.
     */
    public function destroy(Task $task)
    {
        abort_unless(auth()->user()->hasPermission('delete_tasks'), 403, 'You do not have permission to delete tasks.');

        // If this task is linked to a ticket that already has an assigned developer,
        // delete the ticket as well (cascade: attachments directory too).
        $linkedTicket = null;
        if ($task->ticket_id) {
            $ticket = \App\Models\Ticket::withoutGlobalScopes()->find($task->ticket_id);
            if ($ticket && !empty($ticket->assigned_developer_id)) {
                $linkedTicket = $ticket;
            }
        }

        $task->delete();

        if ($linkedTicket) {
            \Illuminate\Support\Facades\Storage::disk('public')
                ->deleteDirectory("ticket-attachments/{$linkedTicket->id}");
            $linkedTicket->delete();
        }

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('tasks.index')->with('success', 'Task deleted!');
    }
}