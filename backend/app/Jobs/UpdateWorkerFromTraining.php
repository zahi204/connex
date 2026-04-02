<?php

namespace App\Jobs;

use App\Enums\Suitability;
use App\Enums\WorkerStatus;
use App\Models\TrainingResult;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateWorkerFromTraining implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public TrainingResult $result) {}

    public function handle(): void
    {
        $result = $this->result->load(['worker', 'trainingCycle']);
        $worker = $result->worker;

        if (!$worker) return;

        $worker->update([
            'training_score' => $result->professional_score,
            'last_training_date' => $result->trainingCycle->end_date,
        ]);

        if ($result->suitability === Suitability::NotSuitable) {
            $worker->update(['status' => WorkerStatus::Inactive]);
        }

        // Update agency billing if worker has agency
        if ($worker->staffing_agency_id) {
            $agency = $worker->staffingAgency;
            if ($agency) {
                $agency->increment('workers_trained');
                $agency->update([
                    'outstanding_balance' => ($agency->workers_trained * 3000) - $agency->payments_made,
                ]);
            }
        }

        // Recalculate team ratings
        $worker->teamMemberships()
            ->whereNull('left_at')
            ->each(fn ($membership) => $membership->team->recalculateAverageRating());
    }
}
