<?php

namespace App\Actions\Approval;

use App\Models\ApprovalQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendApprovalNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public ApprovalQueue $item,
    ) {}

    public function handle(): void
    {
        $user = $this->item->submittedBy;
        if (! $user) {
            return;
        }

        $status = $this->item->status->value;
        $type = $this->item->action_type->label();

        $message = match ($status) {
            'approved' => "Your {$type} has been approved.",
            'rejected' => "Your {$type} has been rejected. Reason: {$this->item->rejection_reason}",
            'changes_required' => "Changes requested for your {$type}. Notes: {$this->item->change_notes}",
            default => null,
        };

        if (! $message) {
            return;
        }

        // Send via database notification
        $user->notify(new \App\Notifications\ApprovalStatusNotification(
            $this->item,
            $message,
        ));

        // In production, also send SMS/WhatsApp
        Log::info("Approval notification for user {$user->phone}: {$message}");
    }
}
