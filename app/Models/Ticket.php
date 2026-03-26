<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'system',
        'priority',
        'impact',
        'status',
    ];

    /**
     * Get the user who created this ticket
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
