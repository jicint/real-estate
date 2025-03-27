<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ShareComparison extends Mailable
{
    use Queueable, SerializesModels;

    public $comparison;
    public $shareUrl;
    public $message;

    /**
     * Create a new message instance.
     */
    public function __construct($comparison, $shareUrl, $message)
    {
        $this->comparison = $comparison;
        $this->shareUrl = $shareUrl;
        $this->message = $message;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Property Comparison Shared With You',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.share-comparison',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    public function build()
    {
        return $this->markdown('emails.share-comparison')
                    ->subject('Property Comparison Shared With You');
    }
}
