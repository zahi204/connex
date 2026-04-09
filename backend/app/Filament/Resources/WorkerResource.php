<?php

namespace App\Filament\Resources;

use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Enums\Skill;
use App\Enums\WorkArea;
use App\Enums\WorkerStatus;
use App\Filament\Resources\WorkerResource\Pages\CreateWorker;
use App\Filament\Resources\WorkerResource\Pages\EditWorker;
use App\Filament\Resources\WorkerResource\Pages\ListWorkers;
use App\Filament\Resources\WorkerResource\RelationManagers;
use App\Models\Payment;
use App\Models\StaffingAgency;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\TrainingCycle;
use App\Models\TrainingResult;
use App\Models\Worker;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Form as FormContainer;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class WorkerResource extends Resource
{
    protected static ?string $model = Worker::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|\UnitEnum|null $navigationGroup = 'משאבים';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'עובד';

    protected static ?string $pluralModelLabel = 'עובדים';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                FormContainer::make([
                    Tabs::make('Worker')
                        ->tabs([
                            Tab::make('פרטים אישיים')
                                ->schema([
                                    Forms\Components\TextInput::make('full_name')
                                        ->required()
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('id_number')
                                        ->maxLength(50),
                                    Forms\Components\SpatieMediaLibraryFileUpload::make('photo')
                                        ->collection('photo')
                                        ->image()
                                        ->maxSize(5120),
                                    Forms\Components\Select::make('user_id')
                                        ->relationship('user', 'phone')
                                        ->searchable()
                                        ->preload(),
                                    Forms\Components\TextInput::make('country_of_origin')
                                        ->maxLength(100),
                                    Forms\Components\TagsInput::make('languages'),
                                    Forms\Components\DatePicker::make('date_of_arrival'),
                                ]),
                            Tab::make('מקצועי')
                                ->schema([
                                    Forms\Components\Select::make('primary_skill')
                                        ->options(Skill::class),
                                    Forms\Components\Select::make('secondary_skill')
                                        ->options(Skill::class),
                                    Forms\Components\Textarea::make('previous_experience')
                                        ->rows(3),
                                    Forms\Components\TextInput::make('professional_rating')
                                        ->numeric()
                                        ->step(0.01)
                                        ->minValue(0)
                                        ->maxValue(10),
                                    Forms\Components\TextInput::make('reliability_rating')
                                        ->numeric()
                                        ->step(0.01)
                                        ->minValue(0)
                                        ->maxValue(10),
                                    Forms\Components\Toggle::make('suitable_for_leader'),
                                    Forms\Components\Select::make('status')
                                        ->options(WorkerStatus::class)
                                        ->required(),
                                    Forms\Components\Select::make('preferred_work_area')
                                        ->options(WorkArea::class),
                                    Forms\Components\Toggle::make('available_daily'),
                                    Forms\Components\Toggle::make('available_contract'),
                                    Forms\Components\Toggle::make('eligible_for_assignment')
                                        ->default(true),
                                    Forms\Components\Toggle::make('blocked'),
                                ]),
                            Tab::make('הכשרה')
                                ->schema([
                                    Forms\Components\TextInput::make('training_score')
                                        ->numeric()
                                        ->step(0.01)
                                        ->disabled(),
                                    Forms\Components\DatePicker::make('last_training_date')
                                        ->disabled(),
                                ]),
                            Tab::make('תשלום')
                                ->schema([
                                    Forms\Components\Select::make('payment_status')
                                        ->options(PaymentStatus::class),
                                    Forms\Components\DatePicker::make('last_payment_date'),
                                ]),
                            Tab::make('סוכנות')
                                ->schema([
                                    Forms\Components\Select::make('staffing_agency_id')
                                        ->relationship('staffingAgency', 'agency_name')
                                        ->searchable()
                                        ->preload(),
                                ]),
                            Tab::make('הערות')
                                ->schema([
                                    Forms\Components\Textarea::make('general_notes')
                                        ->rows(4),
                                ]),
                            Tab::make('היסטוריה')
                                ->schema([
                                    Forms\Components\Placeholder::make('activity_log')
                                        ->label('יומן פעילות')
                                        ->content(function (?Worker $record): string {
                                            if (! $record?->id) {
                                                return 'אין פעילות רשומה עדיין.';
                                            }
                                            $activities = activity()->performedOn($record)
                                                ->latest()
                                                ->limit(30)
                                                ->get();
                                            if ($activities->isEmpty()) {
                                                return 'אין פעילות רשומה עדיין.';
                                            }

                                            return $activities->map(fn ($a) => "[{$a->created_at->format('Y-m-d H:i')}] "
                                                .($a->causer?->name ?? 'מערכת')
                                                .' — '.$a->description
                                            )->implode("\n");
                                        }),
                                ]),
                        ])
                        ->columnSpanFull(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('photo')
                    ->collection('photo')
                    ->circular(),
                Tables\Columns\TextColumn::make('full_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('primary_skill')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (WorkerStatus $state): string => match ($state) {
                        WorkerStatus::Available => 'success',
                        WorkerStatus::Assigned => 'info',
                        WorkerStatus::FutureAssignment => 'warning',
                        WorkerStatus::InTraining => 'primary',
                        WorkerStatus::Waiting => 'gray',
                        WorkerStatus::Inactive => 'danger',
                        WorkerStatus::Frozen => 'danger',
                    }),
                Tables\Columns\TextColumn::make('preferred_work_area')
                    ->badge(),
                Tables\Columns\TextColumn::make('professional_rating')
                    ->numeric(2)
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_status')
                    ->badge()
                    ->color(fn (?PaymentStatus $state): string => match ($state) {
                        PaymentStatus::Paid => 'success',
                        PaymentStatus::Overdue => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('blocked')
                    ->boolean()
                    ->trueIcon('heroicon-o-no-symbol')
                    ->trueColor('danger')
                    ->falseIcon('heroicon-o-check-circle')
                    ->falseColor('success'),
                Tables\Columns\IconColumn::make('available_daily')
                    ->boolean(),
                Tables\Columns\TextColumn::make('last_payment_date')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('staffingAgency.agency_name')
                    ->label('סוכנות')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(WorkerStatus::class),
                Tables\Filters\SelectFilter::make('primary_skill')
                    ->options(Skill::class),
                Tables\Filters\SelectFilter::make('preferred_work_area')
                    ->options(WorkArea::class),
                Tables\Filters\SelectFilter::make('payment_status')
                    ->options(PaymentStatus::class),
                Tables\Filters\TernaryFilter::make('blocked')
                    ->label('חסום'),
                Tables\Filters\TernaryFilter::make('eligible_for_assignment')
                    ->label('כשיר לשיבוץ'),
                Tables\Filters\SelectFilter::make('staffing_agency_id')
                    ->label('סוכנות')
                    ->options(fn () => StaffingAgency::pluck('agency_name', 'id')->toArray())
                    ->searchable(),
                Tables\Filters\Filter::make('overdue')
                    ->label('תשלום באיחור')
                    ->query(fn (Builder $q) => $q->where('payment_status', PaymentStatus::Overdue))
                    ->toggle(),
                Tables\Filters\Filter::make('needs_training')
                    ->label('זקוק להכשרה')
                    ->query(fn (Builder $q) => $q->where(function ($q2) {
                        $q2->whereNull('last_training_date')
                            ->orWhere('last_training_date', '<', Carbon::now()->subYear());
                    }))
                    ->toggle(),
                Tables\Filters\Filter::make('top_rated')
                    ->label('מדורג גבוה (≥ 8)')
                    ->query(fn (Builder $q) => $q->where('professional_rating', '>=', 8))
                    ->toggle(),
                Tables\Filters\Filter::make('without_team')
                    ->label('ללא צוות')
                    ->query(fn (Builder $q) => $q->whereDoesntHave(
                        'teamMemberships',
                        fn ($q2) => $q2->whereNull('left_at')
                    ))
                    ->toggle(),
            ])
            ->headerActions([
                Action::make('enroll_in_training')
                    ->label('רשום להכשרה')
                    ->icon('heroicon-o-academic-cap')
                    ->form([
                        Forms\Components\Select::make('training_cycle_id')
                            ->label('מחזור הכשרה')
                            ->options(fn () => TrainingCycle::where('status', 'open')
                                ->pluck('name', 'id')
                                ->toArray())
                            ->required()
                            ->searchable(),
                        Forms\Components\Select::make('worker_ids')
                            ->label('עובדים')
                            ->options(fn () => Worker::pluck('full_name', 'id')->toArray())
                            ->multiple()
                            ->required()
                            ->searchable(),
                    ])
                    ->action(function (array $data): void {
                        $cycleId = $data['training_cycle_id'];
                        $workerIds = $data['worker_ids'];

                        DB::transaction(function () use ($cycleId, $workerIds): void {
                            foreach ($workerIds as $workerId) {
                                TrainingResult::firstOrCreate(
                                    ['training_cycle_id' => $cycleId, 'worker_id' => $workerId],
                                    []
                                );
                                Worker::where('id', $workerId)->update(['status' => WorkerStatus::InTraining]);
                                activity()
                                    ->performedOn(Worker::find($workerId))
                                    ->causedBy(auth()->user())
                                    ->log("Enrolled in training cycle #{$cycleId}");
                            }
                        });

                        Notification::make()
                            ->title(count($workerIds).' עובדים נרשמו להכשרה.')
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                Action::make('change_status')
                    ->label('שינוי סטטוס')
                    ->icon('heroicon-o-arrow-path')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->options(WorkerStatus::class)
                            ->required(),
                        Forms\Components\Textarea::make('reason')
                            ->label('סיבה')
                            ->rows(2),
                    ])
                    ->action(function (Worker $record, array $data): void {
                        $old = $record->status?->value;
                        $record->update(['status' => $data['status']]);
                        activity()
                            ->performedOn($record)
                            ->causedBy(auth()->user())
                            ->withProperties(['old_status' => $old, 'new_status' => $data['status'], 'reason' => $data['reason'] ?? null])
                            ->log("Status changed from {$old} to {$data['status']}");
                        Notification::make()->title('הסטטוס עודכן.')->success()->send();
                    }),
                Action::make('block_toggle')
                    ->label(fn (Worker $record) => $record->blocked ? 'שחרר חסימה' : 'חסום')
                    ->icon(fn (Worker $record) => $record->blocked ? 'heroicon-o-lock-open' : 'heroicon-o-no-symbol')
                    ->color(fn (Worker $record) => $record->blocked ? 'success' : 'danger')
                    ->requiresConfirmation()
                    ->action(function (Worker $record): void {
                        $blocking = ! $record->blocked;
                        $record->update([
                            'blocked' => $blocking,
                            'eligible_for_assignment' => $blocking ? false : $record->eligible_for_assignment,
                        ]);
                        activity()
                            ->performedOn($record)
                            ->causedBy(auth()->user())
                            ->log($blocking ? 'Worker blocked' : 'Worker unblocked');
                        Notification::make()
                            ->title($blocking ? 'העובד נחסם.' : 'חסימת העובד בוטלה.')
                            ->success()
                            ->send();
                    }),
                Action::make('assign_to_team')
                    ->label('שבץ לצוות')
                    ->icon('heroicon-o-user-plus')
                    ->form([
                        Forms\Components\Select::make('team_id')
                            ->label('צוות')
                            ->options(fn () => Team::whereHas('members', fn ($q) => $q->whereNull('left_at'))
                                ->orDoesntHave('members')
                                ->pluck('name', 'id')
                                ->toArray())
                            ->required()
                            ->searchable(),
                    ])
                    ->action(function (Worker $record, array $data): void {
                        // Close any existing open membership
                        $record->teamMemberships()
                            ->whereNull('left_at')
                            ->update(['left_at' => now()]);

                        TeamMember::create([
                            'team_id' => $data['team_id'],
                            'worker_id' => $record->id,
                            'joined_at' => now(),
                        ]);

                        activity()
                            ->performedOn($record)
                            ->causedBy(auth()->user())
                            ->withProperties(['team_id' => $data['team_id']])
                            ->log("Assigned to team #{$data['team_id']}");

                        Notification::make()->title('העובד שובץ לצוות.')->success()->send();
                    }),
                Action::make('record_payment')
                    ->label('רשום תשלום')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->form([
                        Forms\Components\TextInput::make('amount')
                            ->label('סכום (₪)')
                            ->numeric()
                            ->required()
                            ->minValue(0.01),
                        Forms\Components\DatePicker::make('payment_date')
                            ->label('תאריך תשלום')
                            ->default(now())
                            ->required(),
                        Forms\Components\Select::make('payment_type')
                            ->label('סוג תשלום')
                            ->options(PaymentType::class)
                            ->required(),
                        Forms\Components\Textarea::make('notes')
                            ->rows(2),
                    ])
                    ->action(function (Worker $record, array $data): void {
                        Payment::create([
                            'payable_type' => Worker::class,
                            'payable_id' => $record->id,
                            'payment_type' => $data['payment_type'],
                            'amount' => $data['amount'],
                            'payment_date' => $data['payment_date'],
                            'recorded_by_user_id' => auth()->id(),
                            'notes' => $data['notes'] ?? null,
                        ]);

                        $record->update([
                            'payment_status' => PaymentStatus::Paid,
                            'last_payment_date' => $data['payment_date'],
                        ]);

                        activity()
                            ->performedOn($record)
                            ->causedBy(auth()->user())
                            ->withProperties(['amount' => $data['amount'], 'payment_date' => $data['payment_date']])
                            ->log("Payment of {$data['amount']} ILS recorded");

                        Notification::make()->title('התשלום נרשם.')->success()->send();
                    }),
                EditAction::make(),
                ViewAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('bulk_change_status')
                        ->label('שינוי סטטוס')
                        ->icon('heroicon-o-arrow-path')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->options(WorkerStatus::class)
                                ->required(),
                            Forms\Components\Textarea::make('reason')
                                ->rows(2),
                        ])
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records, array $data): void {
                            foreach ($records as $worker) {
                                $old = $worker->status?->value;
                                $worker->update(['status' => $data['status']]);
                                activity()
                                    ->performedOn($worker)
                                    ->causedBy(auth()->user())
                                    ->withProperties(['old_status' => $old, 'new_status' => $data['status']])
                                    ->log("Bulk status change: {$old} → {$data['status']}");
                            }
                            Notification::make()->title("{$records->count()} עובדים עודכנו.")->success()->send();
                        }),
                    BulkAction::make('bulk_enroll_training')
                        ->label('רשום להכשרה')
                        ->icon('heroicon-o-academic-cap')
                        ->form([
                            Forms\Components\Select::make('training_cycle_id')
                                ->label('מחזור הכשרה')
                                ->options(fn () => TrainingCycle::whereIn('status', ['planned', 'in_progress'])
                                    ->pluck('name', 'id')
                                    ->toArray())
                                ->required()
                                ->searchable(),
                        ])
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records, array $data): void {
                            $cycleId = $data['training_cycle_id'];

                            DB::transaction(function () use ($records, $cycleId): void {
                                foreach ($records as $worker) {
                                    TrainingResult::firstOrCreate(
                                        ['training_cycle_id' => $cycleId, 'worker_id' => $worker->id],
                                        []
                                    );
                                    $worker->update(['status' => WorkerStatus::InTraining]);
                                    activity()
                                        ->performedOn($worker)
                                        ->causedBy(auth()->user())
                                        ->log("Bulk enrolled in training cycle #{$cycleId}");
                                }
                            });

                            Notification::make()
                                ->title("{$records->count()} עובדים נרשמו להכשרה.")
                                ->success()
                                ->send();
                        }),
                    BulkAction::make('bulk_block')
                        ->label('חסום נבחרים')
                        ->icon('heroicon-o-no-symbol')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records): void {
                            foreach ($records as $worker) {
                                $worker->update(['blocked' => true, 'eligible_for_assignment' => false]);
                                activity()->performedOn($worker)->causedBy(auth()->user())->log('Bulk blocked');
                            }
                            Notification::make()->title("{$records->count()} עובדים נחסמו.")->success()->send();
                        }),
                    BulkAction::make('bulk_unblock')
                        ->label('שחרר חסימה לנבחרים')
                        ->icon('heroicon-o-lock-open')
                        ->color('success')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->action(function (Collection $records): void {
                            foreach ($records as $worker) {
                                $worker->update(['blocked' => false]);
                                activity()->performedOn($worker)->causedBy(auth()->user())->log('Bulk unblocked');
                            }
                            Notification::make()->title("{$records->count()} חסימות עובדים בוטלו.")->success()->send();
                        }),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TrainingResultsRelationManager::class,
            RelationManagers\AssignmentsRelationManager::class,
            RelationManagers\DocumentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWorkers::route('/'),
            'create' => CreateWorker::route('/create'),
            'edit' => EditWorker::route('/{record}/edit'),
        ];
    }
}
