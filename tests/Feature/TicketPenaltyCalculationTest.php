<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\TicketComment;
use App\Services\TicketComplianceService;
use Carbon\Carbon;
use Tests\TestCase;

class TicketPenaltyCalculationTest extends TestCase
{
    public function test_p1_breach_costs_two_points_per_business_day(): void
    {
        config(['compliance.holidays' => []]);
        $service = app(TicketComplianceService::class);
        $ticket = new Ticket([
            'user_id' => 5,
            'sla_level' => 'Critical',
            'status' => 'resolved',
            'compliance_status' => 'breached',
            'penalty_points' => 0,
        ]);
        $ticket->assigned_date = Carbon::parse('2026-08-17 09:00'); // Monday
        $ticket->resolved_at = Carbon::parse('2026-08-18 12:00');
        $ticket->setRelation('comments', collect([
            new TicketComment([
                'user_id' => 2,
                'role' => 'developer',
            ]),
        ]));
        $ticket->comments->first()->created_at = Carbon::parse('2026-08-17 09:15');

        $this->assertSame(2, $service->penalty($ticket));
    }

    public function test_p3_ignores_weekends_and_configured_holidays(): void
    {
        config(['compliance.holidays' => ['2026-08-17']]); // Monday holiday
        $service = app(TicketComplianceService::class);

        $due = $service->resolutionDueAt(Carbon::parse('2026-08-14 09:00'), 'Medium');

        $this->assertSame('2026-08-20 17:00', $due?->format('Y-m-d H:i'));
    }

    public function test_superadmin_manual_zero_penalty_overrides_automatic_calculation(): void
    {
        $service = app(TicketComplianceService::class);
        $ticket = new Ticket([
            'sla_level' => 'Critical',
            'compliance_status' => 'breached',
            'penalty_points' => 0,
            'compliance_manually_overridden' => true,
        ]);
        $ticket->assigned_date = Carbon::parse('2026-08-17 09:00');
        $ticket->resolved_at = Carbon::parse('2026-08-18 12:00');

        $this->assertSame(0, $service->penalty($ticket));
    }

    public function test_response_deadlines_and_service_credit_bands_follow_contract(): void
    {
        config(['compliance.holidays' => []]);
        $service = app(TicketComplianceService::class);

        $this->assertSame(
            '2026-08-17 09:30',
            $service->responseDueAt(Carbon::parse('2026-08-17 09:00'), 'Critical')?->format('Y-m-d H:i')
        );
        $this->assertSame(1, $service->serviceCreditPercentage(4));
        $this->assertSame(2, $service->serviceCreditPercentage(5));
        $this->assertSame(4, $service->serviceCreditPercentage(21));
        $this->assertNull($service->serviceCreditPercentage(22));
        $this->assertSame(6, $service->serviceCreditPercentage(25));
        $this->assertSame(10, $service->serviceCreditPercentage(45));
    }
}
