<?php

namespace Template\LandingContact\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Template\LandingContact\Http\Requests\StoreContactMessageRequest;
use Template\LandingContact\Mail\ContactMessageReceived;
use Template\LandingContact\Models\ContactMessage;

class ContactFormController extends Controller
{
    public function __invoke(StoreContactMessageRequest $request): RedirectResponse
    {
        $data = $request->validatedContactData();
        $contactMessage = null;

        if ((bool) config('landing-contact.save_to_database', true)) {
            $contactMessage = ContactMessage::query()->create([
                ...$data,
                'metadata' => $this->metadata(),
                'ip_address' => $request->ip(),
                'user_agent' => Str::limit((string) $request->userAgent(), 1000, ''),
            ]);
        }

        $this->sendNotification($data, $contactMessage);

        $response = config('landing-contact.redirect_after_submit')
            ? redirect()->to(config('landing-contact.redirect_after_submit'))
            : back();

        return $response->with([
            'landing_contact_success' => config('landing-contact.messages.success'),
            'landing_contact_conversion' => $this->conversionPayload(),
        ]);
    }

    protected function sendNotification(array $data, ?ContactMessage $contactMessage): void
    {
        if (! (bool) config('landing-contact.send_email.enabled', true)) {
            return;
        }

        $recipient = trim((string) config('landing-contact.send_email.to'));

        if ($recipient === '') {
            return;
        }

        Mail::to($recipient)->send(new ContactMessageReceived($data, $contactMessage));
    }

    protected function metadata(): array
    {
        return [
            'tracking_enabled' => (bool) config('landing-contact.tracking.enabled', false),
            'tracking_event' => config('landing-contact.tracking.event_name', 'contact_form_submit'),
        ];
    }

    protected function conversionPayload(): ?array
    {
        if (! (bool) config('landing-contact.tracking.enabled', false)) {
            return null;
        }

        return [
            'event' => config('landing-contact.tracking.event_name', 'contact_form_submit'),
        ];
    }
}
