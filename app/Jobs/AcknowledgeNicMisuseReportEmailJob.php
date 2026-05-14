<?php

namespace App\Jobs;

use App\Mail\AcknowledgeNICMisuseReportEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class AcknowledgeNicMisuseReportEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $email;
    protected $reportedMisuseComplaintData;

    /**
     * Create a new job instance.
     */
    public function __construct($email, $reportedMisuseComplaintData)
    {
        $this->email = $email;
        $this->reportedMisuseComplaintData = $reportedMisuseComplaintData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->email)->send(new AcknowledgeNICMisuseReportEmail($this->reportedMisuseComplaintData));
    }
}
