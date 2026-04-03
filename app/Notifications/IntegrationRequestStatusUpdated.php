<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\IntegrationRequest;

class IntegrationRequestStatusUpdated extends Notification
{
    use Queueable;

    public $integrationRequest;

    public function __construct(IntegrationRequest $integrationRequest)
    {
        $this->integrationRequest = $integrationRequest;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $status = ucfirst($this->integrationRequest->status);
        $tool = $this->integrationRequest->requested_integration;

        return [
            'title' => "Integration Request {$status}",
            'message' => "Your request to connect {$tool} has been marked as {$status}.",
            'link' => route('integrations.requests.index'),
            'type' => 'integration_request_status',
        ];
    }
}
