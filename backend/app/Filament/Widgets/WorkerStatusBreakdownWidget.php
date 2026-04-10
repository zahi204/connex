<?php

namespace App\Filament\Widgets;

use App\Enums\WorkerStatus;
use App\Models\Worker;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class WorkerStatusBreakdownWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = '60s';

    protected ?string $heading = 'סקירת כוח אדם';

    protected function getStats(): array
    {
        $counts = Worker::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $totalWorkers = array_sum($counts);
        $blockedCount = Worker::where('blocked', true)->count();

        return [
            Stat::make('סה"כ עובדים', $totalWorkers)
                ->icon('heroicon-o-users')
                ->color('primary')
                ->description('כל העובדים במערכת'),
            Stat::make('זמינים', $counts[WorkerStatus::Available->value] ?? 0)
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->description('מוכנים לשיבוץ')
                ->url(route('filament.admin.resources.workers.index', ['tableTab' => 'available'])),
            Stat::make('משובצים', $counts[WorkerStatus::Assigned->value] ?? 0)
                ->icon('heroicon-o-briefcase')
                ->color('info')
                ->description('פעילים בפרויקטים'),
            Stat::make('בהכשרה', $counts[WorkerStatus::InTraining->value] ?? 0)
                ->icon('heroicon-o-academic-cap')
                ->color('primary')
                ->description('במחזור הכשרה פעיל'),
            Stat::make('שיבוץ עתידי', $counts[WorkerStatus::FutureAssignment->value] ?? 0)
                ->icon('heroicon-o-clock')
                ->color('warning')
                ->description('ממתינים לתחילת עבודה'),
            Stat::make('ממתינים', $counts[WorkerStatus::Waiting->value] ?? 0)
                ->icon('heroicon-o-pause-circle')
                ->color('gray')
                ->description('ללא שיבוץ נוכחי'),
            Stat::make('לא פעילים / מוקפאים', ($counts[WorkerStatus::Inactive->value] ?? 0) + ($counts[WorkerStatus::Frozen->value] ?? 0))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->description($blockedCount > 0 ? "{$blockedCount} חסומים" : 'אין חסומים'),
        ];
    }
}
