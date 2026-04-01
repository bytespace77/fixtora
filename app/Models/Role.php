<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name', 'description', 'permissions'];

    protected $casts = [
        'permissions' => 'array',
    ];

    // All available permissions grouped by category
    // These match the actual routes and features in the system
    public static function allPermissions(): array
    {
        return [
            // ── Dashboard ──────────────────────────────────────────
            'Dashboard' => [
                'view_dashboard',       // View the main dashboard
                'export_dashboard',     // Export dashboard report (PDF/Excel)
            ],

            // ── Tickets ────────────────────────────────────────────
            'Tickets' => [
                'list_tickets',         // View tickets list
                'create_tickets',       // Create a new ticket
                'view_tickets',         // View ticket detail
                'edit_tickets',         // Edit / update ticket status
                'delete_tickets',       // Delete a ticket
                'add_comments',         // Add comments on tickets
                'delete_comments',      // Delete comments on tickets
                'upload_attachments',   // Upload attachments to tickets
                'delete_attachments',   // Delete attachments from tickets
            ],

            // ── Tasks ──────────────────────────────────────────────
            'Tasks' => [
                'list_tasks',           // View tasks board
                'create_tasks',         // Create a new task
                'edit_tasks',           // Edit / move task status
                'delete_tasks',         // Delete a task
            ],

            // ── SLA Monitor ────────────────────────────────────────
            'SLA Monitor' => [
                'view_sla_monitor',     // View SLA monitor page
            ],

            // ── Reports ────────────────────────────────────────────
            'Reports' => [
                'view_reports',         // View reports page
                'view_analytics',       // View analytics charts
                'export_reports',       // Export reports
            ],

            // ── Scheduling ─────────────────────────────────────────
            'Scheduling' => [
                'view_scheduling',      // View scheduling / calendar page
            ],

            // ── User Roles & Permissions ───────────────────────────
            'User Roles' => [
                'view_roles',           // View roles list
                'create_roles',         // Create new roles
                'edit_roles',           // Edit role name/description
                'delete_roles',         // Delete roles
                'assign_permissions',   // Save permissions on a role
                'assign_users_to_role', // Assign users to a role
            ],



            // ── Integrations ───────────────────────────────────────
            'Integrations' => [
                'view_integrations',    // View integrations page
                'submit_custom_request', // Submit a custom integration request
            ],
        ];
    }

    // Users that belong to this role
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Check if role has a specific permission
    public function hasPermission(string $permission): bool
    {
        $perms = $this->permissions ?? [];
        return in_array($permission, $perms);
    }
}