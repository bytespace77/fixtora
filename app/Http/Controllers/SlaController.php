<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Support\Carbon;

class SlaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $total    = Ticket::count();
        $resolved = Ticket::where('status', 'resolved')->count();

        // Overall compliance = resolved / total * 100
        $compliance = $total > 0 ? round(($resolved / $total) * 100) : 0;

        // Active breaches = critical tickets still open
        $criticalOpen = Ticket::where('priority', 'critical')
                              ->where('status', 'open')
                              ->count();

        // Average resolution time in hours — use PHP instead of DB-specific SQL
        $resolvedTickets = Ticket::where('status', 'resolved')
                                 ->select('created_at', 'updated_at')
                                 ->get();

        $avgResolutionHrs = 0;
        if ($resolvedTickets->count() > 0) {
            $totalHrs = $resolvedTickets->sum(function ($t) {
                return $t->created_at->diffInHours($t->updated_at);
            });
            $avgResolutionHrs = round($totalHrs / $resolvedTickets->count());
        }

        // At-risk = open tickets ordered by priority then age (PHP sort, no FIELD())
        $priorityOrder = ['critical' => 0, 'high' => 1, 'medium' => 2, 'low' => 3];
        $atRisk = Ticket::where('status', 'open')
            ->orderBy('created_at')
            ->get()
            ->sortBy(fn($t) => $priorityOrder[$t->priority] ?? 9)
            ->take(5)
            ->values();

        // All open tickets for compliance table
        $allOpen = Ticket::whereIn('status', ['open', 'in_progress', 'in_review'])
            ->orderBy('created_at')
            ->get()
            ->sortBy(fn($t) => $priorityOrder[$t->priority] ?? 9)
            ->values();

        // Quarterly SLA % — last 4 quarters
        $quarterly = [];
        for ($i = 3; $i >= 0; $i--) {
            $start     = Carbon::now()->startOfQuarter()->subQuarters($i);
            $end       = (clone $start)->endOfQuarter();
            $qTotal    = Ticket::whereBetween('created_at', [$start, $end])->count();
            $qResolved = Ticket::whereBetween('created_at', [$start, $end])
                               ->where('status', 'resolved')
                               ->count();
            $pct = $qTotal > 0 ? round(($qResolved / $qTotal) * 100) : 0;
            $quarterly[] = [
                'label' => 'Q' . ceil($start->month / 3) . ' ' . $start->year,
                'pct'   => $pct,
            ];
        }

        return view('sla-monitor.index', compact(
            'compliance', 'criticalOpen', 'resolved',
            'avgResolutionHrs', 'atRisk', 'allOpen', 'quarterly'
        ));
    }
}