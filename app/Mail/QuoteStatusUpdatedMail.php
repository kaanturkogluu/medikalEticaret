<?php

namespace App\Mail;

use App\Models\QuoteRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuoteStatusUpdatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $quote;

    /**
     * Create a new message instance.
     */
    public function __construct(QuoteRequest $quote)
    {
        $this->quote = $quote->load('items');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = 'Teklif Talebiniz Güncellendi - #' . $this->quote->quote_no;
        if ($this->quote->status === 'converted' && $this->quote->custom_payment_link) {
            $subject = '🎉 Özel Teklifiniz Onaylandı & Satın Alma Linkiniz Hazır! - #' . $this->quote->quote_no;
        } elseif ($this->quote->status === 'offered') {
            $subject = '🏷️ Özel Fiyat Teklifiniz Hazırlandı - #' . $this->quote->quote_no;
        }

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.quotes.status_updated',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
