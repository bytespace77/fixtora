<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',       // ✅ Step 14: multi-tenancy
        'user_id',
        'assigned_to',
        'ticket_id',
        'title',
        'description',
        'priority',
        'status',
        'progress',
        'due_date',
    ];

    protected $casts = [
        'due_date' => 'date',
        'progress' => 'integer',
    ];

    // ✅ Step 14: Auto-filter ALL task queries by current user's company
    protected static function booted(): void
    {
        static::addGlobalScope('company', function ($query) {
            if (Auth::check() && Auth::user()->company_id) {
                $query->where('company_id', Auth::user()->company_id);
            }
        });
    }

    // ✅ Step 14: Auto-set company_id when creating a task
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($task) {
            if (Auth::check() && !$task->company_id) {
                $task->company_id = Auth::user()->company_id;
            }
        });
    }

    /** Creator */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Assignee */
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /** Linked ticket */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /** Company */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /** Scope helpers */
    public function scopeTodo($q)    { return $q->where('status', 'todo'); }
    public function scopeDoing($q)   { return $q->where('status', 'doing'); }
    public function scopeDone($q)    { return $q->where('status', 'done'); }
}