<?php

namespace Template\LandingLeadCapture\View\Components;

use Illuminate\Support\Facades\Route;
use Illuminate\View\Component;
use Illuminate\View\View;
use Template\LandingCore\Support\Coerce;
use Template\LandingLeadCapture\Config\LeadCaptureConfig;
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
        $config = app(LeadCaptureConfig::class);

        $this->enabled = $config->enabled() && Coerce::bool($enabled, true);
        $this->action = Coerce::nullableString($action) ?? $this->defaultAction($config);
        $this->fields = (new LeadCaptureFields($fields))->enabled();
        $this->privacyConsent = [
            'enabled' => $config->privacyConsentEnabled(),
            'required' => $config->privacyConsentRequired(),
            'label' => $config->privacyConsentLabel(),
        ];
        $this->honeypotEnabled = $config->honeypotEnabled();
        $this->honeypotField = $config->honeypotField();
        $this->buttonLabel = Coerce::string($buttonLabel, $config->ctaButtonLabel());
        $this->sourcePage = Coerce::nullableString($sourcePage ?? request()->path());
        $this->sourceUrl = Coerce::nullableString($sourceUrl ?? request()->fullUrl());
        $this->source = Coerce::nullableString($source ?? $config->leadSource());
        $this->campaign = Coerce::nullableString($campaign ?? $config->leadCampaign());
        $this->tag = Coerce::nullableString($tag ?? $config->leadTag());
        $this->trackingEnabled = Coerce::bool($tracking, $config->trackingEnabled());
        $this->trackingEvent = Coerce::string($trackingEvent, $config->trackingEventName());
        $this->variant = $this->variantValue($variant ?? $config->variant());
        $this->framed = Coerce::bool($framed, true);
    }

    public function shouldRender(): bool
    {
        return $this->enabled && $this->fields !== [];
    }

    public function render(): View
    {
        return view('landing-lead-capture::components.form');
    }

    protected function defaultAction(LeadCaptureConfig $config): string
    {
        $routeName = $config->routeName();

        if (Route::has($routeName)) {
            return route($routeName);
        }

        return url($config->routeUri());
    }

    protected function variantValue(mixed $value): string
    {
        $variant = Coerce::string($value, 'inline');

        return in_array($variant, ['inline', 'card', 'bar'], true) ? $variant : 'inline';
    }
}
