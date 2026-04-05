<?php

namespace App\Filament\Resources;

use App\Actions\Boq\DeliverBoqAction;
use App\Enums\BoqRequestStatus;
use App\Enums\BoqUrgency;
use App\Filament\Resources\BoqRequestResource\RelationManagers;
use App\Models\BoqRequest;
use Filament\Forms;
use Filament\Schemas\Components\Form as FormContainer;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BoqRequestResource extends Resource
{
    protected static ?string $model = BoqRequest::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'BOQ';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'project_name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                FormContainer::make([
                Forms\Components\Tabs::make('BOQ Request')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Request Info')
                            ->schema([
                                Forms\Components\TextInput::make('project_name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('city')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('region')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('project_type')
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('scope_description')
                                    ->rows(4)
                                    ->columnSpanFull(),
                                Forms\Components\Select::make('urgency')
                                    ->options(BoqUrgency::class)
                                    ->required(),
                                Forms\Components\Select::make('status')
                                    ->options(BoqRequestStatus::class)
                                    ->required(),
                            ]),
                        Forms\Components\Tabs\Tab::make('Assignment')
                            ->schema([
                                Forms\Components\Select::make('requested_by_user_id')
                                    ->relationship('requestedBy', 'phone')
                                    ->searchable()
                                    ->preload()
                                    ->label('Requested By'),
                                Forms\Components\Select::make('prepared_by_user_id')
                                    ->relationship('preparedBy', 'phone')
                                    ->searchable()
                                    ->preload()
                                    ->label('Prepared By'),
                            ]),
                        Forms\Components\Tabs\Tab::make('Financial')
                            ->schema([
                                Forms\Components\TextInput::make('price')
                                    ->numeric()
                                    ->prefix('SAR')
                                    ->disabled()
                                    ->dehydrated(false),
                                Forms\Components\DateTimePicker::make('delivered_at')
                                    ->disabled()
                                    ->dehydrated(false),
                            ]),
                    ])
                    ->columnSpanFull(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                Tables\Columns\TextColumn::make('project_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('urgency')
                    ->badge()
                    ->color(fn (BoqUrgency $state): string => match ($state) {
                        BoqUrgency::Standard => 'gray',
                        BoqUrgency::Urgent => 'warning',
                        BoqUrgency::Express => 'danger',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (BoqRequestStatus $state): string => match ($state) {
                        BoqRequestStatus::New => 'gray',
                        BoqRequestStatus::UnderReview => 'warning',
                        BoqRequestStatus::InPreparation => 'info',
                        BoqRequestStatus::ReadyForReview => 'primary',
                        BoqRequestStatus::Delivered => 'success',
                        BoqRequestStatus::Cancelled => 'danger',
                    }),
                Tables\Columns\TextColumn::make('requestedBy.phone')
                    ->label('Requested By'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(BoqRequestStatus::class),
                Tables\Filters\SelectFilter::make('urgency')
                    ->options(BoqUrgency::class),
            ])
            ->actions([
                Tables\Actions\Action::make('accept')
                    ->label('Accept')
                    ->icon('heroicon-o-check-circle')
                    ->color('info')
                    ->requiresConfirmation()
                    ->visible(fn (BoqRequest $record): bool => in_array($record->status, [
                        BoqRequestStatus::New,
                        BoqRequestStatus::UnderReview,
                    ]))
                    ->action(function (BoqRequest $record): void {
                        $record->update([
                            'status' => BoqRequestStatus::InPreparation,
                            'prepared_by_user_id' => auth()->id(),
                        ]);
                    }),
                Tables\Actions\Action::make('deliver')
                    ->label('Deliver')
                    ->icon('heroicon-o-truck')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (BoqRequest $record): bool => $record->status === BoqRequestStatus::InPreparation
                        || $record->status === BoqRequestStatus::ReadyForReview)
                    ->action(fn (BoqRequest $record) => app(DeliverBoqAction::class)->execute($record)),
                Tables\Actions\EditAction::make(),
            ])
            ->headerActions([
                Tables\Actions\ExportAction::make()
                    ->label('Export Excel')
                    ->icon('heroicon-o-arrow-down-tray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\LineItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\BoqRequestResource\Pages\ListBoqRequests::route('/'),
            'create' => \App\Filament\Resources\BoqRequestResource\Pages\CreateBoqRequest::route('/create'),
            'edit' => \App\Filament\Resources\BoqRequestResource\Pages\EditBoqRequest::route('/{record}/edit'),
        ];
    }
}
