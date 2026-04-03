<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IntegrationRequest;
use App\Models\Integration;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewIntegrationRequest;
use App\Notifications\IntegrationRequestStatusUpdated;

class IntegrationRequestController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        abort_unless($user->hasPermission('view_integrations'), 403, 'You do not have permission to view integrations.');

        if ($user->isSuperAdmin()) {
            $requests = IntegrationRequest::with('user')->latest()->get();
            $title = 'Manage Custom Requests';
        } else {
            $requests = IntegrationRequest::where('user_id', $user->id)->latest()->get();
            $title = 'My Custom Requests';
        }

        return view('integrations.requests', compact('requests', 'title'));
    }

    public function create()
    {
        $user = auth()->user();
        abort_unless($user->hasPermission('view_integrations'), 403, 'You do not have permission to view integrations.');

        $companies = $user->isSuperAdmin() ? Company::orderBy('name')->get() : collect();

        return view('integrations.custom-request', compact('companies'));
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->hasPermission('submit_custom_request'), 403, 'You do not have permission to submit integration requests.');

        $validated = $request->validate([
            'requested_integration' => ['required', 'string', 'max:255'],
            'contact_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        $isSuperAdmin = auth()->user()->isSuperAdmin();
        $status = $isSuperAdmin ? 'accepted' : 'pending';

        $integrationRequest = IntegrationRequest::create([
            'user_id' => $request->user()?->id,
            'status' => $status,
            ...$validated,
        ]);

        if ($isSuperAdmin) {
            // Auto inject into catalog
            Integration::firstOrCreate(
                ['name' => $integrationRequest->requested_integration],
                [
                    'category' => 'other',
                    'color' => '#475569',
                    'desc' => 'Custom Connector provided for ' . ($integrationRequest->company ?: 'internal workflow requests.'),
                ]
            );

            // Notify specific company target dynamically based on the dropdown selection
            if (!empty($validated['company'])) {
                $targetCompany = Company::where('name', $validated['company'])->first();
                if ($targetCompany) {
                    $companyUsers = User::where('company_id', $targetCompany->id)->get();
                    Notification::send($companyUsers, new IntegrationRequestStatusUpdated($integrationRequest));
                }
            }

            return redirect()
                ->route('integrations.requests.index')
                ->with('success', 'Integration auto-approved and added to catalog.');
        }

        // Notify SuperAdmins manually for standard requests
        $superadmins = User::where('role', 'superadmin')->get();
        Notification::send($superadmins, new NewIntegrationRequest($validated));

        return redirect()
            ->route('integrations.requests.index')
            ->with('success', 'Your custom integration request was submitted. We will contact you soon.');
    }

    public function update(Request $request, IntegrationRequest $integrationRequest)
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403, 'Unauthorized.');

        $validated = $request->validate([
            'status' => ['required', 'in:pending,accepted,rejected'],
        ]);

        $oldStatus = $integrationRequest->status;
        $newStatus = $validated['status'];

        $integrationRequest->update(['status' => $newStatus]);

        // Trigger side-effects if status actually changed
        if ($oldStatus !== $newStatus) {
            // Notify the specific company/user dynamically
            $targetCompany = Company::where('name', $integrationRequest->company)->first();
            if ($targetCompany) {
                $notifiables = User::where('company_id', $targetCompany->id)->get();
                Notification::send($notifiables, new IntegrationRequestStatusUpdated($integrationRequest));
            } else if ($integrationRequest->user) {
                $integrationRequest->user->notify(new IntegrationRequestStatusUpdated($integrationRequest));
            }

            // Auto-inject into the global Catalog if accepted
            if ($newStatus === 'accepted') {
                Integration::firstOrCreate(
                    ['name' => $integrationRequest->requested_integration],
                    [
                        'category' => 'other',
                        'color' => '#475569',
                        'desc' => 'Custom Connector provided for ' . ($integrationRequest->company ?: 'internal workflow requests.'),
                    ]
                );
            }
        }

        return back()->with('success', 'Request status updated successfully.');
    }
}