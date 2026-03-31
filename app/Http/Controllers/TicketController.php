<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketComment;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Ticket::with('user')->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $tickets = $query->paginate(10)->withQueryString();

        return view('tickets.index', compact('tickets'));
    }

    public function create()
    {
        return view('tickets.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'system'      => 'required|string',
            'priority'    => 'required|in:low,medium,high,critical',
            'impact'      => 'required|in:low,medium,high,critical',
            'status'      => 'required|in:open,in_progress,in_review,resolved,closed',
            'due_date'    => 'nullable|date',
        ]);

        $validated['user_id'] = auth()->id();

        Ticket::create($validated);

        return redirect()->route('tickets.index')->with('success', 'Ticket created successfully!');
    }

    public function show(Ticket $ticket)
    {
        if (!$ticket->is_read) {
            $ticket->timestamps = false;
            $ticket->is_read = true;
            $ticket->save();
            $ticket->timestamps = true;
        }

        $ticket->load('comments.user');
        return view('tickets.show', compact('ticket'));
    }

    public function update(Request $request, Ticket $ticket)
    {
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
                'user_id' => auth()->id(),
                'body' => 'Status changed to ' . ucfirst(str_replace('_', ' ', $validated['status'])),
                'role' => 'system',
                'type' => 'status_change'
            ]);
        }

        return redirect()->route('tickets.show', $ticket)->with('success', 'Ticket updated!');
    }

    public function addComment(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'body' => 'required|string',
            'role' => 'required|in:superadmin,user',
        ]);

        TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'body' => $validated['body'],
            'role' => $validated['role'],
            'type' => 'comment'
        ]);

        return redirect()->route('tickets.show', $ticket)->with('success', 'Comment posted!');
    }
}