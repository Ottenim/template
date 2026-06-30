<?php

namespace Template\LandingFaq\Config;

use Template\LandingCore\Config\ModuleConfig;

/**
 * Config tipada do módulo de FAQ. Concentra as chaves config('landing-faq.*')
 * antes espalhadas no componente Section, no suporte FaqItems, no model, no
 * provider, nas rotas e no controller de admin.
 *
 * Allowlists de apresentação (layout) e a coerção de limite continuam no
 * componente, que conhece o domínio visual.
 */
class FaqConfig extends ModuleConfig
{
    public static function key(): string
    {
        return 'landing-faq';
    }

    public function enabled(): bool
    {
        return $this->bool('enabled', true);
    }

    public function layout(): string
    {
        return $this->string('layout', 'accordion');
    }

    public function showCategories(): bool
    {
        return $this->bool('show_categories', false);
    }

    public function defaultOpenFirstItem(): bool
    {
        return $this->bool('default_open_first_item', true);
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
        return $this->string('database.table', 'lp_faq_items');
    }

    /**
     * Itens definidos via config; a normalização vive em FaqItems.
     *
     * @return array<int|string, mixed>
     */
    public function items(): array
    {
        return $this->list('items', []);
    }

    public function schemaEnabled(): bool
    {
        return $this->bool('schema.enabled', true);
    }

    public function adminEnabled(): bool
    {
        return $this->bool('admin.enabled', false);
    }

    public function adminPrefix(): string
    {
        return $this->string('admin.prefix', 'admin/faq');
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
