<?php

namespace Template\LandingLeadCapture\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Template\LandingLeadCapture\Config\LeadCaptureConfig;
use Template\LandingLeadCapture\Http\Requests\StoreLeadRequest;
use Template\LandingLeadCapture\Mail\LeadCaptured;
use Template\LandingLeadCapture\Models\Lead;

class LeadCaptureController extends Controller
{
    public function __invoke(StoreLeadRequest $request, LeadCaptureConfig $config): RedirectResponse
    {
        $data = $request->validatedLeadData();
        $lead = null;

        if ($config->saveToDatabase()) {
            $lead = Lead::query()->create([
                ...$data,
                'metadata' => $this->metadata($config, $data),
                'ip_address' => $request->ip(),
                'user_agent' => Str::limit((string) $request->userAgent(), 1000, ''),
            ]);
        }

        $emailQueued = $this->sendNotification($config, $data, $lead);

        Log::info('lead.captured', [
            'lead_id' => $lead?->id,
            'saved_to_database' => $lead !== null,
            'email_queued' => $emailQueued,
            'source' => $data['source'] ?? null,
            'campaign' => $data['campaign'] ?? null,
            'tag' => $data['tag'] ?? null,
        ]);

        $response = $config->redirectAfterSubmit()
            ? redirect()->to($config->redirectAfterSubmit())
            : back();

        return $response->with([
            'landing_lead_capture_success' => $config->successMessage(),
            'landing_lead_capture_conversion' => $this->conversionPayload($config, $data),
        ]);
    }

    protected function sendNotification(LeadCaptureConfig $config, array $data, ?Lead $lead): bool
    {
        if (! $config->emailEnabled()) {
            return false;
        }

        $recipient = $config->emailRecipient();

        if ($recipient === null) {
            return false;
        }

        Mail::to($recipient)->send(new LeadCaptured($data, $lead));

        return true;
    }

    protected function metadata(LeadCaptureConfig $config, array $data): array
    {
        return [
            'tracking_enabled' => $config->trackingEnabled(),
            'tracking_event' => $config->trackingEventName(),
            'source' => $data['source'] ?? null,
            'campaign' => $data['campaign'] ?? null,
            'tag' => $data['tag'] ?? null,
        ];
    }

    protected function conversionPayload(LeadCaptureConfig $config, array $data): ?array
    {
        if (! $config->trackingEnabled()) {
            return null;
        }

        return [
            'event' => $config->trackingEventName(),
            'source' => $data['source'] ?? null,
            'campaign' => $data['campaign'] ?? null,
            'tag' => $data['tag'] ?? null,
        ];
    }
}
