<?php

namespace Template\LandingContact\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Template\LandingContact\Config\ContactConfig;
use Template\LandingContact\Models\ContactMessage;

class ContactMessageReceived extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $backoff = 10;

    public function __construct(
        public array $data,
        public ?ContactMessage $contactMessage = null,
    ) {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: ContactConfig::fromConfig()->emailSubject(),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'landing-contact::mail.contact-message-received',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
