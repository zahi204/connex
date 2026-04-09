<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StaffingAgencyResource\Pages\CreateStaffingAgency;
use App\Filament\Resources\StaffingAgencyResource\Pages\EditStaffingAgency;
use App\Filament\Resources\StaffingAgencyResource\Pages\ListStaffingAgencies;
use App\Filament\Resources\StaffingAgencyResource\RelationManagers;
use App\Models\StaffingAgency;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Form as FormContainer;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class StaffingAgencyResource extends Resource
{
    protected static ?string $model = StaffingAgency::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';

    protected static string|\UnitEnum|null $navigationGroup = 'משאבים';

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'סוכנות כח אדם';

    protected static ?string $pluralModelLabel = 'סוכנויות כח אדם';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                FormContainer::make([
                    Tabs::make('Agency')
                        ->tabs([
                            Tab::make('פרטי סוכנות')
                                ->schema([
                                    Forms\Components\TextInput::make('agency_name')
                                        ->required()
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('registration_number')
                                        ->maxLength(50),
                                    Forms\Components\TextInput::make('email')
                                        ->email()
                                        ->maxLength(255),
                                    Forms\Components\Select::make('user_id')
                                        ->relationship('user', 'phone')
                                        ->searchable()
                                        ->preload(),
                                ]),
                            Tab::make('איש קשר')
                                ->schema([
                                    Forms\Components\TextInput::make('contact_person_name')
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('contact_person_role')
                                        ->maxLength(100),
                                    Forms\Components\TextInput::make('contact_person_phone')
                                        ->tel()
                                        ->maxLength(20),
                                    Forms\Components\TextInput::make('contact_person_email')
                                        ->email()
                                        ->maxLength(255),
                                ]),
                            Tab::make('סוגי עובדים')
                                ->schema([
                                    Forms\Components\TagsInput::make('worker_types'),
                                    Forms\Components\TagsInput::make('countries_of_origin'),
                                    Forms\Components\TextInput::make('average_capacity')
                                        ->numeric(),
                                    Forms\Components\TextInput::make('monthly_throughput')
                                        ->numeric(),
                                ]),
                            Tab::make('חיוב')
                                ->schema([
                                    Forms\Components\TextInput::make('workers_trained')
                                        ->numeric()
                                        ->disabled(),
                                    Forms\Components\Placeholder::make('training_billing')
                                        ->label('עמלות הכשרה לתשלום')
                                        ->content(fn (?StaffingAgency $record) => $record ? number_format($record->training_billing, 2).' NIS' : '-'),
                                    Forms\Components\TextInput::make('payments_made')
                                        ->numeric()
                                        ->prefix('NIS')
                                        ->disabled(),
                                    Forms\Components\TextInput::make('outstanding_balance')
                                        ->numeric()
                                        ->prefix('NIS')
                                        ->disabled(),
                                ]),
                            Tab::make('איכות')
                                ->schema([
                                    Forms\Components\TextInput::make('average_quality')
                                        ->numeric()
                                        ->step(0.01)
                                        ->disabled(),
                                    Forms\Components\Placeholder::make('avg_worker_rating')
                                        ->label('דירוג עובד ממוצע')
                                        ->content(fn (?StaffingAgency $record) => $record?->average_worker_rating ? number_format($record->average_worker_rating, 2) : 'N/A'),
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
                Tables\Columns\TextColumn::make('agency_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('contact_person_name')
                    ->label('איש קשר'),
                Tables\Columns\TextColumn::make('workers_count')
                    ->counts('workers')
                    ->label('עובדים'),
                Tables\Columns\TextColumn::make('workers_trained')
                    ->label('מוכשרים'),
                Tables\Columns\TextColumn::make('average_quality')
                    ->numeric(2)
                    ->label('איכות'),
                Tables\Columns\TextColumn::make('outstanding_balance')
                    ->money('ILS')
                    ->label('יתרה'),
            ])
            ->actions([
                EditAction::make(),
                ViewAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\WorkersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStaffingAgencies::route('/'),
            'create' => CreateStaffingAgency::route('/create'),
            'edit' => EditStaffingAgency::route('/{record}/edit'),
        ];
    }
}
