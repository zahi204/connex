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

    protected int|string|array $columnSpan = [
        'md' => 2,
        'xl' => 1,
    ];

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
                Tables\Columns\TextColumn::make('full_name')
                    ->searchable()
                    ->url(fn (Worker $r) => route('filament.admin.resources.workers.edit', $r)),
                Tables\Columns\TextColumn::make('staffingAgency.agency_name')
                    ->label('סוכנות')
                    ->default('—'),
                Tables\Columns\TextColumn::make('days_overdue')
                    ->label('ימי איחור')
                    ->getStateUsing(fn (Worker $r) => $r->last_payment_date
                        ? Carbon::parse($r->last_payment_date)->diffInDays(now()).'d'
                        : 'לא שולם מעולם'
                    ),
            ])
            ->actions([
                Action::make('record')
                    ->label('רשום תשלום')
                    ->icon('heroicon-o-banknotes')
                    ->url(fn (Worker $r) => route('filament.admin.resources.workers.edit', $r)),
            ])
            ->paginated(false);
    }
}
