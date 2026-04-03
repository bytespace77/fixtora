<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['name', 'slug', 'is_active', 'systems'];

    protected $casts = [
        'is_active' => 'boolean',
        'systems' => 'array',
    ];

    // A company has many users
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // A company has many tickets
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    // A company has many integrations via company_integrations
    public function integrations()
    {
        return $this->belongsToMany(Integration::class, 'company_integrations')
                    ->withPivot(['id', 'status', 'credentials'])
                    ->withTimestamps();
    }
}