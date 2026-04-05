<?php

namespace App\Filament\Resources\TrainingCycleResource\RelationManagers;

use App\Enums\Suitability;
use App\Jobs\GenerateTrainingCertificate;
use App\Models\Worker;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Form as FormContainer;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TrainingResultsRelationManager extends RelationManager
{
    protected static string $relationship = 'results';

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            FormContainer::make([
                Forms\Components\Select::make('worker_id')
                    ->options(Worker::pluck('full_name', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\Toggle::make('attendance_day1')
                    ->label('Attendance Day 1')
                    ->default(false),
                Forms\Components\Toggle::make('attendance_day2')
                    ->label('Attendance Day 2')
                    ->default(false),
                Forms\Components\TextInput::make('professional_score')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100),
                Forms\Components\Select::make('suitability')
                    ->options(Suitability::class),
                Forms\Components\TextInput::make('classification')
                    ->maxLength(255),
                Forms\Components\TextInput::make('primary_field')
                    ->maxLength(255),
                Forms\Components\Textarea::make('notes')
                    ->rows(3),
                Forms\Components\TextInput::make('placement_recommendation')
                    ->maxLength(255),
            ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('worker.full_name')
                    ->label('Worker')
                    ->searchable(),
                Tables\Columns\IconColumn::make('attendance_day1')
                    ->boolean()
                    ->label('Day 1'),
                Tables\Columns\IconColumn::make('attendance_day2')
                    ->boolean()
                    ->label('Day 2'),
                Tables\Columns\TextColumn::make('professional_score')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                Tables\Columns\TextColumn::make('suitability')
                    ->badge()
                    ->color(fn (Suitability $state): string => match ($state) {
                        Suitability::Suitable => 'success',
                        Suitability::Partial => 'warning',
                        Suitability::NotSuitable => 'danger',
                    }),
                Tables\Columns\TextColumn::make('classification'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('generateCertificate')
                    ->label('Generate Certificate')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        GenerateTrainingCertificate::dispatch($record);

                        Notification::make()
                            ->title('Certificate generation queued')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
