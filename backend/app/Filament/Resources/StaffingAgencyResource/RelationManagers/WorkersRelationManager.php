<?php

namespace App\Filament\Resources\StaffingAgencyResource\RelationManagers;

use App\Enums\WorkerStatus;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class WorkersRelationManager extends RelationManager
{
    protected static string $relationship = 'workers';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('primary_skill')->badge(),
                Tables\Columns\TextColumn::make('status')->badge()->color(fn (WorkerStatus $state): string => match ($state) {
                    WorkerStatus::Available => 'success',
                    WorkerStatus::Assigned => 'info',
                    WorkerStatus::FutureAssignment => 'warning',
                    WorkerStatus::InTraining => 'primary',
                    WorkerStatus::Waiting => 'gray',
                    WorkerStatus::Inactive => 'danger',
                    WorkerStatus::Frozen => 'danger',
                }),
                Tables\Columns\TextColumn::make('professional_rating')->numeric(2)->sortable(),
                Tables\Columns\TextColumn::make('training_score')->numeric(2),
            ])
            ->actions([Tables\Actions\ViewAction::make()]);
    }
}
