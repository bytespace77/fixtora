<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('view_dashboard'), 403, 'You do not have permission to view the dashboard.');

        // ── Date range filter ─────────────────────────────────────────────
        $range  = $request->input('range', '7d');   // 24h | 7d | 30d | 90d
        $custom = $request->input('custom');         // YYYY-MM-DD,YYYY-MM-DD

        [$from, $to] = $this->parseRange($range, $custom);

        // ── Stats ─────────────────────────────────────────────────────────
        // ✅ Task 5: All counts are automatically scoped per company via
        //            Ticket model's global 'company' scope (superadmin sees all).
        $stats = [
            'active'   => Ticket::whereNotIn('status', ['resolved', 'closed'])->count(),
            'resolved' => Ticket::where('status', 'resolved')
                                 ->whereBetween('updated_at', [$from, $to])->count(),
            'critical' => Ticket::where('priority', 'critical')
                                 ->whereNotIn('status', ['resolved', 'closed'])->count(),
            'total'    => Ticket::count(), // ✅ Task 5: total tickets (scoped per company)
        ];

        // ── Chart data ────────────────────────────────────────────────────
        $chartData = $this->buildChartData($from, $to, $range);

        // ── Priority Queue ────────────────────────────────────────────────
        $queueTickets = Ticket::with('user')
            ->whereNotIn('status', ['resolved', 'closed'])
            ->orderByRaw("FIELD(priority,'critical','high','medium','low')")
            ->latest()
            ->take(10)
            ->get();

        // ── Recent Tickets (latest 5) ────────────────────────────────────
        $recentTickets = Ticket::with('user')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        // ── Ticket Scheduling Summary (next 30 days) ─────────────────────
        $dueFrom = now()->startOfDay();
        $dueTo   = now()->addDays(30)->endOfDay();

        $taskScheduled = Task::whereNotNull('due_date')
            ->where('status', '!=', 'done')
            ->whereBetween('due_date', [$dueFrom, $dueTo])
            ->count();

        $ticketScheduled = Ticket::whereNotNull('due_date')
            ->whereNotIn('status', ['resolved', 'closed'])
            ->whereBetween('due_date', [$dueFrom, $dueTo])
            ->count();

        $totalScheduled = $taskScheduled + $ticketScheduled;

        // Active in testing: doing tasks + in_progress / in_review tickets
        $taskActiveTesting = Task::whereNotNull('due_date')
            ->where('status', 'doing')
            ->whereBetween('due_date', [$dueFrom, $dueTo])
            ->count();

        $ticketActiveTesting = Ticket::whereNotNull('due_date')
            ->whereIn('status', ['in_progress', 'in_review'])
            ->whereBetween('due_date', [$dueFrom, $dueTo])
            ->count();

        $activeInTesting = $taskActiveTesting + $ticketActiveTesting;

        // Not started: todo tasks + open tickets (due within next 30 days)
        $taskNotStarted = Task::whereNotNull('due_date')
            ->where('status', 'todo')
            ->whereBetween('due_date', [$dueFrom, $dueTo])
            ->count();

        $ticketNotStarted = Ticket::whereNotNull('due_date')
            ->where('status', 'open')
            ->whereBetween('due_date', [$dueFrom, $dueTo])
            ->count();

        $notStarted = $taskNotStarted + $ticketNotStarted;

        // Completed within period: done tasks + resolved/closed tickets (due within next 30 days)
        $taskResolved = Task::whereNotNull('due_date')
            ->where('status', 'done')
            ->whereBetween('due_date', [$dueFrom, $dueTo])
            ->count();

        $ticketResolved = Ticket::whereNotNull('due_date')
            ->whereIn('status', ['resolved', 'closed'])
            ->whereBetween('due_date', [$dueFrom, $dueTo])
            ->count();

        $completedInPeriod = $taskResolved + $ticketResolved;

        // Overdue: due_date in the past, still not done/resolved
        $taskOverdue = Task::whereNotNull('due_date')
            ->where('status', '!=', 'done')
            ->whereDate('due_date', '<', now()->toDateString())
            ->count();

        $ticketOverdue = Ticket::whereNotNull('due_date')
            ->whereNotIn('status', ['resolved', 'closed'])
            ->whereDate('due_date', '<', now()->toDateString())
            ->count();

        $overdue = $taskOverdue + $ticketOverdue;

        // ── Search ────────────────────────────────────────────────────────
        $search        = $request->input('q', '');
        $searchResults = collect();
        if ($search !== '') {
            $searchResults = Ticket::with('user')
                ->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('system', 'like', "%{$search}%")
                      ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
                })
                ->latest()
                ->take(20)
                ->get();
        }

        // ── Export ────────────────────────────────────────────────────────
        if ($request->has('export')) {
            abort_unless(auth()->user()->hasPermission('export_dashboard'), 403, 'You do not have permission to export reports.');
            return $this->handleExport($request->input('export'), $from, $to, $stats);
        }

        return view('dashboard', compact(
            'stats', 'chartData', 'queueTickets',
            'recentTickets', 'totalScheduled', 'activeInTesting', 'overdue',
            'notStarted', 'completedInPeriod',
            'search', 'searchResults', 'range', 'from', 'to'
        ));
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    private function parseRange(string $range, ?string $custom): array
    {
        $to = Carbon::now()->endOfDay();

        if ($range === 'custom' && $custom) {
            $parts = explode(',', $custom);
            $from  = Carbon::parse($parts[0])->startOfDay();
            $to    = isset($parts[1]) ? Carbon::parse($parts[1])->endOfDay() : $to;
            return [$from, $to];
        }

        $from = match ($range) {
            '24h'  => Carbon::now()->subHours(24),
            '30d'  => Carbon::now()->subDays(30)->startOfDay(),
            '90d'  => Carbon::now()->subDays(90)->startOfDay(),
            default => Carbon::now()->subDays(6)->startOfDay(),
        };
        return [$from, $to];
    }

    private function buildChartData(Carbon $from, Carbon $to, string $range): array
    {
        $inflowRaw = Ticket::select(
                DB::raw('DATE(created_at) as day'),
                DB::raw('COUNT(*) as cnt')
            )
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('day')
            ->pluck('cnt', 'day')
            ->toArray();

        $resolvedRaw = Ticket::select(
                DB::raw('DATE(updated_at) as day'),
                DB::raw('COUNT(*) as cnt')
            )
            ->where('status', 'resolved')
            ->whereBetween('updated_at', [$from, $to])
            ->groupBy('day')
            ->pluck('cnt', 'day')
            ->toArray();

        $labels   = [];
        $inflow   = [];
        $resolved = [];
        $diff     = $from->diffInDays($to);

        $cursor = $from->copy()->startOfDay();
        while ($cursor->lte($to)) {
            $key = $cursor->toDateString();
            $label = $diff <= 7
                ? strtoupper($cursor->format('D'))
                : $cursor->format('d M');

            $labels[]   = $label;
            $inflow[]   = $inflowRaw[$key]  ?? 0;
            $resolved[] = $resolvedRaw[$key] ?? 0;

            $cursor->addDay();
        }

        return compact('labels', 'inflow', 'resolved');
    }

    private function handleExport(string $type, Carbon $from, Carbon $to, array $stats)
    {
        $tickets = Ticket::with('user')
            ->whereBetween('created_at', [$from, $to])
            ->latest()
            ->get();

        return $type === 'excel'
            ? $this->exportCsv($tickets, $stats, $from, $to)
            : $this->exportPdf($tickets, $stats, $from, $to);
    }

    private function exportCsv($tickets, array $stats, Carbon $from, Carbon $to)
    {
        $filename = 'fixtora-report-' . $from->format('Ymd') . '-' . $to->format('Ymd') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($tickets, $stats, $from, $to) {
            $h = fopen('php://output', 'w');
            fputcsv($h, ['Fixtora – Operational Report']);
            fputcsv($h, ['Period', $from->format('Y-m-d') . ' to ' . $to->format('Y-m-d')]);
            fputcsv($h, ['Generated', now()->format('Y-m-d H:i:s')]);
            fputcsv($h, []);
            fputcsv($h, ['SUMMARY']);
            fputcsv($h, ['Active Tickets',    $stats['active']]);
            fputcsv($h, ['Resolved (period)', $stats['resolved']]);
            fputcsv($h, ['Critical Open',     $stats['critical']]);
            fputcsv($h, []);
            fputcsv($h, ['ID','Title','System','Priority','Impact','Status','Reporter','Created']);
            foreach ($tickets as $t) {
                fputcsv($h, [
                    '#'.$t->id, $t->title, $t->system ?? '—',
                    ucfirst($t->priority), ucfirst($t->impact),
                    ucfirst(str_replace('_',' ',$t->status)),
                    optional($t->user)->name ?? '—',
                    $t->created_at->format('Y-m-d H:i'),
                ]);
            }
            fclose($h);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportPdf($tickets, array $stats, Carbon $from, Carbon $to)
    {
        $rows = '';
        foreach ($tickets as $t) {
            $color = match(strtolower($t->priority)) {
                'critical' => '#dc2626', 'high' => '#f97316',
                'medium'   => '#2563eb', default => '#6b7280',
            };
            $rows .= "<tr>
                <td>#".e($t->id)."</td>
                <td>".e($t->title)."</td>
                <td>".e($t->system ?? '—')."</td>
                <td style='color:{$color};font-weight:700'>".ucfirst(e($t->priority))."</td>
                <td>".ucfirst(str_replace('_',' ',e($t->status)))."</td>
                <td>".e(optional($t->user)->name ?? '—')."</td>
                <td>".e($t->created_at->format('Y-m-d H:i'))."</td>
            </tr>";
        }

        $period  = $from->format('Y-m-d').' → '.$to->format('Y-m-d');
        $genAt   = now()->format('Y-m-d H:i:s');
        $a=$stats['active']; $r=$stats['resolved']; $c=$stats['critical'];

        $html = <<<HTML
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"/>
<title>Fixtora Report</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:Arial,sans-serif;font-size:12px;color:#111827;padding:36px 40px}
h1{font-size:22px;font-weight:800;color:#1e3a8a;margin-bottom:4px}
.sub{font-size:11px;color:#6b7280;margin-bottom:24px}
.stats{display:flex;gap:16px;margin-bottom:28px}
.stat{border:1px solid #e5e7ef;border-radius:8px;padding:14px 20px;flex:1}
.stat-label{font-size:9px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:#6b7280;margin-bottom:6px}
.stat-value{font-size:26px;font-weight:800;color:#111827}
table{width:100%;border-collapse:collapse}
th{text-align:left;padding:8px 10px;font-size:9px;font-weight:700;letter-spacing:.6px;text-transform:uppercase;color:#6b7280;border-bottom:2px solid #e5e7ef}
td{padding:9px 10px;border-bottom:1px solid #f3f4f6;font-size:11px;color:#374151}
.footer{margin-top:28px;font-size:10px;color:#9ca3af;text-align:right}
@media print{.no-print{display:none!important}}
</style></head><body>
<h1>Fixtora – Operational Report</h1>
<div class="sub">Period: {$period} &nbsp;|&nbsp; Generated: {$genAt}</div>
<div class="stats">
  <div class="stat"><div class="stat-label">Active Tickets</div><div class="stat-value">{$a}</div></div>
  <div class="stat"><div class="stat-label">Resolved (period)</div><div class="stat-value">{$r}</div></div>
  <div class="stat"><div class="stat-label">Critical Open</div><div class="stat-value">{$c}</div></div>
</div>
<table><thead><tr><th>ID</th><th>Title</th><th>System</th><th>Priority</th><th>Status</th><th>Reporter</th><th>Created</th></tr></thead>
<tbody>{$rows}</tbody></table>
<div class="footer">Fixtora Helpdesk &copy; {$genAt}</div>
<script>window.onload=function(){window.print();};</script>
</body></html>
HTML;

        return response($html)->header('Content-Type', 'text/html; charset=utf-8');
    }
}