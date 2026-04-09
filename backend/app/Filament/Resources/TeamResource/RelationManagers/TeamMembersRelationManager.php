<?php

namespace App\Filament\Resources\TeamResource\RelationManagers;

use App\Models\Worker;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Form as FormContainer;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class TeamMembersRelationManager extends RelationManager
{
    protected static string $relationship = 'members';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                FormContainer::make([
                    Forms\Components\Select::make('worker_id')
                        ->label('עובד')
                        ->options(Worker::query()->pluck('full_name', 'id'))
                        ->required()
                        ->searchable(),
                    Forms\Components\TextInput::make('role')
                        ->maxLength(100),
                    Forms\Components\DatePicker::make('joined_at')
                        ->default(now()),
                    Forms\Components\DatePicker::make('left_at'),
                ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('worker.full_name')
                    ->label('עובד')
                    ->searchable(),
                Tables\Columns\TextColumn::make('role'),
                Tables\Columns\TextColumn::make('worker.primary_skill')
                    ->label('מיומנות')
                    ->badge(),
                Tables\Columns\TextColumn::make('worker.professional_rating')
                    ->label('דירוג')
                    ->numeric(2),
                Tables\Columns\TextColumn::make('joined_at')
                    ->date(),
                Tables\Columns\TextColumn::make('left_at')
                    ->date()
                    ->placeholder('פעיל'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                Action::make('remove')
                    ->label('הסר')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['left_at' => now()]);
                    }),
            ]);
    }
}
