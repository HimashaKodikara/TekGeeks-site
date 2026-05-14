<?php

namespace App\Jobs;

use App\Mail\OtpLoginMail;
use App\Mail\SuccessfullyRegisteredEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendSuccessfullEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $email;
    protected $nic;

    /**
     * Create a new job instance.
     */
    public function __construct($email, $nic)
    {
        $this->email = $email;
        $this->nic = $nic;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->email)->send(new SuccessfullyRegisteredEmail($this->nic));
    }
}
