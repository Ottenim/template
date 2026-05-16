<?php

namespace Template\LandingContact\View\Components;

use Illuminate\Support\Facades\Route;
use Illuminate\View\Component;
use Illuminate\View\View;
use Template\LandingContact\Support\ContactFields;

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

    public bool $trackingEnabled;

    public string $trackingEvent;

    public function __construct(
        ?array $fields = null,
        ?string $action = null,
        ?string $buttonLabel = null,
        ?string $sourcePage = null,
        ?string $sourceUrl = null,
        mixed $tracking = null,
        ?string $trackingEvent = null,
        mixed $enabled = null,
    ) {
        $this->enabled = $this->boolValue(config('landing-contact.enabled', true), true)
            && $this->boolValue($enabled, true);
        $this->action = $this->nullableString($action) ?? $this->defaultAction();
        $this->fields = (new ContactFields($fields))->enabled();
        $this->privacyConsent = $this->privacyConsent();
        $this->honeypotEnabled = $this->boolValue(config('landing-contact.anti_spam.honeypot', true), true);
        $this->honeypotField = $this->stringValue(config('landing-contact.anti_spam.honeypot_field'), 'website');
        $this->buttonLabel = $this->stringValue($buttonLabel, config('landing-contact.button.label', 'Solicitar contato'));
        $this->sourcePage = $this->nullableString($sourcePage ?? request()->path());
        $this->sourceUrl = $this->nullableString($sourceUrl ?? request()->fullUrl());
        $this->trackingEnabled = $this->boolValue($tracking, (bool) config('landing-contact.tracking.enabled', false));
        $this->trackingEvent = $this->stringValue(
            $trackingEvent,
            config('landing-contact.tracking.event_name', 'contact_form_submit'),
        );
    }

    public function shouldRender(): bool
    {
        return $this->enabled && $this->fields !== [];
    }

    public function render(): View
    {
        return view('landing-contact::components.form');
    }

    protected function defaultAction(): string
    {
        $routeName = $this->nullableString(config('landing-contact.route.name', 'landing-contact.submit'));

        if ($routeName && Route::has($routeName)) {
            return route($routeName);
        }

        return url(config('landing-contact.route.uri', 'contact'));
    }

    protected function privacyConsent(): array
    {
        return [
            'enabled' => $this->boolValue(config('landing-contact.privacy_consent.enabled', true), true),
            'required' => $this->boolValue(config('landing-contact.privacy_consent.required', true), true),
            'label' => $this->stringValue(
                config('landing-contact.privacy_consent.label'),
                'Li e aceito a politica de privacidade.',
            ),
        ];
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
