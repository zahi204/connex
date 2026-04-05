<?php

namespace App\Filament\Resources;

use App\Enums\AssignmentStatus;
use App\Enums\EngagementType;
use App\Models\Assignment;
use App\Models\Subcontractor;
use App\Models\Team;
use App\Models\Worker;
use Filament\Forms;
use Filament\Schemas\Components\Form as FormContainer;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AssignmentResource extends Resource
{
    protected static ?string $model = Assignment::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static string|\UnitEnum|null $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                FormContainer::make([
                Forms\Components\Select::make('project_id')
                    ->relationship('project', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('resource_type')
                    ->options([
                        Worker::class => 'Worker',
                        Team::class => 'Team',
                        Subcontractor::class => 'Subcontractor',
                    ])
                    ->required()
                    ->live(),
                Forms\Components\Select::make('resource_id')
                    ->label('Resource')
                    ->options(function (Forms\Get $get) {
                        $type = $get('resource_type');
                        if (! $type) return [];
                        return match ($type) {
                            Worker::class => Worker::pluck('full_name', 'id'),
                            Team::class => Team::pluck('name', 'id'),
                            Subcontractor::class => Subcontractor::pluck('name', 'id'),
                            default => [],
                        };
                    })
                    ->required()
                    ->searchable(),
                Forms\Components\Select::make('engagement_type')
                    ->options(EngagementType::class)
                    ->required(),
                Forms\Components\DatePicker::make('start_date')
                    ->required(),
                Forms\Components\DatePicker::make('estimated_end_date'),
                Forms\Components\DatePicker::make('actual_end_date'),
                Forms\Components\Textarea::make('work_description')
                    ->rows(3),
                Forms\Components\TextInput::make('price_per_day')
                    ->numeric()
                    ->prefix('NIS'),
                Forms\Components\TextInput::make('contract_amount')
                    ->numeric()
                    ->prefix('NIS'),
                Forms\Components\TextInput::make('commission_rate')
                    ->numeric()
                    ->suffix('%'),
                Forms\Components\Select::make('status')
                    ->options(AssignmentStatus::class)
                    ->required(),
                Forms\Components\Textarea::make('notes')
                    ->rows(2),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('project.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('resource_type')
                    ->formatStateUsing(fn (string $state) => class_basename($state))
                    ->badge(),
                Tables\Columns\TextColumn::make('engagement_type')
                    ->badge(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (AssignmentStatus $state): string => match ($state) {
                        AssignmentStatus::New => 'gray',
                        AssignmentStatus::InProgress => 'info',
                        AssignmentStatus::Completed => 'success',
                        AssignmentStatus::Cancelled => 'danger',
                        AssignmentStatus::Suspended => 'warning',
                    }),
                Tables\Columns\TextColumn::make('price_per_day')
                    ->money('ILS')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('contract_amount')
                    ->money('ILS')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(AssignmentStatus::class),
                Tables\Filters\SelectFilter::make('engagement_type')
                    ->options(EngagementType::class),
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

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\AssignmentResource\Pages\ListAssignments::route('/'),
            'create' => \App\Filament\Resources\AssignmentResource\Pages\CreateAssignment::route('/create'),
            'edit' => \App\Filament\Resources\AssignmentResource\Pages\EditAssignment::route('/{record}/edit'),
        ];
    }
}
