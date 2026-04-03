<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Integration;
use Illuminate\Support\Facades\Auth;

class IntegrationController extends Controller
{
    public function index(Request $request)
    {
        // Only allow non-developers to view integrations
        abort_if(Auth::user()->isDeveloper(), 403, 'You do not have permission to view integrations.');

        $activeTab = $request->input('tab', 'all');
        $filter = $request->input('filter', '');

        // Fetch catalog from DB
        $query = Integration::query();

        if ($activeTab !== 'all') {
            $query->where('category', $activeTab);
        }

        if ($filter) {
            $query->where('name', 'like', "%{$filter}%");
        }

        $catalog = $query->get()->toArray();

        // Fetch active connections for current company
        $company = Auth::user()->company;
        $active_connections = [];
        $connectedNames = [];

        if ($company) {
            $activeIntegrations = $company->integrations()->get();
            $connectedNames = $activeIntegrations->pluck('name')->toArray();
            
            // Map models to expected array shape for view
            $active_connections = $activeIntegrations->map(function ($integration) {
                return [
                    'id' => $integration->id,
                    'name' => $integration->name,
                    'category' => $integration->category,
                    'color' => $integration->color,
                    'desc' => $integration->desc,
                    'status' => $integration->pivot->status,
                ];
            })->toArray();
            
            // For Demo, since there's no UI for actually "Connecting" in Phase 1 except this Ecosystem View, 
            // if we have no active connections we can simulate having 1 or 2. 
            // BUT user requested REAL data, so we won't mock active connections if they have none. 
            // Empty active connections is the correct reality for a new company.
        }

        $tabs = [
            'all' => 'All Tools',
            'communication' => 'Communication',
            'developer' => 'Developer Tools',
            'analytics' => 'Analytics',
        ];

        // Pass variables to view
        $filtered = $catalog; // Already filtered array

        return view('integrations.index', compact('activeTab', 'filter', 'tabs', 'catalog', 'filtered', 'active_connections', 'connectedNames'));
    }

    public function connect(Request $request, Integration $integration)
    {
        $user = Auth::user();
        $company = $user->company;
        abort_unless($user->isAdmin() || $user->isSuperAdmin(), 403, 'Only Administrative users can manage integrations.');
        abort_if(!$company, 403, 'You must belong to a company to connect tools.');

        $company->integrations()->syncWithoutDetaching([
            $integration->id => [
                'status' => 'connected',
                'credentials' => null,
            ]
        ]);

        return back()->with('success', "{$integration->name} has been connected successfully.");
    }

    public function configure(Request $request, Integration $integration)
    {
        $user = Auth::user();
        $company = $user->company;
        abort_unless($user->isAdmin() || $user->isSuperAdmin(), 403, 'Only Administrative users can configure integrations.');
        abort_if(!$company, 403, 'Unauthorized.');

        $connection = $company->integrations()->where('integration_id', $integration->id)->first();
        abort_if(!$connection, 404, 'Connection not found.');

        // Decode credentials if they exist
        $credentials = $connection->pivot->credentials ?? [];

        return view('integrations.configure', compact('integration', 'connection', 'credentials'));
    }

    public function saveConfig(Request $request, Integration $integration)
    {
        $user = Auth::user();
        $company = $user->company;
        abort_unless($user->isAdmin() || $user->isSuperAdmin(), 403, 'Only Administrative users can save configurations.');
        abort_if(!$company, 403, 'Unauthorized.');

        $validated = $request->validate([
            'api_key' => 'nullable|string|max:255',
            'webhook_url' => 'nullable|url|max:255',
            'sync_tickets' => 'nullable|boolean',
            'send_notifications' => 'nullable|boolean',
        ]);

        $company->integrations()->updateExistingPivot($integration->id, [
            'credentials' => $validated,
        ]);

        return back()->with('success', "Configuration for {$integration->name} saved.");
    }

    public function disconnect(Request $request, Integration $integration)
    {
        $user = Auth::user();
        $company = $user->company;
        abort_unless($user->isAdmin() || $user->isSuperAdmin(), 403, 'Only Administrative users can disconnect integrations.');
        abort_if(!$company, 403, 'Unauthorized.');

        $company->integrations()->detach($integration->id);

        return redirect()->route('integrations.index')->with('success', "{$integration->name} has been safely disconnected.");
    }
}
