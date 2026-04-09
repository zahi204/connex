<?php

namespace App\Filament\Resources\DeveloperResource\RelationManagers;

use App\Enums\ProjectStatus;
use App\Enums\ProjectType;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Form as FormContainer;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ProjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'projects';

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            FormContainer::make([
                Forms\Components\TextInput::make('name')->required()->maxLength(255),
                Forms\Components\Select::make('project_type')->options(ProjectType::class)->required(),
                Forms\Components\Select::make('status')->options(ProjectStatus::class)->required(),
                Forms\Components\DatePicker::make('estimated_start_date'),
                Forms\Components\DatePicker::make('estimated_completion'),
            ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('project_type')->badge(),
                Tables\Columns\TextColumn::make('status')->badge()->color(fn (ProjectStatus $state): string => match ($state) {
                    ProjectStatus::New => 'gray',
                    ProjectStatus::UnderReview => 'warning',
                    ProjectStatus::ResourcesAssigned => 'info',
                    ProjectStatus::InProgress => 'primary',
                    ProjectStatus::Completed => 'success',
                    ProjectStatus::Cancelled => 'danger',
                }),
                Tables\Columns\TextColumn::make('estimated_start_date')->date(),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([EditAction::make(), DeleteAction::make()]);
    }
}
