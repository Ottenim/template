<?php

namespace Template\LandingTestimonials\Config;

use Template\LandingCore\Config\ModuleConfig;

/**
 * Config tipada do módulo de depoimentos. Concentra as chaves
 * config('landing-testimonials.*') antes espalhadas no componente Section, no
 * suporte Testimonials, no model, no provider, nas rotas e no controller de
 * admin.
 *
 * Allowlist de apresentação (layout), clamp de colunas e coerção de limite
 * seguem no componente, que conhece o domínio visual.
 */
class TestimonialsConfig extends ModuleConfig
{
    public static function key(): string
    {
        return 'landing-testimonials';
    }

    public function enabled(): bool
    {
        return $this->bool('enabled', true);
    }

    public function layout(): string
    {
        return $this->string('layout', 'grid');
    }

    public function columns(): int
    {
        return $this->int('columns', 3);
    }

    public function showAvatar(): bool
    {
        return $this->bool('show_avatar', true);
    }

    public function showRating(): bool
    {
        return $this->bool('show_rating', false);
    }

    public function showCompany(): bool
    {
        return $this->bool('show_company', true);
    }

    public function showLogo(): bool
    {
        return $this->bool('show_logo', true);
    }

    public function limit(): ?int
    {
        $limit = $this->int('limit', 6);

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
        return $this->string('database.table', 'lp_testimonials');
    }

    /**
     * Itens definidos via config; a normalização vive em Testimonials.
     *
     * @return array<int|string, mixed>
     */
    public function items(): array
    {
        return $this->list('items', []);
    }

    public function adminEnabled(): bool
    {
        return $this->bool('admin.enabled', false);
    }

    public function adminPrefix(): string
    {
        return $this->string('admin.prefix', 'admin/testimonials');
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
