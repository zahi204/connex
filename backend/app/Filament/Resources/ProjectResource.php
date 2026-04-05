<?php

namespace App\Filament\Resources;

use App\Enums\ProjectSource;
use App\Enums\ProjectStatus;
use App\Enums\ProjectType;
use App\Enums\Skill;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Filament\Forms;
use Filament\Schemas\Components\Form as FormContainer;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office';

    protected static string|\UnitEnum|null $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                FormContainer::make([
                Forms\Components\Tabs::make('Project')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Details')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('address')
                                    ->maxLength(500),
                                Forms\Components\TextInput::make('city')
                                    ->maxLength(100),
                                Forms\Components\TextInput::make('region')
                                    ->maxLength(100),
                                Forms\Components\Select::make('project_type')
                                    ->options(ProjectType::class)
                                    ->required(),
                                Forms\Components\Select::make('status')
                                    ->options(ProjectStatus::class)
                                    ->required(),
                                Forms\Components\Select::make('source')
                                    ->options(ProjectSource::class),
                                Forms\Components\Select::make('developer_id')
                                    ->relationship('developer', 'company_name')
                                    ->searchable()
                                    ->preload(),
                            ]),
                        Forms\Components\Tabs\Tab::make('Timeline')
                            ->schema([
                                Forms\Components\DatePicker::make('estimated_start_date'),
                                Forms\Components\DatePicker::make('estimated_completion'),
                            ]),
                        Forms\Components\Tabs\Tab::make('Trades')
                            ->schema([
                                Forms\Components\CheckboxList::make('required_trades')
                                    ->options(
                                        collect(Skill::cases())
                                            ->mapWithKeys(fn ($skill) => [$skill->value => $skill->label()])
                                            ->toArray()
                                    )
                                    ->columns(3),
                            ]),
                        Forms\Components\Tabs\Tab::make('Notes')
                            ->schema([
                                Forms\Components\Textarea::make('notes')
                                    ->rows(4),
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
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('city')
                    ->searchable(),
                Tables\Columns\TextColumn::make('project_type')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (ProjectStatus $state): string => match ($state) {
                        ProjectStatus::New => 'gray',
                        ProjectStatus::UnderReview => 'warning',
                        ProjectStatus::ResourcesAssigned => 'info',
                        ProjectStatus::InProgress => 'primary',
                        ProjectStatus::Completed => 'success',
                        ProjectStatus::Cancelled => 'danger',
                    }),
                Tables\Columns\TextColumn::make('developer.company_name')
                    ->label('Developer'),
                Tables\Columns\TextColumn::make('estimated_start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('assignments_count')
                    ->counts('assignments')
                    ->label('Assignments'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(ProjectStatus::class),
                Tables\Filters\SelectFilter::make('project_type')
                    ->options(ProjectType::class),
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
            RelationManagers\AssignmentsRelationManager::class,
            RelationManagers\NotesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\ProjectResource\Pages\ListProjects::route('/'),
            'create' => \App\Filament\Resources\ProjectResource\Pages\CreateProject::route('/create'),
            'edit' => \App\Filament\Resources\ProjectResource\Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
