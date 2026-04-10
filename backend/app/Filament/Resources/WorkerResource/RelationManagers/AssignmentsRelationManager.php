<?php

namespace App\Filament\Resources\WorkerResource\RelationManagers;

use App\Enums\AssignmentStatus;
use App\Enums\EngagementType;
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

class AssignmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'assignments';

    protected static ?string $title = 'שיבוצים';

    protected static string|\BackedEnum|null $icon = 'heroicon-o-briefcase';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                FormContainer::make([
                    Grid::make(2)
                        ->schema([
                            Forms\Components\Select::make('project_id')
                                ->label('פרויקט')
                                ->relationship('project', 'name')
                                ->required()
                                ->searchable()
                                ->preload()
                                ->prefixIcon('heroicon-o-building-office-2'),
                            Forms\Components\Select::make('engagement_type')
                                ->label('סוג התקשרות')
                                ->options(EngagementType::class)
                                ->required()
                                ->prefixIcon('heroicon-o-document-text'),
                        ]),
                    Grid::make(2)
                        ->schema([
                            Forms\Components\DatePicker::make('start_date')
                                ->label('תאריך התחלה')
                                ->required()
                                ->prefixIcon('heroicon-o-calendar'),
                            Forms\Components\DatePicker::make('estimated_end_date')
                                ->label('תאריך סיום משוער')
                                ->prefixIcon('heroicon-o-calendar'),
                        ]),
                    Forms\Components\Textarea::make('work_description')
                        ->label('תיאור עבודה')
                        ->rows(2)
                        ->columnSpanFull(),
                    Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('price_per_day')
                                ->label('מחיר ליום')
                                ->numeric()
                                ->prefix('₪')
                                ->prefixIcon('heroicon-o-currency-dollar'),
                            Forms\Components\TextInput::make('contract_amount')
                                ->label('סכום חוזה')
                                ->numeric()
                                ->prefix('₪')
                                ->prefixIcon('heroicon-o-currency-dollar'),
                        ]),
                    Forms\Components\Select::make('status')
                        ->label('סטטוס')
                        ->options(AssignmentStatus::class)
                        ->required()
                        ->prefixIcon('heroicon-o-signal')
                        ->columnSpanFull(),
                ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('project.name')
                    ->label('פרויקט')
                    ->weight('bold')
                    ->searchable()
                    ->icon('heroicon-o-building-office-2'),
                Tables\Columns\TextColumn::make('engagement_type')
                    ->label('סוג')
                    ->badge(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('התחלה')
                    ->date('d/m/Y')
                    ->icon('heroicon-o-calendar'),
                Tables\Columns\TextColumn::make('estimated_end_date')
                    ->label('סיום')
                    ->date('d/m/Y')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('price_per_day')
                    ->label('מחיר/יום')
                    ->money('ILS')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('status')
                    ->label('סטטוס')
                    ->badge()
                    ->color(fn (AssignmentStatus $state): string => match ($state) {
                        AssignmentStatus::New => 'gray',
                        AssignmentStatus::InProgress => 'info',
                        AssignmentStatus::Completed => 'success',
                        AssignmentStatus::Cancelled => 'danger',
                        AssignmentStatus::Suspended => 'warning',
                    })
                    ->icon(fn (AssignmentStatus $state): string => match ($state) {
                        AssignmentStatus::New => 'heroicon-o-sparkles',
                        AssignmentStatus::InProgress => 'heroicon-o-play',
                        AssignmentStatus::Completed => 'heroicon-o-check-circle',
                        AssignmentStatus::Cancelled => 'heroicon-o-x-circle',
                        AssignmentStatus::Suspended => 'heroicon-o-pause-circle',
                    }),
            ])
            ->defaultSort('start_date', 'desc')
            ->headerActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus'),
            ])
            ->actions([
                EditAction::make()
                    ->iconButton(),
                DeleteAction::make()
                    ->iconButton(),
            ])
            ->striped()
            ->emptyStateHeading('אין שיבוצים')
            ->emptyStateDescription('צור שיבוץ חדש לעובד זה')
            ->emptyStateIcon('heroicon-o-briefcase');
    }
}
