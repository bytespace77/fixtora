<?php

namespace App\Providers;

use App\Models\Task;
use App\Models\Ticket;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        if (str_contains(request()->getHost(), 'ngrok')) {
            URL::forceScheme('https');
        }

        View::composer('*', function ($view) {
            if (!Auth::check()) {
                return;
            }

            $notifications = collect();

            try {
                $notifications = $this->buildNotificationsFeed();
            } catch (Throwable $e) {
                // Keep page rendering healthy if notifications source fails.
                $notifications = collect();
            }

            $view->with('allNotifications', $notifications);
            $view->with('topNotifications', $notifications->take(6));
            $view->with('newNotificationsCount', $notifications->where('is_new', true)->count());
        });
    }

    private function buildNotificationsFeed()
    {
        $user = Auth::user();
        if (!$user) return collect();

        $hasGlobalDataAccess = $user->hasGlobalDataAccess();
        $isDeveloper = $user->isDeveloper();
        $companyId = $user->company_id;
        $viewerRole = $this->resolveNotificationRole($user);

        $items = collect();

        // ══════════════════════════════════════════════════════
        // DEVELOPER-SPECIFIC NOTIFICATION FEED
        // ══════════════════════════════════════════════════════
        if ($isDeveloper && !$hasGlobalDataAccess) {
            $userId = $user->id;

            // 1. Tasks assigned directly to this developer
            $myTasks = Task::with('ticket')
                ->withoutGlobalScopes()
                ->where('assigned_to', $userId)
                ->orderByDesc('updated_at')
                ->take(30)
                ->get();

            foreach ($myTasks as $task) {
                $ticketRef = $task->ticket ? '#TK-' . str_pad((string) $task->ticket->id, 4, '0', STR_PAD_LEFT) : null;

                // A) Task assigned notification (based on assigned_date or created_at)
                $assignedAt = $task->assigned_date ? \Carbon\Carbon::parse($task->assigned_date) : $task->created_at;
                $items->push([
                    'unique_id'   => 'task_assigned_' . $task->id,
                    'title'       => $ticketRef ? 'Task Assigned — ' . $ticketRef : 'New Task Assigned',
                    'description' => ($task->title ?: 'Untitled task') . ($ticketRef ? ' · ' . $ticketRef : '') . ($task->sla_level ? ' · SLA: ' . $task->sla_level : ''),
                    'time'        => $assignedAt,
                    'time_human'  => $this->humanTime($assignedAt),
                    'is_new'      => $assignedAt && $assignedAt->greaterThan(now()->subDay()),
                    'type'        => 'blue',
                    'category'    => 'Task Assigned',
                    'url'         => $task->ticket_id ? route('tickets.show', $task->ticket_id) : route('tasks.index'),
                ]);

                // B) Task status change notifications
                // When status changed (updated_at differs from assigned_date/created_at)
                if ($task->updated_at && $task->assigned_date &&
                    $task->updated_at->gt(\Carbon\Carbon::parse($task->assigned_date)->addMinutes(5))) {
                    $statusLabel = match ($task->status) {
                        'doing' => 'In Progress',
                        'done'  => 'Resolved',
                        'todo'  => 'To Do',
                        default => ucfirst($task->status),
                    };
                    $items->push([
                        'unique_id'   => 'task_status_' . $task->id . '_' . $task->updated_at->timestamp,
                        'title'       => 'Task Status Updated — ' . ($ticketRef ?? '#TASK-' . str_pad((string) $task->id, 4, '0', STR_PAD_LEFT)),
                        'description' => ($task->title ?: 'Untitled task') . ' · Status changed to ' . $statusLabel,
                        'time'        => $task->updated_at,
                        'time_human'  => $this->humanTime($task->updated_at),
                        'is_new'      => $task->updated_at->greaterThan(now()->subDay()),
                        'type'        => $this->mapTaskType($task->status),
                        'category'    => 'Task Update',
                        'url'         => $task->ticket_id ? route('tickets.show', $task->ticket_id) : route('tasks.index'),
                    ]);
                }

                // C) Estimated delivery reminder if set
                if ($task->estimated_delivery_date) {
                    $estDate = \Carbon\Carbon::parse($task->estimated_delivery_date);
                    if ($estDate->isFuture() && $estDate->diffInDays(now()) <= 2) {
                        $items->push([
                            'unique_id'   => 'task_due_' . $task->id,
                            'title'       => 'Delivery Due Soon — ' . ($ticketRef ?? '#TASK-' . str_pad((string) $task->id, 4, '0', STR_PAD_LEFT)),
                            'description' => ($task->title ?: 'Untitled') . ' · Due ' . $estDate->diffForHumans(),
                            'time'        => $estDate,
                            'time_human'  => $estDate->diffForHumans(),
                            'is_new'      => true,
                            'type'        => 'orange',
                            'category'    => 'Reminder',
                            'url'         => $task->ticket_id ? route('tickets.show', $task->ticket_id) : route('tasks.index'),
                        ]);
                    }
                }
            }

            // 2. Tickets directly assigned to this developer (assigned_developer_id)
            $myTickets = Ticket::withoutGlobalScope('company')
                ->where('assigned_developer_id', $userId)
                ->orderByDesc('updated_at')
                ->take(20)
                ->get();

            foreach ($myTickets as $ticket) {
                $ticketRef = '#TK-' . str_pad((string) $ticket->id, 4, '0', STR_PAD_LEFT);

                // Assignment notification
                $assignedAt = $ticket->assigned_date ? \Carbon\Carbon::parse($ticket->assigned_date) : $ticket->created_at;
                $items->push([
                    'unique_id'   => 'ticket_assigned_' . $ticket->id,
                    'title'       => 'Ticket Assigned to You — ' . $ticketRef,
                    'description' => $ticket->title . ($ticket->sla_level ? ' · SLA: ' . $ticket->sla_level : '') . ' · Priority: ' . ucfirst($ticket->priority),
                    'time'        => $assignedAt,
                    'time_human'  => $this->humanTime($assignedAt),
                    'is_new'      => $assignedAt && $assignedAt->greaterThan(now()->subDay()),
                    'type'        => $this->mapTicketType($ticket->priority),
                    'category'    => 'Ticket Assigned',
                    'url'         => route('tickets.show', $ticket),
                ]);

                // Status change notification (if updated after assignment)
                if ($ticket->updated_at && $assignedAt &&
                    $ticket->updated_at->gt($assignedAt->addMinutes(5))) {
                    $items->push([
                        'unique_id'   => 'ticket_status_' . $ticket->id . '_' . $ticket->updated_at->timestamp,
                        'title'       => 'Ticket Status Changed — ' . $ticketRef,
                        'description' => $ticket->title . ' · Status: ' . ucfirst(str_replace('_', ' ', $ticket->status)),
                        'time'        => $ticket->updated_at,
                        'time_human'  => $this->humanTime($ticket->updated_at),
                        'is_new'      => $ticket->updated_at->greaterThan(now()->subDay()),
                        'type'        => 'orange',
                        'category'    => 'Ticket Update',
                        'url'         => route('tickets.show', $ticket),
                    ]);
                }
            }

            // 3. Comments (client updates) on tickets/tasks assigned to this developer
            $myTicketIds = $myTickets->pluck('id')->merge(
                $myTasks->whereNotNull('ticket_id')->pluck('ticket_id')
            )->unique()->values()->toArray();

            if (!empty($myTicketIds)) {
                $comments = \App\Models\TicketComment::with('ticket')
                    ->whereIn('ticket_id', $myTicketIds)
                    ->where('user_id', '!=', $userId)
                    ->whereNotIn('type', ['system', 'status_change']) // skip system auto-comments
                    ->orderByDesc('created_at')
                    ->take(20)
                    ->get();

                foreach ($comments as $comment) {
                    $ticketRef = '#TK-' . str_pad((string) $comment->ticket_id, 4, '0', STR_PAD_LEFT);
                    $items->push([
                        'unique_id'   => 'comment_' . $comment->id,
                        'title'       => 'Client Update — ' . $ticketRef,
                        'description' => \Illuminate\Support\Str::limit($comment->body, 60),
                        'time'        => $comment->created_at,
                        'time_human'  => $this->humanTime($comment->created_at),
                        'is_new'      => $comment->created_at && $comment->created_at->greaterThan(now()->subDay()),
                        'type'        => 'blue',
                        'category'    => 'Client Update',
                        'url'         => route('tickets.show', $comment->ticket_id),
                    ]);
                }

                // 4. Workflow notifications targeted at developer role
                $workflowComments = \App\Models\TicketComment::with('ticket')
                    ->whereIn('ticket_id', $myTicketIds)
                    ->where('type', 'workflow_notification')
                    ->where('target_role', 'developer')
                    ->orderByDesc('created_at')
                    ->take(10)
                    ->get();

                foreach ($workflowComments as $comment) {
                    $ticketRef = '#TK-' . str_pad((string) $comment->ticket_id, 4, '0', STR_PAD_LEFT);
                    $items->push([
                        'unique_id'   => 'workflow_' . $comment->id,
                        'title'       => 'Action Required — ' . $ticketRef,
                        'description' => $comment->body,
                        'time'        => $comment->created_at,
                        'time_human'  => $this->humanTime($comment->created_at),
                        'is_new'      => $comment->created_at && $comment->created_at->greaterThan(now()->subDay()),
                        'type'        => 'orange',
                        'category'    => 'Workflow',
                        'url'         => route('tickets.show', $comment->ticket_id),
                    ]);
                }
            }

            // 5. DB notifications for this user
            $dbNotifications = $user->notifications()->take(10)->get()->map(function ($notif) {
                $data  = $notif->data;
                $isNew = $notif->unread() || ($notif->created_at && $notif->created_at->greaterThan(now()->subDay()));
                $type  = 'blue';
                if (($data['type'] ?? '') === 'integration_request') $type = 'orange';
                if (($data['type'] ?? '') === 'integration_request_status') $type = 'green';
                return [
                    'unique_id'   => 'db_' . $notif->id,
                    'title'       => $data['title'] ?? 'System Notification',
                    'description' => $data['message'] ?? '',
                    'time'        => $notif->created_at,
                    'time_human'  => $this->humanTime($notif->created_at),
                    'is_new'      => $isNew,
                    'type'        => $type,
                    'category'    => 'System',
                    'url'         => $data['link'] ?? route('notifications.index'),
                ];
            });

            return $items->merge($dbNotifications)
                ->unique('unique_id')
                ->sortByDesc('time')
                ->values();
        }

        // ══════════════════════════════════════════════════════
        // SUPERADMIN / ADMIN / OTHER ROLES NOTIFICATION FEED
        // ══════════════════════════════════════════════════════

        // --- TICKETS ---
        $ticketQuery = Ticket::query()->latest();
        if (!$hasGlobalDataAccess) {
            $ticketQuery->where('company_id', $companyId ?: 0);
        }

        $ticketItems = $ticketQuery
            ->take(20)
            ->get()
            ->map(function (Ticket $ticket) {
                $isNew = $ticket->created_at && $ticket->created_at->greaterThan(now()->subDay());
                return [
                    'unique_id'   => 'ticket_' . $ticket->id . '_' . $ticket->updated_at->timestamp,
                    'title'       => 'New Ticket: #' . str_pad((string) $ticket->id, 4, '0', STR_PAD_LEFT),
                    'description' => $ticket->title ?: 'New ticket submitted.',
                    'time'        => $ticket->created_at,
                    'time_human'  => $this->humanTime($ticket->created_at),
                    'is_new'      => $isNew,
                    'type'        => $this->mapTicketType($ticket->priority),
                    'category'    => 'Ticket',
                    'url'         => route('tickets.show', $ticket),
                ];
            });

        // --- COMMENTS ---
        $commentQuery = \App\Models\TicketComment::with('ticket')->latest();
        if (!$hasGlobalDataAccess) {
            $commentQuery->whereHas('ticket', function ($q) use ($companyId) {
                $q->where('company_id', $companyId ?: 0);
            });
        }

        $commentItems = $commentQuery
            ->take(20)
            ->get()
            ->filter(function (\App\Models\TicketComment $comment) use ($hasGlobalDataAccess, $viewerRole) {
                if ($hasGlobalDataAccess) return true;
                if ($comment->type === 'workflow_notification') {
                    return ($comment->target_role ?? '') === $viewerRole;
                }
                return true;
            })
            ->map(function (\App\Models\TicketComment $comment) {
                $isNew = $comment->created_at && $comment->created_at->greaterThan(now()->subDay());
                if ($comment->type === 'workflow_notification') {
                    $target = strtoupper((string) ($comment->target_role ?? 'TEAM'));
                    $title  = $target . ' Notification: #' . str_pad((string) $comment->ticket_id, 4, '0', STR_PAD_LEFT);
                    $desc   = $comment->body;
                    $typeColor = 'orange';
                    $category  = 'Workflow';
                } elseif ($comment->type === 'status_change') {
                    $title  = 'Status Update: #' . str_pad((string) $comment->ticket_id, 4, '0', STR_PAD_LEFT);
                    $desc   = $comment->body;
                    $typeColor = 'orange';
                    $category  = 'System';
                } else {
                    $title  = 'New Comment: #' . str_pad((string) $comment->ticket_id, 4, '0', STR_PAD_LEFT);
                    $desc   = \Illuminate\Support\Str::limit($comment->body, 50);
                    $typeColor = 'blue';
                    $category  = 'Discussion';
                }
                return [
                    'unique_id'   => 'comment_' . $comment->id,
                    'title'       => $title,
                    'description' => $desc,
                    'time'        => $comment->created_at,
                    'time_human'  => $this->humanTime($comment->created_at),
                    'is_new'      => $isNew,
                    'type'        => $typeColor,
                    'category'    => $category,
                    'url'         => route('tickets.show', $comment->ticket_id),
                ];
            });

        // --- TASKS ---
        $taskQuery = Task::with('ticket')->orderByDesc('updated_at');
        if (!$hasGlobalDataAccess) {
            $taskQuery->where('company_id', $companyId ?: 0);
        }

        $taskItems = $taskQuery
            ->take(20)
            ->get()
            ->map(function (Task $task) {
                $isNew     = $task->updated_at && $task->updated_at->greaterThan(now()->subDay());
                $taskLabel = $task->title ?: ('Task #' . $task->id);
                $url       = $task->ticket_id ? route('tickets.show', $task->ticket_id) : route('tasks.index');
                return [
                    'unique_id'   => 'task_' . $task->id . '_' . $task->updated_at->timestamp,
                    'title'       => 'Task activity: ' . $taskLabel,
                    'description' => 'Status: ' . strtoupper((string) $task->status) . ($task->ticket ? ' · Linked to ticket #' . str_pad((string) $task->ticket->id, 4, '0', STR_PAD_LEFT) : ''),
                    'time'        => $task->updated_at,
                    'time_human'  => $this->humanTime($task->updated_at),
                    'is_new'      => $isNew,
                    'type'        => $this->mapTaskType($task->status),
                    'category'    => 'Task',
                    'url'         => $url,
                ];
            });

        // --- DB NOTIFICATIONS ---
        $dbNotifications = $user->notifications()
            ->take(15)
            ->get()
            ->map(function ($notif) {
                $data  = $notif->data;
                $isNew = $notif->unread() || ($notif->created_at && $notif->created_at->greaterThan(now()->subDay()));
                $type  = 'blue';
                if (($data['type'] ?? '') === 'integration_request') $type = 'orange';
                if (($data['type'] ?? '') === 'integration_request_status') $type = 'green';
                return [
                    'unique_id'   => 'db_' . $notif->id,
                    'title'       => $data['title'] ?? 'System Notification',
                    'description' => $data['message'] ?? '',
                    'time'        => $notif->created_at,
                    'time_human'  => $this->humanTime($notif->created_at),
                    'is_new'      => $isNew,
                    'type'        => $type,
                    'category'    => 'System',
                    'url'         => $data['link'] ?? route('notifications.index'),
                ];
            });

        return $ticketItems
            ->merge($commentItems)
            ->merge($taskItems)
            ->merge($dbNotifications)
            ->sortByDesc('time')
            ->values();
    }

    private function mapTicketType(?string $priority): string
    {
        return match ($priority) {
            'critical', 'high' => 'red',
            'medium' => 'orange',
            default => 'blue',
        };
    }

    private function mapTaskType(?string $status): string
    {
        return match ($status) {
            'done' => 'green',
            'doing' => 'orange',
            default => 'blue',
        };
    }

    private function humanTime($time): string
    {
        return $time instanceof CarbonInterface ? $time->diffForHumans() : 'just now';
    }

    private function resolveNotificationRole(?User $user): string
    {
        if (!$user) {
            return 'client';
        }

        $roleName = strtolower(trim((string) optional($user->userRole)->name));
        $accountRole = strtolower(trim((string) $user->role));

        if ($roleName === 'developer' || $accountRole === 'developer') {
            return 'developer';
        }

        if (in_array($roleName, ['qc', 'quality control', 'quality assurance', 'tester'], true) ||
            in_array($accountRole, ['qc', 'tester'], true)) {
            return 'qc';
        }

        return 'client';
    }
}