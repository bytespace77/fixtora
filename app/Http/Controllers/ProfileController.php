<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

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

        return view('profile.show', compact('user', 'ticketCount', 'resolvedCount'));
    }

    /**
     * Update the user's name.
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        Auth::user()->update([
            'name' => $request->name,
        ]);

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully.');
    }
}