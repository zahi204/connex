<?php

namespace App\Filament\Resources\WorkerResource\Pages;

use App\Enums\UserRole;
use App\Enums\WorkerStatus;
use App\Filament\Resources\WorkerResource;
use App\Models\User;
use App\Models\Worker;
use Filament\Actions;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\EditRecord;

class EditWorker extends EditRecord
{
    protected static string $resource = WorkerResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $phone = $data['phone'] ?? null;
        unset($data['phone']);

        if ($phone) {
            $user = User::firstOrCreate(
                ['phone' => $phone],
                [
                    'role' => UserRole::Worker,
                    'is_active' => true,
                ],
            );

            $data['user_id'] = $user->id;
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('change_status')
                ->label('שינוי סטטוס')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->form([
                    \Filament\Forms\Components\Select::make('status')
                        ->label('סטטוס חדש')
                        ->options(WorkerStatus::class)
                        ->required()
                        ->default(fn () => $this->record->status),
                ])
                ->action(function (array $data): void {
                    $old = $this->record->status?->value;
                    $this->record->update(['status' => $data['status']]);
                    activity()
                        ->performedOn($this->record)
                        ->causedBy(auth()->user())
                        ->withProperties(['old_status' => $old, 'new_status' => $data['status']])
                        ->log("Status changed from {$old} to {$data['status']}");
                    \Filament\Notifications\Notification::make()->title('הסטטוס עודכן.')->success()->send();
                }),
            Actions\Action::make('block_toggle')
                ->label(fn () => $this->record->blocked ? 'שחרר חסימה' : 'חסום עובד')
                ->icon(fn () => $this->record->blocked ? 'heroicon-o-lock-open' : 'heroicon-o-no-symbol')
                ->color(fn () => $this->record->blocked ? 'success' : 'danger')
                ->requiresConfirmation()
                ->action(function (): void {
                    $blocking = ! $this->record->blocked;
                    $this->record->update([
                        'blocked' => $blocking,
                        'eligible_for_assignment' => $blocking ? false : $this->record->eligible_for_assignment,
                    ]);
                    activity()
                        ->performedOn($this->record)
                        ->causedBy(auth()->user())
                        ->log($blocking ? 'Worker blocked' : 'Worker unblocked');
                    \Filament\Notifications\Notification::make()
                        ->title($blocking ? 'העובד נחסם.' : 'חסימת העובד בוטלה.')
                        ->success()
                        ->send();
                }),
            Actions\DeleteAction::make()
                ->icon('heroicon-o-trash'),
        ];
    }
}
