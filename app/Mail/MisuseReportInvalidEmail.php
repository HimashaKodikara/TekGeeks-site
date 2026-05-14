<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MisuseReportInvalidEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $mailBodyContent;

    public function __construct(array $mailBodyContent)
    {
        $this->mailBodyContent = $mailBodyContent;
    }

    public function build()
    {
        $complaintDetails = $this->mailBodyContent;
        $fromAddress = config('mail.from.address');
        return $this->from($fromAddress, 'DECLARATION OF ASSETS AND LIABILITIES')
                    ->subject('Your NIC Misuse Report Has Been Resolved – Reference No:'.$complaintDetails['reference_no'])
                    ->view('email.reported-complaint-invalid-email')
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
    //         subject: 'Sharing Key for Covered Persons',
    //     );
    // }

    /**
     * Get the message content definition.
     */
    // public function content(): Content
    // {
    //     return new Content(
    //         view: 'email.sharing-key-email',
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
