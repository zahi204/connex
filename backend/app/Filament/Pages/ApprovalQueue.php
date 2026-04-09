<?php

namespace App\Filament\Pages;

use App\Actions\Approval\ApproveItemAction;
use App\Actions\Approval\BulkApproveAction;
use App\Actions\Approval\RejectItemAction;
use App\Actions\Approval\RequestChangesAction;
use App\Enums\ApprovalActionType;
use App\Enums\ApprovalStatus;
use App\Models\ApprovalQueue as ApprovalQueueModel;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Forms;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class ApprovalQueue extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static string|\UnitEnum|null $navigationGroup = 'תפעול';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'תור אישורים';

    protected static ?string $title = 'תור אישורים';

    protected string $view = 'filament.pages.approval-queue';

    public function table(Table $table): Table
    {
        return $table
            ->query(ApprovalQueueModel::query()->with(['submittedBy', 'reviewedBy', 'approvable']))
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('מזהה')
                    ->sortable(),
                Tables\Columns\TextColumn::make('entity_name')
                    ->label('ישות')
                    ->getStateUsing(function (ApprovalQueueModel $record): string {
                        try {
                            $entity = $record->approvable;
                            if (! $entity) {
                                return class_basename($record->approvable_type).' #'.$record->approvable_id;
                            }
                            $name = $entity->full_name
                                ?? $entity->name
                                ?? $entity->agency_name
                                ?? $entity->title
                                ?? null;

                            return class_basename($record->approvable_type).': '.($name ?? "#{$entity->id}");
                        } catch (\Throwable) {
                            return class_basename($record->approvable_type).' #'.$record->approvable_id;
                        }
                    })
                    ->searchable(false),
                Tables\Columns\TextColumn::make('submittedBy.phone')
                    ->label('הוגש על ידי')
                    ->searchable(),
                Tables\Columns\TextColumn::make('approvable_type')
                    ->label('סוג ישות')
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
                    ->label('תאריך הגשה')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('age')
                    ->label('גיל')
                    ->getStateUsing(fn (ApprovalQueueModel $record) => $record->created_at->diffForHumans(short: true)),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('action_type')
                    ->options(ApprovalActionType::class),
                Tables\Filters\SelectFilter::make('status')
                    ->options(ApprovalStatus::class)
                    ->default('pending'),
                Tables\Filters\SelectFilter::make('approvable_type')
                    ->label('סוג ישות')
                    ->options(fn () => ApprovalQueueModel::query()
                        ->distinct()
                        ->pluck('approvable_type')
                        ->mapWithKeys(fn ($t) => [$t => class_basename($t)])
                        ->toArray()
                    ),
            ])
            ->actions([
                Action::make('view_diff')
                    ->label('סקירה')
                    ->icon('heroicon-o-eye')
                    ->modalContent(function (ApprovalQueueModel $record) {
                        $submitted = $record->submitted_data ?? [];
                        $current = [];
                        try {
                            $entity = $record->approvable;
                            if ($entity) {
                                $current = $entity->only(array_keys($submitted));
                            }
                        } catch (\Throwable) {
                            // entity may be deleted
                        }

                        $entityName = null;
                        try {
                            $entity ??= $record->approvable;
                            $entityName = $entity?->full_name
                                ?? $entity?->name
                                ?? $entity?->agency_name
                                ?? null;
                        } catch (\Throwable) {
                        }

                        return view('filament.pages.approval-diff', [
                            'submitted' => $submitted,
                            'current' => $current,
                            'record' => $record,
                            'entityName' => $entityName,
                        ]);
                    })
                    ->modalHeading(fn (ApprovalQueueModel $r) => 'סקירה — '.($r->action_type?->value ?? $r->action_type))
                    ->modalSubmitAction(false)
                    ->modalWidth('2xl'),
                Action::make('approve')
                    ->label('אשר')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (ApprovalQueueModel $record) => $record->status === ApprovalStatus::Pending)
                    ->action(function (ApprovalQueueModel $record) {
                        app(ApproveItemAction::class)->execute($record, auth()->user());
                    }),
                Action::make('reject')
                    ->label('דחה')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (ApprovalQueueModel $record) => $record->status === ApprovalStatus::Pending)
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('סיבה')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (ApprovalQueueModel $record, array $data) {
                        app(RejectItemAction::class)->execute($record, auth()->user(), $data['rejection_reason']);
                    }),
                Action::make('request_changes')
                    ->label('בקש שינויים')
                    ->icon('heroicon-o-pencil-square')
                    ->color('info')
                    ->visible(fn (ApprovalQueueModel $record) => $record->status === ApprovalStatus::Pending)
                    ->form([
                        Forms\Components\Textarea::make('change_notes')
                            ->label('הערות')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (ApprovalQueueModel $record, array $data) {
                        app(RequestChangesAction::class)->execute($record, auth()->user(), $data['change_notes']);
                    }),
                Action::make('go_to_entity')
                    ->label('צפה בישות')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(function (ApprovalQueueModel $record): ?string {
                        $type = class_basename($record->approvable_type);
                        $id = $record->approvable_id;

                        return match ($type) {
                            'Worker' => route('filament.admin.resources.workers.edit', $id),
                            default => null,
                        };
                    })
                    ->openUrlInNewTab()
                    ->visible(fn (ApprovalQueueModel $r) => $r->approvable_id !== null),
            ])
            ->bulkActions([
                BulkAction::make('bulk_approve')
                    ->label('אשר נבחרים')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->deselectRecordsAfterCompletion()
                    ->action(function ($records) {
                        app(BulkApproveAction::class)->execute($records, auth()->user());
                    }),
                BulkAction::make('bulk_request_changes')
                    ->label('בקש שינויים (קבוצה)')
                    ->icon('heroicon-o-pencil-square')
                    ->color('info')
                    ->form([
                        Forms\Components\Textarea::make('change_notes')
                            ->label('סיבה משותפת')
                            ->required()
                            ->rows(3),
                    ])
                    ->deselectRecordsAfterCompletion()
                    ->action(function ($records, array $data) {
                        foreach ($records as $record) {
                            if ($record->status === ApprovalStatus::Pending) {
                                app(RequestChangesAction::class)
                                    ->execute($record, auth()->user(), $data['change_notes']);
                            }
                        }
                    }),
            ]);
    }
}
