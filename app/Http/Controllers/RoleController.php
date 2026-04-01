<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    // List all roles
    public function index()
    {
        $roles = Role::withCount('users')->orderBy('name')->get();
        return view('roles.index', compact('roles'));
    }

    // Show create form
    public function create()
    {
        $permissions = Role::allPermissions();
        return view('roles.create', compact('permissions'));
    }

    // Store new role
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100|unique:roles,name',
            'description' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
        ]);

        Role::create([
            'name'        => $request->name,
            'description' => $request->description,
            'permissions' => $request->permissions ?? [],
        ]);

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    // Show edit form (permissions + user assignment)
    public function edit(Role $role)
    {
        $permissions    = Role::allPermissions();
        $assignedUsers  = $role->users()->orderBy('name')->get();
        $unassignedUsers = User::whereNull('role_id')
                               ->orWhere('role_id', '!=', $role->id)
                               ->orderBy('name')->get();

        return view('roles.edit', compact('role', 'permissions', 'assignedUsers', 'unassignedUsers'));
    }

    // Update role name/description
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name'        => 'required|string|max:100|unique:roles,name,' . $role->id,
            'description' => 'nullable|string|max:255',
        ]);

        $role->update([
            'name'        => $request->name,
            'description' => $request->description,
        ]);

        return back()->with('success', 'Role updated.');
    }

    // Save permissions for a role
    public function savePermissions(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'nullable|array',
        ]);

        $role->update(['permissions' => $request->permissions ?? []]);

        return back()->with('success', 'Permissions saved.');
    }

    // Save user assignments for a role
    public function saveAssociation(Request $request, Role $role)
    {
        $request->validate([
            'assigned_users' => 'nullable|array',
            'assigned_users.*' => 'exists:users,id',
        ]);

        // Remove all users from this role first, then re-assign selected ones
        User::where('role_id', $role->id)->update(['role_id' => null]);

        if ($request->assigned_users) {
            User::whereIn('id', $request->assigned_users)->update(['role_id' => $role->id]);
        }

        return back()->with('success', 'User assignments saved.');
    }

    // Delete a role
    public function destroy(Role $role)
    {
        // Unassign users before deleting
        $role->users()->update(['role_id' => null]);
        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role deleted.');
    }
}