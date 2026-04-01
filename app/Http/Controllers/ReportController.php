<?php

namespace App\Http\Controllers;

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

    public function index(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('view_reports'), 403, 'You do not have permission to view reports.');

        $range  = $request->input('range', '30d');
        $custom = $request->input('custom');

        [$from, $to] = $this->parseRange($range, $custom);

        $totalTickets = Ticket::whereBetween('created_at', [$from, $to])->count();

        // Avg Resolution Time in Hours
        $resolvedTickets = Ticket::whereIn('status', ['resolved', 'closed'])
            ->whereBetween('updated_at', [$from, $to])->get();

        if ($resolvedTickets->count() > 0) {
            $totalHours = $resolvedTickets->sum(function($t) {
                return $t->created_at->diffInHours($t->updated_at);
            });
            $avgResolution = round($totalHours / $resolvedTickets->count());
        } else {
            $avgResolution = 0;
        }

        // SLA Compliance Check
        $slaBoundTickets = Ticket::whereIn('status', ['resolved', 'closed'])
            ->whereNotNull('due_date')
            ->whereBetween('updated_at', [$from, $to])->get();

        if ($slaBoundTickets->count() > 0) {
            $compliant = $slaBoundTickets->filter(function($t) {
                return $t->updated_at <= Carbon::parse($t->due_date)->endOfDay();
            })->count();
            $slaCompliance = round(($compliant / $slaBoundTickets->count()) * 100);
        } else {
            $slaCompliance = 100;
        }

        $csat = '4.8/5';

        // Trend Data
        $chartData = $this->buildChartData($from, $to, $range);
        $labels = $chartData['labels'];
        $newTrend = $chartData['inflow'];
        $closedTrend = $chartData['resolved'];

        // Issue Distribution Map
        $distributionRaw = Ticket::select('system', DB::raw('count(*) as count'))
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('system')
            ->pluck('count', 'system')
            ->toArray();
        
        $backend = ($distributionRaw['Payment GW'] ?? 0) + ($distributionRaw['Cloud Infra'] ?? 0);
        $frontend = $distributionRaw['CRM Portal'] ?? 0;
        $api = $distributionRaw['Auth Core'] ?? 0;
        $others = array_sum($distributionRaw) - $backend - $frontend - $api;
        
        $distribution = [$backend + $others, $frontend, $api];

        // Team Performance (Using Users to mock Agents)
        $users = User::take(5)->get();
        $agents = $users->map(function($u) use ($from, $to) {
            $resolvedCount = Ticket::where('user_id', $u->id)
                ->whereIn('status', ['resolved', 'closed'])
                ->whereBetween('updated_at', [$from, $to])
                ->count();
            return [
                'name' => $u->name,
                'role' => 'Support Agent',
                'initials' => strtoupper(substr($u->name, 0, 2)),
                'color' => '#' . str_pad(dechex(crc32($u->name) & 0xFFFFFF), 6, '0', STR_PAD_LEFT),
                'resolved' => $resolvedCount,
                'avg_response' => rand(1, 4) . 'h ' . rand(10, 50) . 'm',
                'load' => rand(10, 70),
                'csat' => '4.' . rand(5, 9),
                'status' => 'online'
            ];
        })->toArray();

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
            'totalTickets', 'avgResolution', 'slaCompliance', 'csat',
            'labels', 'newTrend', 'closedTrend', 'distribution', 'agents',
            'range', 'from', 'to'
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
            '7d'   => Carbon::now()->subDays(6)->startOfDay(),
            '90d'  => Carbon::now()->subDays(90)->startOfDay(),
            default => Carbon::now()->subDays(30)->startOfDay(), // 30d default for reports
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
            ->whereIn('status', ['resolved', 'closed'])
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