<?php

namespace Template\LandingSeo\Http\Controllers;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Template\LandingCore\Support\Coerce;
use Template\LandingSeo\Config\SeoConfig;
use Template\LandingSeo\Http\Requests\StoreSeoPageRequest;
use Template\LandingSeo\Http\Requests\UpdateSeoPageRequest;
use Template\LandingSeo\Models\SeoPage;
use Template\LandingSeo\Support\SeoUrl;

class SeoPageAdminController extends Controller
{
    public function index(SeoConfig $config): View
    {
        $seoPages = SeoPage::query()
            ->ordered()
            ->paginate($config->adminPerPage());

        return view('landing-seo::admin.index', [
            'seoPages' => $seoPages,
            'defaultRobots' => $config->defaultsRobots(),
        ]);
    }

    public function create(SeoConfig $config): View
    {
        return view('landing-seo::admin.create', [
            'seoPage' => new SeoPage([
                'page_key' => 'home',
                'path' => '/',
                'robots' => $config->defaultsRobots(),
                'og_type' => $config->openGraphType(),
                'twitter_card' => $config->twitterCard(),
                'sitemap_enabled' => true,
                'sitemap_changefreq' => $config->sitemapDefaultChangefreq(),
                'sitemap_priority' => $config->sitemapDefaultPriority(),
                'is_active' => true,
            ]),
            'previewDefaults' => $this->previewDefaults($config),
        ]);
    }

    public function store(StoreSeoPageRequest $request): RedirectResponse
    {
        SeoPage::query()->create($this->payload($request));

        return redirect()
            ->route('seo.admin.index')
            ->with('landing_seo_success', 'Pagina SEO criada com sucesso.');
    }

    public function edit(SeoPage $seoPage, SeoConfig $config): View
    {
        return view('landing-seo::admin.edit', [
            'seoPage' => $seoPage,
            'previewDefaults' => $this->previewDefaults($config),
        ]);
    }

    public function update(UpdateSeoPageRequest $request, SeoPage $seoPage): RedirectResponse
    {
        $seoPage->update($this->payload($request));

        return redirect()
            ->route('seo.admin.index')
            ->with('landing_seo_success', 'Pagina SEO atualizada com sucesso.');
    }

    public function destroy(SeoPage $seoPage): RedirectResponse
    {
        $seoPage->delete();

        return redirect()
            ->route('seo.admin.index')
            ->with('landing_seo_success', 'Pagina SEO removida com sucesso.');
    }

    protected function payload(FormRequest $request): array
    {
        return [
            'page_key' => Coerce::nullableString($request->input('page_key')),
            'path' => SeoUrl::normalizePath($request->input('path')),
            'route_name' => Coerce::nullableString($request->input('route_name')),
            'title' => Coerce::nullableString($request->input('title')),
            'description' => Coerce::nullableString($request->input('description')),
            'canonical_url' => SeoUrl::normalize($request->input('canonical_url'), true),
            'image_url' => SeoUrl::normalize($request->input('image_url'), true),
            'favicon_url' => SeoUrl::normalize($request->input('favicon_url'), true),
            'robots' => Coerce::nullableString($request->input('robots')),
            'og_title' => Coerce::nullableString($request->input('og_title')),
            'og_description' => Coerce::nullableString($request->input('og_description')),
            'og_image' => SeoUrl::normalize($request->input('og_image'), true),
            'og_type' => Coerce::nullableString($request->input('og_type')),
            'twitter_title' => Coerce::nullableString($request->input('twitter_title')),
            'twitter_description' => Coerce::nullableString($request->input('twitter_description')),
            'twitter_image' => SeoUrl::normalize($request->input('twitter_image'), true),
            'twitter_card' => Coerce::nullableString($request->input('twitter_card')),
            'schema' => $this->schemaArray($request->input('schema')),
            'sitemap_enabled' => $request->boolean('sitemap_enabled'),
            'sitemap_changefreq' => Coerce::nullableString($request->input('sitemap_changefreq')),
            'sitemap_priority' => $this->priorityValue($request->input('sitemap_priority')),
            'is_active' => $request->boolean('is_active'),
        ];
    }

    protected function schemaArray(mixed $value): ?array
    {
        $json = Coerce::nullableString($value);

        if ($json === null) {
            return null;
        }

        $decoded = json_decode($json, true);

        return is_array($decoded) ? $decoded : null;
    }

    protected function priorityValue(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return min(1, max(0, (float) $value));
    }

    /**
     * @return array<string, mixed>
     */
    protected function previewDefaults(SeoConfig $config): array
    {
        return [
            'title' => $config->defaultsTitle(),
            'description' => $config->defaultsDescription(),
            'image' => $config->defaultsImage(),
        ];
    }
}
