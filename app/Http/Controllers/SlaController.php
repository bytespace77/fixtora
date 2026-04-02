<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SlaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // -----------------------------------------------------------------------
    // Default SLA time limits (hours) per priority
    // -----------------------------------------------------------------------
    private function slaLimits(): array
    {
        return session('sla_limits', [
            'critical' => 4,
            'high'     => 8,
            'medium'   => 24,
            'low'      => 72,
        ]);
    }

    // -----------------------------------------------------------------------
    // POST /sla-monitor/configure — save SLA limits to session
    // -----------------------------------------------------------------------
    public function configure(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('view_sla_monitor'), 403);

        $request->validate([
            'critical' => 'required|integer|min:1|max:720',
            'high'     => 'required|integer|min:1|max:720',
            'medium'   => 'required|integer|min:1|max:720',
            'low'      => 'required|integer|min:1|max:720',
        ]);

        session(['sla_limits' => [
            'critical' => (int) $request->critical,
            'high'     => (int) $request->high,
            'medium'   => (int) $request->medium,
            'low'      => (int) $request->low,
        ]]);

        return back()->with('sla_saved', 'SLA limits updated successfully.');
    }

    // -----------------------------------------------------------------------
    // GET /sla-monitor
    // Supports ?quarter=2026-Q1 to filter by a specific quarter
    // -----------------------------------------------------------------------
    public function index(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('view_sla_monitor'), 403);

        $slaLimits     = $this->slaLimits();
        $selectedQuarter = $request->query('quarter'); // e.g. "2026-Q2" or null

        // Build list of last 8 quarters for the dropdown
        $quarterOptions = [];
        for ($i = 0; $i < 8; $i++) {
            $start = Carbon::now()->startOfQuarter()->subQuarters($i);
            $qNum  = ceil($start->month / 3);
            $key   = $start->year . '-Q' . $qNum;
            $quarterOptions[] = [
                'key'   => $key,
                'label' => 'Q' . $qNum . ' ' . $start->year,
                'start' => $start->copy(),
                'end'   => $start->copy()->endOfQuarter(),
            ];
        }

        // Resolve selected quarter date range
        $filterStart = null;
        $filterEnd   = null;
        $filterLabel = null;

        if ($selectedQuarter) {
            foreach ($quarterOptions as $opt) {
                if ($opt['key'] === $selectedQuarter) {
                    $filterStart = $opt['start'];
                    $filterEnd   = $opt['end'];
                    $filterLabel = $opt['label'];
                    break;
                }
            }
            // If not found in list, ignore
            if (!$filterStart) $selectedQuarter = null;
        }

        // Base query helper — scoped to selected quarter or all-time
        $base = function () use ($selectedQuarter, $filterStart, $filterEnd) {
            $q = Ticket::query();
            if ($selectedQuarter && $filterStart) {
                $q->whereBetween('created_at', [$filterStart, $filterEnd]);
            }
            return $q;
        };

        // Task 18: SLA Compliance Rate
        $total      = $base()->count();
        $resolved   = $base()->where('status', 'resolved')->count();
        $compliance = $total > 0 ? round(($resolved / $total) * 100) : 0;

        // Task 19: Active Breach Count
        $criticalOpen = $base()->where('priority', 'critical')
                               ->where('status', 'open')
                               ->count();

        // Task 20: Average Resolution Time
        $resolvedTickets  = $base()->where('status', 'resolved')->select('created_at', 'updated_at')->get();
        $avgResolutionHrs = 0;
        if ($resolvedTickets->count() > 0) {
            $totalHrs         = $resolvedTickets->sum(fn($t) => $t->created_at->diffInHours($t->updated_at));
            $avgResolutionHrs = round($totalHrs / $resolvedTickets->count());
        }

        // Task 21: At-Risk Tickets
        $priorityOrder = ['critical' => 0, 'high' => 1, 'medium' => 2, 'low' => 3];
        $atRisk = $base()->where('status', 'open')
            ->orderBy('created_at')
            ->get()
            ->sortBy(fn($t) => $priorityOrder[$t->priority] ?? 9)
            ->take(5)
            ->values();

        // Task 22: Compliance Table
        $allOpen = $base()->whereIn('status', ['open', 'in_progress', 'in_review'])
            ->orderBy('created_at')
            ->get()
            ->sortBy(fn($t) => $priorityOrder[$t->priority] ?? 9)
            ->values();

        // Task 18: Quarterly SLA % Trend chart (always all-time, last 4 quarters)
        $quarterly = [];
        for ($i = 3; $i >= 0; $i--) {
            $start     = Carbon::now()->startOfQuarter()->subQuarters($i);
            $end       = (clone $start)->endOfQuarter();
            $qTotal    = Ticket::whereBetween('created_at', [$start, $end])->count();
            $qResolved = Ticket::whereBetween('created_at', [$start, $end])->where('status', 'resolved')->count();
            $pct       = $qTotal > 0 ? round(($qResolved / $qTotal) * 100) : 0;
            $quarterly[] = [
                'label' => 'Q' . ceil($start->month / 3) . ' ' . $start->year,
                'pct'   => $pct,
            ];
        }

        return view('sla-monitor.index', compact(
            'total', 'compliance', 'criticalOpen', 'resolved',
            'avgResolutionHrs', 'atRisk', 'allOpen', 'quarterly',
            'slaLimits', 'selectedQuarter', 'quarterOptions', 'filterLabel',
            'filterStart', 'filterEnd'
        ));
    }
}