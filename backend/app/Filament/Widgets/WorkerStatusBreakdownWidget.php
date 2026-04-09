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

    protected function getStats(): array
    {
        $counts = Worker::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return [
            Stat::make('זמין', $counts[WorkerStatus::Available->value] ?? 0)
                ->icon('heroicon-o-check-circle')
                ->color('success'),
            Stat::make('משובץ', $counts[WorkerStatus::Assigned->value] ?? 0)
                ->icon('heroicon-o-briefcase')
                ->color('info'),
            Stat::make('בהכשרה', $counts[WorkerStatus::InTraining->value] ?? 0)
                ->icon('heroicon-o-academic-cap')
                ->color('primary'),
            Stat::make('שיבוץ עתידי', $counts[WorkerStatus::FutureAssignment->value] ?? 0)
                ->icon('heroicon-o-clock')
                ->color('warning'),
            Stat::make('ממתין', $counts[WorkerStatus::Waiting->value] ?? 0)
                ->icon('heroicon-o-pause-circle')
                ->color('gray'),
            Stat::make('לא פעיל / מוקפא', ($counts[WorkerStatus::Inactive->value] ?? 0) + ($counts[WorkerStatus::Frozen->value] ?? 0))
                ->icon('heroicon-o-x-circle')
                ->color('danger'),
        ];
    }
}
