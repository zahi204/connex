<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\WorkerFullResource;
use App\Http\Resources\WorkerPublicResource;
use App\Models\Worker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkerDirectoryController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Worker::query();

        if ($request->filled('skill')) {
            $query->where(function ($q) use ($request) {
                $q->where('primary_skill', $request->input('skill'))
                  ->orWhere('secondary_skill', $request->input('skill'));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('area')) {
            $query->where('preferred_work_area', $request->input('area'));
        }

        if ($request->filled('min_rating')) {
            $query->where('professional_rating', '>=', $request->input('min_rating'));
        }

        if ($request->filled('available_daily')) {
            $query->where('available_daily', true);
        }

        if ($request->filled('available_contract')) {
            $query->where('available_contract', true);
        }

        if ($request->filled('search')) {
            $query->where('full_name', 'ilike', '%' . $request->input('search') . '%');
        }

        $sortBy = $request->input('sort_by', 'full_name');
        $allowedSorts = ['full_name', 'professional_rating', 'status', 'primary_skill'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy);
        }

        $perPage = min((int) $request->input('per_page', 20), 100);
        $workers = $query->paginate($perPage);

        // Use full resource for admin, public for others
        $isAdmin = $request->user()->isAdmin();
        $resourceClass = $isAdmin ? WorkerFullResource::class : WorkerPublicResource::class;

        return $this->success(
            $resourceClass::collection($workers)->response()->getData(true)
        );
    }
}
