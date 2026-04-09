<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use App\Enums\AssignmentStatus;
use App\Enums\EngagementType;
use App\Models\Subcontractor;
use App\Models\Team;
use App\Models\Worker;
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
                Forms\Components\Select::make('resource_type')
                    ->options([Worker::class => 'Worker', Team::class => 'Team', Subcontractor::class => 'Subcontractor'])
                    ->required()->live(),
                Forms\Components\Select::make('resource_id')->label('Resource')
                    ->options(function (Forms\Get $get) {
                        $type = $get('resource_type');
                        if (! $type) {
                            return [];
                        }

                        return match ($type) {
                            Worker::class => Worker::pluck('full_name', 'id'),
                            Team::class => Team::pluck('name', 'id'),
                            Subcontractor::class => Subcontractor::pluck('name', 'id'),
                            default => [],
                        };
                    })->required()->searchable(),
                Forms\Components\Select::make('engagement_type')->options(EngagementType::class)->required(),
                Forms\Components\DatePicker::make('start_date')->required(),
                Forms\Components\DatePicker::make('estimated_end_date'),
                Forms\Components\Textarea::make('work_description')->rows(2),
                Forms\Components\TextInput::make('price_per_day')->numeric()->prefix('NIS'),
                Forms\Components\TextInput::make('contract_amount')->numeric()->prefix('NIS'),
                Forms\Components\TextInput::make('commission_rate')->numeric()->suffix('%'),
                Forms\Components\Select::make('status')->options(AssignmentStatus::class)->required(),
            ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('resource_type')->formatStateUsing(fn (string $state) => class_basename($state))->badge(),
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
