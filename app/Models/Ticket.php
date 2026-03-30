<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'user_id',
        'title',
        'description',
        'system',
        'priority',
        'impact',
        'status',
    ];

    // Auto-filter ALL ticket queries by current user's company
    protected static function booted(): void
    {
        static::addGlobalScope('company', function ($query) {
            if (Auth::check() && Auth::user()->company_id) {
                $query->where('company_id', Auth::user()->company_id);
            }
        });
    }

    // Auto-set company_id when creating a ticket
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($ticket) {
            if (Auth::check() && !$ticket->company_id) {
                $ticket->company_id = Auth::user()->company_id;
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}