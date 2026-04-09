<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Approval\SubmitForApprovalAction;
use App\Http\Resources\WorkerFullResource;
use App\Models\Worker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpResponseException;

class WorkerPortalController extends BaseApiController
{
    private function authenticatedWorker(Request $request): Worker
    {
        $profile = $request->user()->profile;
        if (! $profile instanceof Worker) {
            throw new HttpResponseException(
                $this->error(
                    'Worker profile not found. Finish onboarding or contact support if this persists.',
                    404
                )
            );
        }

        return $profile;
    }

    public function profile(Request $request): JsonResponse
    {
        $worker = $this->authenticatedWorker($request);

        return $this->success(new WorkerFullResource($worker));
    }

    public function updateProfile(Request $request, SubmitForApprovalAction $action): JsonResponse
    {
        $data = $request->validate([
            'full_name' => 'nullable|string|max:255',
            'primary_skill' => 'nullable|string',
            'secondary_skill' => 'nullable|string',
            'preferred_work_area' => 'nullable|string',
            'available_daily' => 'nullable|boolean',
            'available_contract' => 'nullable|boolean',
            'languages' => 'nullable|array',
        ]);

        $worker = $this->authenticatedWorker($request);
        $action->execute($worker, 'profile_update', $data, $request->user());

        return $this->success(null, 'Profile update submitted for approval.');
    }

    public function assignments(Request $request): JsonResponse
    {
        $worker = $this->authenticatedWorker($request);
        $assignments = $worker->assignments()
            ->with('project:id,name')
            ->latest()
            ->paginate(20);

        return $this->success($assignments);
    }

    public function team(Request $request): JsonResponse
    {
        $worker = $this->authenticatedWorker($request);
        $membership = $worker->teamMemberships()
            ->whereNull('left_at')
            ->with(['team.teamLeader:id,full_name', 'team.members.worker:id,full_name'])
            ->first();

        return $this->success($membership);
    }

    public function training(Request $request): JsonResponse
    {
        $worker = $this->authenticatedWorker($request);
        $results = $worker->trainingResults()
            ->with('trainingCycle:id,name,start_date,end_date')
            ->latest()
            ->get();

        return $this->success($results);
    }

    public function documents(Request $request): JsonResponse
    {
        $worker = $this->authenticatedWorker($request);

        return $this->success($worker->documents()->latest()->get());
    }

    public function uploadDocument(Request $request, SubmitForApprovalAction $action): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'document_type' => 'required|string',
        ]);

        $worker = $this->authenticatedWorker($request);
        $action->execute($worker, 'document_upload', $data, $request->user());

        return $this->success(null, 'Document upload submitted for approval.');
    }

    public function payments(Request $request): JsonResponse
    {
        $worker = $this->authenticatedWorker($request);

        return $this->success($worker->payments()->latest()->paginate(20));
    }

    public function updateAvailability(Request $request, SubmitForApprovalAction $action): JsonResponse
    {
        $data = $request->validate([
            'available_daily' => 'nullable|boolean',
            'available_contract' => 'nullable|boolean',
            'status' => 'nullable|string',
        ]);

        $worker = $this->authenticatedWorker($request);
        $action->execute($worker, 'availability_change', $data, $request->user());

        return $this->success(null, 'Availability change submitted for approval.');
    }
}
