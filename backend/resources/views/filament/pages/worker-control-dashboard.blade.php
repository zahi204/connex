<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Status breakdown - full width --}}
        <div>
            @livewire(\App\Filament\Widgets\WorkerStatusBreakdownWidget::class)
        </div>

        {{-- Main grid: 2/3 + 1/3 layout on xl --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            {{-- Left column: tables --}}
            <div class="xl:col-span-2 space-y-6">
                @livewire(\App\Filament\Widgets\TopRatedAvailableWidget::class)
                @livewire(\App\Filament\Widgets\OverduePaymentsWidget::class)
            </div>

            {{-- Right column: stats & pipeline --}}
            <div class="space-y-6">
                @livewire(\App\Filament\Widgets\PendingApprovalsWidget::class)
                @livewire(\App\Filament\Widgets\ActiveAssignmentsWidget::class)
                @livewire(\App\Filament\Widgets\TrainingPipelineWidget::class)
            </div>
        </div>
    </div>
</x-filament-panels::page>
