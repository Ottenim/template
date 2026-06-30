<?php

namespace Template\LandingLeadCapture\Config;

use Template\LandingCore\Analytics\LandingEvent;
use Template\LandingCore\Config\ModuleConfig;

/**
 * Config tipada do módulo de captura de lead. Concentra, num único lugar, as
 * chaves config('landing-lead-capture.*') antes espalhadas em rota, request,
 * controller, mail e componentes.
 */
class LeadCaptureConfig extends ModuleConfig
{
    public static function key(): string
    {
        return 'landing-lead-capture';
    }

    public function enabled(): bool
    {
        return $this->bool('enabled', true);
    }

    public function variant(): string
    {
        return $this->string('variant', 'inline');
    }

    public function routeEnabled(): bool
    {
        return $this->bool('route.enabled', true);
    }

    public function routeUri(): string
    {
        return $this->string('route.uri', 'lead-capture');
    }

    public function routeName(): string
    {
        return $this->string('route.name', 'landing-lead-capture.submit');
    }

    /**
     * @return array<int, string>
     */
    public function routeMiddleware(): array
    {
        return array_values(array_filter(
            array_map(
                fn (mixed $middleware): string => trim((string) $middleware),
                $this->list('route.middleware', ['web']),
            ),
            fn (string $middleware): bool => $middleware !== '',
        ));
    }

    public function sectionEnabled(): bool
    {
        return $this->bool('section.enabled', true);
    }

    public function sectionEyebrow(): ?string
    {
        return $this->nullableString('section.eyebrow');
    }

    public function sectionBenefit(): ?string
    {
        return $this->nullableString('section.benefit');
    }

    public function ctaTitle(): ?string
    {
        return $this->nullableString('cta.title');
    }

    public function ctaSubtitle(): ?string
    {
        return $this->nullableString('cta.subtitle');
    }

    public function ctaButtonLabel(): string
    {
        return $this->string('cta.button_label', 'Quero receber');
    }

    public function leadSource(): ?string
    {
        return $this->nullableString('lead.source');
    }

    public function leadCampaign(): ?string
    {
        return $this->nullableString('lead.campaign');
    }

    public function leadTag(): ?string
    {
        return $this->nullableString('lead.tag');
    }

    public function databaseTable(): string
    {
        return $this->string('database.table', 'lp_leads');
    }

    public function saveToDatabase(): bool
    {
        return $this->bool('save_to_database', true);
    }

    public function emailEnabled(): bool
    {
        return $this->bool('send_email.enabled', false);
    }

    public function emailRecipient(): ?string
    {
        return $this->nullableString('send_email.to');
    }

    public function emailSubject(): string
    {
        return $this->string('send_email.subject', 'Novo lead capturado');
    }

    public function redirectAfterSubmit(): ?string
    {
        return $this->nullableString('redirect_after_submit');
    }

    public function successMessage(): ?string
    {
        return $this->nullableString('messages.success');
    }

    public function privacyConsentEnabled(): bool
    {
        return $this->bool('privacy_consent.enabled', true);
    }

    public function privacyConsentRequired(): bool
    {
        return $this->bool('privacy_consent.required', true);
    }

    public function privacyConsentLabel(): string
    {
        return $this->string('privacy_consent.label', 'Li e aceito a politica de privacidade.');
    }

    public function honeypotEnabled(): bool
    {
        return $this->bool('anti_spam.honeypot', true);
    }

    public function honeypotField(): string
    {
        return $this->string('anti_spam.honeypot_field', 'website');
    }

    public function rateLimitEnabled(): bool
    {
        return $this->bool('anti_spam.rate_limit', true);
    }

    public function rateLimitMaxAttempts(): int
    {
        return $this->int('anti_spam.rate_limit_max_attempts', 5);
    }

    public function rateLimitDecayMinutes(): int
    {
        return $this->int('anti_spam.rate_limit_decay_minutes', 1);
    }

    public function trackingEnabled(): bool
    {
        return $this->bool('tracking.enabled', false);
    }

    public function trackingEventName(): string
    {
        return $this->string('tracking.event_name', LandingEvent::LeadCaptureSubmit->value);
    }
}
