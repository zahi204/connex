<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Approval\SubmitForApprovalAction;
use App\Models\TrainingResult;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AgencyPortalController extends BaseApiController
{
    public function profile(Request $request): JsonResponse
    {
        return $this->success($request->user()->profile);
    }

    public function updateProfile(Request $request, SubmitForApprovalAction $action): JsonResponse
    {
        $data = $request->validate([
            'agency_name' => 'nullable|string|max:255',
            'contact_person_name' => 'nullable|string|max:255',
            'contact_person_phone' => 'nullable|string|max:20',
            'worker_types' => 'nullable|array',
            'countries_of_origin' => 'nullable|array',
        ]);

        $agency = $request->user()->profile;
        $action->execute($agency, 'profile_update', $data, $request->user());

        return $this->success(null, 'Profile update submitted for approval.');
    }

    public function workers(Request $request): JsonResponse
    {
        $agency = $request->user()->profile;
        return $this->success(
            $agency->workers()
                ->select('id', 'full_name', 'primary_skill', 'status', 'professional_rating', 'training_score')
                ->paginate(20)
        );
    }

    public function trainingResults(Request $request): JsonResponse
    {
        $agency = $request->user()->profile;
        $workerIds = $agency->workers()->pluck('id');

        $results = TrainingResult::whereIn('worker_id', $workerIds)
            ->with(['worker:id,full_name', 'trainingCycle:id,name'])
            ->latest()
            ->paginate(20);

        return $this->success($results);
    }

    public function billing(Request $request): JsonResponse
    {
        $agency = $request->user()->profile;

        return $this->success([
            'workers_trained' => $agency->workers_trained ?? 0,
            'training_fee_per_worker' => 3000,
            'total_fees' => $agency->training_billing,
            'payments_made' => $agency->payments_made ?? 0,
            'outstanding_balance' => $agency->outstanding_balance ?? 0,
        ]);
    }

    public function qualityMetrics(Request $request): JsonResponse
    {
        $agency = $request->user()->profile;

        return $this->success([
            'average_quality' => $agency->average_quality,
            'average_worker_rating' => $agency->average_worker_rating,
            'total_workers' => $agency->workers()->count(),
        ]);
    }
}
