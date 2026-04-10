<?php

namespace App\Filament\Widgets;

use App\Enums\TrainingCycleStatus;
use App\Models\TrainingCycle;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TrainingPipelineWidget extends BaseWidget
{
    protected static ?int $sort = 6;

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = '60s';

    protected static ?string $heading = 'צינור הכשרות';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                TrainingCycle::whereIn('status', [
                    TrainingCycleStatus::Planned,
                    TrainingCycleStatus::InProgress,
                ])
                    ->withCount('results')
                    ->orderBy('start_date')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('מחזור')
                    ->weight('bold')
                    ->icon('heroicon-o-academic-cap'),
                Tables\Columns\TextColumn::make('status')
                    ->label('סטטוס')
                    ->badge()
                    ->color(fn (TrainingCycleStatus $state): string => match ($state) {
                        TrainingCycleStatus::InProgress => 'success',
                        TrainingCycleStatus::Planned => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('תחילה')
                    ->date('d/m/Y')
                    ->icon('heroicon-o-calendar'),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('סיום')
                    ->date('d/m/Y'),
                Tables\Columns\TextColumn::make('results_count')
                    ->label('רשומים')
                    ->icon('heroicon-o-users')
                    ->badge()
                    ->color('primary'),
            ])
            ->paginated(false)
            ->striped()
            ->emptyStateHeading('אין מחזורי הכשרה פעילים')
            ->emptyStateDescription('צור מחזור הכשרה חדש כדי להתחיל')
            ->emptyStateIcon('heroicon-o-academic-cap');
    }
}
