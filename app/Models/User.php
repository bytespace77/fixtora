<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'company_id',
        'role',         // ← Task 36: added
        'role_id',
    ];

    protected $hidden = ['password', 'remember_token'];

    // Task 39: eager-load company so every auth check is N+1 free
    protected $with = ['company', 'userRole'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // A user belongs to one company
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Helper: is this user a super-admin?
    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    // Helper: is this user the company admin?
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // Helper: is this user a developer?
    public function isDeveloper(): bool
    {
        $roleName = strtolower(trim((string) optional($this->userRole)->name));
        $accountRole = strtolower(trim((string) $this->role));
        return $roleName === 'developer' || $accountRole === 'developer';
    }

    // Global data access users bypass company scoping in core modules.
    public function hasGlobalDataAccess(): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $roleName = strtolower(trim((string) optional($this->userRole)->name));
        $accountRole = strtolower(trim((string) $this->role));

        return $roleName === 'developer' || $accountRole === 'developer';
    }

    // A user belongs to one assigned role
    public function userRole()
    {
        return $this->belongsTo(\App\Models\Role::class, 'role_id');
    }

    // Check if user has a specific permission via their assigned role
    // superadmin always has all permissions
    public function hasPermission(string $permission): bool
    {
        if ($this->isSuperAdmin()) return true;
        $role = $this->userRole;
        if (!$role) return false;
        return in_array($permission, $role->permissions ?? []);
    }

    // Check if user can access a section (any permission in that group)
    public function canAccess(string $section): bool
    {
        if ($this->isSuperAdmin()) return true;
        $role = $this->userRole;
        if (!$role) return false;
        $perms = $role->permissions ?? [];

        // Map section names to permission keywords
        $map = [
            'ticket'      => ['ticket'],
            'task'        => ['task'],
            'report'      => ['report', 'view_reports'],
            'sla'         => ['view_sla_monitor'],
            'scheduling'  => ['view_scheduling'],
            'user'        => ['user', 'role'],
            'integration' => ['integration', 'custom_request'],
            'settings'    => ['settings', 'profile'],
            'dashboard'   => ['dashboard'],
        ];

        $keywords = $map[$section] ?? [$section];
        foreach ($perms as $perm) {
            foreach ($keywords as $keyword) {
                if (str_contains($perm, $keyword)) return true;
            }
        }
        return false;
    }
}