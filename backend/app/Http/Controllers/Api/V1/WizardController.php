<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Registration\RegisterAgencyAction;
use App\Actions\Registration\RegisterDeveloperAction;
use App\Actions\Registration\RegisterSubcontractorAction;
use App\Actions\Registration\RegisterWorkerAction;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WizardController extends BaseApiController
{
    public function submitWorker(Request $request, RegisterWorkerAction $action): JsonResponse
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'id_number' => 'nullable|string|max:50',
            'country_of_origin' => 'nullable|string|max:100',
            'languages' => 'nullable|array',
            'date_of_arrival' => 'nullable|date',
            'primary_skill' => 'nullable|string',
            'secondary_skill' => 'nullable|string',
            'previous_experience' => 'nullable|string',
            'preferred_work_area' => 'nullable|string',
            'available_daily' => 'nullable|boolean',
            'available_contract' => 'nullable|boolean',
            'staffing_agency_id' => 'nullable|exists:staffing_agencies,id',
        ]);

        $action->execute($request->user(), $data);

        return $this->success([
            'user' => new UserResource($request->user()->fresh()->load('profile')),
        ], 'Registration complete.');
    }

    public function submitDeveloper(Request $request, RegisterDeveloperAction $action): JsonResponse
    {
        $data = $request->validate([
            'company_name' => 'required|string|max:255',
            'registration_number' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'company_description' => 'nullable|string',
            'company_size' => 'nullable|string|max:50',
            'areas_of_operation' => 'nullable|array',
            'specializations' => 'nullable|array',
            'contact_person_name' => 'nullable|string|max:255',
            'contact_person_role' => 'nullable|string|max:100',
            'contact_person_phone' => 'nullable|string|max:20',
            'contact_person_email' => 'nullable|email|max:255',
        ]);

        $action->execute($request->user(), $data);

        return $this->success([
            'user' => new UserResource($request->user()->fresh()->load('profile')),
        ], 'Registration complete.');
    }

    public function submitSubcontractor(Request $request, RegisterSubcontractorAction $action): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'registration_number' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'specializations' => 'nullable|array',
            'operating_areas' => 'nullable|array',
            'number_of_workers' => 'nullable|integer',
            'years_of_experience' => 'nullable|integer',
            'notable_projects' => 'nullable|string',
        ]);

        $action->execute($request->user(), $data);

        return $this->success([
            'user' => new UserResource($request->user()->fresh()->load('profile')),
        ], 'Registration complete.');
    }

    public function submitAgency(Request $request, RegisterAgencyAction $action): JsonResponse
    {
        $data = $request->validate([
            'agency_name' => 'required|string|max:255',
            'registration_number' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'contact_person_name' => 'nullable|string|max:255',
            'contact_person_role' => 'nullable|string|max:100',
            'contact_person_phone' => 'nullable|string|max:20',
            'contact_person_email' => 'nullable|email|max:255',
            'worker_types' => 'nullable|array',
            'countries_of_origin' => 'nullable|array',
            'average_capacity' => 'nullable|integer',
            'monthly_throughput' => 'nullable|integer',
        ]);

        $action->execute($request->user(), $data);

        return $this->success([
            'user' => new UserResource($request->user()->fresh()->load('profile')),
        ], 'Registration complete.');
    }

    public function saveDraft(Request $request, string $type): JsonResponse
    {
        $request->validate(['data' => 'required|array']);
        cache()->put("wizard-draft:{$type}:{$request->user()->id}", $request->input('data'), now()->addDays(7));
        return $this->success(null, 'Draft saved.');
    }

    public function loadDraft(Request $request, string $type): JsonResponse
    {
        $data = cache()->get("wizard-draft:{$type}:{$request->user()->id}");
        return $this->success(['data' => $data]);
    }
}
