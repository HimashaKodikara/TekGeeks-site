<?php

namespace App\Jobs;

use App\Exceptions\SmsDeliveryFailedException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $phone;
    public $message;

    public function __construct($phone, $message)
    {
        $this->phone = $phone;
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        // Build the URL dynamically but safely
        $baseUrl = rtrim(config('services.sms.url'), '/');
        $fullUrl = $baseUrl . '/govsms/V1/prod/send';

        Redis::throttle('sms-gateway')->allow(50)->every(1)->then(function () use ($fullUrl) {
            
            $response = Http::post($fullUrl, [
                'userName'    => config('services.sms.username'),
                'password'    => config('services.sms.password'),
                'sIDCode'     => config('services.sms.sidcode'),
                'phoneNumber' => $this->phone,
                'data'        => $this->message,
            ]);

            if ($response->failed()) {
                Log::error("GovSMS API Error", [
                    'status' => $response->status(),
                    'body'   => $response->json()
                ]);
                throw new SmsDeliveryFailedException("SMS Failed: " . $response->body());
            }

        }, function () {
            return $this->release(10);
        });
    }
}
