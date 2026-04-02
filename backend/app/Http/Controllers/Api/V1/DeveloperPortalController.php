<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Approval\SubmitForApprovalAction;
use App\Actions\Project\SubmitProjectAction;
use App\Models\BoqRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeveloperPortalController extends BaseApiController
{
    public function profile(Request $request): JsonResponse
    {
        return $this->success($request->user()->profile);
    }

    public function updateProfile(Request $request, SubmitForApprovalAction $action): JsonResponse
    {
        $data = $request->validate([
            'company_name' => 'nullable|string|max:255',
            'company_description' => 'nullable|string',
            'contact_person_name' => 'nullable|string|max:255',
            'contact_person_phone' => 'nullable|string|max:20',
        ]);

        $developer = $request->user()->profile;
        $action->execute($developer, 'profile_update', $data, $request->user());

        return $this->success(null, 'Profile update submitted for approval.');
    }

    public function submitProject(Request $request, SubmitProjectAction $action): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'project_type' => 'required|string',
            'estimated_start_date' => 'nullable|date',
            'estimated_completion' => 'nullable|date',
            'required_trades' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        $project = $action->execute($request->user(), $data);

        return $this->created($project);
    }

    public function projects(Request $request): JsonResponse
    {
        $developer = $request->user()->profile;
        $projects = $developer->projects()
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->latest()
            ->paginate(20);

        return $this->success($projects);
    }

    public function projectDetail(Request $request, int $id): JsonResponse
    {
        $developer = $request->user()->profile;
        $project = $developer->projects()
            ->with(['assignments' => function ($q) {
                // Exclude financial details for developer
                $q->select('id', 'project_id', 'resource_type', 'resource_id', 'engagement_type', 'start_date', 'estimated_end_date', 'status', 'work_description');
            }, 'notes'])
            ->findOrFail($id);

        return $this->success($project);
    }

    public function boqRequests(Request $request): JsonResponse
    {
        $requests = BoqRequest::where('requested_by_user_id', $request->user()->id)
            ->latest()
            ->paginate(20);

        return $this->success($requests);
    }
}
