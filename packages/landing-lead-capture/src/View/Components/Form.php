<?php

namespace Template\LandingLeadCapture\View\Components;

use Illuminate\Support\Facades\Route;
use Illuminate\View\Component;
use Illuminate\View\View;
use Template\LandingLeadCapture\Support\LeadCaptureFields;

class Form extends Component
{
    public bool $enabled;

    public string $action;

    public array $fields;

    public array $privacyConsent;

    public bool $honeypotEnabled;

    public string $honeypotField;

    public string $buttonLabel;

    public ?string $sourcePage;

    public ?string $sourceUrl;

    public ?string $source;

    public ?string $campaign;

    public ?string $tag;

    public bool $trackingEnabled;

    public string $trackingEvent;

    public string $variant;

    public bool $framed;

    public function __construct(
        ?array $fields = null,
        ?string $action = null,
        ?string $buttonLabel = null,
        ?string $sourcePage = null,
        ?string $sourceUrl = null,
        ?string $source = null,
        ?string $campaign = null,
        ?string $tag = null,
        mixed $tracking = null,
        ?string $trackingEvent = null,
        ?string $variant = null,
        mixed $framed = null,
        mixed $enabled = null,
    ) {
        $this->enabled = $this->boolValue(config('landing-lead-capture.enabled', true), true)
            && $this->boolValue($enabled, true);
        $this->action = $this->nullableString($action) ?? $this->defaultAction();
        $this->fields = (new LeadCaptureFields($fields))->enabled();
        $this->privacyConsent = $this->privacyConsent();
        $this->honeypotEnabled = $this->boolValue(config('landing-lead-capture.anti_spam.honeypot', true), true);
        $this->honeypotField = $this->stringValue(config('landing-lead-capture.anti_spam.honeypot_field'), 'website');
        $this->buttonLabel = $this->stringValue($buttonLabel, config('landing-lead-capture.cta.button_label', 'Quero receber'));
        $this->sourcePage = $this->nullableString($sourcePage ?? request()->path());
        $this->sourceUrl = $this->nullableString($sourceUrl ?? request()->fullUrl());
        $this->source = $this->nullableString($source ?? config('landing-lead-capture.lead.source'));
        $this->campaign = $this->nullableString($campaign ?? config('landing-lead-capture.lead.campaign'));
        $this->tag = $this->nullableString($tag ?? config('landing-lead-capture.lead.tag'));
        $this->trackingEnabled = $this->boolValue($tracking, (bool) config('landing-lead-capture.tracking.enabled', false));
        $this->trackingEvent = $this->stringValue(
            $trackingEvent,
            config('landing-lead-capture.tracking.event_name', 'lead_capture_submit'),
        );
        $this->variant = $this->variantValue($variant ?? config('landing-lead-capture.variant', 'inline'));
        $this->framed = $this->boolValue($framed, true);
    }

    public function shouldRender(): bool
    {
        return $this->enabled && $this->fields !== [];
    }

    public function render(): View
    {
        return view('landing-lead-capture::components.form');
    }

    protected function defaultAction(): string
    {
        $routeName = $this->nullableString(config('landing-lead-capture.route.name', 'landing-lead-capture.submit'));

        if ($routeName && Route::has($routeName)) {
            return route($routeName);
        }

        return url(config('landing-lead-capture.route.uri', 'lead-capture'));
    }

    protected function privacyConsent(): array
    {
        return [
            'enabled' => $this->boolValue(config('landing-lead-capture.privacy_consent.enabled', true), true),
            'required' => $this->boolValue(config('landing-lead-capture.privacy_consent.required', true), true),
            'label' => $this->stringValue(
                config('landing-lead-capture.privacy_consent.label'),
                'Li e aceito a politica de privacidade.',
            ),
        ];
    }

    protected function variantValue(mixed $value): string
    {
        $variant = $this->stringValue($value, 'inline');

        return in_array($variant, ['inline', 'card', 'bar'], true) ? $variant : 'inline';
    }

    protected function boolValue(mixed $value, bool $default): bool
    {
        if ($value === null) {
            return $default;
        }

        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $default;
        }

        return (bool) $value;
    }

    protected function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    protected function stringValue(mixed $value, string $default): string
    {
        return $this->nullableString($value) ?? $default;
    }
}
