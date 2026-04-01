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
    ];

    protected $hidden = ['password', 'remember_token'];

    // Task 39: eager-load company so every auth check is N+1 free
    protected $with = ['company'];

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
}