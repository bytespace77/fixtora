<?php

namespace App\Providers;

use App\Models\Task;
use App\Models\Ticket;
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
        $ticketItems = Ticket::latest()
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

        $commentItems = \App\Models\TicketComment::with('ticket')
            ->latest()
            ->take(20)
            ->get()
            ->map(function (\App\Models\TicketComment $comment) {
                $isNew = $comment->created_at && $comment->created_at->greaterThan(now()->subDay());

                if ($comment->type === 'status_change') {
                    $title = 'Status Update: #' . str_pad((string) $comment->ticket_id, 4, '0', STR_PAD_LEFT);
                    $desc = $comment->body;
                    $typeColor = 'orange';
                } else {
                    $title = 'New Comment: #' . str_pad((string) $comment->ticket_id, 4, '0', STR_PAD_LEFT);
                    $desc = \Illuminate\Support\Str::limit($comment->body, 50);
                    $typeColor = 'blue';
                }

                return [
                    'title' => $title,
                    'description' => $desc,
                    'time' => $comment->created_at,
                    'time_human' => $this->humanTime($comment->created_at),
                    'is_new' => $isNew,
                    'type' => $typeColor,
                    'category' => $comment->type === 'status_change' ? 'System' : 'Discussion',
                    'url' => route('tickets.show', $comment->ticket_id),
                ];
            });

        $taskItems = Task::with('ticket')
            ->latest()
            ->take(20)
            ->get()
            ->map(function (Task $task) {
                $isNew = $task->created_at && $task->created_at->greaterThan(now()->subDay());
                $taskLabel = $task->title ?: ('Task #' . $task->id);

                return [
                    'title' => 'Task activity: ' . $taskLabel,
                    'description' => 'Status: ' . strtoupper((string) $task->status) . ($task->ticket ? ' · Linked to ticket #' . str_pad((string) $task->ticket->id, 4, '0', STR_PAD_LEFT) : ''),
                    'time' => $task->updated_at,
                    'time_human' => $this->humanTime($task->updated_at),
                    'is_new' => $isNew,
                    'type' => $this->mapTaskType($task->status),
                    'category' => 'Task',
                    'url' => route('tasks.index'),
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
}

