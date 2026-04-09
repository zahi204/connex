<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Approval\SubmitForApprovalAction;
use App\Http\Resources\AssignmentPortalResource;
use App\Http\Resources\WorkerFullResource;
use App\Models\TrainingResult;
use App\Models\Worker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
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

        $query = $worker->assignments()->with('project:id,name,description,status');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('from')) {
            $query->where('start_date', '>=', $request->input('from'));
        }
        if ($request->filled('to')) {
            $query->where('estimated_end_date', '<=', $request->input('to'));
        }

        $paginated = $query->latest('start_date')->paginate(20);

        return $this->success(
            AssignmentPortalResource::collection($paginated)->response()->getData()
        );
    }

    public function team(Request $request): JsonResponse
    {
        $worker = $this->authenticatedWorker($request);

        $membership = $worker->teamMemberships()
            ->whereNull('left_at')
            ->with([
                'team.teamLeader:id,full_name,primary_skill',
                'team.members.worker:id,full_name,primary_skill',
                'team.currentProject:id,name,status',
            ])
            ->first();

        if (! $membership) {
            return $this->success(null);
        }

        $team = $membership->team;
        $leader = $team->teamLeader;

        $members = $team->members->map(fn ($m) => [
            'id' => $m->worker?->id,
            'full_name' => $m->worker?->full_name,
            'primary_skill' => $m->worker?->primary_skill?->value ?? $m->worker?->primary_skill,
            'role' => $m->role ?? null,
            'joined_at' => $m->joined_at?->toDateString(),
            'is_leader' => $leader && $m->worker_id === $leader->id,
        ]);

        return $this->success([
            'id' => $team->id,
            'name' => $team->name,
            'status' => $team->status?->value ?? $team->status,
            'leader' => $leader ? [
                'id' => $leader->id,
                'full_name' => $leader->full_name,
                'primary_skill' => $leader->primary_skill?->value ?? $leader->primary_skill,
            ] : null,
            'current_project' => $team->currentProject ? [
                'id' => $team->currentProject->id,
                'name' => $team->currentProject->name,
                'status' => $team->currentProject->status?->value ?? $team->currentProject->status,
            ] : null,
            'members' => $members,
        ]);
    }

    public function training(Request $request): JsonResponse
    {
        $worker = $this->authenticatedWorker($request);

        $results = $worker->trainingResults()
            ->with('trainingCycle:id,name,start_date,end_date')
            ->latest('id')
            ->get();

        $mapped = $results->map(fn (TrainingResult $r) => [
            'id' => $r->id,
            'training_cycle_id' => $r->training_cycle_id,
            'cycle_name' => $r->trainingCycle?->name,
            'cycle_start' => $r->trainingCycle?->start_date?->toDateString(),
            'cycle_end' => $r->trainingCycle?->end_date?->toDateString(),
            'professional_score' => $r->professional_score,
            'suitability' => $r->suitability?->value ?? $r->suitability,
            'classification' => $r->classification,
            'primary_field' => $r->primary_field,
            'attendance_day1' => $r->attendance_day1,
            'attendance_day2' => $r->attendance_day2,
            'certificate_available' => ! empty($r->certificate_path),
            'download_url' => ! empty($r->certificate_path)
                ? url("/api/v1/worker/training/{$r->id}/certificate")
                : null,
        ]);

        return $this->success($mapped);
    }

    public function downloadCertificate(Request $request, int $resultId): StreamedResponse|JsonResponse
    {
        $worker = $this->authenticatedWorker($request);

        $result = TrainingResult::where('id', $resultId)
            ->where('worker_id', $worker->id)
            ->first();

        if (! $result) {
            return $this->error('Training result not found.', 404);
        }

        if (empty($result->certificate_path)) {
            return $this->error('Certificate not yet issued for this training result.', 404);
        }

        if (! Storage::exists($result->certificate_path)) {
            return $this->error('Certificate file not found on server.', 404);
        }

        $filename = 'certificate_'.$resultId.'_'.$worker->id.'.pdf';

        return Storage::download($result->certificate_path, $filename);
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
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $worker = $this->authenticatedWorker($request);

        $mediaId = null;
        if ($request->hasFile('file')) {
            $media = $worker
                ->addMediaFromRequest('file')
                ->usingName($data['name'])
                ->toMediaCollection('documents');
            $mediaId = $media->id;
        }

        $approvalData = [
            'name' => $data['name'],
            'document_type' => $data['document_type'],
            'media_id' => $mediaId,
        ];

        $action->execute($worker, 'document_upload', $approvalData, $request->user());

        return $this->success(null, 'Document upload submitted for approval.');
    }

    public function payments(Request $request): JsonResponse
    {
        $worker = $this->authenticatedWorker($request);

        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();

        $currentMonthPayments = $worker->payments()
            ->where('payment_date', '>=', $startOfMonth)
            ->get();

        $lastPayment = $worker->payments()
            ->latest('payment_date')
            ->first();

        $totalPaidYear = (float) $worker->payments()
            ->where('payment_date', '>=', $now->copy()->startOfYear())
            ->sum('amount');

        $currentMonthStatus = $worker->payment_status?->value ?? 'unknown';

        $paginated = $worker->payments()->latest('payment_date')->paginate(20);

        return $this->success([
            'summary' => [
                'current_month_status' => $currentMonthStatus,
                'current_month_total' => $currentMonthPayments->sum('amount'),
                'last_payment_amount' => $lastPayment?->amount,
                'last_payment_date' => $lastPayment?->payment_date?->toDateString(),
                'total_paid_year' => $totalPaidYear,
            ],
            'payments' => $paginated,
        ]);
    }

    public function updateAvailability(Request $request, SubmitForApprovalAction $action): JsonResponse
    {
        $data = $request->validate([
            'available_daily' => 'nullable|boolean',
            'available_contract' => 'nullable|boolean',
            'status' => 'nullable|string',
            'not_available_until' => 'nullable|date',
        ]);

        $worker = $this->authenticatedWorker($request);
        $action->execute($worker, 'availability_change', $data, $request->user());

        return $this->success(null, 'Availability change submitted for approval.');
    }
}
