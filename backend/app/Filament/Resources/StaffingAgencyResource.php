<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StaffingAgencyResource\RelationManagers;
use App\Models\StaffingAgency;
use Filament\Forms;
use Filament\Schemas\Components\Form as FormContainer;
use Filament\Schemas\Schema;
use Filament\Infolists;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StaffingAgencyResource extends Resource
{
    protected static ?string $model = StaffingAgency::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';

    protected static string|\UnitEnum|null $navigationGroup = 'Resources';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                FormContainer::make([
                Forms\Components\Tabs::make('Agency')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Agency Info')
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
                        Forms\Components\Tabs\Tab::make('Contact Person')
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
                        Forms\Components\Tabs\Tab::make('Worker Types')
                            ->schema([
                                Forms\Components\TagsInput::make('worker_types'),
                                Forms\Components\TagsInput::make('countries_of_origin'),
                                Forms\Components\TextInput::make('average_capacity')
                                    ->numeric(),
                                Forms\Components\TextInput::make('monthly_throughput')
                                    ->numeric(),
                            ]),
                        Forms\Components\Tabs\Tab::make('Billing')
                            ->schema([
                                Forms\Components\TextInput::make('workers_trained')
                                    ->numeric()
                                    ->disabled(),
                                Forms\Components\Placeholder::make('training_billing')
                                    ->label('Training Fees Owed')
                                    ->content(fn (?StaffingAgency $record) => $record ? number_format($record->training_billing, 2) . ' NIS' : '-'),
                                Forms\Components\TextInput::make('payments_made')
                                    ->numeric()
                                    ->prefix('NIS')
                                    ->disabled(),
                                Forms\Components\TextInput::make('outstanding_balance')
                                    ->numeric()
                                    ->prefix('NIS')
                                    ->disabled(),
                            ]),
                        Forms\Components\Tabs\Tab::make('Quality')
                            ->schema([
                                Forms\Components\TextInput::make('average_quality')
                                    ->numeric()
                                    ->step(0.01)
                                    ->disabled(),
                                Forms\Components\Placeholder::make('avg_worker_rating')
                                    ->label('Avg. Worker Rating')
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
                    ->label('Contact'),
                Tables\Columns\TextColumn::make('workers_count')
                    ->counts('workers')
                    ->label('Workers'),
                Tables\Columns\TextColumn::make('workers_trained')
                    ->label('Trained'),
                Tables\Columns\TextColumn::make('average_quality')
                    ->numeric(2)
                    ->label('Quality'),
                Tables\Columns\TextColumn::make('outstanding_balance')
                    ->money('ILS')
                    ->label('Balance'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => \App\Filament\Resources\StaffingAgencyResource\Pages\ListStaffingAgencies::route('/'),
            'create' => \App\Filament\Resources\StaffingAgencyResource\Pages\CreateStaffingAgency::route('/create'),
            'edit' => \App\Filament\Resources\StaffingAgencyResource\Pages\EditStaffingAgency::route('/{record}/edit'),
        ];
    }
}
