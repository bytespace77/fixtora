<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Every method in this controller is superadmin-only
        $this->middleware(function ($request, $next) {
            abort_unless(auth()->user()->isSuperAdmin(), 403, 'Super Admin access required.');
            return $next($request);
        });
    }

    // ── Task 33: Super Admin Dashboard ───────────────────────────────────
    public function dashboard()
    {
        $companies = Company::withCount(['users', 'tickets'])->get();

        $totalCompanies = $companies->count();
        $activeCompanies = $companies->where('is_active', true)->count();
        $inactiveCompanies = $companies->where('is_active', false)->count();
        $totalUsers = User::count();
        $totalTickets = Ticket::withoutGlobalScope('company')->count();

        // ── Global CSAT stats ──────────────────────────────────────────────
        $csatTickets = Ticket::withoutGlobalScope('company')
            ->whereNotNull('csat_rating')
            ->whereNotNull('csat_submitted_at');

        $csatCount       = (clone $csatTickets)->count();
        $csatAvg         = $csatCount > 0 ? round((clone $csatTickets)->avg('csat_rating'), 1) : null;
        $csatDistribution = [
            5 => (clone $csatTickets)->where('csat_rating', 5)->count(),
            4 => (clone $csatTickets)->where('csat_rating', 4)->count(),
            3 => (clone $csatTickets)->where('csat_rating', 3)->count(),
            2 => (clone $csatTickets)->where('csat_rating', 2)->count(),
            1 => (clone $csatTickets)->where('csat_rating', 1)->count(),
        ];

        // Recent CSAT submissions (last 5)
        $recentCsat = Ticket::withoutGlobalScope('company')
            ->with(['user', 'company'])
            ->whereNotNull('csat_rating')
            ->whereNotNull('csat_submitted_at')
            ->orderByDesc('csat_submitted_at')
            ->limit(5)
            ->get();

        // Per-company stats for the table
        $companyStats = $companies->map(function ($company) {
            $openTickets = Ticket::withoutGlobalScope('company')
                ->where('company_id', $company->id)
                ->whereNotIn('status', ['resolved', 'closed'])
                ->count();

            $resolvedTickets = Ticket::withoutGlobalScope('company')
                ->where('company_id', $company->id)
                ->whereIn('status', ['resolved', 'closed'])
                ->count();

            $companyCsatAvg = Ticket::withoutGlobalScope('company')
                ->where('company_id', $company->id)
                ->whereNotNull('csat_rating')
                ->avg('csat_rating');

            $companyCsatCount = Ticket::withoutGlobalScope('company')
                ->where('company_id', $company->id)
                ->whereNotNull('csat_rating')
                ->count();

            return [
                'id'               => $company->id,
                'name'             => $company->name,
                'slug'             => $company->slug,
                'is_active'        => $company->is_active,
                'users_count'      => $company->users_count,
                'tickets_count'    => $company->tickets_count,
                'open_tickets'     => $openTickets,
                'resolved_tickets' => $resolvedTickets,
                'csat_avg'         => $companyCsatAvg ? round($companyCsatAvg, 1) : null,
                'csat_count'       => $companyCsatCount,
            ];
        });

        return view('superadmin.dashboard', compact(
            'companyStats',
            'totalCompanies',
            'activeCompanies',
            'inactiveCompanies',
            'totalUsers',
            'totalTickets',
            'csatAvg',
            'csatCount',
            'csatDistribution',
            'recentCsat'
        ));
    }

    /**
     * Configuration hub: links to company list (system names per company), etc.
     */
    public function configuration()
    {
        $companyCount = Company::count();

        return view('superadmin.configuration', compact('companyCount'));
    }

    // ── Task 34: Company Management ───────────────────────────────────────

    /**
     * @return list<string>
     */
    private function normalizedSystemsFromRequest(Request $request): array
    {
        return collect($request->input('systems', []))
            ->map(fn ($s) => trim((string) $s))
            ->filter(fn ($s) => $s !== '')
            ->unique()
            ->values()
            ->all();
    }

    // List all companies
    public function companiesIndex()
    {
        $companies = Company::withCount(['users', 'tickets'])->latest()->paginate(20);
        return view('superadmin.companies.index', compact('companies'));
    }

    // Show a single company detail
    public function companiesShow(Company $company)
    {
        $company->loadCount(['users', 'tickets']);

        $openTickets = Ticket::withoutGlobalScope('company')
            ->where('company_id', $company->id)
            ->whereNotIn('status', ['resolved', 'closed'])
            ->count();

        $resolvedTickets = Ticket::withoutGlobalScope('company')
            ->where('company_id', $company->id)
            ->whereIn('status', ['resolved', 'closed'])
            ->count();

        $users = User::where('company_id', $company->id)->latest()->get();

        return view('superadmin.companies.show', compact('company', 'openTickets', 'resolvedTickets', 'users'));
    }

    // Show create form
    public function companiesCreate()
    {
        return view('superadmin.companies.create');
    }

    // Store new company
    public function companiesStore(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'slug'      => 'required|string|max:255|unique:companies,slug|alpha_dash',
            'is_active' => 'nullable|boolean',
            'systems'   => 'nullable|array',
            'systems.*' => 'nullable|string|max:255',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $data['systems'] = $this->normalizedSystemsFromRequest($request);

        Company::create($data);

        return redirect()->route('superadmin.companies.index')
            ->with('success', 'Company created successfully.');
    }

    // Show edit form
    public function companiesEdit(Company $company)
    {
        return view('superadmin.companies.edit', compact('company'));
    }

    // Update company
    public function companiesUpdate(Request $request, Company $company)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'slug'      => 'required|string|max:255|unique:companies,slug,' . $company->id . '|alpha_dash',
            'is_active' => 'nullable|boolean',
            'systems'   => 'nullable|array',
            'systems.*' => 'nullable|string|max:255',
        ]);

        $data['is_active'] = $request->boolean('is_active', false);
        $data['systems'] = $this->normalizedSystemsFromRequest($request);

        $company->update($data);

        return redirect()->route('superadmin.companies.index')
            ->with('success', 'Company updated successfully.');
    }

    // Toggle active/inactive
    public function companiesToggle(Company $company)
    {
        $company->update(['is_active' => !$company->is_active]);

        $status = $company->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Company \"{$company->name}\" has been {$status}.");
    }

    // Deactivate (soft delete via is_active flag)
    public function companiesDeactivate(Company $company)
    {
        $company->update(['is_active' => false]);

        return back()->with('success', "Company \"{$company->name}\" has been deactivated.");
    }
}