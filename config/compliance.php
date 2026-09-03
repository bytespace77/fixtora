<?php

return [
    // SLA is measured continuously (24/7) from assignment to resolution.
    'sla_hours' => [
        'critical' => 4,
        'high' => 8,
        'medium' => 24,
        'low' => 72,
    ],

    // Contract penalty terms. App SLA levels map to P1/P2/P3.
    'penalty_terms' => [
        'critical' => ['priority' => 'P1', 'response_minutes' => 30, 'target_business_days' => 1, 'points_per_breach_day' => 2],
        'high' => ['priority' => 'P2', 'response_minutes' => 120, 'target_business_days' => 2, 'points_per_breach_day' => 2],
        'medium' => ['priority' => 'P3', 'response_minutes' => 480, 'target_business_days' => 4, 'points_per_breach_day' => 1],
        'low' => ['priority' => 'P3', 'response_minutes' => 480, 'target_business_days' => 4, 'points_per_breach_day' => 1],
    ],

    'business_day_starts_at' => '09:00',
    'business_day_ends_at' => '17:00',

    // The source contract has no band for 22-24 points, so it is deliberately
    // left undefined instead of inventing a percentage.
    'service_credit_bands' => [
        ['min' => 1, 'max' => 4, 'percentage' => 1],
        ['min' => 5, 'max' => 14, 'percentage' => 2],
        ['min' => 15, 'max' => 21, 'percentage' => 4],
        ['min' => 25, 'max' => 34, 'percentage' => 6],
        ['min' => 35, 'max' => 44, 'percentage' => 8],
        ['min' => 45, 'max' => null, 'percentage' => 10],
    ],

    // Add Negeri Sembilan public holidays as YYYY-MM-DD values. This can also
    // be supplied as a comma-separated COMPLIANCE_HOLIDAYS environment value.
    'holidays' => array_values(array_filter(array_map(
        'trim',
        explode(',', env('COMPLIANCE_HOLIDAYS', ''))
    ))),
];
