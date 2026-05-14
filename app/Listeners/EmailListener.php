<?php

namespace App\Listeners;

use App\Events\EmailEvent;
use App\Services\EmailManagementService;
use Illuminate\Support\Facades\Log;

class EmailListener
{
    protected EmailManagementService $api;

    /**
     * Create the event listener.
     */
    public function __construct(EmailManagementService $api)
    {
        // Only ONE constructor should exist here
        $this->api = $api;
    }

    /**
     * Handle the event.
     */
    public function handle(EmailEvent $event): void
    {
        try {
            $apiData = [
                'email' => $event->email,
                'otp'   => $event->otp,
                'type'  => $event->type,
                // Using config() as we discussed for safety
                'jwt'   => config('services.email.email_secret'), 
            ];

            // Make sure your service has a 'post' method defined
            $this->api->post('send-email-server', $apiData);

        } catch (\Throwable $e) {
            Log::error("Failed to send email data to backend: " . $e->getMessage());
        }
    }
}