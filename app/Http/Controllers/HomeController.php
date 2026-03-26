<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $stats = [
            'active'   => Ticket::where('status', 'open')->count(),
            'resolved' => Ticket::where('status', 'resolved')->count(),
            'critical' => Ticket::where('priority', 'critical')->where('status', 'open')->count(),
            'total'    => Ticket::count(),
        ];

        $recentTickets = Ticket::with('user')->latest()->take(5)->get();

        return view('home', compact('stats', 'recentTickets'));
    }
}