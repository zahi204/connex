<?php

namespace App\Filament\Resources;

use App\Enums\ProjectSource;
use App\Enums\ProjectStatus;
use App\Enums\ProjectType;
use App\Enums\Skill;
use App\Filament\Resources\ProjectResource\Pages\CreateProject;
use App\Filament\Resources\ProjectResource\Pages\EditProject;
use App\Filament\Resources\ProjectResource\Pages\ListProjects;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Form as FormContainer;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office';

    protected static string|\UnitEnum|null $navigationGroup = 'תפעול';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'פרויקט';

    protected static ?string $pluralModelLabel = 'פרויקטים';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                FormContainer::make([
                    Tabs::make('Project')
                        ->tabs([
                            Tab::make('פרטים')
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
                            Tab::make('ציר זמן')
                                ->schema([
                                    Forms\Components\DatePicker::make('estimated_start_date'),
                                    Forms\Components\DatePicker::make('estimated_completion'),
                                ]),
                            Tab::make('מקצועות')
                                ->schema([
                                    Forms\Components\CheckboxList::make('required_trades')
                                        ->options(
                                            collect(Skill::cases())
                                                ->mapWithKeys(fn ($skill) => [$skill->value => $skill->getLabel()])
                                                ->toArray()
                                        )
                                        ->columns(3),
                                ]),
                            Tab::make('הערות')
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
                    ->label('יזם'),
                Tables\Columns\TextColumn::make('estimated_start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('assignments_count')
                    ->counts('assignments')
                    ->label('שיבוצים'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(ProjectStatus::class),
                Tables\Filters\SelectFilter::make('project_type')
                    ->options(ProjectType::class),
            ])
            ->actions([
                EditAction::make(),
                ViewAction::make(),
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
            RelationManagers\AssignmentsRelationManager::class,
            RelationManagers\NotesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProjects::route('/'),
            'create' => CreateProject::route('/create'),
            'edit' => EditProject::route('/{record}/edit'),
        ];
    }
}
