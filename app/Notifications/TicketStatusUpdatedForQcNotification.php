<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class TicketStatusUpdatedForQcNotification extends Notification
{
    use Queueable;

    public $model;
    public $type;

    public function __construct($model, $type)
    {
        $this->model = $model;
        $this->type = $type;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $identifier = '#' . str_pad($this->model->id ?? 0, 4, '0', STR_PAD_LEFT);

        $url = route('tasks.index');
        if ($this->type === 'Ticket') {
            $url = route('tickets.show', $this->model);
        } elseif ($this->type === 'Task' && !empty($this->model->ticket_id)) {
            $url = route('tickets.show', $this->model->ticket_id);
        }

        $title = $this->model->title ?? $this->type;
        // e.g., in_progress -> In Progress
        $status = ucwords(str_replace('_', ' ', $this->model->status));

        return (new MailMessage)
            ->subject("QC Required: {$identifier} marked as {$status}")
            ->greeting("Hello {$notifiable->name},")
            ->line(new HtmlString("A {$this->type} has transitioned to <strong>{$status}</strong> and requires QC attention."))
            ->line(new HtmlString("<strong>{$this->type} ID:</strong> {$identifier}"))
            ->line(new HtmlString("<strong>Title:</strong> {$title}"))
            ->action("View {$this->type}", $url);
    }
}
