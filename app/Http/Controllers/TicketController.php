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

    /**
     * Show create ticket form
     */
    public function create()
    {
        return view('tickets.create');
    }

    /**
     * Store ticket in database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'system' => 'required|string',
            'priority' => 'required|in:low,medium,high,critical',
            'impact' => 'required|in:low,medium,high',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'open';

        Ticket::create($validated);

        return redirect()->route('tickets.index')->with('success', 'Ticket created successfully!');
    }

    /**
     * Show all tickets
     */
    public function index()
    {
        $tickets = Ticket::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('tickets.index', compact('tickets'));
    }

    /**
     * Show single ticket
     */
    public function show(Ticket $ticket)
    {
        $this->authorize('view', $ticket);
        return view('tickets.show', compact('ticket'));
    }
}
