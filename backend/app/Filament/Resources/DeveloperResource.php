<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeveloperResource\RelationManagers;
use App\Models\Developer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DeveloperResource extends Resource
{
    protected static ?string $model = Developer::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Resources';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Developer')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Company Info')
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
                        Forms\Components\Tabs\Tab::make('Operations')
                            ->schema([
                                Forms\Components\TagsInput::make('areas_of_operation'),
                                Forms\Components\TagsInput::make('specializations'),
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
                    ])
                    ->columnSpanFull(),
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
                    ->label('Contact'),
                Tables\Columns\TextColumn::make('projects_count')
                    ->counts('projects')
                    ->label('Projects'),
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
            RelationManagers\ProjectsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\DeveloperResource\Pages\ListDevelopers::route('/'),
            'create' => \App\Filament\Resources\DeveloperResource\Pages\CreateDeveloper::route('/create'),
            'edit' => \App\Filament\Resources\DeveloperResource\Pages\EditDeveloper::route('/{record}/edit'),
        ];
    }
}
