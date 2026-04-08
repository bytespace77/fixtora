<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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

        // Recent CSAT submissions (paginated)
        $recentCsat = Ticket::withoutGlobalScope('company')
            ->with(['user', 'company'])
            ->whereNotNull('csat_rating')
            ->whereNotNull('csat_submitted_at')
            ->orderByDesc('csat_submitted_at')
            ->paginate(10);

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

        // All users for the inline Users tab
        $allUsers = User::with('company')->latest()->get();

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
            'recentCsat',
            'allUsers'
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

    // ── User Management ───────────────────────────────────────────────────

    public function usersIndex(Request $request)
    {
        $query = User::with('company')->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($companyId = $request->input('company_id')) {
            $query->where('company_id', $companyId);
        }

        if ($status = $request->input('status')) {
            $query->where('is_disabled', $status === 'disabled');
        }

        $users     = $query->paginate(20);
        $companies = Company::orderBy('name')->get();
        $roles     = \App\Models\Role::orderBy('name')->get();

        return view('superadmin.users.index', compact('users', 'companies', 'roles'));
    }

    public function usersToggle(User $user)
    {
        abort_if($user->isSuperAdmin(), 403, 'Cannot disable super admin accounts.');
        $user->update(['is_disabled' => !$user->is_disabled]);
        $status = $user->is_disabled ? 'disabled' : 'enabled';
        return back()->with('success', "User \"{$user->name}\" has been {$status}.");
    }

    public function usersResetPassword(User $user)
    {
        $tempPassword = Str::random(10);
        $user->update([
            'password'            => Hash::make($tempPassword),
            'password_changed_at' => null,
        ]);
        return redirect()->route('superadmin.users.index')
                         ->with('temp_password', $tempPassword)
                         ->with('success', "Password reset for \"{$user->name}\".");
    }

    public function usersStore(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'phone'      => 'nullable|regex:/^[0-9]+$/|max:50',
            'password'   => 'required|string|min:8',
            'company_id' => 'nullable|exists:companies,id',
            'role'       => 'required|string|max:100',
            'role_id'    => 'nullable|exists:roles,id',
        ]);

        $data['password'] = \Illuminate\Support\Facades\Hash::make($data['password']);

        \App\Models\User::create($data);

        return redirect()->route('superadmin.users.index')
                         ->with('success', "User \"{$data['name']}\" created successfully.");
    }

    public function usersUpdate(Request $request, \App\Models\User $user)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $user->id,
            'phone'      => 'nullable|regex:/^[0-9]+$/|max:50',
            'company_id' => 'nullable|exists:companies,id',
            'role'       => 'required|string|max:100',
            'role_id'    => 'nullable|exists:roles,id',
        ]);

        $user->update($data);

        return redirect()->route('superadmin.users.index')
                         ->with('success', "User \"{$user->name}\" updated successfully.");
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