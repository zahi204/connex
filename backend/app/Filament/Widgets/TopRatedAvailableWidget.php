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

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = '60s';

    protected static ?string $heading = 'עובדים זמינים בדירוג גבוה';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Worker::where('status', WorkerStatus::Available)
                    ->where('blocked', false)
                    ->whereNotNull('professional_rating')
                    ->orderByDesc('professional_rating')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('photo')
                    ->label('')
                    ->collection('photo')
                    ->circular()
                    ->size(36),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('שם')
                    ->weight('bold')
                    ->url(fn (Worker $r) => route('filament.admin.resources.workers.edit', $r))
                    ->description(fn (Worker $r): ?string => $r->country_of_origin),
                Tables\Columns\TextColumn::make('primary_skill')
                    ->label('מקצוע')
                    ->badge()
                    ->icon('heroicon-o-wrench-screwdriver'),
                Tables\Columns\TextColumn::make('professional_rating')
                    ->label('דירוג')
                    ->numeric(1)
                    ->sortable()
                    ->icon('heroicon-o-star')
                    ->color(fn (?float $state): string => $state >= 8.0 ? 'success' : 'warning'),
                Tables\Columns\TextColumn::make('reliability_rating')
                    ->label('אמינות')
                    ->numeric(1)
                    ->icon('heroicon-o-shield-check'),
                Tables\Columns\TextColumn::make('preferred_work_area')
                    ->label('אזור')
                    ->badge()
                    ->icon('heroicon-o-map-pin'),
            ])
            ->paginated(false)
            ->striped();
    }
}
