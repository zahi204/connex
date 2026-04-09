<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ActiveAssignmentsWidget;
use App\Filament\Widgets\OverduePaymentsWidget;
use App\Filament\Widgets\PendingApprovalsWidget;
use App\Filament\Widgets\TopRatedAvailableWidget;
use App\Filament\Widgets\TrainingPipelineWidget;
use App\Filament\Widgets\WorkerStatusBreakdownWidget;
use Filament\Pages\Page;

class WorkerControlDashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static string|\UnitEnum|null $navigationGroup = 'תפעול';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'מרכז בקרת עובדים';

    protected static ?string $title = 'מרכז בקרת עובדים';

    protected string $view = 'filament.pages.worker-control-dashboard';

    public function getWidgets(): array
    {
        return [
            WorkerStatusBreakdownWidget::class,
            PendingApprovalsWidget::class,
            OverduePaymentsWidget::class,
            TopRatedAvailableWidget::class,
            ActiveAssignmentsWidget::class,
            TrainingPipelineWidget::class,
        ];
    }

    public function getColumns(): int|string|array
    {
        return 2;
    }
}
