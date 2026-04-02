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
        abort_unless(auth()->user()->hasPermission('view_sla_monitor'), 403, 'You do not have permission to view the SLA monitor.');

        // -----------------------------------------------------------------------
        // Task 18: SLA Compliance Rate — % resolved tickets vs total
        // -----------------------------------------------------------------------
        $total    = Ticket::count();
        $resolved = Ticket::where('status', 'resolved')->count();

        // Overall compliance = resolved / total * 100
        $compliance = $total > 0 ? round(($resolved / $total) * 100) : 0;

        // -----------------------------------------------------------------------
        // Task 19: Active Breach Count — critical-priority open tickets
        // -----------------------------------------------------------------------
        $criticalOpen = Ticket::where('priority', 'critical')
                              ->where('status', 'open')
                              ->count();

        // -----------------------------------------------------------------------
        // Task 20: Average Resolution Time — mean hours from creation to resolution
        // -----------------------------------------------------------------------
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

        // -----------------------------------------------------------------------
        // Task 21: At-Risk Tickets List — top-5 open sorted by priority then age
        // -----------------------------------------------------------------------
        $priorityOrder = ['critical' => 0, 'high' => 1, 'medium' => 2, 'low' => 3];

        $atRisk = Ticket::where('status', 'open')
            ->orderBy('created_at')           // oldest first (longest waiting)
            ->get()
            ->sortBy(fn($t) => $priorityOrder[$t->priority] ?? 9)  // critical first
            ->take(5)
            ->values();

        // -----------------------------------------------------------------------
        // Task 22: Compliance Table — all open/in-progress tickets with priority & age
        // -----------------------------------------------------------------------
        $allOpen = Ticket::whereIn('status', ['open', 'in_progress', 'in_review'])
            ->orderBy('created_at')
            ->get()
            ->sortBy(fn($t) => $priorityOrder[$t->priority] ?? 9)
            ->values();

        // -----------------------------------------------------------------------
        // Task 18: Quarterly SLA % Trend — last 4 quarters for chart
        // -----------------------------------------------------------------------
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
            'total', 'compliance', 'criticalOpen', 'resolved',
            'avgResolutionHrs', 'atRisk', 'allOpen', 'quarterly'
        ));
    }
}