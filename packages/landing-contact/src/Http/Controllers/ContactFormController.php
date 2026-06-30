<?php

namespace Template\LandingContact\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Template\LandingContact\Config\ContactConfig;
use Template\LandingContact\Http\Requests\StoreContactMessageRequest;
use Template\LandingContact\Mail\ContactMessageReceived;
use Template\LandingContact\Models\ContactMessage;

class ContactFormController extends Controller
{
    public function __invoke(StoreContactMessageRequest $request, ContactConfig $config): RedirectResponse
    {
        $data = $request->validatedContactData();
        $contactMessage = null;

        if ($config->saveToDatabase()) {
            $contactMessage = ContactMessage::query()->create([
                ...$data,
                'metadata' => $this->metadata($config),
                'ip_address' => $request->ip(),
                'user_agent' => Str::limit((string) $request->userAgent(), 1000, ''),
            ]);
        }

        $emailQueued = $this->sendNotification($config, $data, $contactMessage);

        Log::info('contact.submitted', [
            'contact_message_id' => $contactMessage?->id,
            'saved_to_database' => $contactMessage !== null,
            'email_queued' => $emailQueued,
        ]);

        $response = $config->redirectAfterSubmit()
            ? redirect()->to($config->redirectAfterSubmit())
            : back();

        return $response->with([
            'landing_contact_success' => $config->successMessage(),
            'landing_contact_conversion' => $this->conversionPayload($config),
        ]);
    }

    protected function sendNotification(ContactConfig $config, array $data, ?ContactMessage $contactMessage): bool
    {
        if (! $config->emailEnabled()) {
            return false;
        }

        $recipient = $config->emailRecipient();

        if ($recipient === null) {
            return false;
        }

        Mail::to($recipient)->send(new ContactMessageReceived($data, $contactMessage));

        return true;
    }

    protected function metadata(ContactConfig $config): array
    {
        return [
            'tracking_enabled' => $config->trackingEnabled(),
            'tracking_event' => $config->trackingEventName(),
        ];
    }

    protected function conversionPayload(ContactConfig $config): ?array
    {
        if (! $config->trackingEnabled()) {
            return null;
        }

        return [
            'event' => $config->trackingEventName(),
        ];
    }
}
