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

        // --- TICKETS ---
        $ticketQuery = Ticket::query()->latest();
        if (!$hasGlobalDataAccess) {
            $ticketQuery->where('company_id', $companyId ?: 0);
        }

        // If Developer, we don't show raw "New Ticket" notifications, only "Ticket Assigned" and "Client Updates"
        $ticketItems = collect();
        if (!$isDeveloper) {
            $ticketItems = $ticketQuery
                ->take(20)
                ->get()
                ->map(function (Ticket $ticket) {
                    $isNew = $ticket->created_at && $ticket->created_at->greaterThan(now()->subDay());

                    return [
                        'title' => 'New Ticket: #' . str_pad((string) $ticket->id, 4, '0', STR_PAD_LEFT),
                        'description' => $ticket->title ?: 'New ticket submitted.',
                        'time' => $ticket->created_at,
                        'time_human' => $this->humanTime($ticket->created_at),
                        'is_new' => $isNew,
                        'type' => $this->mapTicketType($ticket->priority),
                        'category' => 'Ticket',
                        'url' => route('tickets.show', $ticket),
                    ];
                });
        }

        // --- COMMENTS (Client Updates) ---
        $commentQuery = \App\Models\TicketComment::with('ticket')->latest();
        if ($isDeveloper) {
            // ONLY comments on tickets assigned to the developer!
            $commentQuery->whereHas('ticket', function ($q) use ($user) {
                $q->whereHas('tasks', function ($tq) use ($user) {
                    $tq->where('assigned_to', $user->id);
                });
            });
            // Assume comments are "client updates" for now
            $commentQuery->where('user_id', '!=', $user->id); 
        } elseif (!$hasGlobalDataAccess) {
            $commentQuery->whereHas('ticket', function ($q) use ($companyId) {
                $q->where('company_id', $companyId ?: 0);
            });
        }

        $commentItems = $commentQuery
            ->take(20)
            ->get()
            ->filter(function (\App\Models\TicketComment $comment) use ($hasGlobalDataAccess, $viewerRole, $isDeveloper) {
                if ($isDeveloper) return true;
                if ($hasGlobalDataAccess) return true;

                if ($comment->type === 'workflow_notification') {
                    return ($comment->target_role ?? '') === $viewerRole;
                }

                return true;
            })
            ->map(function (\App\Models\TicketComment $comment) use ($isDeveloper) {
                $isNew = $comment->created_at && $comment->created_at->greaterThan(now()->subDay());

                if ($comment->type === 'workflow_notification') {
                    $target = strtoupper((string) ($comment->target_role ?? 'TEAM'));
                    $title = $target . ' Notification: #' . str_pad((string) $comment->ticket_id, 4, '0', STR_PAD_LEFT);
                    $desc = $comment->body;
                    $typeColor = 'orange';
                    $category = 'Workflow';
                } elseif ($comment->type === 'status_change') {
                    $title = 'Status Update: #' . str_pad((string) $comment->ticket_id, 4, '0', STR_PAD_LEFT);
                    $desc = $comment->body;
                    $typeColor = 'orange';
                    $category = 'System';
                } else {
                    $title = ($isDeveloper ? 'Client Update: #' : 'New Comment: #') . str_pad((string) $comment->ticket_id, 4, '0', STR_PAD_LEFT);
                    $desc = \Illuminate\Support\Str::limit($comment->body, 50);
                    $typeColor = 'blue';
                    $category = 'Discussion';
                }

                return [
                    'title' => $title,
                    'description' => $desc,
                    'time' => $comment->created_at,
                    'time_human' => $this->humanTime($comment->created_at),
                    'is_new' => $isNew,
                    'type' => $typeColor,
                    'category' => $category,
                    'url' => route('tickets.show', $comment->ticket_id),
                ];
            });

        // --- TASKS (Ticket Assigned / Task Updates) ---
        // IMPORTANT: Do not require a linked ticket for filtering.
        // Developers may have tasks without ticket_id, and using whereHas('ticket')
        // would drop those notifications completely.
        $taskQuery = Task::with('ticket')->orderByDesc('updated_at');
        if (!$hasGlobalDataAccess) {
            $taskQuery->where('company_id', $companyId ?: 0);
        }

        $taskItems = $taskQuery
            ->take(20)
            ->get()
            ->map(function (Task $task) use ($isDeveloper) {
                // For task notifications, treat BOTH assignment and updates as "new"
                // (assigned_to changes updated_at, not created_at).
                $isNew = $task->updated_at && $task->updated_at->greaterThan(now()->subDay());
                
                if ($isDeveloper) {
                    $taskId    = str_pad((string) $task->id, 4, '0', STR_PAD_LEFT);
                    $ticketId  = $task->ticket ? str_pad((string) $task->ticket->id, 4, '0', STR_PAD_LEFT) : null;
                    $ticketTxt = $ticketId ? 'Ticket #TK-' . $ticketId : 'No linked ticket';
                    $title     = $ticketId
                        ? 'Task for you: #TK-' . $ticketId
                        : 'New task assigned to you';

                    $desc = $task->ticket
                        ? $ticketTxt . ' · Task #' . $taskId . ' · ' . ($task->title ?: 'Untitled')
                        : 'Task #' . $taskId . ' · ' . ($task->title ?: 'Untitled');
                } else {
                    $taskLabel = $task->title ?: ('Task #' . $task->id);
                    $title = 'Task activity: ' . $taskLabel;
                    $desc = 'Status: ' . strtoupper((string) $task->status) . ($task->ticket ? ' · Linked to ticket #' . str_pad((string) $task->ticket->id, 4, '0', STR_PAD_LEFT) : '');
                }

                $url = $task->ticket_id
                    ? route('tickets.show', $task->ticket_id)
                    : route('tasks.index');

                return [
                    'title' => $title,
                    'description' => $desc,
                    'time' => $task->updated_at,
                    'time_human' => $this->humanTime($task->updated_at),
                    'is_new' => $isNew,
                    'type' => $this->mapTaskType($task->status),
                    'category' => 'Task',
                    'url' => $url,
                ];
            });

        return $ticketItems
            ->merge($commentItems)
            ->merge($taskItems)
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

