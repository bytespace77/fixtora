<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
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
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status']  = 'open';

        Ticket::create($validated);

        return redirect()->route('tickets.index')->with('success', 'Ticket created successfully!');
    }

    public function show(Ticket $ticket)
    {
        return view('tickets.show', compact('ticket'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);

        $ticket->update(['status' => $request->status]);

        return redirect()->route('tickets.show', $ticket)->with('success', 'Ticket status updated!');
    }
}