<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
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

    /** Scope helpers */
    public function scopeTodo($q)    { return $q->where('status', 'todo'); }
    public function scopeDoing($q)   { return $q->where('status', 'doing'); }
    public function scopeDone($q)    { return $q->where('status', 'done'); }
}