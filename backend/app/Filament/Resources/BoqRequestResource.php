<?php

namespace App\Filament\Resources;

use App\Actions\Boq\DeliverBoqAction;
use App\Enums\BoqRequestStatus;
use App\Enums\BoqUrgency;
use App\Filament\Resources\BoqRequestResource\Pages\CreateBoqRequest;
use App\Filament\Resources\BoqRequestResource\Pages\EditBoqRequest;
use App\Filament\Resources\BoqRequestResource\Pages\ListBoqRequests;
use App\Filament\Resources\BoqRequestResource\RelationManagers;
use App\Models\BoqRequest;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Form as FormContainer;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class BoqRequestResource extends Resource
{
    protected static ?string $model = BoqRequest::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'כתב כמויות';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'project_name';

    protected static ?string $modelLabel = 'בקשת כמויות';

    protected static ?string $pluralModelLabel = 'בקשות כמויות';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                FormContainer::make([
                    Tabs::make('BOQ Request')
                        ->tabs([
                            Tab::make('פרטי בקשה')
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
                            Tab::make('שיבוץ')
                                ->schema([
                                    Forms\Components\Select::make('requested_by_user_id')
                                        ->relationship('requestedBy', 'phone')
                                        ->searchable()
                                        ->preload()
                                        ->label('הוגש על ידי'),
                                    Forms\Components\Select::make('prepared_by_user_id')
                                        ->relationship('preparedBy', 'phone')
                                        ->searchable()
                                        ->preload()
                                        ->label('הוכן על ידי'),
                                ]),
                            Tab::make('פיננסי')
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
                    ->label('הוגש על ידי'),
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
                Action::make('accept')
                    ->label('קבל')
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
                Action::make('deliver')
                    ->label('מסור')
                    ->icon('heroicon-o-truck')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (BoqRequest $record): bool => $record->status === BoqRequestStatus::InPreparation
                        || $record->status === BoqRequestStatus::ReadyForReview)
                    ->action(fn (BoqRequest $record) => app(DeliverBoqAction::class)->execute($record)),
                EditAction::make(),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label('ייצוא לאקסל')
                    ->icon('heroicon-o-arrow-down-tray'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => ListBoqRequests::route('/'),
            'create' => CreateBoqRequest::route('/create'),
            'edit' => EditBoqRequest::route('/{record}/edit'),
        ];
    }
}
