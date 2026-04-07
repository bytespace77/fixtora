<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the profile page.
     */
    public function show()
    {
        $user          = Auth::user();
        $ticketCount   = \App\Models\Ticket::where('user_id', $user->id)->count();
        $resolvedCount = \App\Models\Ticket::where('user_id', $user->id)->where('status', 'resolved')->count();

        // Real storage: sum of all attachment sizes for this user's company (in bytes)
        $usedBytes = \App\Models\TicketAttachment::whereHas('ticket', function ($q) use ($user) {
            $q->where('company_id', $user->company_id);
        })->sum('size');
        $limitBytes  = 10 * 1024 * 1024 * 1024; // 10 GB
        $usedGB      = round($usedBytes / (1024 * 1024 * 1024), 1);
        $usedPercent = min(100, round(($usedBytes / $limitBytes) * 100));

        // Fetch real active sessions for this user
        $currentSessionId = session()->getId();
        $activeSessions = DB::table('sessions')
            ->where('user_id', $user->id)
            ->orderByDesc('last_activity')
            ->get()
            ->map(function ($s) use ($currentSessionId) {
                $ua = $s->user_agent ?? '';
                $os = str_contains($ua, 'Windows') ? 'Windows'
                    : (str_contains($ua, 'Mac')     ? 'macOS'
                    : (str_contains($ua, 'Linux')   ? 'Linux'
                    : (str_contains($ua, 'Android') ? 'Android'
                    : (str_contains($ua, 'iPhone') || str_contains($ua, 'iPad') ? 'iOS'
                    : 'Unknown OS'))));
                $browser = str_contains($ua, 'Edg')     ? 'Edge Browser'
                         : (str_contains($ua, 'OPR')    ? 'Opera Browser'
                         : (str_contains($ua, 'Chrome') ? 'Chrome Browser'
                         : (str_contains($ua, 'Firefox')? 'Firefox Browser'
                         : (str_contains($ua, 'Safari') ? 'Safari Browser'
                         : 'Unknown Browser'))));
                return (object) [
                    'id'          => $s->id,
                    'ip_address'  => $s->ip_address ?? '—',
                    'os'          => $os,
                    'browser'     => $browser,
                    'last_active' => Carbon::createFromTimestamp($s->last_activity)->diffForHumans(),
                    'is_current'  => (string) $s->id === (string) $currentSessionId,
                ];
            });

        return view('profile.show', compact(
            'user', 'ticketCount', 'resolvedCount',
            'activeSessions', 'usedGB', 'usedPercent'
        ));
    }

    /**
     * Upload a new profile avatar.
     */
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
        ]);

        $user = Auth::user();

        // Delete old avatar if exists
        if ($user->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');

        $user->update(['avatar' => $path]);

        return redirect()->route('profile.show')->with('success', 'Profile picture updated.');
    }

    /**
     * Update the user's name.
     */
    public function update(Request $request)
    {
        $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'regex:/^[0-9]+$/', 'max:50'],
        ]);

        Auth::user()->update([
            'name'  => $request->name,
            'phone' => $request->phone,
        ]);

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully.');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password'      => ['required'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.'])->with('open_tab', 'security');
        }

        $user->update([
            'password'            => Hash::make($request->password),
            'password_changed_at' => now(),
        ]);

        return redirect()->route('profile.show')->with('success', 'Password updated successfully.')->with('open_tab', 'security');
    }

    /**
     * Revoke (logout) a specific session by ID.
     */
    public function destroySession(Request $request)
    {
        $sessionId = $request->input('session_id');
        // Only delete sessions belonging to the authenticated user
        DB::table('sessions')
            ->where('user_id', Auth::id())
            ->where('id', $sessionId)
            ->delete();

        return redirect()->route('profile.show')
            ->with('success', 'Session logged out successfully.')
            ->with('open_tab', 'security');
    }
}