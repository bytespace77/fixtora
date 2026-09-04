<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Company;
use App\Services\TicketComplianceService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
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
    private function companyTask(?int $companyId = null): \Illuminate\Database\Eloquent\Builder
    {
        $user = auth()->user();
        $query = Task::withoutGlobalScopes();

        if ($user->hasGlobalDataAccess()) {
            return $query->when($companyId, fn ($builder) => $builder->where('company_id', $companyId));
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

        $isSuperAdmin = auth()->user()->hasGlobalDataAccess();
        $companyFilter = $isSuperAdmin ? $request->integer('company_id') : null;
        $complianceFilter = trim((string) $request->input('compliance_status', ''));
        if (!in_array($complianceFilter, ['compliant', 'breached', 'pending', 'not_applicable'], true)) {
            $complianceFilter = '';
        }
        $statusFilter = !$isSuperAdmin ? trim((string) $request->input('status', '')) : '';
        $allowedStatuses = ['open', 'in_progress', 'in_review', 'resolved', 'closed', 'pending_user_response', 'escalated'];
        if (!in_array($statusFilter, $allowedStatuses, true)) {
            $statusFilter = '';
        }

        $tableFrom = $request->filled('table_from') ? Carbon::parse($request->input('table_from'))->startOfDay() : $from->copy();
        $tableTo = $request->filled('table_to') ? Carbon::parse($request->input('table_to'))->endOfDay() : $to->copy();
        if ($tableFrom->gt($tableTo)) {
            [$tableFrom, $tableTo] = [$tableTo->copy()->startOfDay(), $tableFrom->copy()->endOfDay()];
        }

        $tableQuery = Ticket::with(['user', 'company', 'assignedDeveloper', 'comments'])
            ->whereBetween('created_at', [$tableFrom, $tableTo]);
        if ($companyFilter) {
            $tableQuery->where('company_id', $companyFilter);
        }
        if ($statusFilter !== '') {
            $tableQuery->where('status', $statusFilter);
        }

        $totalTickets = Ticket::when($companyFilter, fn ($query) => $query->where('company_id', $companyFilter))
            ->whereBetween('created_at', [$from, $to])->count();

        // Total Tasks in period
        $totalTasks = $this->companyTask($companyFilter)
            ->whereBetween('created_at', [$from, $to])
            ->count();

        // Avg Resolution Time — combined tickets (resolved/closed) + tasks (done)
        $resolvedTickets = Ticket::when($companyFilter, fn ($query) => $query->where('company_id', $companyFilter))
            ->whereIn('status', ['resolved', 'closed'])
            ->whereBetween('updated_at', [$from, $to])->get();
        $ticketsResolved = $resolvedTickets->where('status', 'resolved')->count();

        $doneTasks = $this->companyTask($companyFilter)
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
        $slaTotalTasks = $this->companyTask($companyFilter)
            ->whereNotNull('due_date')
            ->whereBetween('created_at', [$from, $to])
            ->count();

        if ($slaTotalTasks > 0) {
            $slaOnTime = $this->companyTask($companyFilter)
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

        // Ticket SLA compliance uses the assignment-time SLA snapshot. Active
        // tickets are counted as breached only after their SLA deadline passes.
        $complianceService = app(TicketComplianceService::class);
        $complianceTickets = Ticket::when($companyFilter, fn ($query) => $query->where('company_id', $companyFilter))
            ->whereBetween('created_at', [$from, $to])->get();
        $complianceFollowed = $complianceTickets
            ->filter(fn (Ticket $ticket) => $complianceService->status($ticket) === 'compliant')
            ->count();
        $complianceNotFollowed = $complianceTickets
            ->filter(fn (Ticket $ticket) => $complianceService->status($ticket) === 'breached')
            ->count();

        // Real CSAT — average of submitted ratings within the period
        $csatData = Ticket::when($companyFilter, fn ($query) => $query->where('company_id', $companyFilter))
            ->whereNotNull('csat_rating')
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
        $chartData   = $this->buildChartData($from, $to, $range, $companyFilter);
        $labels      = $chartData['labels'];
        $newTrend    = $chartData['inflow'];
        $closedTrend = $chartData['resolved'];
        $newTaskTrend  = $chartData['taskInflow'];
        $doneTaskTrend = $chartData['taskDone'];

        // Ticket Status Overview. Internal review is grouped into In Progress,
        // while Closed is grouped into Resolved as requested by the five-part chart.
        $distributionRaw = Ticket::when($companyFilter, fn ($query) => $query->where('company_id', $companyFilter))
            ->select('status', DB::raw('count(*) as count'))
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $openCount       = (int) ($distributionRaw['open'] ?? 0);
        $inProgressCount = (int) ($distributionRaw['in_progress'] ?? 0) + (int) ($distributionRaw['in_review'] ?? 0);
        $resolvedCount   = (int) ($distributionRaw['resolved'] ?? 0) + (int) ($distributionRaw['closed'] ?? 0);
        $pendingUserResponseCount = (int) ($distributionRaw['pending_user_response'] ?? 0);
        $escalatedCount = (int) ($distributionRaw['escalated'] ?? 0);

        $distribution = [$openCount, $inProgressCount, $resolvedCount, $pendingUserResponseCount, $escalatedCount];

        // Team Performance — superadmin only, grouped by role
        $agentsByRole = [];

        if ($isSuperAdmin) {
            // Helper: real stats for an array of user IDs, sourced from tasks
            $buildStats = function (array $userIds) use ($from, $to, $companyFilter): array {
                if (empty($userIds)) {
                    return ['resolved' => 0, 'avg_response' => '—', 'load' => 0, 'pending_tickets' => 0];
                }

                // Resolved: tasks assigned to these users with status 'done' within the date range
                // withoutGlobalScopes() prevents the Task scope from re-filtering by Auth::user()
                $doneTasks = Task::withoutGlobalScopes()
                    ->whereIn('assigned_to', $userIds)
                    ->when($companyFilter, fn ($query) => $query->where('company_id', $companyFilter))
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
                    ->when($companyFilter, fn ($query) => $query->where('company_id', $companyFilter))
                    ->whereIn('status', ['todo', 'doing'])
                    ->count();
                $loadPct = min(100, $openCount * 5);

                // Pending tickets: tickets assigned to these users not yet resolved/closed
                $pendingTickets = Ticket::withoutGlobalScope('company')
                    ->whereIn('assigned_developer_id', $userIds)
                    ->when($companyFilter, fn ($query) => $query->where('company_id', $companyFilter))
                    ->whereNotIn('status', ['resolved', 'closed'])
                    ->count();

                return ['resolved' => $resolvedCount, 'avg_response' => $avgResponse, 'load' => $loadPct, 'pending_tickets' => $pendingTickets];
            };

            $roleConfig = [
                'superadmin'        => ['label' => 'Super Admin', 'color' => '#1e3a6e'],
                'developer'         => ['label' => 'Developer',   'color' => '#2a7a5e'],
                'admin'             => ['label' => 'Admin',        'color' => '#5a3e8a'],
                'qc'                => ['label' => 'QC',           'color' => '#b45309'],
                'qa / qc'           => ['label' => 'QC',           'color' => '#b45309'],
                'qa/qc'             => ['label' => 'QC',           'color' => '#b45309'],
                'qa'                => ['label' => 'QC',           'color' => '#b45309'],
                'quality control'   => ['label' => 'QC',           'color' => '#b45309'],
                'quality assurance' => ['label' => 'QC',           'color' => '#b45309'],
                'tester'            => ['label' => 'QC',           'color' => '#b45309'],
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
                    $q->whereIn('role', ['developer', 'admin', 'qc', 'qa', 'tester'])
                      ->orWhereHas('userRole', function ($r) {
                          $r->where(function ($rq) {
                              $rq->whereRaw('LOWER(name) IN (?,?,?)', ['developer', 'admin', 'tester'])
                                 ->orWhereRaw('LOWER(name) LIKE ?', ['%qc%'])
                                 ->orWhereRaw('LOWER(name) LIKE ?', ['%qa%'])
                                 ->orWhereRaw('LOWER(name) LIKE ?', ['%quality%']);
                          });
                      });
                })
                ->with('userRole')
                ->orderBy('name')
                ->get();

            foreach ($staffUsers as $u) {
                // Prefer the linked custom role name; fall back to the role string column
                $rawRole   = strtolower(trim((string) (optional($u->userRole)->name ?: $u->role)));
                $cfg       = $roleConfig[$rawRole] ?? ['label' => ucfirst($rawRole), 'color' => '#3b6ea8'];
                $roleLabel = $cfg['label'];

                // QC stats: pending = tickets in_review, resolved = tickets they moved to resolved
                if ($roleLabel === 'QC') {
                    $pendingTickets = Ticket::withoutGlobalScope('company')
                        ->where('status', 'in_review')
                        ->when($companyFilter, fn ($query) => $query->where('company_id', $companyFilter))
                        ->whereBetween('updated_at', [$from, $to])
                        ->count();

                    $resolvedByQc = \App\Models\TicketComment::where('user_id', $u->id)
                        ->where('type', 'status_change')
                        ->when($companyFilter, fn ($query) => $query->whereHas('ticket', fn ($ticketQuery) => $ticketQuery->withoutGlobalScope('company')->where('company_id', $companyFilter)))
                        ->whereRaw("LOWER(body) LIKE ?", ['%resolved%'])
                        ->whereBetween('created_at', [$from, $to])
                        ->count();

                    $avgResponse = '—';
                    $loadPct = min(100, $pendingTickets * 10);

                    $agentsByRole[$roleLabel][] = [
                        'name'            => $u->name,
                        'initials'        => strtoupper(substr($u->name, 0, 2)),
                        'color'           => $cfg['color'],
                        'avatar_url'      => $u->avatar_url,
                        'resolved'        => $resolvedByQc,
                        'avg_response'    => $avgResponse,
                        'load'            => $loadPct,
                        'pending_tickets' => $pendingTickets,
                    ];
                } else {
                    $s = $buildStats([$u->id]);
                    $agentsByRole[$roleLabel][] = [
                        'name'            => $u->name,
                        'initials'        => strtoupper(substr($u->name, 0, 2)),
                        'color'           => $cfg['color'],
                        'avatar_url'      => $u->avatar_url,
                        'resolved'        => $s['resolved'],
                        'avg_response'    => $s['avg_response'],
                        'load'            => $s['load'],
                        'pending_tickets' => $s['pending_tickets'],
                    ];
                }
            }

            // Ensure consistent role order: Developer → QC → Admin → Super Admin
            $roleOrder = ['Developer', 'QC', 'Admin', 'Super Admin'];
            uksort($agentsByRole, function ($a, $b) use ($roleOrder) {
                $posA = array_search($a, $roleOrder);
                $posB = array_search($b, $roleOrder);
                $posA = $posA === false ? 99 : $posA;
                $posB = $posB === false ? 99 : $posB;
                return $posA <=> $posB;
            });
        }

        // Compliance tables. The same scoped and filtered query is reused by
        // both the on-screen rows and export, preventing cross-company drift.
        $filteredTickets = (clone $tableQuery)->latest('updated_at')->get();
        if ($complianceFilter !== '') {
            $filteredTickets = $filteredTickets
                ->filter(fn (Ticket $ticket) => $complianceService->status($ticket) === $complianceFilter)
                ->values();
        }
        $companies = $isSuperAdmin ? Company::orderBy('name')->get(['id', 'name']) : collect();

        $complianceSummary = collect();
        if ($isSuperAdmin) {
            $complianceSummary = $filteredTickets->groupBy(fn (Ticket $ticket) => $ticket->company_id ?: 0)
                ->map(function ($tickets) use ($complianceService) {
                    $resolved = $tickets->whereIn('status', ['resolved', 'closed']);
                    $minutes = $resolved->map(fn (Ticket $ticket) => $complianceService->resolutionMinutes($ticket))->filter(fn ($value) => $value !== null);
                    return [
                        'company' => optional($tickets->first()->company)->name ?? 'No Company',
                        'total' => $tickets->count(),
                        'closed' => $tickets->where('status', 'closed')->count(),
                        'pending' => $tickets->whereNotIn('status', ['resolved', 'closed'])->count(),
                        'resolved' => $resolved->count(),
                        'avg_resolution_minutes' => $minutes->isEmpty() ? null : (int) round($minutes->avg()),
                        'first_response_minutes' => $this->averageFirstResponseMinutes($tickets),
                        'compliant' => $tickets->filter(fn (Ticket $ticket) => $complianceService->status($ticket) === 'compliant')->count(),
                        'breached' => $tickets->filter(fn (Ticket $ticket) => $complianceService->status($ticket) === 'breached')->count(),
                        'penalty' => $tickets->sum(fn (Ticket $ticket) => $complianceService->penalty($ticket)),
                    ];
                })->sortBy('company')->values();
        }

        $page = LengthAwarePaginator::resolveCurrentPage('compliance_page');
        $complianceTickets = new LengthAwarePaginator(
            $filteredTickets->forPage($page, 7)->values(),
            $filteredTickets->count(),
            7,
            $page,
            ['path' => $request->url(), 'query' => $request->query(), 'pageName' => 'compliance_page']
        );
        $complianceTickets->fragment('ticket-status-breakdown');
        $complianceTickets->getCollection()->each(function (Ticket $ticket) use ($complianceService) {
            $ticket->setAttribute('report_compliance', $complianceService->status($ticket));
            $ticket->setAttribute('report_first_response_minutes', $this->firstResponseMinutes($ticket));
            $ticket->setAttribute('report_resolution_minutes', $complianceService->resolutionMinutes($ticket));
            $ticket->setAttribute('report_penalty_points', $complianceService->penalty($ticket));
        });

        // ── Export ────────────────────────────────────────────────────────
        if ($request->has('export')) {
            abort_unless(auth()->user()->hasPermission('export_reports'), 403, 'You do not have permission to export reports.');
            $exportResolutionMinutes = $filteredTickets
                ->map(fn (Ticket $ticket) => $complianceService->resolutionMinutes($ticket))
                ->filter(fn ($minutes) => $minutes !== null);
            $stats = [
                'total' => $filteredTickets->count(),
                'avgResolution' => $exportResolutionMinutes->isEmpty()
                    ? 0
                    : round($exportResolutionMinutes->avg() / 60),
                'slaCompliance' => $slaCompliance,
                'complianceFollowed' => $filteredTickets
                    ->filter(fn (Ticket $ticket) => $complianceService->status($ticket) === 'compliant')->count(),
                'complianceNotFollowed' => $filteredTickets
                    ->filter(fn (Ticket $ticket) => $complianceService->status($ticket) === 'breached')->count(),
            ];
            return $this->handleExport($request->input('export'), $from, $to, $stats, $filteredTickets);
        }

        return view('reports.index', compact(
            'totalTickets', 'totalTasks', 'ticketsResolved', 'avgResolution', 'slaCompliance', 'csat', 'csatCount',
            'complianceFollowed', 'complianceNotFollowed',
            'labels', 'newTrend', 'closedTrend', 'newTaskTrend', 'doneTaskTrend', 'distribution',
            'isSuperAdmin', 'agentsByRole',
            'companies', 'companyFilter', 'complianceFilter', 'statusFilter', 'tableFrom', 'tableTo',
            'complianceSummary', 'complianceTickets',
            'range', 'from', 'to'
        ));
    }

    public function updateCompliance(Request $request, Ticket $ticket)
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403, 'Only Superadmin can edit compliance results.');

        $validated = $request->validate([
            'compliance_status' => 'required|in:compliant,breached,pending,not_applicable',
            'penalty_points' => 'required|integer|min:0|max:999999',
        ]);

        $ticket->forceFill($validated + ['compliance_manually_overridden' => true])->save();

        return back()->with('success', 'Compliance and penalty points updated for ticket #'.str_pad((string) $ticket->id, 4, '0', STR_PAD_LEFT).'.');
    }

    private function averageFirstResponseMinutes($tickets): ?int
    {
        $responseTimes = $tickets->map(fn (Ticket $ticket) => $this->firstResponseMinutes($ticket))
            ->filter(fn ($minutes) => $minutes !== null);

        return $responseTimes->isEmpty() ? null : (int) round($responseTimes->avg());
    }

    private function firstResponseMinutes(Ticket $ticket): ?int
    {
        $firstResponse = $ticket->comments
            ->where('user_id', '!=', $ticket->user_id)
            ->where('role', '!=', 'system')
            ->sortBy('created_at')
            ->first();

        return $firstResponse
            ? max(0, $ticket->created_at->diffInMinutes($firstResponse->created_at))
            : null;
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

    private function buildChartData(Carbon $from, Carbon $to, string $range, ?int $companyId = null): array
    {
        // Ticket inflow
        $inflowRaw = Ticket::when($companyId, fn ($query) => $query->where('company_id', $companyId))
            ->select(
                DB::raw('DATE(created_at) as day'),
                DB::raw('COUNT(*) as cnt')
            )
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('day')
            ->pluck('cnt', 'day')
            ->toArray();

        // Tickets resolved/closed
        $resolvedRaw = Ticket::when($companyId, fn ($query) => $query->where('company_id', $companyId))
            ->select(
                DB::raw('DATE(updated_at) as day'),
                DB::raw('COUNT(*) as cnt')
            )
            ->whereIn('status', ['resolved', 'closed'])
            ->whereBetween('updated_at', [$from, $to])
            ->groupBy('day')
            ->pluck('cnt', 'day')
            ->toArray();

        // Tasks created
        $taskInflowRaw = $this->companyTask($companyId)
            ->select(DB::raw('DATE(created_at) as day'), DB::raw('COUNT(*) as cnt'))
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('day')
            ->pluck('cnt', 'day')
            ->toArray();

        // Tasks done
        $taskDoneRaw = $this->companyTask($companyId)
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

    private function handleExport(string $type, Carbon $from, Carbon $to, array $stats, $tickets)
    {
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
            $compliance = app(TicketComplianceService::class);
            $showCompany = auth()->user()->isSuperAdmin();
            $h = fopen('php://output', 'w');
            fputcsv($h, ['Fixtora – Analytics Report']);
            fputcsv($h, ['Period', $from->format('Y-m-d') . ' to ' . $to->format('Y-m-d')]);
            fputcsv($h, ['Generated', now()->format('Y-m-d H:i:s')]);
            fputcsv($h, []);
            fputcsv($h, ['SUMMARY']);
            fputcsv($h, ['Total Tickets',    $stats['total']]);
            fputcsv($h, ['Pending Tickets', $tickets->whereNotIn('status', ['resolved', 'closed'])->count()]);
            fputcsv($h, ['Resolved Tickets', $tickets->whereIn('status', ['resolved', 'closed'])->count()]);
            fputcsv($h, ['Avg Resolution (h)', $stats['avgResolution']]);
            fputcsv($h, ['Compliance Followed', $stats['complianceFollowed']]);
            fputcsv($h, ['Compliance Not Followed', $stats['complianceNotFollowed']]);
            fputcsv($h, ['Total Penalty Points', $tickets->sum(fn (Ticket $ticket) => $compliance->penalty($ticket))]);
            fputcsv($h, []);
            fputcsv($h, ['BREAKDOWN']);
            $columns = ['Ticket ID', 'Title', 'Name'];
            if ($showCompany) $columns[] = 'Company';
            $columns = array_merge($columns, ['Status', 'Start Date', 'End Date', 'Response Time', 'Resolution Time', 'Compliance', 'Penalty Points']);
            fputcsv($h, $columns);
            foreach ($tickets as $t) {
                $minutes = $compliance->resolutionMinutes($t);
                $duration = $minutes === null ? 'Pending' : intdiv($minutes, 60).'h '.($minutes % 60).'m';
                $responseMinutes = $this->firstResponseMinutes($t);
                $responseDuration = $responseMinutes === null ? 'No response' : intdiv($responseMinutes, 60).'h '.($responseMinutes % 60).'m';
                $status = $compliance->status($t);
                $row = [
                    '#'.str_pad((string) $t->id, 4, '0', STR_PAD_LEFT),
                    $t->title,
                    optional($t->user)->name ?? '—',
                ];
                if ($showCompany) $row[] = optional($t->company)->name ?? '—';
                $row = array_merge($row, [
                    ucfirst(str_replace('_',' ',$t->status)),
                    optional($t->assigned_date)->format('Y-m-d H:i') ?? '—',
                    optional($t->resolved_at ?: $t->actual_delivery_date)->format('Y-m-d H:i') ?? '—',
                    $responseDuration,
                    $duration,
                    ucfirst(str_replace('_', ' ', $status)),
                    $compliance->penalty($t),
                ]);
                fputcsv($h, $row);
            }
            fclose($h);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportPdf($tickets, array $stats, Carbon $from, Carbon $to)
    {
        $compliance = app(TicketComplianceService::class);
        $showCompany = auth()->user()->isSuperAdmin();
        $rows = '';
        foreach ($tickets as $t) {
            $minutes = $compliance->resolutionMinutes($t);
            $duration = $minutes === null ? 'Pending' : intdiv($minutes, 60).'h '.($minutes % 60).'m';
            $responseMinutes = $this->firstResponseMinutes($t);
            $responseDuration = $responseMinutes === null ? 'No response' : intdiv($responseMinutes, 60).'h '.($responseMinutes % 60).'m';
            $complianceStatus = $compliance->status($t);
            $statusClass = match ($complianceStatus) {
                'compliant' => 'ok', 'breached' => 'bad', 'pending' => 'wait', default => 'na',
            };
            $companyCell = $showCompany ? '<td>'.e(optional($t->company)->name ?? '-').'</td>' : '';
            $rows .= "<tr>
                <td>#".str_pad((string) $t->id, 4, '0', STR_PAD_LEFT)."</td>
                <td>".e($t->title)."</td>
                <td>".e(optional($t->user)->name ?? '-')."</td>
                {$companyCell}
                <td>".e(ucfirst(str_replace('_', ' ', $t->status)))."</td>
                <td>".e(optional($t->assigned_date)->format('Y-m-d H:i') ?? '-')."</td>
                <td>".e(optional($t->resolved_at ?: $t->actual_delivery_date)->format('Y-m-d H:i') ?? '-')."</td>
                <td>".e($responseDuration)."</td>
                <td>".e($duration)."</td>
                <td><span class='badge {$statusClass}'>".e(ucfirst(str_replace('_', ' ', $complianceStatus)))."</span></td>
                <td class='points'>".$compliance->penalty($t)."</td>
            </tr>";
        }

        if ($rows === '') {
            $rows = '<tr><td colspan="'.($showCompany ? 11 : 10).'" class="empty">No tickets match the selected filters.</td></tr>';
        }

        $pending = $tickets->whereNotIn('status', ['resolved', 'closed'])->count();
        $closed = $tickets->where('status', 'closed')->count();
        $penalties = $tickets->sum(fn (Ticket $ticket) => $compliance->penalty($ticket));
        $period  = $from->format('Y-m-d').' to '.$to->format('Y-m-d');
        $genAt   = now()->format('Y-m-d H:i:s');
        $total = $stats['total'];
        $followed = $stats['complianceFollowed'];
        $notFollowed = $stats['complianceNotFollowed'];
        $companyHeader = $showCompany ? '<th>Company</th>' : '';
        $tableClass = $showCompany ? 'with-company' : 'without-company';
        $statsClass = $showCompany ? 'stats' : 'stats six';
        $closedCard = $showCompany ? '' : '<td><div class="stat-label">Closed Tickets</div><div class="stat-value">'.$closed.'</div></td>';

        $html = <<<HTML
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"/>
<title>Fixtora Analytics</title>
<style>
@page{margin:25px 28px 30px}
*{box-sizing:border-box}
body{font-family:DejaVu Sans,Arial,sans-serif;font-size:9px;color:#1f2937;margin:0}
h1{font-size:20px;color:#102a56;margin:0 0 3px}
.sub{font-size:9px;color:#6b7280;margin-bottom:15px}
.section-title{font-size:12px;font-weight:700;color:#102a56;margin:15px 0 7px}
.stats{width:100%;border-collapse:separate;border-spacing:6px;margin-left:-6px}
.stats td{width:20%;border:1px solid #dbe3ef;border-radius:6px;padding:9px;background:#f8fafc;vertical-align:top}
.stats.six td{width:16.66%;padding:8px 7px}
.stat-label{font-size:6.8px;font-weight:700;text-transform:uppercase;color:#64748b;margin-bottom:4px}
.stat-value{font-size:16px;font-weight:800;color:#102a56}
.report{width:100%;table-layout:fixed;border-collapse:collapse}
.report thead{display:table-header-group}
.report th{background:#102a56;color:#fff;padding:6px 4px;font-size:6.5px;text-align:left;text-transform:uppercase}
.report td{padding:6px 3px;border-bottom:1px solid #e5e7eb;font-size:6px;vertical-align:top;word-wrap:break-word}
.report tbody tr:nth-child(even){background:#f8fafc}
.with-company th:nth-child(1){width:6%}.with-company th:nth-child(2){width:17%}.with-company th:nth-child(3){width:7%}.with-company th:nth-child(4){width:9%}.with-company th:nth-child(5){width:8%}.with-company th:nth-child(6){width:11%}.with-company th:nth-child(7){width:11%}.with-company th:nth-child(8){width:7%}.with-company th:nth-child(9){width:7%}.with-company th:nth-child(10){width:10%}.with-company th:nth-child(11){width:7%}
.without-company th:nth-child(1){width:7%}.without-company th:nth-child(2){width:20%}.without-company th:nth-child(3){width:8%}.without-company th:nth-child(4){width:9%}.without-company th:nth-child(5){width:13%}.without-company th:nth-child(6){width:13%}.without-company th:nth-child(7){width:8%}.without-company th:nth-child(8){width:8%}.without-company th:nth-child(9){width:9%}.without-company th:nth-child(10){width:5%}
.badge{display:inline-block;padding:2px 4px;border-radius:8px;font-size:6px;font-weight:700;text-transform:uppercase}
.ok{background:#dcfce7;color:#047857}.bad{background:#fee2e2;color:#b91c1c}.wait{background:#fef3c7;color:#b45309}.na{background:#e2e8f0;color:#475569}
.points{font-weight:700}.empty{text-align:center;color:#64748b;padding:18px!important}
.footer{position:fixed;bottom:-18px;left:0;right:0;color:#94a3b8;font-size:7px;text-align:right}
.page:after{content:counter(page)}
</style></head><body>
<h1>Fixtora Analytics Report</h1>
<div class="sub">Period: {$period} &nbsp; | &nbsp; Generated: {$genAt}</div>
<div class="section-title">Summary</div>
<table class="{$statsClass}"><tr>
  <td><div class="stat-label">Total Tickets</div><div class="stat-value">{$total}</div></td>
  <td><div class="stat-label">Pending Tickets</div><div class="stat-value">{$pending}</div></td>
  {$closedCard}
  <td><div class="stat-label">Compliance Followed</div><div class="stat-value">{$followed}</div></td>
  <td><div class="stat-label">Compliance Not Followed</div><div class="stat-value">{$notFollowed}</div></td>
  <td><div class="stat-label">Penalty Points</div><div class="stat-value">{$penalties}</div></td>
</tr></table>
<div class="section-title">Ticket Breakdown</div>
<table class="report {$tableClass}"><thead><tr><th>Ticket ID</th><th>Title</th><th>User</th>{$companyHeader}<th>Status</th><th>Start Date</th><th>End Date</th><th>Response Time</th><th>Resolution Time</th><th>Compliance</th><th>Points</th></tr></thead>
<tbody>{$rows}</tbody></table>
<div class="footer">Fixtora Helpdesk &nbsp; | &nbsp; Page <span class="page"></span></div>
</body></html>
HTML;

        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $filename = 'fixtora-analytics-'.$from->format('Ymd').'-'.$to->format('Ymd').'.pdf';
            return \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('a4', 'portrait')->download($filename);
        }
        
        // Fallback if dompdf isn't somehow available
        return response($html)->header('Content-Type', 'text/html; charset=utf-8');
    }
}
