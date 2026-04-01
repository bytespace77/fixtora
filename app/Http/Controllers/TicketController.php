<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\TicketAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('list_tickets'), 403, 'You do not have permission to view tickets.');

        $query = Ticket::with('user')->latest();
        if ($request->status) {
            $query->where('status', $request->status);
        }
        $tickets = $query->paginate(10)->withQueryString();
        return view('tickets.index', compact('tickets'));
    }

    public function create()
    {
        abort_unless(auth()->user()->hasPermission('create_tickets'), 403, 'You do not have permission to create tickets.');

        return view('tickets.create');
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

        if (!$ticket->is_read) {
            $ticket->timestamps = false;
            $ticket->is_read = true;
            $ticket->save();
            $ticket->timestamps = true;
        }

        // Load comments with their attachments, and ticket-level attachments (no comment_id)
        $ticket->load([
            'comments.user',
            'comments.attachments',
            'attachments' => fn($q) => $q->whereNull('comment_id'),
        ]);

        return view('tickets.show', compact('ticket'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        abort_unless(auth()->user()->hasPermission('edit_tickets'), 403, 'You do not have permission to edit tickets.');

        $validated = $request->validate([
            'title'       => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'system'      => 'sometimes|nullable|string',
            'priority'    => 'sometimes|required|in:low,medium,high,critical',
            'impact'      => 'sometimes|required|in:low,medium,high,critical',
            'status'      => 'sometimes|required|in:open,in_progress,in_review,resolved,closed',
            'due_date'    => 'nullable|date',
        ]);

        $oldStatus = $ticket->status;
        $ticket->update($validated);

        if (isset($validated['status']) && $validated['status'] !== $oldStatus) {
            TicketComment::create([
                'ticket_id' => $ticket->id,
                'user_id'   => auth()->id(),
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

        // Save any files attached to this comment
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

    // Upload new attachment to an existing ticket (no comment)
    public function uploadAttachment(Request $request, Ticket $ticket)
    {
        abort_unless(auth()->user()->hasPermission('upload_attachments'), 403, 'You do not have permission to upload attachments.');

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

    // Delete a single attachment
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

        Storage::disk('public')->deleteDirectory("ticket-attachments/{$ticket->id}");
        $ticket->delete();
        return redirect()->route('tickets.index')->with('success', 'Ticket deleted!');
    }

    // Delete a single comment (and its attachments) — only the comment's author may delete
    public function deleteComment(Ticket $ticket, \App\Models\TicketComment $comment)
    {
        abort_unless(auth()->user()->hasPermission('delete_comments'), 403, 'You do not have permission to delete comments.');
        abort_if($comment->ticket_id !== $ticket->id, 403);
        abort_if($comment->user_id !== auth()->id(), 403);
        // Delete any files attached to this comment
        foreach ($comment->attachments as $att) {
            Storage::disk('public')->delete($att->stored_path);
            $att->delete();
        }
        $comment->delete();
        return response()->json(['success' => true]);
    }
}