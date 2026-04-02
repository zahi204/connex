<?php

namespace App\Notifications;

use App\Models\ApprovalQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ApprovalStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        public ApprovalQueue $item,
        public string $message,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'approval_id' => $this->item->id,
            'action_type' => $this->item->action_type->value,
            'status' => $this->item->status->value,
            'message' => $this->message,
        ];
    }
}
