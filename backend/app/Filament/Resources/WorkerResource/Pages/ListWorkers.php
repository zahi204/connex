<?php

namespace App\Filament\Resources\WorkerResource\Pages;

use App\Enums\PaymentStatus;
use App\Enums\WorkerStatus;
use App\Filament\Resources\WorkerResource;
use App\Models\Worker;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListWorkers extends ListRecords
{
    protected static string $resource = WorkerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        $counts = Worker::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $totalWorkers = array_sum($counts);
        $overdueCount = Worker::where('payment_status', PaymentStatus::Overdue)->count();
        $blockedCount = Worker::where('blocked', true)->count();

        return [
            'all' => Tab::make('הכל')
                ->badge($totalWorkers)
                ->icon('heroicon-o-users'),
            'available' => Tab::make('זמינים')
                ->badge($counts[WorkerStatus::Available->value] ?? 0)
                ->badgeColor('success')
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn ($query) => $query->where('status', WorkerStatus::Available)),
            'assigned' => Tab::make('משובצים')
                ->badge($counts[WorkerStatus::Assigned->value] ?? 0)
                ->badgeColor('info')
                ->icon('heroicon-o-briefcase')
                ->modifyQueryUsing(fn ($query) => $query->where('status', WorkerStatus::Assigned)),
            'in_training' => Tab::make('בהכשרה')
                ->badge($counts[WorkerStatus::InTraining->value] ?? 0)
                ->badgeColor('primary')
                ->icon('heroicon-o-academic-cap')
                ->modifyQueryUsing(fn ($query) => $query->where('status', WorkerStatus::InTraining)),
            'waiting' => Tab::make('ממתינים')
                ->badge($counts[WorkerStatus::Waiting->value] ?? 0)
                ->badgeColor('gray')
                ->icon('heroicon-o-pause-circle')
                ->modifyQueryUsing(fn ($query) => $query->where('status', WorkerStatus::Waiting)),
            'overdue' => Tab::make('תשלום באיחור')
                ->badge($overdueCount)
                ->badgeColor($overdueCount > 0 ? 'danger' : 'gray')
                ->icon('heroicon-o-exclamation-triangle')
                ->modifyQueryUsing(fn ($query) => $query->where('payment_status', PaymentStatus::Overdue)),
            'blocked' => Tab::make('חסומים')
                ->badge($blockedCount)
                ->badgeColor($blockedCount > 0 ? 'danger' : 'gray')
                ->icon('heroicon-o-no-symbol')
                ->modifyQueryUsing(fn ($query) => $query->where('blocked', true)),
        ];
    }
}
