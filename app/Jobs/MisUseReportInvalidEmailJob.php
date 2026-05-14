<?php

namespace App\Jobs;

use App\Mail\MisuseReportInvalidEmail;
use App\Mail\OtpLoginMail;
use App\Mail\SuccessfullyRegisteredEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class MisUseReportInvalidEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $email;
    protected $misuseReportDetails;

    /**
     * Create a new job instance.
     */
    public function __construct($email, $misuseReportDetails)
    {
        $this->email = $email;
        $this->misuseReportDetails = $misuseReportDetails;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->email)->send(new MisuseReportInvalidEmail($this->misuseReportDetails));
    }
}
