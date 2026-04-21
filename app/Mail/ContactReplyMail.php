<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public $contactMessage,
        public string $messageBody
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reply to your message - GroceMate',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-reply',
            with: [
                'contactMessage' => $this->contactMessage,
                'messageBody' => $this->messageBody,
            ],
        );
    }
}
