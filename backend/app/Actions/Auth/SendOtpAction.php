<?php

namespace App\Actions\Auth;

use App\Jobs\SendOtpViaSms;
use App\Jobs\SendOtpViaWhatsApp;
use Illuminate\Support\Facades\Redis;

class SendOtpAction
{
    public function execute(string $phone): void
    {
        $code = (string) random_int(100000, 999999);

        Redis::setex("otp:{$phone}", 300, $code);

        try {
            SendOtpViaWhatsApp::dispatch($phone, $code)->onQueue('otp');
        } catch (\Throwable) {
            SendOtpViaSms::dispatch($phone, $code)->onQueue('otp');
        }
    }
}
