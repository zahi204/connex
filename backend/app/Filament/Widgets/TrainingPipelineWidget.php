<?php

namespace App\Filament\Widgets;

use App\Models\TrainingCycle;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TrainingPipelineWidget extends BaseWidget
{
    protected static ?int $sort = 6;

    protected int|string|array $columnSpan = [
        'md' => 2,
        'xl' => 1,
    ];

    protected ?string $pollingInterval = '60s';

    protected static ?string $heading = 'צינור הכשרות';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                TrainingCycle::whereIn('status', ['planned', 'in_progress'])
                    ->withCount('results')
                    ->orderBy('start_date')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('מחזור'),
                Tables\Columns\TextColumn::make('start_date')
                    ->date(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date(),
                Tables\Columns\TextColumn::make('results_count')
                    ->label('רשומים'),
            ])
            ->paginated(false);
    }
}
