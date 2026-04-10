<?php

namespace App\Filament\Widgets;

use App\Models\Assignment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ActiveAssignmentsWidget extends BaseWidget
{
    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = '60s';

    protected ?string $heading = 'שיבוצים פעילים';

    protected function getStats(): array
    {
        $active = Assignment::whereIn('status', ['active', 'new'])->count();

        $byType = Assignment::whereIn('status', ['active', 'new'])
            ->selectRaw('resource_type, COUNT(*) as cnt')
            ->groupBy('resource_type')
            ->pluck('cnt', 'resource_type')
            ->mapWithKeys(fn ($v, $k) => [class_basename($k) => $v])
            ->toArray();

        $stats = [
            Stat::make('סה"כ שיבוצים פעילים', $active)
                ->icon('heroicon-o-briefcase')
                ->color('info')
                ->description('שיבוצים בסטטוס פעיל או חדש'),
        ];

        foreach ($byType as $type => $count) {
            $icon = $type === 'Worker' ? 'heroicon-o-user' : 'heroicon-o-building-office';
            $label = $type === 'Worker' ? 'עובדים' : 'קבלנים';
            $stats[] = Stat::make("שיבוצי {$label}", $count)
                ->icon($icon)
                ->color($type === 'Worker' ? 'primary' : 'warning');
        }

        return $stats;
    }
}
