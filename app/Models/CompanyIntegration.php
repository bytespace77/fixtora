<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CompanyIntegration extends Pivot
{
    protected $table = 'company_integrations';

    protected $fillable = [
        'company_id',
        'integration_id',
        'status',
        'credentials',
    ];

    protected $casts = [
        'credentials' => 'encrypted:array',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function integration()
    {
        return $this->belongsTo(Integration::class);
    }
}
