<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Task 39: After login, check company is active and route to correct workspace.
     * Blocks inactive company users immediately.
     */
    protected function authenticated(Request $request, $user)
    {
        // Block disabled users
        if ($user->is_disabled) {
            $this->guard()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/login')->withErrors([
                'email' => 'Your account has been disabled. Please contact support.',
            ]);
        }

        // Block if company doesn't exist or is inactive
        if (!$user->company || !$user->company->is_active) {
            $this->guard()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/login')->withErrors([
                'email' => 'Your company account is inactive. Please contact support.',
            ]);
        }

        // Task 39: Store company info in session for easy tenant scoping elsewhere
        session([
            'company_id'   => $user->company_id,
            'company_name' => $user->company->name,
            'company_slug' => $user->company->slug,
        ]);

        // Route to the user's company workspace
        $user = auth()->user();
        if ($user->isSuperAdmin() || $user->hasPermission('view_dashboard')) {
            return redirect()->intended('/home');
        }
        // Find first permitted section
        $fallbacks = [
            'list_tickets'    => '/tickets',
            'create_tickets'  => '/tickets',
            'list_tasks'      => '/tasks',
            'view_reports'    => '/reports',
            'view_sla_monitor'=> '/sla-monitor',
            'view_scheduling' => '/scheduling',
            'view_roles'      => '/roles',
            'view_integrations'=> '/integrations/custom-request',
        ];
        foreach ($fallbacks as $permission => $route) {
            if ($user->hasPermission($permission)) {
                return redirect($route);
            }
        }
        return redirect('/profile');
    }

    protected function loggedOut(Request $request)
    {
        return redirect('/login');
    }
}