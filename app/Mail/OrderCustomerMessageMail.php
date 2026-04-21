<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderCustomerMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public $order,
        public string $messageBody
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Message about Order ' . $this->order->order_number . ' - GroceMate',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-customer-message',
            with: [
                'order' => $this->order,
                'messageBody' => $this->messageBody,
            ],
        );
    }
}
