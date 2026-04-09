<?php

namespace App\Filament\Widgets;

use App\Enums\ApprovalStatus;
use App\Models\ApprovalQueue;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class PendingApprovalsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = [
        'md' => 2,
        'xl' => 1,
    ];

    protected ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $pending = ApprovalQueue::where('status', ApprovalStatus::Pending);
        $total = (clone $pending)->count();
        $stale = (clone $pending)
            ->where('created_at', '<', Carbon::now()->subHours(48))
            ->count();
        $profileUpdates = (clone $pending)
            ->where('action_type', 'profile_update')
            ->count();
        $docUploads = (clone $pending)
            ->where('action_type', 'document_upload')
            ->count();
        $availChanges = (clone $pending)
            ->where('action_type', 'availability_change')
            ->count();

        return [
            Stat::make('סה"כ ממתינים', $total)
                ->icon('heroicon-o-clock')
                ->color($total > 0 ? 'warning' : 'success')
                ->url(route('filament.admin.pages.approval-queue')),
            Stat::make('ישנים (> 48 שעות)', $stale)
                ->icon('heroicon-o-exclamation-triangle')
                ->color($stale > 0 ? 'danger' : 'success'),
            Stat::make('עדכוני פרופיל', $profileUpdates)
                ->icon('heroicon-o-user'),
            Stat::make('העלאות מסמכים', $docUploads)
                ->icon('heroicon-o-document'),
            Stat::make('שינויי זמינות', $availChanges)
                ->icon('heroicon-o-calendar'),
        ];
    }
}
