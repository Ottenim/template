<?php

namespace Template\LandingContact\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Template\LandingContact\Models\ContactMessage;

class ContactMessageReceived extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public array $data,
        public ?ContactMessage $contactMessage = null,
    ) {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: config('landing-contact.send_email.subject', 'Nova mensagem de contato'),
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
