<?php

namespace App\Filament\Resources;

use App\Enums\PaymentStatus;
use App\Enums\Skill;
use App\Enums\WorkArea;
use App\Enums\WorkerStatus;
use App\Filament\Resources\WorkerResource\RelationManagers;
use App\Models\Worker;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WorkerResource extends Resource
{
    protected static ?string $model = Worker::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Resources';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Worker')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Personal Info')
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
                        Forms\Components\Tabs\Tab::make('Professional')
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
                        Forms\Components\Tabs\Tab::make('Training')
                            ->schema([
                                Forms\Components\TextInput::make('training_score')
                                    ->numeric()
                                    ->step(0.01)
                                    ->disabled(),
                                Forms\Components\DatePicker::make('last_training_date')
                                    ->disabled(),
                            ]),
                        Forms\Components\Tabs\Tab::make('Payment')
                            ->schema([
                                Forms\Components\Select::make('payment_status')
                                    ->options(PaymentStatus::class),
                                Forms\Components\DatePicker::make('last_payment_date'),
                            ]),
                        Forms\Components\Tabs\Tab::make('Agency')
                            ->schema([
                                Forms\Components\Select::make('staffing_agency_id')
                                    ->relationship('staffingAgency', 'agency_name')
                                    ->searchable()
                                    ->preload(),
                            ]),
                        Forms\Components\Tabs\Tab::make('Notes')
                            ->schema([
                                Forms\Components\Textarea::make('general_notes')
                                    ->rows(4),
                            ]),
                    ])
                    ->columnSpanFull(),
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
                Tables\Columns\IconColumn::make('available_daily')
                    ->boolean(),
                Tables\Columns\TextColumn::make('staffingAgency.agency_name')
                    ->label('Agency')
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
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => \App\Filament\Resources\WorkerResource\Pages\ListWorkers::route('/'),
            'create' => \App\Filament\Resources\WorkerResource\Pages\CreateWorker::route('/create'),
            'edit' => \App\Filament\Resources\WorkerResource\Pages\EditWorker::route('/{record}/edit'),
        ];
    }
}
