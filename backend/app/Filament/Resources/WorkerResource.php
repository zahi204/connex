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
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class WorkerResource extends Resource
{
    protected static ?string $model = Worker::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|\UnitEnum|null $navigationGroup = 'משאבים';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'עובד';

    protected static ?string $pluralModelLabel = 'עובדים';

    protected static ?string $recordTitleAttribute = 'full_name';

    public static function getGloballySearchableAttributes(): array
    {
        return ['full_name', 'id_number'];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Tabs::make('Worker')
                    ->tabs([
                            Tab::make('פרטים אישיים')
                                ->icon('heroicon-o-user')
                                ->schema([
                                    Grid::make(3)
                                        ->schema([
                                            Section::make('תמונה ומזהה')
                                                ->icon('heroicon-o-identification')
                                                ->columnSpan(1)
                                                ->schema([
                                                    Forms\Components\SpatieMediaLibraryFileUpload::make('photo')
                                                        ->label('תמונה')
                                                        ->collection('photo')
                                                        ->image()
                                                        ->imageEditor()
                                                        ->circleCropper()
                                                        ->maxSize(5120),
                                                    Forms\Components\TextInput::make('id_number')
                                                        ->label('מס׳ זהות')
                                                        ->maxLength(50)
                                                        ->prefixIcon('heroicon-o-finger-print'),
                                                ]),
                                            Section::make('פרטים אישיים')
                                                ->icon('heroicon-o-user-circle')
                                                ->columnSpan(2)
                                                ->schema([
                                                    Grid::make(2)
                                                        ->schema([
                                                            Forms\Components\TextInput::make('full_name')
                                                                ->label('שם מלא')
                                                                ->required()
                                                                ->maxLength(255)
                                                                ->prefixIcon('heroicon-o-user'),
                                                            Forms\Components\TextInput::make('phone')
                                                                ->label('טלפון')
                                                                ->required()
                                                                ->tel()
                                                                ->placeholder('+972501234567')
                                                                ->helperText('מספר הטלפון של העובד. אם לא קיים משתמש, ייווצר אוטומטית.')
                                                                ->prefixIcon('heroicon-o-device-phone-mobile')
                                                                ->afterStateHydrated(function (Forms\Components\TextInput $component, ?Worker $record) {
                                                                    if ($record?->user) {
                                                                        $component->state($record->user->phone);
                                                                    }
                                                                }),
                                                            Forms\Components\TextInput::make('country_of_origin')
                                                                ->label('ארץ מוצא')
                                                                ->maxLength(100)
                                                                ->prefixIcon('heroicon-o-globe-alt'),
                                                            Forms\Components\DatePicker::make('date_of_arrival')
                                                                ->label('תאריך הגעה')
                                                                ->prefixIcon('heroicon-o-calendar'),
                                                            Forms\Components\TagsInput::make('languages')
                                                                ->label('שפות')
                                                                ->columnSpanFull(),
                                                        ]),
                                                ]),
                                        ]),
                                ]),

                            Tab::make('מקצועי')
                                ->icon('heroicon-o-wrench-screwdriver')
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            Section::make('מיומנויות')
                                                ->icon('heroicon-o-cog-6-tooth')
                                                ->schema([
                                                    Grid::make(2)
                                                        ->schema([
                                                            Forms\Components\Select::make('primary_skill')
                                                                ->label('מקצוע ראשי')
                                                                ->options(Skill::class)
                                                                ->prefixIcon('heroicon-o-star'),
                                                            Forms\Components\Select::make('secondary_skill')
                                                                ->label('מקצוע משני')
                                                                ->options(Skill::class)
                                                                ->prefixIcon('heroicon-o-star'),
                                                        ]),
                                                    Forms\Components\Textarea::make('previous_experience')
                                                        ->label('ניסיון קודם')
                                                        ->rows(3),
                                                ]),
                                            Section::make('דירוגים')
                                                ->icon('heroicon-o-chart-bar')
                                                ->schema([
                                                    Forms\Components\TextInput::make('professional_rating')
                                                        ->label('דירוג מקצועי')
                                                        ->numeric()
                                                        ->step(0.01)
                                                        ->minValue(0)
                                                        ->maxValue(10)
                                                        ->suffixIcon('heroicon-o-star')
                                                        ->helperText('0-10, כאשר 10 הוא הגבוה ביותר'),
                                                    Forms\Components\TextInput::make('reliability_rating')
                                                        ->label('דירוג אמינות')
                                                        ->numeric()
                                                        ->step(0.01)
                                                        ->minValue(0)
                                                        ->maxValue(10)
                                                        ->suffixIcon('heroicon-o-shield-check')
                                                        ->helperText('0-10, כאשר 10 הוא הגבוה ביותר'),
                                                    Forms\Components\Toggle::make('suitable_for_leader')
                                                        ->label('מתאים לתפקיד מוביל')
                                                        ->default(false)
                                                        ->onIcon('heroicon-o-flag')
                                                        ->onColor('success'),
                                                ]),
                                        ]),
                                    Grid::make(2)
                                        ->schema([
                                            Section::make('סטטוס ואזור')
                                                ->icon('heroicon-o-map-pin')
                                                ->schema([
                                                    Forms\Components\Select::make('status')
                                                        ->label('סטטוס')
                                                        ->options(WorkerStatus::class)
                                                        ->default(WorkerStatus::Available)
                                                        ->required()
                                                        ->prefixIcon('heroicon-o-signal'),
                                                    Forms\Components\Select::make('preferred_work_area')
                                                        ->label('אזור עבודה מועדף')
                                                        ->options(WorkArea::class)
                                                        ->prefixIcon('heroicon-o-map'),
                                                ]),
                                            Section::make('זמינות')
                                                ->icon('heroicon-o-clock')
                                                ->schema([
                                                    Grid::make(2)
                                                        ->schema([
                                                            Forms\Components\Toggle::make('available_daily')
                                                                ->label('זמין יומי')
                                                                ->default(true)
                                                                ->onIcon('heroicon-o-sun')
                                                                ->onColor('success'),
                                                            Forms\Components\Toggle::make('available_contract')
                                                                ->label('זמין קבלן')
                                                                ->default(true)
                                                                ->onIcon('heroicon-o-document-text')
                                                                ->onColor('success'),
                                                            Forms\Components\Toggle::make('eligible_for_assignment')
                                                                ->label('כשיר לשיבוץ')
                                                                ->default(true)
                                                                ->onIcon('heroicon-o-check-circle')
                                                                ->onColor('success'),
                                                            Forms\Components\Toggle::make('blocked')
                                                                ->label('חסום')
                                                                ->default(false)
                                                                ->onIcon('heroicon-o-no-symbol')
                                                                ->onColor('danger'),
                                                        ]),
                                                ]),
                                        ]),
                                ]),

                            Tab::make('הכשרה')
                                ->icon('heroicon-o-academic-cap')
                                ->schema([
                                    Section::make('נתוני הכשרה')
                                        ->icon('heroicon-o-chart-bar-square')
                                        ->description('נתונים אלו מחושבים אוטומטית מתוצאות ההכשרות')
                                        ->schema([
                                            Grid::make(2)
                                                ->schema([
                                                    Forms\Components\TextInput::make('training_score')
                                                        ->label('ציון הכשרה')
                                                        ->numeric()
                                                        ->step(0.01)
                                                        ->disabled()
                                                        ->suffixIcon('heroicon-o-academic-cap'),
                                                    Forms\Components\DatePicker::make('last_training_date')
                                                        ->label('תאריך הכשרה אחרון')
                                                        ->disabled()
                                                        ->prefixIcon('heroicon-o-calendar'),
                                                ]),
                                        ]),
                                    Section::make('מצב הכשרה')
                                        ->schema([
                                            Forms\Components\Placeholder::make('training_status_info')
                                                ->label('')
                                                ->content(function (?Worker $record): HtmlString {
                                                    if (! $record?->id) {
                                                        return new HtmlString('<p class="text-sm text-gray-500">שמור את העובד כדי לראות מידע על הכשרות.</p>');
                                                    }
                                                    $results = $record->trainingResults()->with('trainingCycle')->latest()->limit(3)->get();
                                                    if ($results->isEmpty()) {
                                                        return new HtmlString('<div class="flex items-center gap-2 text-sm text-amber-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg> העובד טרם עבר הכשרה. רשום אותו למחזור הכשרה דרך לשונית ״תוצאות הכשרה״.</div>');
                                                    }
                                                    $html = '<div class="space-y-2">';
                                                    foreach ($results as $r) {
                                                        $cycleName = $r->trainingCycle?->name ?? '—';
                                                        $score = $r->professional_score ? number_format($r->professional_score, 1) : '—';
                                                        $html .= "<div class=\"flex items-center justify-between rounded-lg bg-gray-50 px-3 py-2 text-sm\"><span class=\"font-medium\">{$cycleName}</span><span class=\"text-gray-500\">ציון: {$score}</span></div>";
                                                    }
                                                    $html .= '</div>';

                                                    return new HtmlString($html);
                                                }),
                                        ]),
                                ]),

                            Tab::make('תשלום')
                                ->icon('heroicon-o-banknotes')
                                ->schema([
                                    Section::make('מצב תשלום')
                                        ->icon('heroicon-o-credit-card')
                                        ->schema([
                                            Grid::make(2)
                                                ->schema([
                                                    Forms\Components\Select::make('payment_status')
                                                        ->label('סטטוס תשלום')
                                                        ->options(PaymentStatus::class)
                                                        ->default(PaymentStatus::Paid)
                                                        ->required()
                                                        ->prefixIcon('heroicon-o-currency-dollar'),
                                                    Forms\Components\DatePicker::make('last_payment_date')
                                                        ->label('תאריך תשלום אחרון')
                                                        ->prefixIcon('heroicon-o-calendar'),
                                                ]),
                                        ]),
                                    Section::make('היסטוריית תשלומים אחרונה')
                                        ->schema([
                                            Forms\Components\Placeholder::make('recent_payments')
                                                ->label('')
                                                ->content(function (?Worker $record): HtmlString {
                                                    if (! $record?->id) {
                                                        return new HtmlString('<p class="text-sm text-gray-500">שמור את העובד כדי לראות היסטוריית תשלומים.</p>');
                                                    }
                                                    $payments = $record->payments()->latest('payment_date')->limit(5)->get();
                                                    if ($payments->isEmpty()) {
                                                        return new HtmlString('<p class="text-sm text-gray-500">אין תשלומים רשומים.</p>');
                                                    }
                                                    $html = '<div class="space-y-2">';
                                                    foreach ($payments as $p) {
                                                        $date = $p->payment_date?->format('d/m/Y') ?? '—';
                                                        $amount = number_format($p->amount, 0);
                                                        $type = $p->payment_type?->getLabel() ?? '—';
                                                        $html .= "<div class=\"flex items-center justify-between rounded-lg bg-gray-50 px-3 py-2 text-sm\"><div class=\"flex items-center gap-3\"><span class=\"font-semibold text-green-700\">₪{$amount}</span><span class=\"text-gray-500\">{$type}</span></div><span class=\"text-gray-400 text-xs\">{$date}</span></div>";
                                                    }
                                                    $html .= '</div>';

                                                    return new HtmlString($html);
                                                }),
                                        ]),
                                ]),

                            Tab::make('סוכנות')
                                ->icon('heroicon-o-building-office')
                                ->schema([
                                    Section::make('סוכנות כוח אדם')
                                        ->icon('heroicon-o-building-office-2')
                                        ->description('בחר את סוכנות כוח האדם שמנהלת את העובד')
                                        ->schema([
                                            Forms\Components\Select::make('staffing_agency_id')
                                                ->label('סוכנות')
                                                ->relationship('staffingAgency', 'agency_name')
                                                ->searchable()
                                                ->preload()
                                                ->prefixIcon('heroicon-o-building-office'),
                                        ]),
                                ]),

                            Tab::make('הערות')
                                ->icon('heroicon-o-chat-bubble-left-right')
                                ->schema([
                                    Section::make('הערות כלליות')
                                        ->icon('heroicon-o-pencil-square')
                                        ->schema([
                                            Forms\Components\Textarea::make('general_notes')
                                                ->label('')
                                                ->rows(6)
                                                ->placeholder('הוסף הערות כלליות על העובד...'),
                                        ]),
                                ]),

                            Tab::make('היסטוריה')
                                ->icon('heroicon-o-clock')
                                ->schema([
                                    Section::make('יומן פעילות')
                                        ->icon('heroicon-o-list-bullet')
                                        ->description('30 הפעולות האחרונות שבוצעו על עובד זה')
                                        ->schema([
                                            Forms\Components\Placeholder::make('activity_log')
                                                ->label('')
                                                ->content(function (?Worker $record): HtmlString {
                                                    if (! $record?->id) {
                                                        return new HtmlString('<p class="text-sm text-gray-500">אין פעילות רשומה עדיין.</p>');
                                                    }
                                                    $activities = \Spatie\Activitylog\Models\Activity::where('subject_type', Worker::class)
                                                        ->where('subject_id', $record->id)
                                                        ->latest()
                                                        ->limit(30)
                                                        ->get();
                                                    if ($activities->isEmpty()) {
                                                        return new HtmlString('<p class="text-sm text-gray-500">אין פעילות רשומה עדיין.</p>');
                                                    }

                                                    $html = '<div class="space-y-1">';
                                                    foreach ($activities as $a) {
                                                        $date = $a->created_at->format('d/m/Y H:i');
                                                        $causer = e($a->causer?->name ?? 'מערכת');
                                                        $desc = e($a->description);
                                                        $html .= "<div class=\"flex items-start gap-3 rounded-lg px-3 py-2 text-sm hover:bg-gray-50 transition-colors\"><span class=\"shrink-0 text-xs text-gray-400 font-mono mt-0.5\">{$date}</span><span class=\"font-medium text-gray-700\">{$causer}</span><span class=\"text-gray-500\">—</span><span class=\"text-gray-600\">{$desc}</span></div>";
                                                    }
                                                    $html .= '</div>';

                                                    return new HtmlString($html);
                                                }),
                                        ]),
                                ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('photo')
                    ->label('')
                    ->collection('photo')
                    ->circular()
                    ->size(40),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('שם')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Worker $record): ?string => $record->id_number ? "ת.ז. {$record->id_number}" : null),
                Tables\Columns\TextColumn::make('primary_skill')
                    ->label('מקצוע')
                    ->badge()
                    ->icon('heroicon-o-wrench-screwdriver'),
                Tables\Columns\TextColumn::make('status')
                    ->label('סטטוס')
                    ->badge()
                    ->color(fn (WorkerStatus $state): string => match ($state) {
                        WorkerStatus::Available => 'success',
                        WorkerStatus::Assigned => 'info',
                        WorkerStatus::FutureAssignment => 'warning',
                        WorkerStatus::InTraining => 'primary',
                        WorkerStatus::Waiting => 'gray',
                        WorkerStatus::Inactive => 'danger',
                        WorkerStatus::Frozen => 'danger',
                    })
                    ->icon(fn (WorkerStatus $state): string => match ($state) {
                        WorkerStatus::Available => 'heroicon-o-check-circle',
                        WorkerStatus::Assigned => 'heroicon-o-briefcase',
                        WorkerStatus::FutureAssignment => 'heroicon-o-clock',
                        WorkerStatus::InTraining => 'heroicon-o-academic-cap',
                        WorkerStatus::Waiting => 'heroicon-o-pause-circle',
                        WorkerStatus::Inactive => 'heroicon-o-x-circle',
                        WorkerStatus::Frozen => 'heroicon-o-no-symbol',
                    }),
                Tables\Columns\TextColumn::make('preferred_work_area')
                    ->label('אזור')
                    ->badge()
                    ->icon('heroicon-o-map-pin'),
                Tables\Columns\TextColumn::make('professional_rating')
                    ->label('דירוג')
                    ->numeric(1)
                    ->sortable()
                    ->icon('heroicon-o-star')
                    ->color(fn (?float $state): string => match (true) {
                        $state === null => 'gray',
                        $state >= 8.0 => 'success',
                        $state >= 5.0 => 'warning',
                        default => 'danger',
                    }),
                Tables\Columns\TextColumn::make('payment_status')
                    ->label('תשלום')
                    ->badge()
                    ->color(fn (?PaymentStatus $state): string => match ($state) {
                        PaymentStatus::Paid => 'success',
                        PaymentStatus::Overdue => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (?PaymentStatus $state): string => match ($state) {
                        PaymentStatus::Paid => 'heroicon-o-check-circle',
                        PaymentStatus::Overdue => 'heroicon-o-exclamation-triangle',
                        default => 'heroicon-o-clock',
                    }),
                Tables\Columns\IconColumn::make('blocked')
                    ->label('חסום')
                    ->boolean()
                    ->trueIcon('heroicon-o-no-symbol')
                    ->trueColor('danger')
                    ->falseIcon('heroicon-o-check-circle')
                    ->falseColor('success'),
                Tables\Columns\IconColumn::make('available_daily')
                    ->label('יומי')
                    ->boolean(),
                Tables\Columns\TextColumn::make('staffingAgency.agency_name')
                    ->label('סוכנות')
                    ->icon('heroicon-o-building-office')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('reliability_rating')
                    ->label('אמינות')
                    ->numeric(1)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('last_payment_date')
                    ->label('תשלום אחרון')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('country_of_origin')
                    ->label('ארץ מוצא')
                    ->icon('heroicon-o-globe-alt')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('נוצר')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('full_name')
            ->striped()
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('סטטוס')
                    ->options(WorkerStatus::class)
                    ->multiple()
                    ->preload(),
                Tables\Filters\SelectFilter::make('primary_skill')
                    ->label('מקצוע')
                    ->options(Skill::class)
                    ->multiple()
                    ->preload(),
                Tables\Filters\SelectFilter::make('preferred_work_area')
                    ->label('אזור')
                    ->options(WorkArea::class)
                    ->multiple()
                    ->preload(),
                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('סטטוס תשלום')
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
            ->filtersFormColumns(3)
            ->headerActions([
                Action::make('enroll_in_training')
                    ->label('רשום להכשרה')
                    ->icon('heroicon-o-academic-cap')
                    ->color('primary')
                    ->form([
                        Forms\Components\Select::make('training_cycle_id')
                            ->label('מחזור הכשרה')
                            ->options(fn () => TrainingCycle::where('status', 'open')
                                ->pluck('name', 'id')
                                ->toArray())
                            ->required()
                            ->searchable()
                            ->prefixIcon('heroicon-o-academic-cap'),
                        Forms\Components\Select::make('worker_ids')
                            ->label('עובדים')
                            ->options(fn () => Worker::pluck('full_name', 'id')->toArray())
                            ->multiple()
                            ->required()
                            ->searchable()
                            ->prefixIcon('heroicon-o-users'),
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
                \Filament\Actions\ActionGroup::make([
                    Action::make('change_status')
                        ->label('שינוי סטטוס')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->label('סטטוס חדש')
                                ->options(WorkerStatus::class)
                                ->required()
                                ->prefixIcon('heroicon-o-signal'),
                            Forms\Components\Textarea::make('reason')
                                ->label('סיבה')
                                ->rows(2)
                                ->placeholder('ציין את הסיבה לשינוי הסטטוס...'),
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
                        ->color('info')
                        ->form([
                            Forms\Components\Select::make('team_id')
                                ->label('צוות')
                                ->options(fn () => Team::whereHas('members', fn ($q) => $q->whereNull('left_at'))
                                    ->orDoesntHave('members')
                                    ->pluck('name', 'id')
                                    ->toArray())
                                ->required()
                                ->searchable()
                                ->prefixIcon('heroicon-o-user-group'),
                        ])
                        ->action(function (Worker $record, array $data): void {
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
                                ->label('סכום')
                                ->numeric()
                                ->required()
                                ->minValue(0.01)
                                ->prefix('₪')
                                ->prefixIcon('heroicon-o-currency-dollar'),
                            Forms\Components\DatePicker::make('payment_date')
                                ->label('תאריך תשלום')
                                ->default(now())
                                ->required()
                                ->prefixIcon('heroicon-o-calendar'),
                            Forms\Components\Select::make('payment_type')
                                ->label('סוג תשלום')
                                ->options(PaymentType::class)
                                ->required()
                                ->prefixIcon('heroicon-o-credit-card'),
                            Forms\Components\Textarea::make('notes')
                                ->label('הערות')
                                ->rows(2)
                                ->placeholder('הערות לתשלום...'),
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
                ])
                    ->icon('heroicon-o-ellipsis-vertical')
                    ->tooltip('פעולות'),
                EditAction::make()
                    ->iconButton(),
                ViewAction::make()
                    ->iconButton(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('bulk_change_status')
                        ->label('שינוי סטטוס')
                        ->icon('heroicon-o-arrow-path')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->label('סטטוס חדש')
                                ->options(WorkerStatus::class)
                                ->required(),
                            Forms\Components\Textarea::make('reason')
                                ->label('סיבה')
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
