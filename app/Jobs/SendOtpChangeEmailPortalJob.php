<?php

namespace App\Jobs;

use App\Mail\OtpChangePortalEMail;
use App\Mail\OtpLoginMail;
use App\Mail\OtpMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendOtpChangeEmailPortalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $newEmail;
    protected $otp;

    /**
     * Create a new job instance.
     */
    public function __construct($newEmail, $otp)
    {
        $this->newEmail = $newEmail;
        $this->otp = $otp;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->newEmail)->send(new OtpChangePortalEMail($this->otp));
    }
}
