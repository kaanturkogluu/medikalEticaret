<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Sipariş Faturanız - #' . ($this->order->external_order_id ?? $this->order->id),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.invoice',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        if ($this->order->invoice_file) {
            $path = storage_path('app/private/' . $this->order->invoice_file); // checking private first if laravel 11?
            if (!file_exists($path)) {
                $path = storage_path('app/' . $this->order->invoice_file);
            }
            if (!file_exists($path)) {
                $path = storage_path('app/public/' . $this->order->invoice_file);
            }

            if (file_exists($path)) {
                return [
                    Attachment::fromPath($path)
                        ->as('Fatura-' . ($this->order->external_order_id ?? $this->order->id) . '.pdf')
                        ->withMime('application/pdf'),
                ];
            }
        }

        return [];
    }
}
