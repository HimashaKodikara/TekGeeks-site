<?php

namespace App\Jobs;

use App\Mail\AccountSuspendedEmail;
use App\Mail\MisuseReportInvalidEmail;
use App\Mail\OtpLoginMail;
use App\Mail\SuccessfullyRegisteredEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class AccountSuspendReportOwnerEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $suspenderEmail;
    protected $complaintRelatedDeclarantDetails;
    protected $requestSendUserEmail;
    protected $accountSuspendDetails;

    /**
     * Create a new job instance.
     */
    public function __construct($suspenderEmail, $complaintRelatedDeclarantDetails, $requestSendUserEmail, $accountSuspendDetails)
    {
        $this->suspenderEmail = $suspenderEmail;
        $this->complaintRelatedDeclarantDetails = $complaintRelatedDeclarantDetails;
        $this->requestSendUserEmail = $requestSendUserEmail;
        $this->accountSuspendDetails = $accountSuspendDetails;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->suspenderEmail)->send(new AccountSuspendedEmail($this->complaintRelatedDeclarantDetails));
        Mail::to($this->requestSendUserEmail)->send(new AccountSuspendedEmail($this->accountSuspendDetails));
    }
}
