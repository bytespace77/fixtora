<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\TicketAttachment;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class TicketController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ─────────────────────────────────────────────────────────
    // Task 41: Company isolation guard (defense-in-depth).
    // The Ticket model's global scope already filters list/index
    // queries. This guard protects individual-record endpoints
    // (show, update, addComment, uploadAttachment, destroy, etc.)
    // from direct URL access by users of another company.
    // ─────────────────────────────────────────────────────────
    private function authorizeTicketCompany(Ticket $ticket): void
    {
        $user = auth()->user();

        // Superadmin and QC can access all companies
        if ($user->hasGlobalDataAccess()) {
            return;
        }

        // Users whose role has been explicitly granted view_tickets (or any ticket
        // permission) by the superadmin are trusted to see tickets across companies.
        // The permission check in the calling method already verified they hold the
        // required permission, so we only enforce company isolation for users whose
        // role has NO ticket permissions at all (i.e. the role never granted access).
        $role = $user->userRole;
        $rolePermissions = $role ? ($role->permissions ?? []) : [];
        $hasAnyTicketPermission = collect($rolePermissions)
            ->contains(fn($p) => str_contains($p, 'ticket'));

        if ($hasAnyTicketPermission) {
            return; // Role was granted ticket access by admin — trust that grant
        }

        abort_unless(
            $ticket->company_id === $user->company_id,
            403,
            'You do not have permission to access this ticket.'
        );
    }

    public function index(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('list_tickets'), 403, 'You do not have permission to view tickets.');

        $query = Ticket::with('user')->latest();

        // Unassigned filter — ?unassigned=1
        $filterUnassigned = $request->boolean('unassigned');
        if ($filterUnassigned) {
            $query->whereNull('assigned_developer_id');
        }

        // Status filter — supports ?status=open, ?status[]=…, and ?status[]=open&status[]=in_progress (also status[0]=… from http_build_query)
        $statuses = [];
        if ($request->has('status')) {
            $s = $request->input('status');
            $statuses = array_filter(is_array($s) ? $s : [$s]);
        } elseif ($request->has('status[]')) {
            $statuses = array_filter((array) $request->input('status[]'));
        }
        if (!empty($statuses)) {
            $query->whereIn('status', $statuses);
        }

        // Priority filter — supports ?priority[]=critical and ?priority=critical
        $priorities = [];
        if ($request->has('priority')) {
            $p = $request->input('priority');
            $priorities = array_filter(is_array($p) ? $p : [$p]);
        } elseif ($request->has('priority[]')) {
            $priorities = array_filter((array) $request->input('priority[]'));
        }
        if (!empty($priorities)) {
            $query->whereIn('priority', $priorities);
        }

        // System / company filter — 'system' column stores the company name
        $systems = array_filter((array) $request->input('system[]', []));
        if (!empty($systems)) {
            $query->whereIn('system', $systems);
        }

        // Date range filter (created_at)
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Updated-at range (e.g. dashboard "Resolved in selected period" drill-down)
        if ($request->filled('updated_from')) {
            $query->whereDate('updated_at', '>=', $request->updated_from);
        }
        if ($request->filled('updated_to')) {
            $query->whereDate('updated_at', '<=', $request->updated_to);
        }

        $tickets = $query->with('company')->paginate(10)->withQueryString();
        $companySystems = auth()->user()->company?->systems ?? [];
        $companySystemMap = Company::where('is_active', true)->orderBy('name')->get(['name', 'systems'])->mapWithKeys(fn ($c) => [$c->name => $c->systems ?? []]);

        // Count for Unassigned tab
        $unassignedCount = Ticket::whereNull('assigned_developer_id')
            ->whereNotIn('status', ['resolved', 'closed'])
            ->count();

        // Count for each status tab
        $statusCounts = Ticket::groupBy('status')
            ->selectRaw('status, count(*) as count')
            ->pluck('count', 'status')
            ->toArray();

        return view('tickets.index', compact('tickets', 'companySystems', 'companySystemMap', 'unassignedCount', 'statusCounts'));
    }

    public function create()
    {
        abort_unless(auth()->user()->hasPermission('create_tickets'), 403, 'You do not have permission to create tickets.');

        $companies = auth()->user()->hasGlobalDataAccess()
            ? \App\Models\Company::where('is_active', true)->pluck('name')
            : [];

        $companySystems = auth()->user()->company?->systems ?? [];
        $companySystemMap = Company::where('is_active', true)->orderBy('name')->get(['name', 'systems'])->mapWithKeys(fn ($c) => [$c->name => $c->systems ?? []]);

        return view('tickets.create', compact('companies', 'companySystems', 'companySystemMap'));
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('create_tickets'), 403, 'You do not have permission to create tickets.');

        $rules = [
            'title'         => 'required|string|max:255',
            'description'   => 'required|string',
            'system'        => 'required|string',
            'system_name'   => 'nullable|string|max:255',
            'priority'      => 'required|in:low,medium,high,critical',
            'impact'        => 'required|in:low,medium,high,critical',
            'status'        => 'required|in:open,in_progress,in_review,resolved,closed',
            'due_date'      => 'nullable|date',
            'attachments'   => 'nullable|array|max:10',
            'attachments.*' => 'file|max:25600|mimes:jpg,jpeg,png,json,zip',
        ];

        $validated = $request->validate($rules);

        $validated['user_id'] = auth()->id();
        unset($validated['attachments']);

        if (empty($validated['status'])) {
            $validated['status'] = 'open';
        }

        $validated['company_id'] = auth()->user()->company_id;
        if (auth()->user()->hasGlobalDataAccess()) {
            $company = Company::where('name', $validated['system'])->first();
            if ($company) {
                $validated['company_id'] = $company->id;
            }
        }

        $company = Company::find($validated['company_id']);
        $this->assertSystemNameAllowed($company, $validated['system_name'] ?? null);

        $ticket = Ticket::create($validated);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store("ticket-attachments/{$ticket->id}", 'public');
                TicketAttachment::create([
                    'ticket_id'     => $ticket->id,
                    'comment_id'    => null,
                    'user_id'       => auth()->id(),
                    'original_name' => $file->getClientOriginalName(),
                    'stored_path'   => $path,
                    'mime_type'     => $file->getMimeType(),
                    'size'          => $file->getSize(),
                ]);
            }
        }
        
        try {
            if (auth()->user()->isSuperAdmin()) {
                $companyUsers = \App\Models\User::where('is_disabled', false)
                    ->where('company_id', $ticket->company_id)
                    ->get();
                if ($companyUsers->isNotEmpty()) {
                    \Illuminate\Support\Facades\Notification::send($companyUsers, new \App\Notifications\TicketTaskCreatedNotification($ticket, 'Ticket', 'company'));
                }
            } else {
                $superadmins = \App\Models\User::where('is_disabled', false)->get()->filter(fn($u) => $u->isSuperAdmin());
                if ($superadmins->isNotEmpty()) {
                    \Illuminate\Support\Facades\Notification::send($superadmins, new \App\Notifications\TicketTaskCreatedNotification($ticket, 'Ticket', 'superadmin'));
                }
            }

            if ($ticket->assigned_developer_id) {
                $developer = \App\Models\User::find($ticket->assigned_developer_id);
                if ($developer && !$developer->is_disabled) {
                    $developer->notify(new \App\Notifications\TicketTaskAssignedNotification($ticket, 'Ticket'));
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send Ticket Creation notification: ' . $e->getMessage());
        }

        return redirect()->route('tickets.index')->with('success', 'Ticket created successfully!');
    }

    public function show(Ticket $ticket)
    {
        abort_unless(auth()->user()->hasPermission('view_tickets'), 403, 'You do not have permission to view this ticket.');
        $this->authorizeTicketCompany($ticket); // ← Task 41

        if (!$ticket->is_read) {
            $ticket->timestamps = false;
            $ticket->is_read = true;
            $ticket->save();
            $ticket->timestamps = true;
        }

        $ticket->load([
            'comments.user',
            'comments.attachments',
            'attachments' => fn($q) => $q->whereNull('comment_id'),
            'tasks.assignee',
            'assignedDeveloper',
            'company',
        ]);

        $developers = collect();
        if (auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('assign_developer')) {
            $developerQuery = \App\Models\User::with('userRole')
                ->assignableDevelopers()
                ->orderBy('name');

            if ($ticket->company_id) {
                $developerQuery->where('company_id', $ticket->company_id);
            }

            $developers = $developerQuery->get();

            // Superadmin: prefer ticket company, but avoid an empty list when no devs match that company.
            if ($developers->isEmpty() && auth()->user()->isSuperAdmin()) {
                $developers = \App\Models\User::with('userRole')
                    ->assignableDevelopers()
                    ->orderBy('name')
                    ->get();
            }
        }

        return view('tickets.show', compact('ticket', 'developers'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $user = auth()->user();
        $this->authorizeTicketCompany($ticket); // ← Task 41

        $canEditTicket = $user->hasPermission('edit_tickets');
        $isAssignedDeveloper = (int) $ticket->assigned_developer_id === (int) $user->id;
        $canAssignTicket = $user->isSuperAdmin() || $user->hasPermission('assign_developer');
        $isQcUser = $user->isQc() && $ticket->status === 'in_review';

        abort_unless(
            $canEditTicket || $isAssignedDeveloper || $canAssignTicket || $isQcUser,
            403,
            'You do not have permission to update this ticket.'
        );

        $rules = [];

        if ($canEditTicket) {
            $rules = array_merge($rules, [
                'estimated_delivery_date' => 'sometimes|nullable|date',
                'actual_delivery_date'    => 'sometimes|nullable|date',
                'title'       => 'sometimes|required|string|max:255',
                'description' => 'sometimes|required|string',
                'system'      => 'sometimes|nullable|string',
                'system_name' => 'sometimes|nullable|string|max:255',
                'priority'    => 'sometimes|required|in:low,medium,high,critical',
                'impact'      => 'sometimes|required|in:low,medium,high,critical',
                'status'      => 'sometimes|required|in:open,in_progress,in_review,resolved,closed',
                'due_date'    => 'sometimes|nullable|date',
                'assigned_developer_id' => 'sometimes|nullable|exists:users,id',
                'sla_level'             => 'sometimes|nullable|in:Low,Medium,High,Critical',
                'qc_test_date'          => 'sometimes|nullable|date',
                'redirect_to'           => 'sometimes|nullable|in:tasks',
            ]);
        } elseif ($isAssignedDeveloper) {
            $rules = array_merge($rules, [
                'estimated_delivery_date' => 'sometimes|nullable|date',
                'actual_delivery_date'    => 'sometimes|nullable|date',
            ]);
            if ($canAssignTicket) {
                $rules = array_merge($rules, [
                    'assigned_developer_id' => 'sometimes|nullable|exists:users,id',
                    'sla_level'             => 'sometimes|nullable|in:Low,Medium,High,Critical',
                    'redirect_to'           => 'sometimes|nullable|in:tasks',
                ]);
            }
        } elseif ($canAssignTicket) {
            $rules = array_merge($rules, [
                'assigned_developer_id' => 'sometimes|nullable|exists:users,id',
                'sla_level'             => 'sometimes|nullable|in:Low,Medium,High,Critical',
                'redirect_to'           => 'sometimes|nullable|in:tasks',
            ]);
        } elseif ($isQcUser) {
            // QC users can only update status (in_review → resolved) and qc_test_date
            $rules = array_merge($rules, [
                'status'       => 'sometimes|required|in:in_review,resolved',
                'qc_test_date' => 'sometimes|nullable|date',
            ]);
        }

        $validated = $request->validate($rules);

        if (!$canEditTicket) {
            $allowedKeys = [];
            if ($isAssignedDeveloper) {
                $allowedKeys = array_merge($allowedKeys, ['estimated_delivery_date', 'actual_delivery_date']);
            }
            if ($canAssignTicket) {
                $allowedKeys = array_merge($allowedKeys, ['assigned_developer_id', 'sla_level', 'redirect_to']);
            }
            if ($isQcUser) {
                $allowedKeys = array_merge($allowedKeys, ['status', 'qc_test_date']);
            }
            $validated = array_intersect_key($validated, array_flip(array_unique($allowedKeys)));
        }

        $shouldCreateTaskFromTicket = false;

        if (array_key_exists('assigned_developer_id', $validated) || array_key_exists('sla_level', $validated)) {
            abort_unless($user->isSuperAdmin() || $user->hasPermission('assign_developer'), 403, 'You do not have permission to assign a developer or SLA.');

            $selectedDeveloper = $validated['assigned_developer_id'] ?? $ticket->assigned_developer_id;
            $selectedSla = $validated['sla_level'] ?? $ticket->sla_level;
            if (!empty($selectedDeveloper) && empty($selectedSla)) {
                throw ValidationException::withMessages([
                    'sla_level' => 'SLA level is required when a developer is assigned.',
                ]);
            }
            if (!empty($selectedSla) && empty($selectedDeveloper)) {
                throw ValidationException::withMessages([
                    'assigned_developer_id' => 'Developer is required when SLA is set.',
                ]);
            }

            if (!empty($selectedDeveloper) && !empty($selectedSla)
                && ((int) $selectedDeveloper !== (int) $ticket->assigned_developer_id || $selectedSla !== $ticket->sla_level)) {
                $validated['assigned_by'] = $user->id;
                $validated['assigned_date'] = now();

                TicketComment::create([
                    'ticket_id' => $ticket->id,
                    'user_id'   => $user->id,
                    'body'      => 'New assignment: ticket #' . str_pad((string) $ticket->id, 4, '0', STR_PAD_LEFT) . ' with SLA ' . $selectedSla . '.',
                    'role'      => 'system',
                    'type'      => 'workflow_notification',
                    'target_role' => 'developer',
                ]);

                // When a ticket is newly assigned and it has no related tasks yet,
                // automatically create a corresponding task so it appears on the
                // Kanban board.
                if (!$ticket->tasks()->exists()) {
                    $shouldCreateTaskFromTicket = true;
                }
            }
        }

        if ($canEditTicket && (array_key_exists('system_name', $validated) || array_key_exists('system', $validated))) {
            $companyForName = Company::find($ticket->company_id);
            if (!empty($validated['system'])) {
                $c = Company::where('name', $validated['system'])->first();
                if ($c) {
                    $companyForName = $c;
                }
            }
            $sn = array_key_exists('system_name', $validated) ? $validated['system_name'] : $ticket->system_name;
            $this->assertSystemNameAllowed($companyForName, $sn);
        }

        $oldStatus = $ticket->status;

        if (isset($validated['status']) && $validated['status'] !== $oldStatus) {
            $allowedTransitions = [
                'open' => ['in_progress'],
                'in_progress' => ['in_review'],
                'in_review' => ['resolved'],
                'resolved' => ['closed', 'in_progress'],
                'closed' => ['in_progress'],
            ];

            if (!in_array($validated['status'], $allowedTransitions[$oldStatus] ?? [], true)) {
                throw ValidationException::withMessages([
                    'status' => 'Invalid workflow transition.',
                ]);
            }

            if ($validated['status'] === 'in_progress') {
                $hasRelatedTasks = $ticket->tasks()->exists();
                if ($hasRelatedTasks) {
                    $hasAssignedDeveloperAndSla = $ticket->tasks()
                        ->whereNotNull('assigned_to')
                        ->whereNotNull('sla_level')
                        ->exists();

                    if (!$hasAssignedDeveloperAndSla) {
                        throw ValidationException::withMessages([
                            'status' => 'Assign developer and SLA first to related tasks.',
                        ]);
                    }
                } else {
                    $dev = $validated['assigned_developer_id'] ?? $ticket->assigned_developer_id;
                    $sla = $validated['sla_level'] ?? $ticket->sla_level;
                    if (empty($dev) || empty($sla)) {
                        throw ValidationException::withMessages([
                            'status' => 'Assign developer and SLA first.',
                        ]);
                    }
                }

                // Notify QC that ticket is now in progress (they can monitor)
                TicketComment::create([
                    'ticket_id'   => $ticket->id,
                    'user_id'     => $user->id,
                    'body'        => 'Ticket #' . str_pad((string) $ticket->id, 4, '0', STR_PAD_LEFT) . ' is now in progress. QC testing will be required once development is complete.',
                    'role'        => 'system',
                    'type'        => 'workflow_notification',
                    'target_role' => 'qc',
                ]);
            }

            if ($validated['status'] === 'in_review') {
                $hasRelatedTasks = $ticket->tasks()->exists();
                if ($hasRelatedTasks) {
                    $tasksWithEstimate = $ticket->tasks()
                        ->whereNotNull('estimated_delivery_date');

                    if ($user->isDeveloper() && !$user->isSuperAdmin()) {
                        $tasksWithEstimate->where('assigned_to', $user->id);
                    }

                    if (!$tasksWithEstimate->exists()) {
                        throw ValidationException::withMessages([
                            'status' => 'Developer must update estimated delivery on the task first.',
                        ]);
                    }

                    $tasksWithEstimate->update([
                        'actual_delivery_date' => now(),
                    ]);
                } else {
                    $est = $validated['estimated_delivery_date'] ?? $ticket->estimated_delivery_date;
                    if (empty($est)) {
                        throw ValidationException::withMessages([
                            'status' => 'Developer must update estimated delivery first.',
                        ]);
                    }
                    $validated['actual_delivery_date'] = now();
                }

                TicketComment::create([
                    'ticket_id' => $ticket->id,
                    'user_id'   => $user->id,
                    'body'      => 'Developer delivered fix and notified QC for testing.',
                    'role'      => 'system',
                    'type'      => 'status_change',
                ]);

                TicketComment::create([
                    'ticket_id' => $ticket->id,
                    'user_id'   => $user->id,
                    'body'      => 'Developer delivered fix for ticket #' . str_pad((string) $ticket->id, 4, '0', STR_PAD_LEFT) . '. QC testing is required.',
                    'role'      => 'system',
                    'type'      => 'workflow_notification',
                    'target_role' => 'qc',
                ]);
            }

            if ($validated['status'] === 'resolved') {
                $hasRelatedTasks = $ticket->tasks()->exists();
                if ($hasRelatedTasks) {
                    $ticket->tasks()
                        ->whereNotNull('actual_delivery_date')
                        ->whereNull('qc_test_date')
                        ->update([
                            'qc_test_date' => now(),
                        ]);
                } else {
                    $validated['qc_test_date'] = now();
                }

                TicketComment::create([
                    'ticket_id' => $ticket->id,
                    'user_id'   => $user->id,
                    'body'      => 'QC confirmed test results and notified client.',
                    'role'      => 'system',
                    'type'      => 'status_change',
                ]);

                TicketComment::create([
                    'ticket_id' => $ticket->id,
                    'user_id'   => $user->id,
                    'body'      => 'QC passed ticket #' . str_pad((string) $ticket->id, 4, '0', STR_PAD_LEFT) . '. Client confirmation is required.',
                    'role'      => 'system',
                    'type'      => 'workflow_notification',
                    'target_role' => 'client',
                ]);
            }

            if ($validated['status'] === 'in_progress' && in_array($oldStatus, ['resolved', 'closed'], true)) {
                TicketComment::create([
                    'ticket_id' => $ticket->id,
                    'user_id'   => $user->id,
                    'body'      => 'Client reported issue during testing. Ticket sent back to developer.',
                    'role'      => 'system',
                    'type'      => 'status_change',
                ]);

                TicketComment::create([
                    'ticket_id' => $ticket->id,
                    'user_id'   => $user->id,
                    'body'      => 'Client updated ticket #' . str_pad((string) $ticket->id, 4, '0', STR_PAD_LEFT) . '. Developer action is required.',
                    'role'      => 'system',
                    'type'      => 'workflow_notification',
                    'target_role' => 'developer',
                ]);
            }
        }

        if (!empty($validated['system'])) {
            $company = \App\Models\Company::where('name', $validated['system'])->first();
            if ($company) {
                $validated['company_id'] = $company->id;
            }
        }

        $redirectTo = $validated['redirect_to'] ?? null;
        unset($validated['redirect_to']);

        $oldAssignee = $ticket->assigned_developer_id;
        $ticket->update($validated);

        if ($ticket->tasks()->exists()) {
            $taskUpdates = [];
            if (array_key_exists('assigned_developer_id', $validated)) {
                $taskUpdates['assigned_to'] = $validated['assigned_developer_id'];
            }
            if (array_key_exists('sla_level', $validated)) {
                $taskUpdates['sla_level'] = $validated['sla_level'];
            }
            if (array_key_exists('estimated_delivery_date', $validated)) {
                $taskUpdates['estimated_delivery_date'] = $validated['estimated_delivery_date'];
            }
            if (array_key_exists('actual_delivery_date', $validated)) {
                $taskUpdates['actual_delivery_date'] = $validated['actual_delivery_date'];
            }
            if (array_key_exists('qc_test_date', $validated)) {
                $taskUpdates['qc_test_date'] = $validated['qc_test_date'];
            }

            // Sync task status based on ticket status change
            // ticket open        → task todo
            // ticket in_progress → task doing
            // ticket in_review   → task doing
            // ticket resolved    → task done
            // ticket closed      → task done
            if (isset($validated['status']) && $validated['status'] !== $oldStatus) {
                $taskStatusMap = [
                    'open'        => 'todo',
                    'in_progress' => 'doing',
                    'in_review'   => 'doing',
                    'resolved'    => 'done',
                    'closed'      => 'done',
                ];
                if (isset($taskStatusMap[$validated['status']])) {
                    $taskUpdates['status'] = $taskStatusMap[$validated['status']];
                }
            }

            if (!empty($taskUpdates)) {
                $ticket->tasks()->update($taskUpdates);
            }
        } elseif ($shouldCreateTaskFromTicket) {
            // Map ticket priority to task priority scale.
            $priorityMap = [
                'low'      => 'low',
                'medium'   => 'medium',
                'high'     => 'high',
                'critical' => 'urgent',
            ];

            $taskPriority = $priorityMap[$ticket->priority] ?? 'medium';

            Task::create([
                'title'       => $ticket->title,
                'description' => $ticket->description,
                'priority'    => $taskPriority,
                'status'      => 'todo',
                'assigned_to' => $ticket->assigned_developer_id,
                'ticket_id'   => $ticket->id,
                'due_date'    => $ticket->due_date,
                'sla_level'   => $ticket->sla_level,
                'estimated_delivery_date' => $ticket->estimated_delivery_date,
                'actual_delivery_date'    => $ticket->actual_delivery_date,
                'qc_test_date'            => $ticket->qc_test_date,
                'company_id'  => $ticket->company_id,
                'user_id'     => $user->id,
            ]);
        }

        if (isset($validated['status']) && $validated['status'] !== $oldStatus) {
            TicketComment::create([
                'ticket_id' => $ticket->id,
                'user_id'   => $user->id,
                'body'      => 'Status changed to ' . ucfirst(str_replace('_', ' ', $validated['status'])),
                'role'      => 'system',
                'type'      => 'status_change',
            ]);
            
            try {
                if (in_array($ticket->status, ['in_progress', 'in_review'])) {
                    $qcUsers = \App\Models\User::where('is_disabled', false)->get()->filter(fn($u) => $u->isQc());
                    if ($qcUsers->isNotEmpty()) {
                        \Illuminate\Support\Facades\Notification::send($qcUsers, new \App\Notifications\TicketStatusUpdatedForQcNotification($ticket, 'Ticket'));
                    }
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send Ticket QC notification: ' . $e->getMessage());
            }
        }
        
        try {
            if ($ticket->assigned_developer_id && $ticket->assigned_developer_id != $oldAssignee) {
                $developer = \App\Models\User::find($ticket->assigned_developer_id);
                if ($developer && !$developer->is_disabled) {
                    $developer->notify(new \App\Notifications\TicketTaskAssignedNotification($ticket, 'Ticket'));
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send Ticket Update notification: ' . $e->getMessage());
        }

        if ($redirectTo === 'tasks') {
            if ($request->wantsJson()) {
                session()->flash('success', 'Ticket updated!');
                return response()->json(['success' => true, 'redirect' => route('tasks.index')]);
            }
            return redirect()->route('tasks.index')->with('success', 'Ticket updated!');
        }

        if ($request->wantsJson()) {
            session()->flash('success', 'Ticket updated!');
            return response()->json(['success' => true, 'redirect' => route('tickets.show', $ticket)]);
        }
        return redirect()->route('tickets.show', $ticket)->with('success', 'Ticket updated!');
    }

    public function addComment(Request $request, Ticket $ticket)
    {
        abort_unless(auth()->user()->hasPermission('add_comments'), 403, 'You do not have permission to add comments.');
        $this->authorizeTicketCompany($ticket); // ← Task 41

        $request->validate([
            'body'          => 'required|string',
            'attachments'   => 'nullable|array|max:10',
            'attachments.*' => 'file|max:25600|mimes:jpg,jpeg,png,json,zip',
        ]);

        $comment = TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id'   => auth()->id(),
            'body'      => $request->body,
            'role'      => auth()->user()->role ?? 'user',
            'type'      => 'comment',
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store("ticket-attachments/{$ticket->id}", 'public');
                TicketAttachment::create([
                    'ticket_id'     => $ticket->id,
                    'comment_id'    => $comment->id,
                    'user_id'       => auth()->id(),
                    'original_name' => $file->getClientOriginalName(),
                    'stored_path'   => $path,
                    'mime_type'     => $file->getMimeType(),
                    'size'          => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('tickets.show', $ticket)->with('success', 'Comment posted!');
    }

    public function uploadAttachment(Request $request, Ticket $ticket)
    {
        abort_unless(auth()->user()->hasPermission('upload_attachments'), 403, 'You do not have permission to upload attachments.');
        $this->authorizeTicketCompany($ticket); // ← Task 41

        $request->validate([
            'attachments'   => 'required|array|max:10',
            'attachments.*' => 'file|max:25600|mimes:jpg,jpeg,png,json,zip',
        ]);

        foreach ($request->file('attachments') as $file) {
            $path = $file->store("ticket-attachments/{$ticket->id}", 'public');
            TicketAttachment::create([
                'ticket_id'     => $ticket->id,
                'comment_id'    => null,
                'user_id'       => auth()->id(),
                'original_name' => $file->getClientOriginalName(),
                'stored_path'   => $path,
                'mime_type'     => $file->getMimeType(),
                'size'          => $file->getSize(),
            ]);
        }

        return redirect()->route('tickets.show', $ticket)->with('success', 'File(s) uploaded!');
    }

    public function deleteAttachment(Ticket $ticket, TicketAttachment $attachment)
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403, 'Only superadmins can delete attachments.');
        abort_if($attachment->ticket_id !== $ticket->id, 403);
        Storage::disk('public')->delete($attachment->stored_path);
        $attachment->delete();
        return redirect()->route('tickets.show', $ticket)->with('success', 'Attachment deleted!');
    }

    public function submitRating(Request $request, Ticket $ticket)
    {
        $this->authorizeTicketCompany($ticket);

        // Only the ticket creator (the client) can rate
        abort_unless(
            (int) $ticket->user_id === (int) auth()->id(),
            403,
            'Only the ticket reporter can submit a satisfaction rating.'
        );

        // Only allowed when ticket is resolved
        abort_unless(
            in_array($ticket->status, ['resolved', 'closed'], true),
            422,
            'You can only rate a resolved ticket.'
        );

        // Only allow one rating per ticket
        if ($ticket->csat_submitted_at) {
            return back()->with('error', 'You have already rated this ticket.');
        }

        $request->validate([
            'csat_rating'  => 'required|integer|min:1|max:5',
            'csat_comment' => 'nullable|string|max:1000',
        ]);

        $ticket->update([
            'csat_rating'       => $request->csat_rating,
            'csat_comment'      => $request->csat_comment,
            'csat_submitted_at' => now(),
        ]);

        return back()->with('success', 'Thank you for your feedback!');
    }

    public function destroy(Ticket $ticket)
    {
        abort_unless(auth()->user()->hasPermission('delete_tickets'), 403, 'You do not have permission to delete tickets.');
        $this->authorizeTicketCompany($ticket); // ← Task 41

        Storage::disk('public')->deleteDirectory("ticket-attachments/{$ticket->id}");
        $ticket->delete();
        return redirect()->route('tickets.index')->with('success', 'Ticket deleted!');
    }

    public function deleteComment(Ticket $ticket, \App\Models\TicketComment $comment)
    {
        abort_unless(auth()->user()->hasPermission('delete_comments'), 403, 'You do not have permission to delete comments.');
        $this->authorizeTicketCompany($ticket); // ← Task 41
        abort_if($comment->ticket_id !== $ticket->id, 403);
        abort_if($comment->user_id !== auth()->id(), 403);
        foreach ($comment->attachments as $att) {
            Storage::disk('public')->delete($att->stored_path);
            $att->delete();
        }
        $comment->delete();
        return response()->json(['success' => true]);
    }

    /**
     * When a company defines `systems` (JSON), `system_name` must be one of those values.
     * If `systems` is empty, any value is allowed (legacy / no catalog).
     */
    private function assertSystemNameAllowed(?Company $company, ?string $systemName): void
    {
        if (!$company) {
            return;
        }

        $systems = $company->systems ?? [];
        if (!is_array($systems)) {
            $systems = [];
        }

        if (count($systems) === 0) {
            return;
        }

        $name = $systemName !== null ? trim($systemName) : '';
        if ($name === '') {
            throw ValidationException::withMessages([
                'system_name' => 'Select a system name for this company.',
            ]);
        }

        if (!in_array($name, $systems, true)) {
            throw ValidationException::withMessages([
                'system_name' => 'That system name is not valid for the selected company.',
            ]);
        }
    }
}