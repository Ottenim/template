<?php

namespace Template\LandingPricing\Config;

use Template\LandingCore\Analytics\LandingEvent;
use Template\LandingCore\Config\ModuleConfig;

/**
 * Config tipada do módulo de planos. Concentra as chaves
 * config('landing-pricing.*') antes espalhadas no componente Section, no
 * suporte PricingPlans, no model, no provider, nas rotas e no controller de
 * admin.
 *
 * Allowlist de apresentação (layout), clamp de colunas, coerção de limite e a
 * validação de URL (PricingUrl) seguem no código de domínio.
 */
class PricingConfig extends ModuleConfig
{
    public static function key(): string
    {
        return 'landing-pricing';
    }

    public function enabled(): bool
    {
        return $this->bool('enabled', true);
    }

    public function layout(): string
    {
        return $this->string('layout', 'cards');
    }

    public function columns(): int
    {
        return $this->int('columns', 3);
    }

    public function showFeaturedPlan(): bool
    {
        return $this->bool('show_featured_plan', true);
    }

    public function featuredLabel(): ?string
    {
        return $this->nullableString('featured_label');
    }

    public function currency(): string
    {
        return $this->string('currency', 'R$');
    }

    public function billingPeriodLabel(): string
    {
        return $this->string('billing_period_label', '/mes');
    }

    public function limit(): ?int
    {
        $limit = $this->int('limit', 0);

        return $limit > 0 ? $limit : null;
    }

    public function sectionEnabled(): bool
    {
        return $this->bool('section.enabled', true);
    }

    public function sectionEyebrow(): ?string
    {
        return $this->nullableString('section.eyebrow');
    }

    public function sectionTitle(): ?string
    {
        return $this->nullableString('section.title');
    }

    public function sectionSubtitle(): ?string
    {
        return $this->nullableString('section.subtitle');
    }

    public function databaseEnabled(): bool
    {
        return $this->bool('database.enabled', true);
    }

    public function databaseTable(): string
    {
        return $this->string('database.table', 'lp_pricing_plans');
    }

    /**
     * Planos definidos via config; a normalização vive em PricingPlans.
     *
     * @return array<int|string, mixed>
     */
    public function plans(): array
    {
        return $this->list('plans', []);
    }

    public function ctaDefaultLabel(): string
    {
        return $this->string('cta.default_label', 'Escolher plano');
    }

    public function ctaDefaultUrl(): string
    {
        return $this->string('cta.default_url', '#contact');
    }

    public function trackingEnabled(): bool
    {
        return $this->bool('tracking.enabled', true);
    }

    public function trackingEventName(): string
    {
        return $this->string('tracking.event_name', LandingEvent::PricingCtaClick->value);
    }

    public function adminEnabled(): bool
    {
        return $this->bool('admin.enabled', false);
    }

    public function adminPrefix(): string
    {
        return $this->string('admin.prefix', 'admin/pricing');
    }

    /**
     * @return array<int, string>
     */
    public function adminMiddleware(): array
    {
        return array_values(array_filter(
            array_map(
                fn (mixed $middleware): string => trim((string) $middleware),
                $this->list('admin.middleware', ['web', 'auth']),
            ),
            fn (string $middleware): bool => $middleware !== '',
        ));
    }

    public function adminPerPage(): int
    {
        return $this->int('admin.per_page', 15);
    }
}
