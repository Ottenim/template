<?php

namespace Template\LandingLeadCapture\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Template\LandingLeadCapture\Models\Lead;

class LeadCaptured extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public array $data,
        public ?Lead $lead = null,
    ) {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: config('landing-lead-capture.send_email.subject', 'Novo lead capturado'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'landing-lead-capture::mail.lead-captured',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
