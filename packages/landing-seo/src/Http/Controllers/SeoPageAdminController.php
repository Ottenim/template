<?php

namespace Template\LandingSeo\Http\Controllers;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Template\LandingSeo\Http\Requests\StoreSeoPageRequest;
use Template\LandingSeo\Http\Requests\UpdateSeoPageRequest;
use Template\LandingSeo\Models\SeoPage;
use Template\LandingSeo\Support\SeoUrl;

class SeoPageAdminController extends Controller
{
    public function index(): View
    {
        $seoPages = SeoPage::query()
            ->ordered()
            ->paginate((int) config('landing-seo.admin.per_page', 15));

        return view('landing-seo::admin.index', [
            'seoPages' => $seoPages,
        ]);
    }

    public function create(): View
    {
        return view('landing-seo::admin.create', [
            'seoPage' => new SeoPage([
                'page_key' => 'home',
                'path' => '/',
                'robots' => config('landing-seo.defaults.robots', 'index,follow'),
                'og_type' => config('landing-seo.open_graph.type', 'website'),
                'twitter_card' => config('landing-seo.twitter.card', 'summary_large_image'),
                'sitemap_enabled' => true,
                'sitemap_changefreq' => config('landing-seo.sitemap.default_changefreq', 'weekly'),
                'sitemap_priority' => config('landing-seo.sitemap.default_priority', 0.5),
                'is_active' => true,
            ]),
        ]);
    }

    public function store(StoreSeoPageRequest $request): RedirectResponse
    {
        SeoPage::query()->create($this->payload($request));

        return redirect()
            ->route('seo.admin.index')
            ->with('landing_seo_success', 'Pagina SEO criada com sucesso.');
    }

    public function edit(SeoPage $seoPage): View
    {
        return view('landing-seo::admin.edit', [
            'seoPage' => $seoPage,
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
            'page_key' => $this->nullableString($request->input('page_key')),
            'path' => SeoUrl::normalizePath($request->input('path')),
            'route_name' => $this->nullableString($request->input('route_name')),
            'title' => $this->nullableString($request->input('title')),
            'description' => $this->nullableString($request->input('description')),
            'canonical_url' => SeoUrl::normalize($request->input('canonical_url'), true),
            'image_url' => SeoUrl::normalize($request->input('image_url'), true),
            'favicon_url' => SeoUrl::normalize($request->input('favicon_url'), true),
            'robots' => $this->nullableString($request->input('robots')),
            'og_title' => $this->nullableString($request->input('og_title')),
            'og_description' => $this->nullableString($request->input('og_description')),
            'og_image' => SeoUrl::normalize($request->input('og_image'), true),
            'og_type' => $this->nullableString($request->input('og_type')),
            'twitter_title' => $this->nullableString($request->input('twitter_title')),
            'twitter_description' => $this->nullableString($request->input('twitter_description')),
            'twitter_image' => SeoUrl::normalize($request->input('twitter_image'), true),
            'twitter_card' => $this->nullableString($request->input('twitter_card')),
            'schema' => $this->schemaArray($request->input('schema')),
            'sitemap_enabled' => $request->boolean('sitemap_enabled'),
            'sitemap_changefreq' => $this->nullableString($request->input('sitemap_changefreq')),
            'sitemap_priority' => $this->priorityValue($request->input('sitemap_priority')),
            'is_active' => $request->boolean('is_active'),
        ];
    }

    protected function schemaArray(mixed $value): ?array
    {
        $json = $this->nullableString($value);

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

    protected function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
