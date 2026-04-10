<?php

namespace App\Filament\Resources\WorkerResource\RelationManagers;

use App\Enums\Suitability;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Form as FormContainer;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class TrainingResultsRelationManager extends RelationManager
{
    protected static string $relationship = 'trainingResults';

    protected static ?string $title = 'תוצאות הכשרה';

    protected static string|\BackedEnum|null $icon = 'heroicon-o-academic-cap';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                FormContainer::make([
                    Forms\Components\Select::make('training_cycle_id')
                        ->label('מחזור הכשרה')
                        ->relationship('trainingCycle', 'name')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->prefixIcon('heroicon-o-academic-cap')
                        ->columnSpanFull(),
                    Grid::make(2)
                        ->schema([
                            Forms\Components\Toggle::make('attendance_day1')
                                ->label('נוכחות יום 1')
                                ->onIcon('heroicon-o-check')
                                ->onColor('success'),
                            Forms\Components\Toggle::make('attendance_day2')
                                ->label('נוכחות יום 2')
                                ->onIcon('heroicon-o-check')
                                ->onColor('success'),
                        ]),
                    Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('professional_score')
                                ->label('ציון מקצועי')
                                ->numeric()
                                ->step(0.01)
                                ->suffixIcon('heroicon-o-star'),
                            Forms\Components\Select::make('suitability')
                                ->label('התאמה')
                                ->options(Suitability::class)
                                ->prefixIcon('heroicon-o-hand-thumb-up'),
                        ]),
                    Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('classification')
                                ->label('סיווג')
                                ->maxLength(100),
                            Forms\Components\TextInput::make('primary_field')
                                ->label('תחום עיקרי')
                                ->maxLength(100),
                        ]),
                    Forms\Components\Textarea::make('notes')
                        ->label('הערות')
                        ->rows(2)
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('placement_recommendation')
                        ->label('המלצת השמה')
                        ->maxLength(255)
                        ->prefixIcon('heroicon-o-light-bulb')
                        ->columnSpanFull(),
                ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('trainingCycle.name')
                    ->label('מחזור')
                    ->weight('bold')
                    ->icon('heroicon-o-academic-cap'),
                Tables\Columns\IconColumn::make('attendance_day1')
                    ->label('יום 1')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->trueColor('success')
                    ->falseIcon('heroicon-o-x-circle')
                    ->falseColor('danger'),
                Tables\Columns\IconColumn::make('attendance_day2')
                    ->label('יום 2')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->trueColor('success')
                    ->falseIcon('heroicon-o-x-circle')
                    ->falseColor('danger'),
                Tables\Columns\TextColumn::make('professional_score')
                    ->label('ציון')
                    ->numeric(1)
                    ->icon('heroicon-o-star')
                    ->color(fn (?float $state): string => match (true) {
                        $state === null => 'gray',
                        $state >= 8.0 => 'success',
                        $state >= 5.0 => 'warning',
                        default => 'danger',
                    }),
                Tables\Columns\TextColumn::make('suitability')
                    ->label('התאמה')
                    ->badge(),
                Tables\Columns\TextColumn::make('classification')
                    ->label('סיווג')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('placement_recommendation')
                    ->label('המלצה')
                    ->limit(30)
                    ->tooltip(fn ($state) => $state),
            ])
            ->headerActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus'),
            ])
            ->actions([
                EditAction::make()
                    ->iconButton(),
                DeleteAction::make()
                    ->iconButton(),
            ])
            ->striped()
            ->emptyStateHeading('אין תוצאות הכשרה')
            ->emptyStateDescription('רשום את העובד למחזור הכשרה כדי לתעד תוצאות')
            ->emptyStateIcon('heroicon-o-academic-cap');
    }
}
