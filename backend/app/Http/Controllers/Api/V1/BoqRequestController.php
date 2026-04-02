<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Boq\BoqExportAction;
use App\Actions\Boq\SubmitBoqRequestAction;
use App\Models\BoqRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BoqRequestController extends BaseApiController
{
    public function store(Request $request, SubmitBoqRequestAction $action): JsonResponse
    {
        $data = $request->validate([
            'project_name' => 'required|string|max:255',
            'city' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'project_type' => 'nullable|string',
            'scope_description' => 'nullable|string',
            'urgency' => 'nullable|string|in:standard,urgent,express',
        ]);

        $boqRequest = $action->execute($request->user(), $data);

        return $this->created($boqRequest);
    }

    public function index(Request $request): JsonResponse
    {
        $query = BoqRequest::where('requested_by_user_id', $request->user()->id)
            ->latest();

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        return $this->success($query->paginate(20));
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $boqRequest = BoqRequest::where('requested_by_user_id', $request->user()->id)
            ->findOrFail($id);

        return $this->success($boqRequest->load(['lineItems', 'preparedBy']));
    }

    public function items(int $id): JsonResponse
    {
        $boqRequest = BoqRequest::findOrFail($id);
        $items = $boqRequest->lineItems()->orderBy('trade')->get();

        return $this->success($items);
    }

    public function export(int $id, BoqExportAction $action)
    {
        $boqRequest = BoqRequest::findOrFail($id);

        return $action->export($boqRequest);
    }
}
