<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderCancelled extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order->load('items.product');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Siparişiniz İptal Edildi - #' . ($this->order->external_order_id ?? $this->order->id),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.orders.cancelled',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
