<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReportDemoSeeder extends Seeder
{
    public function run(): void
    {
        $tickets = [
            ['company_id'=>5,'user_id'=>5,'title'=>'[REPORT DEMO] Payment gateway timeout','system'=>'Home Sdn Bhd','system_name'=>'Payment','priority'=>'critical','impact'=>'high','status'=>'resolved','assigned_developer_id'=>2,'assigned_date'=>'2026-07-10 09:00:00','assigned_by'=>1,'sla_level'=>'Critical','sla_limit_hours'=>4,'sla_due_at'=>'2026-07-10 13:00:00','actual_delivery_date'=>'2026-07-10 12:00:00','resolved_at'=>'2026-07-10 12:00:00','resolution_minutes'=>180,'compliance_status'=>'compliant','penalty_points'=>0,'created_at'=>'2026-07-10 08:45:00','updated_at'=>'2026-07-10 12:00:00'],
            ['company_id'=>5,'user_id'=>5,'title'=>'[REPORT DEMO] Invoice export failure','system'=>'Home Sdn Bhd','system_name'=>'Finance','priority'=>'high','impact'=>'medium','status'=>'closed','assigned_developer_id'=>3,'assigned_date'=>'2026-07-18 08:00:00','assigned_by'=>1,'sla_level'=>'High','sla_limit_hours'=>8,'sla_due_at'=>'2026-07-18 16:00:00','actual_delivery_date'=>'2026-07-18 20:00:00','resolved_at'=>'2026-07-18 20:00:00','resolution_minutes'=>720,'compliance_status'=>'breached','penalty_points'=>10,'created_at'=>'2026-07-18 07:40:00','updated_at'=>'2026-07-18 20:00:00'],
            ['company_id'=>6,'user_id'=>6,'title'=>'[REPORT DEMO] Customer portal access','system'=>'Apex Solutions','system_name'=>'Portal','priority'=>'medium','impact'=>'medium','status'=>'resolved','assigned_developer_id'=>2,'assigned_date'=>'2026-07-25 09:00:00','assigned_by'=>1,'sla_level'=>'Medium','sla_limit_hours'=>24,'sla_due_at'=>'2026-07-26 09:00:00','actual_delivery_date'=>'2026-07-26 05:00:00','resolved_at'=>'2026-07-26 05:00:00','resolution_minutes'=>1200,'compliance_status'=>'compliant','penalty_points'=>0,'created_at'=>'2026-07-25 08:30:00','updated_at'=>'2026-07-26 05:00:00'],
            ['company_id'=>6,'user_id'=>7,'title'=>'[REPORT DEMO] Monthly report mismatch','system'=>'Apex Solutions','system_name'=>'Reporting','priority'=>'low','impact'=>'low','status'=>'closed','assigned_developer_id'=>3,'assigned_date'=>'2026-08-01 08:00:00','assigned_by'=>1,'sla_level'=>'Low','sla_limit_hours'=>72,'sla_due_at'=>'2026-08-04 08:00:00','actual_delivery_date'=>'2026-08-05 12:00:00','resolved_at'=>'2026-08-05 12:00:00','resolution_minutes'=>6000,'compliance_status'=>'breached','penalty_points'=>10,'created_at'=>'2026-08-01 07:30:00','updated_at'=>'2026-08-05 12:00:00'],
            ['company_id'=>7,'user_id'=>8,'title'=>'[REPORT DEMO] API authentication error','system'=>'TechHive','system_name'=>'API','priority'=>'critical','impact'=>'critical','status'=>'resolved','assigned_developer_id'=>2,'assigned_date'=>'2026-08-07 08:00:00','assigned_by'=>1,'sla_level'=>'Critical','sla_limit_hours'=>4,'sla_due_at'=>'2026-08-07 12:00:00','actual_delivery_date'=>'2026-08-07 18:00:00','resolved_at'=>'2026-08-07 18:00:00','resolution_minutes'=>600,'compliance_status'=>'breached','penalty_points'=>20,'created_at'=>'2026-08-07 07:50:00','updated_at'=>'2026-08-07 18:00:00'],
            ['company_id'=>8,'user_id'=>9,'title'=>'[REPORT DEMO] Dashboard chart loading','system'=>'MIA SDN BHD','system_name'=>'Dashboard','priority'=>'high','impact'=>'medium','status'=>'escalated','assigned_developer_id'=>3,'assigned_date'=>'2026-08-13 08:00:00','assigned_by'=>1,'sla_level'=>'High','sla_limit_hours'=>8,'sla_due_at'=>'2026-08-13 16:00:00','resolution_minutes'=>null,'compliance_status'=>null,'penalty_points'=>0,'created_at'=>'2026-08-13 07:45:00','updated_at'=>'2026-08-14 10:00:00'],
            ['company_id'=>8,'user_id'=>9,'title'=>'[REPORT DEMO] Mobile layout adjustment','system'=>'MIA SDN BHD','system_name'=>'Frontend','priority'=>'medium','impact'=>'low','status'=>'in_review','assigned_developer_id'=>2,'assigned_date'=>'2026-08-15 08:00:00','assigned_by'=>1,'sla_level'=>'Medium','sla_limit_hours'=>24,'sla_due_at'=>'2026-08-16 08:00:00','actual_delivery_date'=>'2026-08-15 11:00:00','resolution_minutes'=>null,'compliance_status'=>null,'penalty_points'=>0,'created_at'=>'2026-08-15 07:30:00','updated_at'=>'2026-08-15 11:00:00'],
            ['company_id'=>9,'user_id'=>10,'title'=>'[REPORT DEMO] New user onboarding','system'=>'TESTING','system_name'=>'User Management','priority'=>'low','impact'=>'low','status'=>'open','assigned_developer_id'=>null,'assigned_date'=>null,'assigned_by'=>null,'sla_level'=>null,'sla_limit_hours'=>null,'sla_due_at'=>null,'resolution_minutes'=>null,'compliance_status'=>null,'penalty_points'=>0,'created_at'=>'2026-08-14 09:00:00','updated_at'=>'2026-08-14 09:00:00'],
            ['company_id'=>7,'user_id'=>8,'title'=>'[REPORT DEMO] Search indexing delay','system'=>'TechHive','system_name'=>'Search','priority'=>'high','impact'=>'high','status'=>'resolved','assigned_developer_id'=>3,'assigned_date'=>'2026-08-10 09:00:00','assigned_by'=>1,'sla_level'=>'High','sla_limit_hours'=>8,'sla_due_at'=>'2026-08-10 17:00:00','actual_delivery_date'=>'2026-08-10 15:30:00','resolved_at'=>'2026-08-10 15:30:00','resolution_minutes'=>390,'compliance_status'=>'compliant','penalty_points'=>0,'created_at'=>'2026-08-10 08:40:00','updated_at'=>'2026-08-10 15:30:00'],
            ['company_id'=>9,'user_id'=>10,'title'=>'[REPORT DEMO] Email notification queue','system'=>'TESTING','system_name'=>'Notification','priority'=>'high','impact'=>'medium','status'=>'pending_user_response','assigned_developer_id'=>2,'assigned_date'=>'2026-08-15 10:00:00','assigned_by'=>1,'sla_level'=>'High','sla_limit_hours'=>8,'sla_due_at'=>'2026-08-15 18:00:00','resolution_minutes'=>null,'compliance_status'=>null,'penalty_points'=>0,'created_at'=>'2026-08-15 09:30:00','updated_at'=>'2026-08-15 10:30:00'],
        ];

        DB::transaction(function () use ($tickets) {
            foreach ($tickets as $ticket) {
                DB::table('tickets')->updateOrInsert(
                    ['title' => $ticket['title']],
                    array_merge([
                        'description' => 'Demo record for compliance reporting.',
                        'due_date' => null,
                        'estimated_delivery_date' => null,
                        'qc_test_date' => null,
                        'csat_rating' => null,
                        'csat_comment' => null,
                        'csat_submitted_at' => null,
                    ], $ticket)
                );
            }

            $ticketIds = DB::table('tickets')->where('title', 'like', '[REPORT DEMO]%')->pluck('id', 'title');
            $responses = [
                ['ticket'=>'[REPORT DEMO] Payment gateway timeout','user_id'=>2,'created_at'=>'2026-07-10 09:05:00'],
                ['ticket'=>'[REPORT DEMO] Invoice export failure','user_id'=>3,'created_at'=>'2026-07-18 08:20:00'],
                ['ticket'=>'[REPORT DEMO] Customer portal access','user_id'=>2,'created_at'=>'2026-07-25 09:10:00'],
                ['ticket'=>'[REPORT DEMO] Monthly report mismatch','user_id'=>3,'created_at'=>'2026-08-01 08:30:00'],
                ['ticket'=>'[REPORT DEMO] API authentication error','user_id'=>2,'created_at'=>'2026-08-07 08:02:00'],
                ['ticket'=>'[REPORT DEMO] Dashboard chart loading','user_id'=>3,'created_at'=>'2026-08-13 08:15:00'],
            ];
            foreach ($responses as $response) {
                $ticketId = $ticketIds[$response['ticket']] ?? null;
                DB::table('ticket_comments')->updateOrInsert(
                    ['ticket_id' => $ticketId, 'body' => '[REPORT DEMO] First support response'],
                    ['user_id' => $response['user_id'], 'role' => 'developer', 'type' => 'comment', 'target_role' => null, 'created_at' => $response['created_at'], 'updated_at' => $response['created_at']]
                );
            }

            $tasks = [
                ['ticket'=>'[REPORT DEMO] Payment gateway timeout','title'=>'[REPORT DEMO] Diagnose payment timeout','company_id'=>5,'user_id'=>1,'assigned_to'=>2,'priority'=>'urgent','status'=>'done','progress'=>100,'created_at'=>'2026-07-10 09:00:00','updated_at'=>'2026-07-10 12:00:00','assigned_date'=>'2026-07-10 09:00:00','actual_delivery_date'=>'2026-07-10 12:00:00','due_date'=>'2026-07-10'],
                ['ticket'=>'[REPORT DEMO] Customer portal access','title'=>'[REPORT DEMO] Restore portal access','company_id'=>6,'user_id'=>1,'assigned_to'=>2,'priority'=>'medium','status'=>'done','progress'=>100,'created_at'=>'2026-07-25 09:00:00','updated_at'=>'2026-07-26 05:00:00','assigned_date'=>'2026-07-25 09:00:00','actual_delivery_date'=>'2026-07-26 05:00:00','due_date'=>'2026-07-26'],
                ['ticket'=>'[REPORT DEMO] Dashboard chart loading','title'=>'[REPORT DEMO] Optimise dashboard queries','company_id'=>8,'user_id'=>1,'assigned_to'=>3,'priority'=>'high','status'=>'doing','progress'=>65,'created_at'=>'2026-08-13 08:00:00','updated_at'=>'2026-08-14 10:00:00','assigned_date'=>'2026-08-13 08:00:00','actual_delivery_date'=>null,'due_date'=>'2026-08-13'],
                ['ticket'=>'[REPORT DEMO] Mobile layout adjustment','title'=>'[REPORT DEMO] Verify responsive layout','company_id'=>8,'user_id'=>1,'assigned_to'=>2,'priority'=>'medium','status'=>'doing','progress'=>90,'created_at'=>'2026-08-15 08:00:00','updated_at'=>'2026-08-15 11:00:00','assigned_date'=>'2026-08-15 08:00:00','actual_delivery_date'=>null,'due_date'=>'2026-08-16'],
            ];
            foreach ($tasks as $task) {
                $ticketId = $ticketIds[$task['ticket']] ?? null;
                unset($task['ticket']);
                DB::table('tasks')->updateOrInsert(
                    ['title' => $task['title']],
                    array_merge($task, [
                        'ticket_id' => $ticketId,
                        'description' => 'Demo task for report trends.',
                        'assigned_by' => 1,
                        'sla_level' => 'Medium',
                        'estimated_delivery_date' => null,
                        'qc_test_date' => null,
                    ])
                );
            }
        });
    }
}
