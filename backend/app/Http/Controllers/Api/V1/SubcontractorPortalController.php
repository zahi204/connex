<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Approval\SubmitForApprovalAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubcontractorPortalController extends BaseApiController
{
    public function profile(Request $request): JsonResponse
    {
        return $this->success($request->user()->profile);
    }

    public function updateProfile(Request $request, SubmitForApprovalAction $action): JsonResponse
    {
        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'specializations' => 'nullable|array',
            'operating_areas' => 'nullable|array',
        ]);

        $sub = $request->user()->profile;
        $action->execute($sub, 'profile_update', $data, $request->user());

        return $this->success(null, 'Profile update submitted for approval.');
    }

    public function assignments(Request $request): JsonResponse
    {
        $sub = $request->user()->profile;
        return $this->success(
            $sub->assignments()->with('project:id,name')->latest()->paginate(20)
        );
    }

    public function rating(Request $request): JsonResponse
    {
        $sub = $request->user()->profile;
        return $this->success(['rating' => $sub->rating]);
    }

    public function documents(Request $request): JsonResponse
    {
        $sub = $request->user()->profile;
        return $this->success($sub->documents()->latest()->get());
    }

    public function updateAvailability(Request $request, SubmitForApprovalAction $action): JsonResponse
    {
        $data = $request->validate([
            'status' => 'nullable|string',
            'availability_date' => 'nullable|date',
        ]);

        $sub = $request->user()->profile;
        $action->execute($sub, 'availability_change', $data, $request->user());

        return $this->success(null, 'Availability update submitted for approval.');
    }

    public function updateCapacity(Request $request, SubmitForApprovalAction $action): JsonResponse
    {
        $data = $request->validate([
            'number_of_workers' => 'nullable|integer',
            'specializations' => 'nullable|array',
        ]);

        $sub = $request->user()->profile;
        $action->execute($sub, 'profile_update', $data, $request->user());

        return $this->success(null, 'Capacity update submitted for approval.');
    }
}
