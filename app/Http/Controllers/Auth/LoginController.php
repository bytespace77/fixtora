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
        return redirect()->intended('/home');
    }

    protected function loggedOut(Request $request)
    {
        return redirect('/login');
    }
}