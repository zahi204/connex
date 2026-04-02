<?php

namespace App\Filament\Resources;

use App\Enums\TrainingCycleStatus;
use App\Filament\Resources\TrainingCycleResource\Pages;
use App\Filament\Resources\TrainingCycleResource\RelationManagers;
use App\Models\TrainingCycle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TrainingCycleResource extends Resource
{
    protected static ?string $model = TrainingCycle::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('start_date')
                    ->required(),
                Forms\Components\DatePicker::make('end_date')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options(TrainingCycleStatus::class)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (TrainingCycleStatus $state): string => match ($state) {
                        TrainingCycleStatus::Planned => 'gray',
                        TrainingCycleStatus::InProgress => 'info',
                        TrainingCycleStatus::Completed => 'success',
                        TrainingCycleStatus::Cancelled => 'danger',
                    }),
                Tables\Columns\TextColumn::make('results_count')
                    ->counts('results')
                    ->label('Results'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(TrainingCycleStatus::class),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTrainingCycles::route('/'),
            'create' => Pages\CreateTrainingCycle::route('/create'),
            'edit' => Pages\EditTrainingCycle::route('/{record}/edit'),
        ];
    }
}
