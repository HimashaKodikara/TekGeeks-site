<?php

namespace App\Jobs;

use App\Mail\ConfirmOtpChangePortalEMail;
use App\Mail\OtpChangePortalEMail;
use App\Mail\OtpLoginMail;
use App\Mail\OtpMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ConfirmChangeEmailUpdateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $updatedDate;
    protected $newEmail;

    /**
     * Create a new job instance.
     */
    public function __construct($updatedDate, $newEmail)
    {
        $this->updatedDate = $updatedDate;
        $this->newEmail = $newEmail;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $data = [
            'updated_date' => $this->updatedDate,
            'new_email' => $this->newEmail
        ];

        Mail::to($this->newEmail)->send(new ConfirmOtpChangePortalEMail($data));
    }
}
