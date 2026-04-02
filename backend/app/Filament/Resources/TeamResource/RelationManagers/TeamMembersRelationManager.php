<?php

namespace App\Filament\Resources\TeamResource\RelationManagers;

use App\Models\Worker;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TeamMembersRelationManager extends RelationManager
{
    protected static string $relationship = 'members';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('worker_id')
                    ->label('Worker')
                    ->options(Worker::query()->pluck('full_name', 'id'))
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('role')
                    ->maxLength(100),
                Forms\Components\DatePicker::make('joined_at')
                    ->default(now()),
                Forms\Components\DatePicker::make('left_at'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('worker.full_name')
                    ->label('Worker')
                    ->searchable(),
                Tables\Columns\TextColumn::make('role'),
                Tables\Columns\TextColumn::make('worker.primary_skill')
                    ->label('Skill')
                    ->badge(),
                Tables\Columns\TextColumn::make('worker.professional_rating')
                    ->label('Rating')
                    ->numeric(2),
                Tables\Columns\TextColumn::make('joined_at')
                    ->date(),
                Tables\Columns\TextColumn::make('left_at')
                    ->date()
                    ->placeholder('Active'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('remove')
                    ->label('Remove')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['left_at' => now()]);
                    }),
            ]);
    }
}
