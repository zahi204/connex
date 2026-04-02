<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\ApprovalQueue;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class RemindPendingApprovals extends Command
{
    protected $signature = 'approval:remind-pending';

    protected $description = 'Notify admins about approval items pending for more than 48 hours';

    public function handle(): int
    {
        $staleCount = ApprovalQueue::stale()->count();

        if ($staleCount === 0) {
            $this->info('No stale approval items found.');
            return self::SUCCESS;
        }

        $admins = User::whereIn('role', [UserRole::Admin, UserRole::Coordinator])->get();

        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\PendingApprovalReminderNotification($staleCount));
        }

        $this->info("Notified {$admins->count()} admin(s) about {$staleCount} stale approval item(s).");

        return self::SUCCESS;
    }
}
