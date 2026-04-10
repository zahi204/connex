<?php

namespace App\Filament\Resources\WorkerResource\RelationManagers;

use App\Enums\DocumentType;
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

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    protected static ?string $title = 'מסמכים';

    protected static string|\BackedEnum|null $icon = 'heroicon-o-document-text';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                FormContainer::make([
                    Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('שם מסמך')
                                ->required()
                                ->maxLength(255)
                                ->prefixIcon('heroicon-o-document-text'),
                            Forms\Components\Select::make('document_type')
                                ->label('סוג מסמך')
                                ->options(DocumentType::class)
                                ->required()
                                ->prefixIcon('heroicon-o-tag'),
                        ]),
                    Forms\Components\SpatieMediaLibraryFileUpload::make('file')
                        ->label('קובץ')
                        ->collection('documents')
                        ->required()
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('notes')
                        ->label('הערות')
                        ->rows(2)
                        ->placeholder('הערות על המסמך...')
                        ->columnSpanFull(),
                ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('שם')
                    ->weight('bold')
                    ->searchable()
                    ->icon('heroicon-o-document-text'),
                Tables\Columns\TextColumn::make('document_type')
                    ->label('סוג')
                    ->badge()
                    ->icon('heroicon-o-tag'),
                Tables\Columns\TextColumn::make('notes')
                    ->label('הערות')
                    ->limit(40)
                    ->tooltip(fn ($state) => $state)
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('נוצר')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus')
                    ->label('העלאת מסמך'),
            ])
            ->actions([
                EditAction::make()
                    ->iconButton(),
                DeleteAction::make()
                    ->iconButton(),
            ])
            ->striped()
            ->emptyStateHeading('אין מסמכים')
            ->emptyStateDescription('העלה מסמכים כמו דרכון, אישורים, או חוזים')
            ->emptyStateIcon('heroicon-o-document-text');
    }
}
