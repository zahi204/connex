<?php

namespace App\Filament\Resources\WorkerResource\RelationManagers;

use App\Enums\Suitability;
use Filament\Forms;
use Filament\Schemas\Components\Form as FormContainer;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TrainingResultsRelationManager extends RelationManager
{
    protected static string $relationship = 'trainingResults';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                FormContainer::make([
                Forms\Components\Select::make('training_cycle_id')
                    ->relationship('trainingCycle', 'name')
                    ->required(),
                Forms\Components\Toggle::make('attendance_day1'),
                Forms\Components\Toggle::make('attendance_day2'),
                Forms\Components\TextInput::make('professional_score')
                    ->numeric()
                    ->step(0.01),
                Forms\Components\Select::make('suitability')
                    ->options(Suitability::class),
                Forms\Components\TextInput::make('classification')
                    ->maxLength(100),
                Forms\Components\TextInput::make('primary_field')
                    ->maxLength(100),
                Forms\Components\Textarea::make('notes')
                    ->rows(2),
                Forms\Components\TextInput::make('placement_recommendation')
                    ->maxLength(255),
                ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('trainingCycle.name')
                    ->label('Cycle'),
                Tables\Columns\IconColumn::make('attendance_day1')
                    ->boolean(),
                Tables\Columns\IconColumn::make('attendance_day2')
                    ->boolean(),
                Tables\Columns\TextColumn::make('professional_score')
                    ->numeric(2),
                Tables\Columns\TextColumn::make('suitability')
                    ->badge(),
                Tables\Columns\TextColumn::make('classification'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
