<?php

namespace App\Filament\Resources;

use App\Enums\TeamStatus;
use App\Enums\WorkerStatus;
use App\Filament\Resources\TeamResource\RelationManagers;
use App\Models\Project;
use App\Models\Team;
use App\Models\Worker;
use Filament\Forms;
use Filament\Schemas\Components\Form as FormContainer;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TeamResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|\UnitEnum|null $navigationGroup = 'Resources';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                FormContainer::make([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('team_type')
                    ->maxLength(100),
                Forms\Components\TextInput::make('primary_field')
                    ->maxLength(100),
                Forms\Components\Select::make('status')
                    ->options(TeamStatus::class)
                    ->required(),
                Forms\Components\TextInput::make('operating_area')
                    ->maxLength(100),
                Forms\Components\TextInput::make('work_types')
                    ->maxLength(255),
                Forms\Components\Select::make('team_leader_id')
                    ->relationship('teamLeader', 'full_name')
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('current_project_id')
                    ->relationship('currentProject', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\DatePicker::make('availability_date'),
                Forms\Components\TextInput::make('average_rating')
                    ->numeric()
                    ->step(0.01)
                    ->disabled(),
                ]),
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
                    ->label('Leader'),
                Tables\Columns\TextColumn::make('currentProject.name')
                    ->label('Project'),
                Tables\Columns\TextColumn::make('members_count')
                    ->counts('members')
                    ->label('Members'),
                Tables\Columns\TextColumn::make('average_rating')
                    ->numeric(2)
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(TeamStatus::class),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('dissolve')
                    ->label('Dissolve')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Dissolve Team')
                    ->modalDescription('This will release all workers and set their status to Available.')
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
                Tables\Actions\Action::make('assignToProject')
                    ->label('Assign to Project')
                    ->icon('heroicon-o-building-office')
                    ->form([
                        Forms\Components\Select::make('project_id')
                            ->label('Project')
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
                Tables\Actions\Action::make('updateAvailability')
                    ->label('Update Availability')
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
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => \App\Filament\Resources\TeamResource\Pages\ListTeams::route('/'),
            'create' => \App\Filament\Resources\TeamResource\Pages\CreateTeam::route('/create'),
            'edit' => \App\Filament\Resources\TeamResource\Pages\EditTeam::route('/{record}/edit'),
        ];
    }
}
