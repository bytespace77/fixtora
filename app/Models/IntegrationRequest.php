<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntegrationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'requested_integration',
        'contact_name',
        'email',
        'company',
        'message',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
