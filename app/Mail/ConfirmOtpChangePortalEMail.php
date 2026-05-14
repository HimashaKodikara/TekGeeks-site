<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConfirmOtpChangePortalEMail extends Mailable
{
    use Queueable, SerializesModels;

    public $mailBodyContent;

    public function __construct($mailBodyContent)
    {
        $this->mailBodyContent = $mailBodyContent;
    }

    public function build()
    {
        $fromAddress = config('mail.from.address');
        return $this->from($fromAddress, 'DECLARATION OF ASSETS AND LIABILITIES')
                    ->subject('Contact Information Update')
                    ->view('email.confirm-change-portal-email')
                    ->with([
                        'mailBodyContent' => $this->mailBodyContent,
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
