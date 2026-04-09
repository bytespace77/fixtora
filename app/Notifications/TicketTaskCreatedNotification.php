<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class TicketTaskCreatedNotification extends Notification
{
    use Queueable;

    public $model;
    public $type;
    public $recipientType;

    public function __construct($model, $type, $recipientType)
    {
        $this->model = $model;
        $this->type = $type;
        $this->recipientType = $recipientType;
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

        $title = $this->model->title ?? 'New ' . $this->type;

        return (new MailMessage)
            ->subject("New {$this->type} Created: {$identifier}")
            ->greeting("Hello {$notifiable->name},")
            ->line("A new {$this->type} has been created in the system.")
            ->line(new HtmlString("<strong>{$this->type} ID:</strong> {$identifier}"))
            ->line(new HtmlString("<strong>Title:</strong> {$title}"))
            ->action("View {$this->type}", $url);
    }
}
