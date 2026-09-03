<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SlaScenarioSeeder extends Seeder
{
    public function run(): void
    {
        $scenarios = [
            ['title'=>'[SLA TEST] P1 payment service restored','company_id'=>5,'user_id'=>5,'developer_id'=>2,'level'=>'Critical','priority'=>'critical','status'=>'resolved','assigned'=>'2026-08-24 09:00:00','response'=>'2026-08-24 09:20:00','resolved'=>'2026-08-24 14:00:00','expected'=>'Compliant - response 20m, same-day resolution, 0 points'],
            ['title'=>'[SLA TEST] P1 late initial acknowledgement','company_id'=>5,'user_id'=>5,'developer_id'=>3,'level'=>'Critical','priority'=>'critical','status'=>'closed','assigned'=>'2026-08-21 09:00:00','response'=>'2026-08-21 10:15:00','resolved'=>'2026-08-21 16:00:00','expected'=>'Response breach - 1 commenced business day x 2 = 2 points'],
            ['title'=>'[SLA TEST] P1 database recovery delayed','company_id'=>6,'user_id'=>6,'developer_id'=>2,'level'=>'Critical','priority'=>'critical','status'=>'resolved','assigned'=>'2026-08-17 09:00:00','response'=>'2026-08-17 09:10:00','resolved'=>'2026-08-20 16:00:00','expected'=>'Resolution breach - 3 business days x 2 = 6 points'],
            ['title'=>'[SLA TEST] P2 portal access restored','company_id'=>6,'user_id'=>7,'developer_id'=>3,'level'=>'High','priority'=>'high','status'=>'resolved','assigned'=>'2026-08-18 09:00:00','response'=>'2026-08-18 10:30:00','resolved'=>'2026-08-19 15:00:00','expected'=>'Compliant - response within 2h and resolution within 2 business days'],
            ['title'=>'[SLA TEST] P2 delayed investigation update','company_id'=>7,'user_id'=>8,'developer_id'=>2,'level'=>'High','priority'=>'high','status'=>'closed','assigned'=>'2026-08-17 09:00:00','response'=>'2026-08-19 10:00:00','resolved'=>'2026-08-19 16:00:00','expected'=>'Response breach - 2 business days after deadline x 2 = 4 points'],
            ['title'=>'[SLA TEST] P2 awaiting customer confirmation','company_id'=>9,'user_id'=>10,'developer_id'=>2,'level'=>'High','priority'=>'high','status'=>'pending_user_response','assigned'=>'2026-08-18 09:00:00','response'=>null,'resolved'=>null,'expected'=>'Live pending breach - points continue on business days until response/resolution'],
            ['title'=>'[SLA TEST] P3 reporting defect resolved','company_id'=>8,'user_id'=>9,'developer_id'=>3,'level'=>'Medium','priority'=>'medium','status'=>'resolved','assigned'=>'2026-08-17 09:00:00','response'=>'2026-08-17 16:30:00','resolved'=>'2026-08-20 15:00:00','expected'=>'Compliant - response within 8h and resolution within 4 business days'],
            ['title'=>'[SLA TEST] P3 search reindex over weekend','company_id'=>7,'user_id'=>8,'developer_id'=>2,'level'=>'Medium','priority'=>'medium','status'=>'resolved','assigned'=>'2026-08-10 09:00:00','response'=>'2026-08-10 12:00:00','resolved'=>'2026-08-19 15:00:00','expected'=>'Resolution breach - weekend excluded, 4 business days x 1 = 4 points'],
            ['title'=>'[SLA TEST] P3 weekend response rollover','company_id'=>8,'user_id'=>9,'developer_id'=>3,'level'=>'Medium','priority'=>'medium','status'=>'closed','assigned'=>'2026-08-14 09:00:00','response'=>'2026-08-17 10:00:00','resolved'=>'2026-08-19 16:00:00','expected'=>'Response breach after Friday deadline - Monday counts as 1 business day x 1 = 1 point'],
        ];

        DB::transaction(function () use ($scenarios) {
            foreach ($scenarios as $scenario) {
                $resolved = $scenario['resolved'];
                DB::table('tickets')->updateOrInsert(
                    ['title' => $scenario['title']],
                    [
                        'company_id' => $scenario['company_id'],
                        'user_id' => $scenario['user_id'],
                        'description' => $scenario['expected'],
                        'system' => 'SLA Validation',
                        'system_name' => 'Support Operations',
                        'priority' => $scenario['priority'],
                        'impact' => $scenario['priority'] === 'critical' ? 'high' : 'medium',
                        'status' => $scenario['status'],
                        'assigned_developer_id' => $scenario['developer_id'],
                        'assigned_date' => $scenario['assigned'],
                        'assigned_by' => 1,
                        'sla_level' => $scenario['level'],
                        'sla_limit_hours' => null,
                        'sla_due_at' => null,
                        'actual_delivery_date' => $resolved,
                        'resolved_at' => $resolved,
                        'resolution_minutes' => $resolved ? abs((int) Carbon::parse($scenario['assigned'])->diffInMinutes(Carbon::parse($resolved))) : null,
                        'compliance_status' => null,
                        'penalty_points' => 0,
                        'compliance_manually_overridden' => false,
                        'created_at' => Carbon::parse($scenario['assigned'])->subMinutes(10),
                        'updated_at' => $resolved ?: $scenario['assigned'],
                    ]
                );

                $ticketId = DB::table('tickets')->where('title', $scenario['title'])->value('id');
                DB::table('ticket_comments')
                    ->where('ticket_id', $ticketId)
                    ->where('body', '[SLA TEST] First developer response')
                    ->delete();

                if ($scenario['response']) {
                    DB::table('ticket_comments')->insert([
                        'ticket_id' => $ticketId,
                        'user_id' => $scenario['developer_id'],
                        'body' => '[SLA TEST] First developer response',
                        'role' => 'developer',
                        'type' => 'comment',
                        'target_role' => null,
                        'created_at' => $scenario['response'],
                        'updated_at' => $scenario['response'],
                    ]);
                }
            }
        });
    }
}
