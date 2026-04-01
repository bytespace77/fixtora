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
        'due_date',
        'assigned_developer_id',
        'assigned_date',
        'assigned_by',
        'sla_level',
        'estimated_delivery_date',
        'actual_delivery_date',
        'qc_test_date',
    ];

    protected $casts = [
        'due_date' => 'date',
        'assigned_date' => 'datetime',
        'estimated_delivery_date' => 'datetime',
        'actual_delivery_date' => 'datetime',
        'qc_test_date' => 'datetime',
    ];

    // Auto-filter by company — superadmin sees ALL
    protected static function booted(): void
    {
        static::addGlobalScope('company', function ($query) {
            if (Auth::check()) {
                $user = Auth::user();
                // Superadmin and developer role can view cross-company data
                if ($user->hasGlobalDataAccess()) {
                    return;
                }
                if ($user->company_id) {
                    $query->where('company_id', $user->company_id);
                }
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

    public function comments()
    {
        return $this->hasMany(TicketComment::class);
    }

    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function assignedDeveloper()
    {
        return $this->belongsTo(User::class, 'assigned_developer_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}