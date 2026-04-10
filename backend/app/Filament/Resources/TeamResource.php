<?php

namespace App\Filament\Resources;

use App\Enums\TeamStatus;
use App\Enums\UserRole;
use App\Enums\WorkerStatus;
use App\Filament\Resources\TeamResource\Pages\CreateTeam;
use App\Filament\Resources\TeamResource\Pages\EditTeam;
use App\Filament\Resources\TeamResource\Pages\ListTeams;
use App\Filament\Resources\TeamResource\RelationManagers;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use App\Models\Worker;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class TeamResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|\UnitEnum|null $navigationGroup = 'משאבים';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'צוות';

    protected static ?string $pluralModelLabel = 'צוותים';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('שם הצוות')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('team_type')
                        ->label('סוג צוות')
                        ->maxLength(50),
                    Forms\Components\TextInput::make('primary_field')
                        ->label('תחום עיקרי')
                        ->maxLength(50),
                    Forms\Components\Select::make('status')
                        ->label('סטטוס')
                        ->options(TeamStatus::class)
                        ->default(TeamStatus::Available)
                        ->required(),
                    Forms\Components\TextInput::make('operating_area')
                        ->label('אזור פעילות')
                        ->maxLength(20),
                    Forms\Components\TextInput::make('work_types')
                        ->label('סוגי עבודה')
                        ->maxLength(20)
                        ->default('both'),
                    Forms\Components\TextInput::make('leader_phone')
                        ->label('טלפון ראש צוות')
                        ->tel()
                        ->placeholder('+972501234567')
                        ->helperText('מספר טלפון של העובד. אם לא קיים, ייווצר משתמש ועובד אוטומטית.')
                        ->prefixIcon('heroicon-o-device-phone-mobile')
                        ->afterStateHydrated(function (Forms\Components\TextInput $component, ?Team $record) {
                            if ($record?->teamLeader?->user) {
                                $component->state($record->teamLeader->user->phone);
                            }
                        }),
                    Forms\Components\Select::make('current_project_id')
                        ->label('פרויקט נוכחי')
                        ->relationship('currentProject', 'name')
                        ->searchable()
                        ->preload()
                        ->nullable()
                        ->placeholder('ללא פרויקט'),
                    Forms\Components\DatePicker::make('availability_date')
                        ->label('תאריך זמינות'),
                    Forms\Components\TextInput::make('average_rating')
                        ->label('דירוג ממוצע')
                        ->numeric()
                        ->step(0.01)
                        ->disabled()
                        ->dehydrated(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('team_type'),
                Tables\Columns\TextColumn::make('primary_field'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (TeamStatus $state): string => match ($state) {
                        TeamStatus::Available => 'success',
                        TeamStatus::Assigned => 'info',
                        TeamStatus::AvailableSoon => 'warning',
                        TeamStatus::InAssembly => 'primary',
                        TeamStatus::Frozen => 'danger',
                    }),
                Tables\Columns\TextColumn::make('teamLeader.full_name')
                    ->label('ראש צוות'),
                Tables\Columns\TextColumn::make('currentProject.name')
                    ->label('פרויקט'),
                Tables\Columns\TextColumn::make('members_count')
                    ->counts('members')
                    ->label('חברים'),
                Tables\Columns\TextColumn::make('average_rating')
                    ->numeric(2)
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(TeamStatus::class),
            ])
            ->actions([
                EditAction::make(),
                Action::make('dissolve')
                    ->label('פרק')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('פירוק צוות')
                    ->modalDescription('פעולה זו תשחרר את כל העובדים ותעדכן את סטטוסם ל"זמין".')
                    ->action(function (Team $record) {
                        $record->members()
                            ->whereNull('left_at')
                            ->each(function ($member) {
                                $member->update(['left_at' => now()]);
                                $member->worker->update(['status' => WorkerStatus::Available]);
                            });
                        $record->update([
                            'status' => TeamStatus::Frozen,
                            'current_project_id' => null,
                        ]);
                    }),
                Action::make('assignToProject')
                    ->label('שבץ לפרויקט')
                    ->icon('heroicon-o-building-office')
                    ->form([
                        Forms\Components\Select::make('project_id')
                            ->label('פרויקט')
                            ->options(Project::query()->pluck('name', 'id'))
                            ->required()
                            ->searchable(),
                    ])
                    ->action(function (Team $record, array $data) {
                        $record->update([
                            'current_project_id' => $data['project_id'],
                            'status' => TeamStatus::Assigned,
                        ]);
                    }),
                Action::make('updateAvailability')
                    ->label('עדכן זמינות')
                    ->icon('heroicon-o-calendar')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->options(TeamStatus::class)
                            ->required(),
                        Forms\Components\DatePicker::make('availability_date'),
                    ])
                    ->action(function (Team $record, array $data) {
                        $record->update($data);
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TeamMembersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTeams::route('/'),
            'create' => CreateTeam::route('/create'),
            'edit' => EditTeam::route('/{record}/edit'),
        ];
    }
}
