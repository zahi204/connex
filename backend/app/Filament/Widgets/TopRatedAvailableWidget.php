<?php

namespace App\Filament\Widgets;

use App\Enums\WorkerStatus;
use App\Models\Worker;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TopRatedAvailableWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = [
        'md' => 2,
        'xl' => 1,
    ];

    protected ?string $pollingInterval = '60s';

    protected static ?string $heading = 'עובדים זמינים בדירוג גבוה';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Worker::where('status', WorkerStatus::Available)
                    ->whereNotNull('professional_rating')
                    ->orderByDesc('professional_rating')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->url(fn (Worker $r) => route('filament.admin.resources.workers.edit', $r)),
                Tables\Columns\TextColumn::make('primary_skill')
                    ->badge(),
                Tables\Columns\TextColumn::make('professional_rating')
                    ->label('דירוג')
                    ->numeric(2)
                    ->sortable(),
                Tables\Columns\TextColumn::make('preferred_work_area')
                    ->badge(),
            ])
            ->paginated(false);
    }
}
