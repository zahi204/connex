<?php

namespace App\Filament\Resources\SubcontractorResource\RelationManagers;

use App\Enums\AssignmentStatus;
use App\Enums\EngagementType;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Form as FormContainer;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class AssignmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'assignments';

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            FormContainer::make([
                Forms\Components\Select::make('project_id')->relationship('project', 'name')->required()->searchable(),
                Forms\Components\Select::make('engagement_type')->options(EngagementType::class)->required(),
                Forms\Components\DatePicker::make('start_date')->required(),
                Forms\Components\DatePicker::make('estimated_end_date'),
                Forms\Components\Select::make('status')->options(AssignmentStatus::class)->required(),
            ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('project.name')->searchable(),
                Tables\Columns\TextColumn::make('engagement_type')->badge(),
                Tables\Columns\TextColumn::make('start_date')->date(),
                Tables\Columns\TextColumn::make('status')->badge()->color(fn (AssignmentStatus $state): string => match ($state) {
                    AssignmentStatus::New => 'gray',
                    AssignmentStatus::InProgress => 'info',
                    AssignmentStatus::Completed => 'success',
                    AssignmentStatus::Cancelled => 'danger',
                    AssignmentStatus::Suspended => 'warning',
                }),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([EditAction::make(), DeleteAction::make()]);
    }
}
