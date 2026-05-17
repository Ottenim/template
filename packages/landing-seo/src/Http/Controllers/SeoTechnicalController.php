<?php

namespace Template\LandingSeo\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Template\LandingSeo\Support\SeoManager;

class SeoTechnicalController extends Controller
{
    public function sitemap(SeoManager $seo): Response
    {
        return response()
            ->view('landing-seo::sitemap', [
                'entries' => $seo->sitemapEntries(),
            ])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots(SeoManager $seo): Response
    {
        return response($seo->robotsText(), 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }
}
