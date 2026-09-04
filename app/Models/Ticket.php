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
        'system_name',
        'priority',
        'impact',
        'status',
        'due_date',
        'assigned_developer_id',
        'assigned_date',
        'assigned_by',
        'sla_level',
        'sla_limit_hours',
        'sla_due_at',
        'estimated_delivery_date',
        'actual_delivery_date',
        'resolved_at',
        'resolution_minutes',
        'compliance_status',
        'penalty_points',
        'compliance_manually_overridden',
        'qc_test_date',
        'csat_rating',
        'csat_comment',
        'csat_submitted_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'assigned_date' => 'datetime',
        'estimated_delivery_date' => 'datetime',
        'actual_delivery_date' => 'datetime',
        'sla_due_at' => 'datetime',
        'resolved_at' => 'datetime',
        'sla_limit_hours' => 'integer',
        'resolution_minutes' => 'integer',
        'penalty_points' => 'integer',
        'compliance_manually_overridden' => 'boolean',
        'qc_test_date' => 'datetime',
        'csat_submitted_at' => 'datetime',
        'csat_rating' => 'integer',
    ];

    // Auto-filter by company — superadmin sees ALL
    protected static function booted(): void
    {
        static::addGlobalScope('company', function ($query) {
            if (Auth::check()) {
                $user = Auth::user();
                if ($user->hasGlobalDataAccess()) {
                    return;
                }
                if ($user->isDeveloper()) {
                    $query->where(function ($q) use ($user) {
                        $q->where('assigned_developer_id', $user->id)
                          ->orWhereHas('tasks', function($taskQ) use ($user) {
                              $taskQ->where('assigned_to', $user->id);
                          });
                    });
                    return;
                }
                if ($user->company_id) {
                    $query->where('company_id', $user->company_id);
                    return;
                }

                // A non-global user without a company must never fall through
                // to an unrestricted ticket query.
                $query->whereRaw('1 = 0');
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
        // Cascade-delete all related tasks when a ticket is deleted
        static::deleting(function ($ticket) {
            $ticket->tasks()->delete();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedDeveloper()
    {
        return $this->belongsTo(User::class, 'assigned_developer_id');
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

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
