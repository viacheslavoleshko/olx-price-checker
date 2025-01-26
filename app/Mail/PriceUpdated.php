<?php

namespace App\Mail;

use App\Models\Price;
use App\Models\Advert;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class PriceUpdated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Advert $advert;
    public Price $price;
    /**
     * Create a new message instance.
     */
    public function __construct(Advert $advert)
    {
        $this->advert = $advert;
        $this->price = $advert->prices()->latest('created_at')->first();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Price Updated',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.price-updated',
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
}
