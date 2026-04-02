<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use App\Enums\NoteType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class NotesRelationManager extends RelationManager
{
    protected static string $relationship = 'notes';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('note_type')->options(NoteType::class)->required(),
            Forms\Components\Textarea::make('content')->required()->rows(4),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('note_type')->badge(),
                Tables\Columns\TextColumn::make('content')->limit(80),
                Tables\Columns\TextColumn::make('author.phone')->label('Author'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->defaultSort('created_at', 'desc');
    }
}
