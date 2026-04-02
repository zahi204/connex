<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PendingApprovalReminderNotification extends Notification
{
    use Queueable;

    public function __construct(
        public int $count,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'pending_approval_reminder',
            'message' => "{$this->count} approval item(s) have been pending for more than 48 hours.",
            'count' => $this->count,
        ];
    }
}
