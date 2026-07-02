<?php

use Illuminate\Support\Facades\Route;
use Template\LandingSeo\Config\SeoConfig;
use Template\LandingSeo\Http\Controllers\SeoPageAdminController;
use Template\LandingSeo\Http\Controllers\SeoTechnicalController;

$config = SeoConfig::fromConfig();

if ($config->enabled() && $config->sitemapEnabled()) {
    Route::get('/sitemap.xml', [SeoTechnicalController::class, 'sitemap'])->name('seo.sitemap');
}

if ($config->enabled() && $config->robotsTxtEnabled()) {
    Route::get('/robots.txt', [SeoTechnicalController::class, 'robots'])->name('seo.robots');
}

if ($config->enabled() && $config->adminEnabled()) {
    Route::middleware($config->adminMiddleware())
        ->prefix($config->adminPrefix())
        ->name('seo.admin.')
        ->group(function () {
            Route::get('/', [SeoPageAdminController::class, 'index'])->name('index');
            Route::get('/create', [SeoPageAdminController::class, 'create'])->name('create');
            Route::post('/', [SeoPageAdminController::class, 'store'])->name('store');
            Route::get('/{seoPage}/edit', [SeoPageAdminController::class, 'edit'])->name('edit');
            Route::put('/{seoPage}', [SeoPageAdminController::class, 'update'])->name('update');
            Route::delete('/{seoPage}', [SeoPageAdminController::class, 'destroy'])->name('destroy');
        });
}
