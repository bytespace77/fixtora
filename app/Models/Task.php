<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'user_id',
        'assigned_to',
        'ticket_id',
        'title',
        'description',
        'priority',
        'status',
        'progress',
        'due_date',
        'assigned_date',
        'assigned_by',
        'sla_level',
        'estimated_delivery_date',
        'actual_delivery_date',
        'qc_test_date',
    ];

    protected $casts = [
        'due_date' => 'date',
        'progress' => 'integer',
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
                if ($user->isSuperAdmin()) {
                    return;
                }
                if ($user->isDeveloper()) {
                    $query->where('assigned_to', $user->id);
                    return;
                }
                if ($user->hasGlobalDataAccess()) {
                    return;
                }
                if ($user->company_id) {
                    $query->where('company_id', $user->company_id);
                }
            }
        });
    }

    // Auto-set company_id when creating or updating a task
    protected static function boot()
    {
        parent::boot();
        static::saving(function ($task) {
            if ($task->ticket_id) {
                $ticket = Ticket::find($task->ticket_id);
                if ($ticket) {
                    $task->company_id = $ticket->company_id;
                }
            } 
            
            if (Auth::check() && empty($task->company_id)) {
                $task->company_id = Auth::user()->company_id;
            }
        });
        static::saved(function ($task) {
            // Auto-sync task workflow fields back to the parent ticket
            if ($task->ticket_id) {
                $ticket = Ticket::withoutGlobalScopes()->find($task->ticket_id);
                if ($ticket) {
                    $ticketUpdates = [];
                    if ($task->isDirty('assigned_to') || !empty($task->assigned_to)) {
                        $ticketUpdates['assigned_developer_id'] = $task->assigned_to;
                    }
                    if ($task->isDirty('sla_level') || !empty($task->sla_level)) {
                        $ticketUpdates['sla_level'] = $task->sla_level;
                    }
                    if ($task->isDirty('estimated_delivery_date') || !empty($task->estimated_delivery_date)) {
                        $ticketUpdates['estimated_delivery_date'] = $task->estimated_delivery_date;
                    }
                    if ($task->isDirty('actual_delivery_date') || !empty($task->actual_delivery_date)) {
                        $ticketUpdates['actual_delivery_date'] = $task->actual_delivery_date;
                    }
                    if ($task->isDirty('qc_test_date') || !empty($task->qc_test_date)) {
                        $ticketUpdates['qc_test_date'] = $task->qc_test_date;
                    }

                    if (!empty($ticketUpdates)) {
                        $ticket->update($ticketUpdates);
                    }
                }
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