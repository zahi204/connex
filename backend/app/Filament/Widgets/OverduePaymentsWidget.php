<?php

namespace App\Filament\Widgets;

use App\Enums\PaymentStatus;
use App\Models\Worker;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Carbon;

class OverduePaymentsWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = '60s';

    protected static ?string $heading = 'תשלומים באיחור';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Worker::where('payment_status', PaymentStatus::Overdue)
                    ->with('staffingAgency:id,agency_name')
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
                    ->searchable()
                    ->url(fn (Worker $r) => route('filament.admin.resources.workers.edit', $r)),
                Tables\Columns\TextColumn::make('staffingAgency.agency_name')
                    ->label('סוכנות')
                    ->icon('heroicon-o-building-office')
                    ->default('—'),
                Tables\Columns\TextColumn::make('days_overdue')
                    ->label('ימי איחור')
                    ->getStateUsing(function (Worker $r) {
                        if (! $r->last_payment_date) {
                            return 'לא שולם מעולם';
                        }
                        $days = Carbon::parse($r->last_payment_date)->diffInDays(now());

                        return $days.' ימים';
                    })
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('danger'),
                Tables\Columns\TextColumn::make('primary_skill')
                    ->label('מקצוע')
                    ->badge(),
            ])
            ->actions([
                Action::make('record')
                    ->label('רשום תשלום')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->url(fn (Worker $r) => route('filament.admin.resources.workers.edit', $r)),
            ])
            ->paginated(false)
            ->striped()
            ->emptyStateHeading('אין תשלומים באיחור')
            ->emptyStateDescription('כל העובדים מעודכנים בתשלומים')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
