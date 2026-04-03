<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Integration extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'color',
        'desc',
        'logo_path',
    ];

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_integrations')
                    ->withPivot(['id', 'status', 'credentials'])
                    ->withTimestamps();
    }
}
