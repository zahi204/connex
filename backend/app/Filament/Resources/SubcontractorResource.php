<?php

namespace App\Filament\Resources;

use App\Enums\SubcontractorStatus;
use App\Filament\Resources\SubcontractorResource\Pages\CreateSubcontractor;
use App\Filament\Resources\SubcontractorResource\Pages\EditSubcontractor;
use App\Filament\Resources\SubcontractorResource\Pages\ListSubcontractors;
use App\Filament\Resources\SubcontractorResource\RelationManagers;
use App\Models\Subcontractor;
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

class SubcontractorResource extends Resource
{
    protected static ?string $model = Subcontractor::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static string|\UnitEnum|null $navigationGroup = 'משאבים';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'קבלן משנה';

    protected static ?string $pluralModelLabel = 'קבלני משנה';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                FormContainer::make([
                    Tabs::make('Subcontractor')
                        ->tabs([
                            Tab::make('פרטים בסיסיים')
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
                            Tab::make('התמחויות')
                                ->schema([
                                    Forms\Components\TagsInput::make('specializations'),
                                    Forms\Components\TagsInput::make('operating_areas'),
                                ]),
                            Tab::make('כושר')
                                ->schema([
                                    Forms\Components\TextInput::make('number_of_workers')
                                        ->numeric(),
                                    Forms\Components\TextInput::make('years_of_experience')
                                        ->numeric(),
                                    Forms\Components\Textarea::make('notable_projects')
                                        ->rows(3),
                                ]),
                            Tab::make('דירוג')
                                ->schema([
                                    Forms\Components\TextInput::make('rating')
                                        ->numeric()
                                        ->step(0.01)
                                        ->minValue(0)
                                        ->maxValue(10),
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
            RelationManagers\AssignmentsRelationManager::class,
            RelationManagers\DocumentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSubcontractors::route('/'),
            'create' => CreateSubcontractor::route('/create'),
            'edit' => EditSubcontractor::route('/{record}/edit'),
        ];
    }
}
