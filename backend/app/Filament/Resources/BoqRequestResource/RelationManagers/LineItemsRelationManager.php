<?php

namespace App\Filament\Resources\BoqRequestResource\RelationManagers;

use App\Enums\UnitOfMeasure;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Form as FormContainer;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class LineItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'lineItems';

    protected static ?string $title = 'פריטי שורה';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                FormContainer::make([
                    Forms\Components\TextInput::make('trade')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('description')
                        ->required()
                        ->maxLength(500),
                    Forms\Components\Select::make('unit_of_measure')
                        ->options(UnitOfMeasure::class)
                        ->required(),
                    Forms\Components\TextInput::make('quantity')
                        ->numeric()
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(fn (Get $get, Set $set) => self::updateTotalCost($get, $set)),
                    Forms\Components\TextInput::make('unit_price')
                        ->numeric()
                        ->required()
                        ->prefix('SAR')
                        ->reactive()
                        ->afterStateUpdated(fn (Get $get, Set $set) => self::updateTotalCost($get, $set)),
                    Forms\Components\TextInput::make('total_cost')
                        ->numeric()
                        ->prefix('SAR')
                        ->disabled()
                        ->dehydrated(),
                ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('trade')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->limit(50),
                Tables\Columns\TextColumn::make('unit_of_measure')
                    ->badge(),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric(3)
                    ->alignEnd(),
                Tables\Columns\TextColumn::make('unit_price')
                    ->money('SAR')
                    ->alignEnd(),
                Tables\Columns\TextColumn::make('total_cost')
                    ->money('SAR')
                    ->alignEnd()
                    ->summarize(Tables\Columns\Summarizers\Sum::make()->money('SAR')),
            ])
            ->groups([
                Tables\Grouping\Group::make('trade')
                    ->collapsible()
                    ->getTitleFromRecordUsing(fn ($record): string => $record->trade),
            ])
            ->defaultGroup('trade')
            ->headerActions([
                CreateAction::make(),
                Action::make('import')
                    ->label('ייבוא')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('gray')
                    ->action(fn () => null),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected static function updateTotalCost(Get $get, Set $set): void
    {
        $quantity = (float) ($get('quantity') ?? 0);
        $unitPrice = (float) ($get('unit_price') ?? 0);

        $set('total_cost', round($quantity * $unitPrice, 2));
    }
}
