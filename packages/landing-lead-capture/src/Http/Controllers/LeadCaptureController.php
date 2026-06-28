<?php

namespace Template\LandingLeadCapture\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Template\LandingLeadCapture\Http\Requests\StoreLeadRequest;
use Template\LandingLeadCapture\Mail\LeadCaptured;
use Template\LandingLeadCapture\Models\Lead;

class LeadCaptureController extends Controller
{
    public function __invoke(StoreLeadRequest $request): RedirectResponse
    {
        $data = $request->validatedLeadData();
        $lead = null;

        if ((bool) config('landing-lead-capture.save_to_database', true)) {
            $lead = Lead::query()->create([
                ...$data,
                'metadata' => $this->metadata($data),
                'ip_address' => $request->ip(),
                'user_agent' => Str::limit((string) $request->userAgent(), 1000, ''),
            ]);
        }

        $emailQueued = $this->sendNotification($data, $lead);

        Log::info('lead.captured', [
            'lead_id' => $lead?->id,
            'saved_to_database' => $lead !== null,
            'email_queued' => $emailQueued,
            'source' => $data['source'] ?? null,
            'campaign' => $data['campaign'] ?? null,
            'tag' => $data['tag'] ?? null,
        ]);

        $response = config('landing-lead-capture.redirect_after_submit')
            ? redirect()->to(config('landing-lead-capture.redirect_after_submit'))
            : back();

        return $response->with([
            'landing_lead_capture_success' => config('landing-lead-capture.messages.success'),
            'landing_lead_capture_conversion' => $this->conversionPayload($data),
        ]);
    }

    protected function sendNotification(array $data, ?Lead $lead): bool
    {
        if (! (bool) config('landing-lead-capture.send_email.enabled', false)) {
            return false;
        }

        $recipient = trim((string) config('landing-lead-capture.send_email.to'));

        if ($recipient === '') {
            return false;
        }

        Mail::to($recipient)->send(new LeadCaptured($data, $lead));

        return true;
    }

    protected function metadata(array $data): array
    {
        return [
            'tracking_enabled' => (bool) config('landing-lead-capture.tracking.enabled', false),
            'tracking_event' => config('landing-lead-capture.tracking.event_name', 'lead_capture_submit'),
            'source' => $data['source'] ?? null,
            'campaign' => $data['campaign'] ?? null,
            'tag' => $data['tag'] ?? null,
        ];
    }

    protected function conversionPayload(array $data): ?array
    {
        if (! (bool) config('landing-lead-capture.tracking.enabled', false)) {
            return null;
        }

        return [
            'event' => config('landing-lead-capture.tracking.event_name', 'lead_capture_submit'),
            'source' => $data['source'] ?? null,
            'campaign' => $data['campaign'] ?? null,
            'tag' => $data['tag'] ?? null,
        ];
    }
}
