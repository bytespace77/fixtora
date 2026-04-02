<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\TicketAttachment;
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

        if ($user->isSuperAdmin()) {
            return; // Superadmin can access all companies
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
        if ($request->status) {
            $query->where('status', $request->status);
        }
        $tickets = $query->paginate(10)->withQueryString();
        $companySystems = auth()->user()->company?->systems ?? [];

        return view('tickets.index', compact('tickets', 'companySystems'));
    }

    public function create()
    {
        abort_unless(auth()->user()->hasPermission('create_tickets'), 403, 'You do not have permission to create tickets.');

        $companies = auth()->user()->isSuperAdmin()
            ? \App\Models\Company::where('is_active', true)->pluck('name')
            : [];

        $companySystems = auth()->user()->company?->systems ?? [];

        return view('tickets.create', compact('companies', 'companySystems'));
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('create_tickets'), 403, 'You do not have permission to create tickets.');

        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'required|string',
            'system'        => 'required|string',
            'priority'      => 'required|in:low,medium,high,critical',
            'impact'        => 'required|in:low,medium,high,critical',
            'status'        => 'required|in:open,in_progress,in_review,resolved,closed',
            'due_date'      => 'nullable|date',
            'attachments'   => 'nullable|array|max:10',
            'attachments.*' => 'file|max:25600|mimes:jpg,jpeg,png,json,zip',
        ]);

        $validated['user_id'] = auth()->id();
        unset($validated['attachments']);

        $validated['company_id'] = auth()->user()->company_id;
        if (auth()->user()->isSuperAdmin()) {
            $company = \App\Models\Company::where('name', $validated['system'])->first();
            if ($company) {
                $validated['company_id'] = $company->id;
            }
        }

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
            'tasks.assignee'
        ]);

        $developers = collect();
        if (auth()->user()->hasPermission('assign_developer')) {
            $developerQuery = \App\Models\User::with('userRole')
                ->whereHas('userRole', function ($q) {
                    $q->whereRaw('LOWER(TRIM(name)) = ?', ['developer']);
                })
                ->orderBy('name');

            if (!auth()->user()->isSuperAdmin()) {
                $developerQuery->where('company_id', $ticket->company_id);
            }

            $developers = $developerQuery->get();
        }

        return view('tickets.show', compact('ticket', 'developers'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $user = auth()->user();
        $this->authorizeTicketCompany($ticket); // ← Task 41

        $canEditTicket = $user->hasPermission('edit_tickets');
        $isAssignedDeveloper = (int) $ticket->assigned_developer_id === (int) $user->id;
        $estimateOnlyKeys = ['_token', '_method', 'estimated_delivery_date'];
        $requestKeys = array_keys($request->all());
        $isEstimateOnlyRequest = empty(array_diff($requestKeys, $estimateOnlyKeys));

        abort_unless(
            $canEditTicket || ($isAssignedDeveloper && $isEstimateOnlyRequest),
            403,
            'You do not have permission to update this ticket.'
        );

        $rules = [
            'estimated_delivery_date' => 'sometimes|nullable|date',
        ];

        if ($canEditTicket) {
            $rules = array_merge($rules, [
                'title'       => 'sometimes|required|string|max:255',
                'description' => 'sometimes|required|string',
                'system'      => 'sometimes|nullable|string',
                'priority'    => 'sometimes|required|in:low,medium,high,critical',
                'impact'      => 'sometimes|required|in:low,medium,high,critical',
                'status'      => 'sometimes|required|in:open,in_progress,in_review,resolved,closed',
                'due_date'    => 'sometimes|nullable|date',
                'assigned_developer_id' => 'sometimes|nullable|exists:users,id',
                'sla_level'             => 'sometimes|nullable|in:Low,Medium,High,Critical',
                'actual_delivery_date'  => 'sometimes|nullable|date',
                'qc_test_date'          => 'sometimes|nullable|date',
            ]);
        }

        $validated = $request->validate($rules);

        if (!$canEditTicket) {
            $validated = array_intersect_key($validated, array_flip(['estimated_delivery_date']));
        }

        if (array_key_exists('assigned_developer_id', $validated) || array_key_exists('sla_level', $validated)) {
            abort_unless($user->isSuperAdmin(), 403, 'Only superadmin can assign developer and SLA.');

            $selectedDeveloper = $validated['assigned_developer_id'] ?? $ticket->assigned_developer_id;
            $selectedSla = $validated['sla_level'] ?? $ticket->sla_level;
            if (empty($selectedDeveloper) || empty($selectedSla)) {
                throw ValidationException::withMessages([
                    'assigned_developer_id' => 'Assigned developer and SLA are required.',
                    'sla_level' => 'Assigned developer and SLA are required.',
                ]);
            }

            if ((int) $selectedDeveloper !== (int) $ticket->assigned_developer_id || $selectedSla !== $ticket->sla_level) {
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
            }
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
                $developerId = $validated['assigned_developer_id'] ?? $ticket->assigned_developer_id;
                $sla = $validated['sla_level'] ?? $ticket->sla_level;
                if (empty($developerId) || empty($sla)) {
                    throw ValidationException::withMessages([
                        'status' => 'Assign developer and SLA first.',
                    ]);
                }
            }

            if ($validated['status'] === 'in_review') {
                $estimate = $validated['estimated_delivery_date'] ?? $ticket->estimated_delivery_date;
                if (empty($estimate)) {
                    throw ValidationException::withMessages([
                        'status' => 'Developer must update estimated delivery first.',
                    ]);
                }
                $validated['actual_delivery_date'] = $validated['actual_delivery_date'] ?? now();

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
                $validated['qc_test_date'] = $validated['qc_test_date'] ?? now();

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

        $ticket->update($validated);

        if (isset($validated['status']) && $validated['status'] !== $oldStatus) {
            TicketComment::create([
                'ticket_id' => $ticket->id,
                'user_id'   => $user->id,
                'body'      => 'Status changed to ' . ucfirst(str_replace('_', ' ', $validated['status'])),
                'role'      => 'system',
                'type'      => 'status_change',
            ]);
        }

        return redirect()->route('tickets.show', $ticket)->with('success', 'Ticket updated!');
    }

    public function addComment(Request $request, Ticket $ticket)
    {
        abort_unless(auth()->user()->hasPermission('add_comments'), 403, 'You do not have permission to add comments.');
        $this->authorizeTicketCompany($ticket); // ← Task 41

        $request->validate([
            'body'          => 'required|string',
            'role'          => 'required|in:superadmin,user',
            'attachments'   => 'nullable|array|max:10',
            'attachments.*' => 'file|max:25600|mimes:jpg,jpeg,png,json,zip',
        ]);

        $comment = TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id'   => auth()->id(),
            'body'      => $request->body,
            'role'      => $request->role,
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
}