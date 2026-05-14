<?php

namespace App\Jobs;

use App\Mail\OtpLoginMail;
use App\Mail\OtpMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendOtpEmailRegistration implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $declarantEmail;
    protected $otp;

    /**
     * Create a new job instance.
     */
    public function __construct($declarantEmail, $otp)
    {
        $this->declarantEmail = $declarantEmail;
        $this->otp = $otp;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->declarantEmail)->send(new OtpMail($this->otp));
    }
}
