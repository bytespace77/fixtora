<?php

namespace App\Services;

use App\Models\Ticket;
use Carbon\Carbon;

class TicketComplianceService
{
    public function slaHours(?string $level): ?int
    {
        if (!$level) {
            return null;
        }

        $limits = function_exists('session')
            ? session('sla_limits', config('compliance.sla_hours', []))
            : config('compliance.sla_hours', []);
        $hours = $limits[strtolower($level)] ?? null;
        return $hours === null ? null : (int) $hours;
    }

    public function snapshotAssignment(Ticket $ticket, array &$values): void
    {
        $level = $values['sla_level'] ?? $ticket->sla_level;
        $start = isset($values['assigned_date'])
            ? Carbon::parse($values['assigned_date'])
            : ($ticket->assigned_date ?: now());
        $hours = $this->slaHours($level);

        $values['sla_limit_hours'] = $hours;
        $values['sla_due_at'] = $this->resolutionDueAt($start, $level);
        $values['resolved_at'] = null;
        $values['resolution_minutes'] = null;
        $values['compliance_status'] = null;
        $values['penalty_points'] = 0;
        $values['compliance_manually_overridden'] = false;
    }

    public function snapshotResolution(Ticket $ticket, array &$values, ?Carbon $resolvedAt = null): void
    {
        $resolvedAt ??= now();
        $start = isset($values['assigned_date'])
            ? Carbon::parse($values['assigned_date'])
            : ($ticket->assigned_date ?: $ticket->created_at);
        $limit = $values['sla_limit_hours'] ?? $ticket->sla_limit_hours ?? $this->slaHours($values['sla_level'] ?? $ticket->sla_level);
        $minutes = max(0, $start->diffInMinutes($resolvedAt));
        $level = $values['sla_level'] ?? $ticket->sla_level;
        $dueAt = $this->resolutionDueAt($start, $level);
        $responseDueAt = $this->responseDueAt($start, $level);
        $firstResponseAt = $this->firstResponseAt($ticket);
        $responseEnd = $firstResponseAt ?: $resolvedAt;
        $responseBreached = $responseDueAt && $responseEnd->gt($responseDueAt);
        $resolutionBreached = $dueAt && $resolvedAt->gt($dueAt);

        $values['sla_limit_hours'] = $limit;
        $values['sla_due_at'] = $dueAt;
        $values['resolved_at'] = $resolvedAt;
        $values['resolution_minutes'] = $minutes;
        $values['compliance_status'] = !$dueAt || !$responseDueAt
            ? 'not_applicable'
            : (($responseBreached || $resolutionBreached) ? 'breached' : 'compliant');
        $values['penalty_points'] = max(
            $responseBreached ? $this->penaltyPoints($level, $responseDueAt, $responseEnd) : 0,
            $resolutionBreached ? $this->penaltyPoints($level, $dueAt, $resolvedAt) : 0,
        );
        $values['compliance_manually_overridden'] = false;
    }

    public function clearResolution(array &$values): void
    {
        $values['resolved_at'] = null;
        $values['resolution_minutes'] = null;
        $values['compliance_status'] = null;
        $values['penalty_points'] = 0;
        $values['compliance_manually_overridden'] = false;
    }

    public function status(Ticket $ticket): string
    {
        if ($ticket->compliance_manually_overridden && $ticket->compliance_status) {
            return $ticket->compliance_status;
        }

        if (!$ticket->assigned_date || !$this->term($ticket->sla_level)) {
            return 'not_applicable';
        }

        $resolutionDueAt = $this->resolutionDueAt($ticket->assigned_date, $ticket->sla_level);
        $responseDueAt = $this->responseDueAt($ticket->assigned_date, $ticket->sla_level);
        if (!$resolutionDueAt || !$responseDueAt) {
            return 'not_applicable';
        }

        $isResolved = in_array($ticket->status, ['resolved', 'closed'], true);
        $resolutionEnd = $isResolved
            ? ($ticket->resolved_at ?: $ticket->actual_delivery_date ?: $ticket->updated_at)
            : now();
        $firstResponseAt = $this->firstResponseAt($ticket);
        $responseEnd = $firstResponseAt ?: $resolutionEnd;

        if ($responseEnd->gt($responseDueAt) || $resolutionEnd->gt($resolutionDueAt)) {
            return 'breached';
        }

        return $isResolved ? 'compliant' : 'pending';
    }

    public function resolutionMinutes(Ticket $ticket): ?int
    {
        if ($ticket->resolution_minutes !== null) {
            return (int) $ticket->resolution_minutes;
        }
        if (!$ticket->assigned_date || !in_array($ticket->status, ['resolved', 'closed'], true)) {
            return null;
        }
        $end = $ticket->resolved_at ?: $ticket->actual_delivery_date ?: $ticket->updated_at;
        return max(0, $ticket->assigned_date->diffInMinutes($end));
    }

    public function penalty(Ticket $ticket): int
    {
        if ($ticket->compliance_manually_overridden) {
            return (int) $ticket->penalty_points;
        }
        if (!$ticket->assigned_date || $this->status($ticket) !== 'breached') {
            return 0;
        }

        $isResolved = in_array($ticket->status, ['resolved', 'closed'], true);
        $resolutionEnd = $isResolved
            ? ($ticket->resolved_at ?: $ticket->actual_delivery_date ?: $ticket->updated_at)
            : now();
        $firstResponseAt = $this->firstResponseAt($ticket);
        $responseEnd = $firstResponseAt ?: $resolutionEnd;
        $responseDueAt = $this->responseDueAt($ticket->assigned_date, $ticket->sla_level);
        $resolutionDueAt = $this->resolutionDueAt($ticket->assigned_date, $ticket->sla_level);

        return max(
            $responseDueAt ? $this->penaltyPoints($ticket->sla_level, $responseDueAt, $responseEnd) : 0,
            $resolutionDueAt ? $this->penaltyPoints($ticket->sla_level, $resolutionDueAt, $resolutionEnd) : 0,
        );
    }

    public function responseDueAt(Carbon $start, ?string $level): ?Carbon
    {
        $term = $this->term($level);
        return $term ? $this->addBusinessMinutes($start, (int) $term['response_minutes']) : null;
    }

    public function firstResponseAt(Ticket $ticket): ?Carbon
    {
        $comment = $ticket->comments
            ->where('user_id', '!=', $ticket->user_id)
            ->where('role', '!=', 'system')
            ->sortBy('created_at')
            ->first();

        return $comment?->created_at;
    }

    public function serviceCreditPercentage(int $points): ?int
    {
        if ($points <= 0) {
            return 0;
        }

        foreach (config('compliance.service_credit_bands', []) as $band) {
            if ($points >= $band['min'] && ($band['max'] === null || $points <= $band['max'])) {
                return (int) $band['percentage'];
            }
        }

        return null;
    }

    public function resolutionDueAt(Carbon $start, ?string $level): ?Carbon
    {
        $term = $this->term($level);
        if (!$term) {
            return null;
        }

        [$endHour, $endMinute] = array_map('intval', explode(':', config('compliance.business_day_ends_at', '17:00')));
        $due = $start->copy();
        if ($this->isBusinessDay($due) && $due->gte($due->copy()->setTime($endHour, $endMinute))) {
            $due->addDay();
        }
        $due->startOfDay();
        while (!$this->isBusinessDay($due)) {
            $due->addDay()->startOfDay();
        }

        $daysRemaining = max(1, (int) $term['target_business_days']) - 1;
        while ($daysRemaining > 0) {
            $due->addDay();
            if ($this->isBusinessDay($due)) {
                $daysRemaining--;
            }
        }

        return $due->setTime($endHour, $endMinute);
    }

    private function penaltyPoints(?string $level, Carbon $dueAt, Carbon $resolvedAt): int
    {
        $term = $this->term($level);
        if (!$term || $resolvedAt->lte($dueAt)) {
            return 0;
        }

        $breachDays = 0;
        $cursor = $dueAt->copy()->addDay()->startOfDay();
        $lastDay = $resolvedAt->copy()->startOfDay();
        while ($cursor->lte($lastDay)) {
            if ($this->isBusinessDay($cursor)) {
                $breachDays++;
            }
            $cursor->addDay();
        }

        // A breach after the deadline on its due date still incurs one day.
        $breachDays = max(1, $breachDays);
        return $breachDays * (int) $term['points_per_breach_day'];
    }

    private function isBusinessDay(Carbon $date): bool
    {
        return !$date->isWeekend()
            && !in_array($date->toDateString(), config('compliance.holidays', []), true);
    }

    private function term(?string $level): ?array
    {
        return config('compliance.penalty_terms.'.strtolower((string) $level));
    }

    private function addBusinessMinutes(Carbon $start, int $minutes): Carbon
    {
        [$startHour, $startMinute] = array_map('intval', explode(':', config('compliance.business_day_starts_at', '09:00')));
        [$endHour, $endMinute] = array_map('intval', explode(':', config('compliance.business_day_ends_at', '17:00')));
        $cursor = $start->copy();

        while (true) {
            if (!$this->isBusinessDay($cursor)) {
                $cursor->addDay()->setTime($startHour, $startMinute);
                continue;
            }

            $dayStart = $cursor->copy()->setTime($startHour, $startMinute);
            $dayEnd = $cursor->copy()->setTime($endHour, $endMinute);
            if ($cursor->lt($dayStart)) {
                $cursor = $dayStart;
            } elseif ($cursor->gte($dayEnd)) {
                $cursor->addDay()->setTime($startHour, $startMinute);
                continue;
            }

            $available = $cursor->diffInMinutes($dayEnd);
            if ($minutes <= $available) {
                return $cursor->addMinutes($minutes);
            }

            $minutes -= $available;
            $cursor->addDay()->setTime($startHour, $startMinute);
        }
    }
}
