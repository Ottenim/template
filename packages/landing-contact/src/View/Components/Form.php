<?php

namespace Template\LandingContact\View\Components;

use Illuminate\Support\Facades\Route;
use Illuminate\View\Component;
use Illuminate\View\View;
use Template\LandingContact\Config\ContactConfig;
use Template\LandingContact\Support\ContactFields;
use Template\LandingCore\Support\Coerce;

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
        $config = app(ContactConfig::class);

        $this->enabled = $config->enabled() && Coerce::bool($enabled, true);
        $this->action = Coerce::nullableString($action) ?? $this->defaultAction($config);
        $this->fields = (new ContactFields($fields))->enabled();
        $this->privacyConsent = [
            'enabled' => $config->privacyConsentEnabled(),
            'required' => $config->privacyConsentRequired(),
            'label' => $config->privacyConsentLabel(),
        ];
        $this->honeypotEnabled = $config->honeypotEnabled();
        $this->honeypotField = $config->honeypotField();
        $this->buttonLabel = Coerce::string($buttonLabel, $config->buttonLabel());
        $this->sourcePage = Coerce::nullableString($sourcePage ?? request()->path());
        $this->sourceUrl = Coerce::nullableString($sourceUrl ?? request()->fullUrl());
        $this->trackingEnabled = Coerce::bool($tracking, $config->trackingEnabled());
        $this->trackingEvent = Coerce::string($trackingEvent, $config->trackingEventName());
    }

    public function shouldRender(): bool
    {
        return $this->enabled && $this->fields !== [];
    }

    public function render(): View
    {
        return view('landing-contact::components.form');
    }

    protected function defaultAction(ContactConfig $config): string
    {
        $routeName = $config->routeName();

        if (Route::has($routeName)) {
            return route($routeName);
        }

        return url($config->routeUri());
    }
}
