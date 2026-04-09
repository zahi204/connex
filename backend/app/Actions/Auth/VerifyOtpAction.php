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

        $request->session()->regenerate();
        auth()->login($user);

        $user->refresh();
        $user->load('profile');

        return [
            'success' => true,
            'user' => $user,
            'needs_role_selection' => $user->role === null,
            'needs_onboarding' => $user->needsOnboarding(),
        ];
    }
}
