<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use PragmaRX\Google2FA\Google2FA;

class VerifyTwoFactorAction
{
    public function execute(User $user, string $code): bool
    {
        if (! $user->two_factor_secret) {
            throw new \InvalidArgumentException('Two-factor authentication is not enabled.');
        }

        $google2fa = new Google2FA();
        $secret = Crypt::decryptString($user->two_factor_secret);

        return $google2fa->verifyKey($secret, $code);
    }
}
