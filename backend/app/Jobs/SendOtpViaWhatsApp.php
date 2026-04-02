<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendOtpViaWhatsApp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public function __construct(
        public string $phone,
        public string $code,
    ) {}

    public function handle(): void
    {
        $token = config('services.whatsapp.token');
        $phoneNumberId = config('services.whatsapp.phone_number_id');

        if (! $token || ! $phoneNumberId) {
            // WhatsApp not configured — fall back to SMS
            SendOtpViaSms::dispatch($this->phone, $this->code)->onQueue('otp');
            return;
        }

        try {
            Http::withToken($token)
                ->post("https://graph.facebook.com/v18.0/{$phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to' => ltrim($this->phone, '+'),
                    'type' => 'template',
                    'template' => [
                        'name' => 'otp_verification',
                        'language' => ['code' => 'he'],
                        'components' => [
                            [
                                'type' => 'body',
                                'parameters' => [
                                    ['type' => 'text', 'text' => $this->code],
                                ],
                            ],
                        ],
                    ],
                ])
                ->throw();
        } catch (\Throwable $e) {
            Log::warning("WhatsApp OTP failed for {$this->phone}: {$e->getMessage()}");
            SendOtpViaSms::dispatch($this->phone, $this->code)->onQueue('otp');
        }
    }
}
