<?php

namespace App\Filament\Widgets;

use App\Models\Assignment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ActiveAssignmentsWidget extends BaseWidget
{
    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = [
        'md' => 2,
        'xl' => 1,
    ];

    protected ?string $pollingInterval = '60s';

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
            Stat::make('שיבוצים פעילים', $active)
                ->icon('heroicon-o-briefcase')
                ->color('info'),
        ];

        foreach ($byType as $type => $count) {
            $stats[] = Stat::make("{$type} שיבוצים", $count)->icon('heroicon-o-users');
        }

        return $stats;
    }
}
