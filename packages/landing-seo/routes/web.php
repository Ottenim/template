<?php

use Illuminate\Support\Facades\Route;
use Template\LandingSeo\Http\Controllers\SeoPageAdminController;
use Template\LandingSeo\Http\Controllers\SeoTechnicalController;

if ((bool) config('landing-seo.enabled', true) && (bool) config('landing-seo.sitemap.enabled', true)) {
    Route::get('/sitemap.xml', [SeoTechnicalController::class, 'sitemap'])->name('seo.sitemap');
}

if ((bool) config('landing-seo.enabled', true) && (bool) config('landing-seo.robots_txt.enabled', true)) {
    Route::get('/robots.txt', [SeoTechnicalController::class, 'robots'])->name('seo.robots');
}

if ((bool) config('landing-seo.enabled', true) && (bool) config('landing-seo.admin.enabled', false)) {
    Route::middleware(array_filter((array) config('landing-seo.admin.middleware', ['web', 'auth'])))
        ->prefix(config('landing-seo.admin.prefix', 'admin/seo'))
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
