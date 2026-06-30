<?php

namespace Template\LandingLeadCapture\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Template\LandingLeadCapture\Config\LeadCaptureConfig;
use Template\LandingLeadCapture\Models\Lead;

class LeadCaptured extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $backoff = 10;

    public function __construct(
        public array $data,
        public ?Lead $lead = null,
    ) {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: LeadCaptureConfig::fromConfig()->emailSubject(),
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
