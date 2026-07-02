<?php

namespace Template\LandingCookieConsent\Config;

use Template\LandingCore\Config\ModuleConfig;

/**
 * Config tipada do módulo de consentimento de cookies. Centraliza as chaves
 * config('landing-cookie-consent.*') antes espalhadas no componente Banner, no
 * manager, no controller, no model, nas rotas e no provider.
 *
 * A normalização de categorias e as allowlists visuais de layout/posição
 * continuam nos consumidores que conhecem o domínio.
 */
class CookieConsentConfig extends ModuleConfig
{
    public static function key(): string
    {
        return 'landing-cookie-consent';
    }

    public function enabled(): bool
    {
        return $this->bool('enabled', true);
    }

    public function storageKey(): string
    {
        return $this->string('storage_key', 'landing_cookie_consent');
    }

    public function version(): string
    {
        return $this->string('version', '1');
    }

    public function policyUrl(): ?string
    {
        return $this->nullableString('policy_url');
    }

    public function consentLifetimeDays(): int
    {
        return $this->int('consent_lifetime_days', 180);
    }

    /**
     * @return array<int|string, mixed>
     */
    public function categories(): array
    {
        return $this->list('categories', []);
    }

    public function bannerEnabled(): bool
    {
        return $this->bool('banner.enabled', true);
    }

    public function bannerPosition(): string
    {
        return $this->string('banner.position', 'bottom');
    }

    public function bannerLayout(): string
    {
        return $this->string('banner.layout', 'bar');
    }

    public function bannerTitle(): ?string
    {
        return $this->nullableString('banner.title');
    }

    public function bannerMessage(): ?string
    {
        return $this->nullableString('banner.message');
    }

    public function bannerPolicyLabel(): string
    {
        return $this->string('banner.policy_label', 'Saiba mais');
    }

    public function bannerAcceptAllLabel(): string
    {
        return $this->string('banner.accept_all_label', 'Aceitar todos');
    }

    public function bannerRejectOptionalLabel(): string
    {
        return $this->string('banner.reject_optional_label', 'Recusar opcionais');
    }

    public function bannerConfigureLabel(): string
    {
        return $this->string('banner.configure_label', 'Configurar');
    }

    public function bannerReopenLabel(): string
    {
        return $this->string('banner.reopen_label', 'Privacidade');
    }

    public function bannerShowReopenButton(): bool
    {
        return $this->bool('banner.show_reopen_button', true);
    }

    public function bannerAriaLabel(): string
    {
        return $this->string('banner.aria_label', 'Aviso de cookies');
    }

    public function modalTitle(): string
    {
        return $this->string('modal.title', 'Gerenciar preferencias de cookies');
    }

    public function modalDescription(): string
    {
        return $this->string('modal.description', 'Escolha quais categorias opcionais podem ser usadas.');
    }

    public function modalSavePreferencesLabel(): string
    {
        return $this->string('modal.save_preferences_label', 'Salvar preferencias');
    }

    public function modalAcceptAllLabel(): ?string
    {
        return $this->nullableString('modal.accept_all_label');
    }

    public function modalRejectOptionalLabel(): ?string
    {
        return $this->nullableString('modal.reject_optional_label');
    }

    public function modalCloseLabel(): string
    {
        return $this->string('modal.close_label', 'Fechar');
    }

    public function scriptsSelector(): string
    {
        return $this->string(
            'scripts.selector',
            'script[type="text/plain"][data-landing-cookie-category], script[type="text/plain"][data-cookie-category]',
        );
    }

    public function loggingEnabled(): bool
    {
        return $this->bool('logging.enabled', true);
    }

    public function loggingStoreIp(): bool
    {
        return $this->bool('logging.store_ip', false);
    }

    public function loggingStoreUserAgent(): bool
    {
        return $this->bool('logging.store_user_agent', true);
    }

    public function loggingDatabaseEnabled(): bool
    {
        return $this->bool('logging.database.enabled', true);
    }

    public function loggingDatabaseTable(): string
    {
        return $this->string('logging.database.table', 'lp_cookie_consents');
    }

    public function loggingRouteEnabled(): bool
    {
        return $this->bool('logging.route.enabled', true);
    }

    public function loggingRouteUri(): string
    {
        return $this->string('logging.route.uri', 'cookie-consent');
    }

    public function loggingRouteName(): string
    {
        return $this->string('logging.route.name', 'landing-cookie-consent.store');
    }

    /**
     * @return array<int, string>
     */
    public function loggingRouteMiddleware(): array
    {
        return array_values(array_filter(
            array_map(
                fn (mixed $middleware): string => trim((string) $middleware),
                $this->list('logging.route.middleware', ['web']),
            ),
            fn (string $middleware): bool => $middleware !== '',
        ));
    }

    public function loggingRouteRateLimit(): bool
    {
        return $this->bool('logging.route.rate_limit', true);
    }

    public function loggingRouteRateLimitMaxAttempts(): int
    {
        return $this->int('logging.route.rate_limit_max_attempts', 30);
    }

    public function loggingRouteRateLimitDecayMinutes(): int
    {
        return $this->int('logging.route.rate_limit_decay_minutes', 1);
    }

    public function integrationsAnalyticsEnabled(): bool
    {
        return $this->bool('integrations.analytics.enabled', true);
    }

    public function integrationsAnalyticsSyncConfig(): bool
    {
        return $this->bool('integrations.analytics.sync_config', true);
    }

    public function integrationsAnalyticsDefaultGranted(): bool
    {
        return $this->bool('integrations.analytics.default_granted', false);
    }

    /**
     * @return array<int|string, mixed>
     */
    public function integrationsAnalyticsCategories(): array
    {
        return $this->list('integrations.analytics.categories', []);
    }
}
