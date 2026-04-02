<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendOtpViaSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public string $phone,
        public string $code,
    ) {}

    public function handle(): void
    {
        // In production, send via Twilio
        if (app()->environment('production') && config('services.twilio.sid')) {
            Http::asForm()
                ->withBasicAuth(
                    config('services.twilio.sid'),
                    config('services.twilio.token'),
                )
                ->post(
                    'https://api.twilio.com/2010-04-01/Accounts/' . config('services.twilio.sid') . '/Messages.json',
                    [
                        'To' => $this->phone,
                        'From' => config('services.twilio.from'),
                        'Body' => "Your Connex verification code is: {$this->code}",
                    ]
                );

            return;
        }

        // In dev/staging, just log the OTP
        Log::channel('single')->info("OTP for {$this->phone}: {$this->code}");
    }
}
