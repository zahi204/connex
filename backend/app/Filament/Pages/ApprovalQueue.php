<?php

namespace App\Filament\Pages;

use App\Enums\ApprovalActionType;
use App\Enums\ApprovalStatus;
use App\Models\ApprovalQueue as ApprovalQueueModel;
use Filament\Forms;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class ApprovalQueue extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 10;

    protected static string $view = 'filament.pages.approval-queue';

    public function table(Table $table): Table
    {
        return $table
            ->query(ApprovalQueueModel::query()->with(['submittedBy', 'reviewedBy']))
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('submittedBy.phone')
                    ->label('Submitted By')
                    ->searchable(),
                Tables\Columns\TextColumn::make('approvable_type')
                    ->label('Entity Type')
                    ->formatStateUsing(fn (string $state) => class_basename($state))
                    ->badge(),
                Tables\Columns\TextColumn::make('action_type')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (ApprovalStatus $state): string => match ($state) {
                        ApprovalStatus::Pending => 'warning',
                        ApprovalStatus::Approved => 'success',
                        ApprovalStatus::Rejected => 'danger',
                        ApprovalStatus::ChangesRequired => 'info',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted At')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('age')
                    ->label('Age')
                    ->getStateUsing(fn (ApprovalQueueModel $record) => $record->created_at->diffForHumans(short: true)),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('action_type')
                    ->options(ApprovalActionType::class),
                Tables\Filters\SelectFilter::make('status')
                    ->options(ApprovalStatus::class)
                    ->default('pending'),
            ])
            ->actions([
                Tables\Actions\Action::make('view_data')
                    ->label('View Data')
                    ->icon('heroicon-o-eye')
                    ->modalContent(fn (ApprovalQueueModel $record) => view('filament.pages.approval-data', ['data' => $record->submitted_data]))
                    ->modalHeading('Submitted Data')
                    ->modalSubmitAction(false),
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (ApprovalQueueModel $record) => $record->status === ApprovalStatus::Pending)
                    ->action(function (ApprovalQueueModel $record) {
                        app(\App\Actions\Approval\ApproveItemAction::class)->execute($record, auth()->user());
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (ApprovalQueueModel $record) => $record->status === ApprovalStatus::Pending)
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Reason')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (ApprovalQueueModel $record, array $data) {
                        app(\App\Actions\Approval\RejectItemAction::class)->execute($record, auth()->user(), $data['rejection_reason']);
                    }),
                Tables\Actions\Action::make('request_changes')
                    ->label('Request Changes')
                    ->icon('heroicon-o-pencil-square')
                    ->color('info')
                    ->visible(fn (ApprovalQueueModel $record) => $record->status === ApprovalStatus::Pending)
                    ->form([
                        Forms\Components\Textarea::make('change_notes')
                            ->label('Notes')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (ApprovalQueueModel $record, array $data) {
                        app(\App\Actions\Approval\RequestChangesAction::class)->execute($record, auth()->user(), $data['change_notes']);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('bulk_approve')
                    ->label('Approve Selected')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->deselectRecordsAfterCompletion()
                    ->action(function ($records) {
                        app(\App\Actions\Approval\BulkApproveAction::class)->execute($records, auth()->user());
                    }),
            ]);
    }
}
