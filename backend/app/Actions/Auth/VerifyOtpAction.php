<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class VerifyOtpAction
{
    public function execute(string $phone, string $code, Request $request): array
    {
        $stored = Redis::get("otp:{$phone}");

        if (! $stored || ! hash_equals($stored, $code)) {
            return ['success' => false, 'message' => 'Invalid or expired OTP.'];
        }

        Redis::del("otp:{$phone}");

        $user = User::firstOrCreate(
            ['phone' => $phone],
            ['is_active' => true, 'preferred_language' => 'he']
        );

        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        // Issue Sanctum session via cookie (SPA auth)
        $request->session()->regenerate();
        auth()->login($user);

        $needsRoleSelection = $user->role === null;

        return [
            'success' => true,
            'user' => $user->load('profile'),
            'needs_role_selection' => $needsRoleSelection,
        ];
    }
}
