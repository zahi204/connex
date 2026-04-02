<?php

namespace App\Filament\Resources;

use App\Enums\SubcontractorStatus;
use App\Filament\Resources\SubcontractorResource\RelationManagers;
use App\Models\Subcontractor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SubcontractorResource extends Resource
{
    protected static ?string $model = Subcontractor::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationGroup = 'Resources';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Subcontractor')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Basic Info')
                            ->schema([
                                Forms\Components\TextInput::make('name')
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
                                Forms\Components\Select::make('status')
                                    ->options(SubcontractorStatus::class)
                                    ->required(),
                                Forms\Components\DatePicker::make('availability_date'),
                            ]),
                        Forms\Components\Tabs\Tab::make('Specializations')
                            ->schema([
                                Forms\Components\TagsInput::make('specializations'),
                                Forms\Components\TagsInput::make('operating_areas'),
                            ]),
                        Forms\Components\Tabs\Tab::make('Capacity')
                            ->schema([
                                Forms\Components\TextInput::make('number_of_workers')
                                    ->numeric(),
                                Forms\Components\TextInput::make('years_of_experience')
                                    ->numeric(),
                                Forms\Components\Textarea::make('notable_projects')
                                    ->rows(3),
                            ]),
                        Forms\Components\Tabs\Tab::make('Rating')
                            ->schema([
                                Forms\Components\TextInput::make('rating')
                                    ->numeric()
                                    ->step(0.01)
                                    ->minValue(0)
                                    ->maxValue(10),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (SubcontractorStatus $state): string => match ($state) {
                        SubcontractorStatus::Available => 'success',
                        SubcontractorStatus::Busy => 'info',
                        SubcontractorStatus::AvailableSoon => 'warning',
                        SubcontractorStatus::Inactive => 'danger',
                    }),
                Tables\Columns\TextColumn::make('specializations')
                    ->badge()
                    ->separator(','),
                Tables\Columns\TextColumn::make('rating')
                    ->numeric(2)
                    ->sortable(),
                Tables\Columns\TextColumn::make('number_of_workers'),
                Tables\Columns\TextColumn::make('availability_date')
                    ->date(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(SubcontractorStatus::class),
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
            RelationManagers\AssignmentsRelationManager::class,
            RelationManagers\DocumentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\SubcontractorResource\Pages\ListSubcontractors::route('/'),
            'create' => \App\Filament\Resources\SubcontractorResource\Pages\CreateSubcontractor::route('/create'),
            'edit' => \App\Filament\Resources\SubcontractorResource\Pages\EditSubcontractor::route('/{record}/edit'),
        ];
    }
}
