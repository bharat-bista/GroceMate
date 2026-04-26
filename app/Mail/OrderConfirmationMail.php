<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $type;

    public function __construct($order, $type = 'confirmation')
    {
        $this->order = $order;
        $this->type = $type;
    }

    public function envelope(): Envelope
    {
        // Keep all order-notification subject mapping in one place so each
        // status transition gets the correct email subject.
        $subject = match ($this->type) {
            'payment_verified' => 'Payment Verified - Order ' . $this->order->order_number . ' - GroceMate',
            'payment_failed' => 'Payment Failed - Order ' . $this->order->order_number . ' - GroceMate',
            'payment_update' => 'Payment Status Updated - Order ' . $this->order->order_number . ' - GroceMate',
            'delivery_update' => 'Delivery Status Updated - Order ' . $this->order->order_number . ' - GroceMate',
            'order_cancelled' => 'Order Cancelled - ' . $this->order->order_number . ' - GroceMate',
            default => 'Order Confirmation - ' . $this->order->order_number . ' - GroceMate',
        };

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-confirmation',
            with: [
                'order' => $this->order,
                'type' => $this->type,
            ],
        );
    }
}
