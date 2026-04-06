<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Return a Task query scoped to the current user's company.
     * SuperAdmin sees all tasks (no company filter).
     * Developers see only their own assigned tasks.
     * All others see tasks belonging to their company.
     */
    private function companyTask(): \Illuminate\Database\Eloquent\Builder
    {
        $user = auth()->user();
        $query = Task::withoutGlobalScopes();

        if ($user->isSuperAdmin()) {
            return $query; // sees everything
        }

        if ($user->isDeveloper()) {
            return $query->where('assigned_to', $user->id);
        }

        if ($user->company_id) {
            return $query->where('company_id', $user->company_id);
        }

        // Fallback: return nothing
        return $query->whereRaw('1 = 0');
    }

    public function index(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('view_reports'), 403, 'You do not have permission to view reports.');

        $range     = $request->input('range', '30d');
        $custom    = $request->input('custom');   // legacy: YYYY-MM-DD,YYYY-MM-DD
        $fromParam = $request->input('from');     // new: YYYY-MM-DD
        $toParam   = $request->input('to');       // new: YYYY-MM-DD

        [$from, $to] = $this->parseRange($range, $custom, $fromParam, $toParam);

        $totalTickets = Ticket::whereBetween('created_at', [$from, $to])->count();

        // Total Tasks in period
        $totalTasks = $this->companyTask()
            ->whereBetween('created_at', [$from, $to])
            ->count();

        // Avg Resolution Time — combined tickets (resolved/closed) + tasks (done)
        $resolvedTickets = Ticket::whereIn('status', ['resolved', 'closed'])
            ->whereBetween('updated_at', [$from, $to])->get();

        $doneTasks = $this->companyTask()
            ->where('status', 'done')
            ->whereBetween('updated_at', [$from, $to])->get();

        $totalResolved = $resolvedTickets->count() + $doneTasks->count();

        if ($totalResolved > 0) {
            $ticketHours = $resolvedTickets->sum(fn($t) => $t->created_at->diffInHours($t->updated_at));
            $taskHours   = $doneTasks->sum(function($t) {
                $start = $t->assigned_date ? \Carbon\Carbon::parse($t->assigned_date) : $t->created_at;
                $end   = $t->actual_delivery_date ? \Carbon\Carbon::parse($t->actual_delivery_date) : $t->updated_at;
                return max(0, $start->diffInHours($end));
            });
            $avgResolution = round(($ticketHours + $taskHours) / $totalResolved);
        } else {
            $avgResolution = 0;
        }

        // SLA Task Success Rate — Task 24
        // % tasks completed on time (actual_delivery_date <= due_date) vs total tasks with due_date
        $slaTotalTasks = $this->companyTask()
            ->whereNotNull('due_date')
            ->whereBetween('created_at', [$from, $to])
            ->count();

        if ($slaTotalTasks > 0) {
            $slaOnTime = $this->companyTask()
                ->where('status', 'done')
                ->whereNotNull('due_date')
                ->whereNotNull('actual_delivery_date')
                ->whereColumn('actual_delivery_date', '<=', 'due_date')
                ->whereBetween('created_at', [$from, $to])
                ->count();
            $slaCompliance = round(($slaOnTime / $slaTotalTasks) * 100);
        } else {
            $slaCompliance = 0;
        }

        // Real CSAT — average of submitted ratings within the period
        $csatData = Ticket::whereNotNull('csat_rating')
            ->whereNotNull('csat_submitted_at')
            ->whereBetween('csat_submitted_at', [$from, $to])
            ->selectRaw('AVG(csat_rating) as avg_rating, COUNT(*) as total')
            ->first();

        if ($csatData && $csatData->total > 0) {
            $csat      = number_format($csatData->avg_rating, 1) . '/5';
            $csatCount = $csatData->total . ' rating' . ($csatData->total > 1 ? 's' : '');
        } else {
            $csat      = 'N/A';
            $csatCount = 'No ratings yet';
        }

        // Trend Data — tickets and tasks
        $chartData   = $this->buildChartData($from, $to, $range);
        $labels      = $chartData['labels'];
        $newTrend    = $chartData['inflow'];
        $closedTrend = $chartData['resolved'];
        $newTaskTrend  = $chartData['taskInflow'];
        $doneTaskTrend = $chartData['taskDone'];

        // Issue Distribution Map (3 buckets)
        // For your workflow: todo -> open, doing -> in_progress, done -> resolved
        // Here we map ticket.status into:
        // - Open
        // - In Progress
        // - Resolved (includes 'resolved' + 'closed')
        $distributionRaw = Ticket::select('status', DB::raw('count(*) as count'))
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $openCount       = (int) ($distributionRaw['open'] ?? 0);
        $inProgressCount = (int) ($distributionRaw['in_progress'] ?? 0);
        $resolvedCount   = (int) ($distributionRaw['resolved'] ?? 0) + (int) ($distributionRaw['closed'] ?? 0);

        $distribution = [$openCount, $inProgressCount, $resolvedCount];

        // Team Performance — superadmin only, grouped by role
        $isSuperAdmin = auth()->user()->isSuperAdmin();
        $agentsByRole = [];

        if ($isSuperAdmin) {
            // Helper: real stats for an array of user IDs, sourced from tasks
            $buildStats = function (array $userIds) use ($from, $to): array {
                if (empty($userIds)) {
                    return ['resolved' => 0, 'avg_response' => '—', 'load' => 0, 'pending_tickets' => 0];
                }

                // Resolved: tasks assigned to these users with status 'done' within the date range
                // withoutGlobalScopes() prevents the Task scope from re-filtering by Auth::user()
                $doneTasks = Task::withoutGlobalScopes()
                    ->whereIn('assigned_to', $userIds)
                    ->where('status', 'done')
                    ->whereBetween('updated_at', [$from, $to])
                    ->get(['assigned_date', 'actual_delivery_date', 'created_at', 'updated_at']);

                $resolvedCount = $doneTasks->count();

                // Avg Response: assigned_date → actual_delivery_date (fall back to created_at / updated_at)
                if ($resolvedCount > 0) {
                    $totalMins = $doneTasks->sum(function ($t) {
                        $start = $t->assigned_date   ? Carbon::parse($t->assigned_date)          : $t->created_at;
                        $end   = $t->actual_delivery_date ? Carbon::parse($t->actual_delivery_date) : $t->updated_at;
                        return max(0, $start->diffInMinutes($end));
                    });
                    $avgMins = intval($totalMins / $resolvedCount);
                    $h = intdiv($avgMins, 60);
                    $m = $avgMins % 60;
                    $avgResponse = $h > 0 ? "{$h}h {$m}m" : "{$m}m";
                } else {
                    $avgResponse = '—';
                }

                // Load: tasks currently active (not yet done)
                $openCount = Task::withoutGlobalScopes()
                    ->whereIn('assigned_to', $userIds)
                    ->whereIn('status', ['todo', 'doing'])
                    ->count();
                $loadPct = min(100, $openCount * 5);

                // Pending tickets: tickets assigned to these users not yet resolved/closed
                $pendingTickets = Ticket::withoutGlobalScope('company')
                    ->whereIn('assigned_developer_id', $userIds)
                    ->whereNotIn('status', ['resolved', 'closed'])
                    ->count();

                return ['resolved' => $resolvedCount, 'avg_response' => $avgResponse, 'load' => $loadPct, 'pending_tickets' => $pendingTickets];
            };

            $roleConfig = [
                'superadmin' => ['label' => 'Super Admin', 'color' => '#1e3a6e'],
                'developer'  => ['label' => 'Developer',   'color' => '#2a7a5e'],
                'admin'      => ['label' => 'Admin',        'color' => '#5a3e8a'],
            ];

            // Superadmin row first (current user)
            $superUser = auth()->user();
            $s = $buildStats([$superUser->id]);
            $agentsByRole['Super Admin'][] = [
                'name'            => $superUser->name,
                'initials'        => strtoupper(substr($superUser->name, 0, 2)),
                'color'           => '#1e3a6e',
                'resolved'        => $s['resolved'],
                'avg_response'    => $s['avg_response'],
                'load'            => $s['load'],
                'pending_tickets' => $s['pending_tickets'],
            ];

            // All staff — match by the `role` string column OR by the linked custom role name
            $staffUsers = User::where(function ($q) {
                    $q->whereIn('role', ['developer', 'admin'])
                      ->orWhereHas('userRole', fn($r) => $r->whereRaw('LOWER(name) IN (?,?)', ['developer', 'admin']));
                })
                ->with('userRole')
                ->orderBy('name')
                ->get();

            foreach ($staffUsers as $u) {
                // Prefer the linked custom role name; fall back to the role string column
                $rawRole   = strtolower(trim((string) (optional($u->userRole)->name ?: $u->role)));
                $cfg       = $roleConfig[$rawRole] ?? ['label' => ucfirst($rawRole), 'color' => '#3b6ea8'];
                $roleLabel = $cfg['label'];
                $s = $buildStats([$u->id]);
                $agentsByRole[$roleLabel][] = [
                    'name'            => $u->name,
                    'initials'        => strtoupper(substr($u->name, 0, 2)),
                    'color'           => $cfg['color'],
                    'resolved'        => $s['resolved'],
                    'avg_response'    => $s['avg_response'],
                    'load'            => $s['load'],
                    'pending_tickets' => $s['pending_tickets'],
                ];
            }

            // Ensure consistent role order: Developer before Admin
            $roleOrder = ['Developer', 'Admin'];
            uksort($agentsByRole, function ($a, $b) use ($roleOrder) {
                $posA = array_search($a, $roleOrder);
                $posB = array_search($b, $roleOrder);
                $posA = $posA === false ? 99 : $posA;
                $posB = $posB === false ? 99 : $posB;
                return $posA <=> $posB;
            });
        }

        // ── Export ────────────────────────────────────────────────────────
        if ($request->has('export')) {
            abort_unless(auth()->user()->hasPermission('export_reports') || auth()->user()->hasPermission('view_reports'), 403, 'Permission denied.');
            $stats = [
                'total' => $totalTickets,
                'avgResolution' => $avgResolution,
                'slaCompliance' => $slaCompliance
            ];
            return $this->handleExport($request->input('export'), $from, $to, $stats);
        }

        return view('reports.index', compact(
            'totalTickets', 'totalTasks', 'avgResolution', 'slaCompliance', 'csat', 'csatCount',
            'labels', 'newTrend', 'closedTrend', 'newTaskTrend', 'doneTaskTrend', 'distribution',
            'isSuperAdmin', 'agentsByRole',
            'range', 'from', 'to'
        ));
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    private function parseRange(string $range, ?string $custom, ?string $fromParam = null, ?string $toParam = null): array
    {
        $to = Carbon::now()->endOfDay();

        // New format: ?range=custom&from=YYYY-MM-DD&to=YYYY-MM-DD
        // Explicit from/to always wins — covers both custom ranges and preset exports
        // so the exported data matches exactly what the user was viewing on screen.
        if ($fromParam && $toParam) {
            $from = Carbon::parse($fromParam)->startOfDay();
            $to   = Carbon::parse($toParam)->endOfDay();
            return [$from, $to];
        }

        // Legacy format: ?range=custom&custom=YYYY-MM-DD,YYYY-MM-DD
        if ($range === 'custom' && $custom) {
            $parts = explode(',', $custom);
            $from  = Carbon::parse($parts[0])->startOfDay();
            $to    = isset($parts[1]) ? Carbon::parse($parts[1])->endOfDay() : $to;
            return [$from, $to];
        }

        $from = match ($range) {
            '24h'  => Carbon::now()->subHours(24),
            '7d'   => Carbon::now()->subDays(6)->startOfDay(),
            '90d'  => Carbon::now()->subDays(90)->startOfDay(),
            default => Carbon::now()->subDays(30)->startOfDay(), // 30d default for reports
        };
        return [$from, $to];
    }

    private function buildChartData(Carbon $from, Carbon $to, string $range): array
    {
        // Ticket inflow
        $inflowRaw = Ticket::select(
                DB::raw('DATE(created_at) as day'),
                DB::raw('COUNT(*) as cnt')
            )
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('day')
            ->pluck('cnt', 'day')
            ->toArray();

        // Tickets resolved/closed
        $resolvedRaw = Ticket::select(
                DB::raw('DATE(updated_at) as day'),
                DB::raw('COUNT(*) as cnt')
            )
            ->whereIn('status', ['resolved', 'closed'])
            ->whereBetween('updated_at', [$from, $to])
            ->groupBy('day')
            ->pluck('cnt', 'day')
            ->toArray();

        // Tasks created
        $taskInflowRaw = $this->companyTask()
            ->select(DB::raw('DATE(created_at) as day'), DB::raw('COUNT(*) as cnt'))
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('day')
            ->pluck('cnt', 'day')
            ->toArray();

        // Tasks done
        $taskDoneRaw = $this->companyTask()
            ->select(DB::raw('DATE(updated_at) as day'), DB::raw('COUNT(*) as cnt'))
            ->where('status', 'done')
            ->whereBetween('updated_at', [$from, $to])
            ->groupBy('day')
            ->pluck('cnt', 'day')
            ->toArray();

        $labels     = [];
        $inflow     = [];
        $resolved   = [];
        $taskInflow = [];
        $taskDone   = [];
        $diff       = $from->diffInDays($to);

        $cursor = $from->copy()->startOfDay();
        while ($cursor->lte($to)) {
            $key   = $cursor->toDateString();
            $label = $diff <= 7
                ? strtoupper($cursor->format('D'))
                : $cursor->format('d M');

            $labels[]     = $label;
            $inflow[]     = $inflowRaw[$key]     ?? 0;
            $resolved[]   = $resolvedRaw[$key]   ?? 0;
            $taskInflow[] = $taskInflowRaw[$key] ?? 0;
            $taskDone[]   = $taskDoneRaw[$key]   ?? 0;

            $cursor->addDay();
        }

        return compact('labels', 'inflow', 'resolved', 'taskInflow', 'taskDone');
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
        $filename = 'fixtora-analytics-' . $from->format('Ymd') . '-' . $to->format('Ymd') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($tickets, $stats, $from, $to) {
            $h = fopen('php://output', 'w');
            fputcsv($h, ['Fixtora – Analytics Report']);
            fputcsv($h, ['Period', $from->format('Y-m-d') . ' to ' . $to->format('Y-m-d')]);
            fputcsv($h, ['Generated', now()->format('Y-m-d H:i:s')]);
            fputcsv($h, []);
            fputcsv($h, ['SUMMARY']);
            fputcsv($h, ['Total Tickets',    $stats['total']]);
            fputcsv($h, ['Avg Resolution (h)', $stats['avgResolution']]);
            fputcsv($h, ['SLA Compliance (%)', $stats['slaCompliance']]);
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
        $t_val=$stats['total']; $ar=$stats['avgResolution']; $sla=$stats['slaCompliance'];

        $html = <<<HTML
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"/>
<title>Fixtora Analytics</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:Arial,sans-serif;font-size:12px;color:#111827;padding:36px 40px}
h1{font-size:22px;font-weight:800;color:#1e3a8a;margin-bottom:4px}
.sub{font-size:11px;color:#6b7280;margin-bottom:24px}
.stats{width:100%;margin-bottom:28px}
.stat{width:31%;float:left;border:1px solid #e5e7ef;border-radius:8px;padding:14px 20px;box-sizing:border-box}
.stat:nth-child(2){margin-left:3.5%;margin-right:3.5%}
.clear{clear:both;margin-bottom:10px}
.stat-label{font-size:9px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:#6b7280;margin-bottom:6px}
.stat-value{font-size:26px;font-weight:800;color:#111827}
table{width:100%;border-collapse:collapse}
th{text-align:left;padding:8px 10px;font-size:9px;font-weight:700;letter-spacing:.6px;text-transform:uppercase;color:#6b7280;border-bottom:2px solid #e5e7ef}
td{padding:9px 10px;border-bottom:1px solid #f3f4f6;font-size:11px;color:#374151}
.footer{margin-top:28px;font-size:10px;color:#9ca3af;text-align:right}
</style></head><body>
<h1>Fixtora – Analytics Report</h1>
<div class="sub">Period: {$period} &nbsp;|&nbsp; Generated: {$genAt}</div>
<div class="stats">
  <div class="stat"><div class="stat-label">Total Tickets</div><div class="stat-value">{$t_val}</div></div>
  <div class="stat"><div class="stat-label">Avg Resolution (h)</div><div class="stat-value">{$ar}h</div></div>
  <div class="stat"><div class="stat-label">SLA Compliance</div><div class="stat-value">{$sla}%</div></div>
</div>
<div class="clear"></div>
<table><thead><tr><th>ID</th><th>Title</th><th>System</th><th>Priority</th><th>Status</th><th>Reporter</th><th>Created</th></tr></thead>
<tbody>{$rows}</tbody></table>
<div class="footer">Fixtora Helpdesk &copy; {$genAt}</div>
</body></html>
HTML;

        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            return \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->download('fixtora-analytics.pdf');
        }
        
        // Fallback if dompdf isn't somehow available
        return response($html)->header('Content-Type', 'text/html; charset=utf-8');
    }
}