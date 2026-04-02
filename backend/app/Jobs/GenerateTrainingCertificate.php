<?php

namespace App\Jobs;

use App\Models\TrainingResult;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateTrainingCertificate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public TrainingResult $result) {}

    public function handle(): void
    {
        $result = $this->result->load(['worker', 'trainingCycle']);
        $certNumber = 'CERT-' . str_pad($result->id, 6, '0', STR_PAD_LEFT);

        $pdf = Pdf::loadView('certificates.training', [
            'worker_name' => $result->worker->full_name,
            'training_name' => $result->trainingCycle->name,
            'start_date' => $result->trainingCycle->start_date->format('d/m/Y'),
            'end_date' => $result->trainingCycle->end_date->format('d/m/Y'),
            'score' => $result->professional_score,
            'classification' => $result->classification,
            'cert_number' => $certNumber,
        ]);

        $path = "certificates/{$certNumber}.pdf";
        \Storage::put($path, $pdf->output());

        $result->update(['certificate_path' => $path]);
    }
}
