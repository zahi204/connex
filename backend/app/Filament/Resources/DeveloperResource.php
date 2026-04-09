<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeveloperResource\Pages\CreateDeveloper;
use App\Filament\Resources\DeveloperResource\Pages\EditDeveloper;
use App\Filament\Resources\DeveloperResource\Pages\ListDevelopers;
use App\Filament\Resources\DeveloperResource\RelationManagers;
use App\Models\Developer;
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

class DeveloperResource extends Resource
{
    protected static ?string $model = Developer::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static string|\UnitEnum|null $navigationGroup = 'משאבים';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'יזם';

    protected static ?string $pluralModelLabel = 'יזמים';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                FormContainer::make([
                    Tabs::make('Developer')
                        ->tabs([
                            Tab::make('פרטי חברה')
                                ->schema([
                                    Forms\Components\TextInput::make('company_name')
                                        ->required()
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('registration_number')
                                        ->maxLength(50),
                                    Forms\Components\TextInput::make('email')
                                        ->email()
                                        ->maxLength(255),
                                    Forms\Components\Textarea::make('company_description')
                                        ->rows(3),
                                    Forms\Components\TextInput::make('company_size')
                                        ->maxLength(50),
                                    Forms\Components\SpatieMediaLibraryFileUpload::make('logo')
                                        ->collection('logo')
                                        ->image()
                                        ->maxSize(2048),
                                    Forms\Components\Select::make('user_id')
                                        ->relationship('user', 'phone')
                                        ->searchable()
                                        ->preload(),
                                ]),
                            Tab::make('תחומי פעילות')
                                ->schema([
                                    Forms\Components\TagsInput::make('areas_of_operation'),
                                    Forms\Components\TagsInput::make('specializations'),
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
                        ])
                        ->columnSpanFull(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('logo')
                    ->collection('logo')
                    ->circular(),
                Tables\Columns\TextColumn::make('company_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('registration_number')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('contact_person_name')
                    ->label('איש קשר'),
                Tables\Columns\TextColumn::make('projects_count')
                    ->counts('projects')
                    ->label('פרויקטים'),
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
            RelationManagers\ProjectsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDevelopers::route('/'),
            'create' => CreateDeveloper::route('/create'),
            'edit' => EditDeveloper::route('/{record}/edit'),
        ];
    }
}
