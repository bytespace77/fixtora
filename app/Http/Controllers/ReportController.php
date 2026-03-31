<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $totalTickets = Ticket::count();

        // Avg Resolution Time in Hours
        $resolvedTickets = Ticket::whereIn('status', ['resolved', 'closed'])->get();
        if ($resolvedTickets->count() > 0) {
            $totalHours = $resolvedTickets->sum(function($t) {
                return $t->created_at->diffInHours($t->updated_at);
            });
            $avgResolution = round($totalHours / $resolvedTickets->count());
        } else {
            $avgResolution = 0;
        }

        // SLA Compliance Check
        $slaBoundTickets = Ticket::whereIn('status', ['resolved', 'closed'])->whereNotNull('due_date')->get();
        if ($slaBoundTickets->count() > 0) {
            $compliant = $slaBoundTickets->filter(function($t) {
                return $t->updated_at <= Carbon::parse($t->due_date)->endOfDay();
            })->count();
            $slaCompliance = round(($compliant / $slaBoundTickets->count()) * 100);
        } else {
            $slaCompliance = 100;
        }

        $csat = '4.8/5';

        // Trend Data (Last 7 Days)
        $labels = [];
        $newTrend = [];
        $closedTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('M d');
            $newTrend[] = Ticket::whereDate('created_at', $date->toDateString())->count();
            $closedTrend[] = Ticket::whereIn('status', ['resolved', 'closed'])
                ->whereDate('updated_at', $date->toDateString())
                ->count();
        }

        // Issue Distribution Map
        $distributionRaw = Ticket::select('system', DB::raw('count(*) as count'))
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
        $agents = $users->map(function($u) {
            $resolvedCount = Ticket::where('user_id', $u->id)->whereIn('status', ['resolved', 'closed'])->count();
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

        return view('reports.index', compact(
            'totalTickets', 'avgResolution', 'slaCompliance', 'csat',
            'labels', 'newTrend', 'closedTrend', 'distribution', 'agents'
        ));
    }
}
