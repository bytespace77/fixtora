<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class IntegrationRequestController extends Controller
{
    public function create()
    {
        return view('integrations.custom-request');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'requested_integration' => ['required', 'string', 'max:255'],
            'contact_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        // For now, we only log the request and show a success message.
        // Later we can persist it to a DB table + add admin management.
        Log::info('Custom integration requested', [
            'user_id' => $request->user()?->id,
            ...$validated,
        ]);

        return redirect()
            ->route('settings.index', ['tab' => 'all'])
            ->with('success', 'Your custom integration request was submitted. We will contact you soon.');
    }
}
