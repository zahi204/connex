<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Auth\SelectRoleAction;
use App\Actions\Auth\SendOtpAction;
use App\Actions\Auth\VerifyOtpAction;
use App\Actions\Auth\VerifyTwoFactorAction;
use App\Http\Requests\Auth\SelectRoleRequest;
use App\Http\Requests\Auth\SendOtpRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends BaseApiController
{
    public function sendOtp(SendOtpRequest $request, SendOtpAction $action): JsonResponse
    {
        $action->execute($request->validated('phone'));

        return $this->noContent();
    }

    public function verifyOtp(VerifyOtpRequest $request, VerifyOtpAction $action): JsonResponse
    {
        $result = $action->execute(
            $request->validated('phone'),
            $request->validated('code'),
            $request,
        );

        if (! $result['success']) {
            return $this->error($result['message'], 422);
        }

        return $this->success([
            'user' => new UserResource($result['user']),
            'needs_role_selection' => $result['needs_role_selection'],
        ]);
    }

    public function selectRole(SelectRoleRequest $request, SelectRoleAction $action): JsonResponse
    {
        $user = $action->execute($request->user(), $request->validated('role'));

        return $this->success([
            'user' => new UserResource($user),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        auth()->guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $this->noContent();
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load('profile');

        return $this->success([
            'user' => new UserResource($user),
        ]);
    }

    public function verifyTwoFactor(Request $request, VerifyTwoFactorAction $action): JsonResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $verified = $action->execute($request->user(), $request->input('code'));

        if (! $verified) {
            return $this->error('Invalid 2FA code.', 422);
        }

        return $this->success(['verified' => true]);
    }
}
