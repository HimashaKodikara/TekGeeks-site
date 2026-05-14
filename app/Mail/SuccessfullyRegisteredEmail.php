<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SuccessfullyRegisteredEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $nic;

    public function __construct($nic)
    {
        $this->nic = $nic;
    }

    public function build()
    {
        $fromAddress = config('mail.from.address');
        return $this->from($fromAddress, 'DECLARATION OF ASSETS AND LIABILITIES')
                    ->subject('Welcome to the Declaration of Assets and Liabilities!')
                    ->view('email.successfully-registered-email')
                    ->with([
                        'nic' => $this->nic,
                    ]);
    }

    /**
     * Get the message envelope.
     */
    // public function envelope(): Envelope
    // {
    //     return new Envelope(
    //         subject: 'Otp Mail',
    //     );
    // }

    /**
     * Get the message content definition.
     */
    // public function content(): Content
    // {
    //     return new Content(
    //         view: 'email.otp-email',
    //     );
    // }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    // public function attachments(): array
    // {
    //     return [];
    // }
}
