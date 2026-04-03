<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewIntegrationRequest extends Notification
{
    use Queueable;

    public $requestData;

    public function __construct($requestData)
    {
        $this->requestData = $requestData;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $companyName = $this->requestData['company'] ?? 'A user';
        return [
            'title' => 'New Integration Request',
            'message' => "{$companyName} has requested a custom integration for {$this->requestData['requested_integration']}.",
            'link' => route('integrations.requests.index'),
            'type' => 'integration_request',
        ];
    }
}
