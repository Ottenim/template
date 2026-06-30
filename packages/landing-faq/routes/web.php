<?php

use Illuminate\Support\Facades\Route;
use Template\LandingFaq\Config\FaqConfig;
use Template\LandingFaq\Http\Controllers\FaqAdminController;

$config = FaqConfig::fromConfig();

if ($config->enabled() && $config->adminEnabled()) {
    Route::middleware($config->adminMiddleware())
        ->prefix($config->adminPrefix())
        ->name('faq.admin.')
        ->group(function () {
            Route::get('/', [FaqAdminController::class, 'index'])->name('index');
            Route::get('/create', [FaqAdminController::class, 'create'])->name('create');
            Route::post('/', [FaqAdminController::class, 'store'])->name('store');
            Route::get('/{faqItem}/edit', [FaqAdminController::class, 'edit'])->name('edit');
            Route::put('/{faqItem}', [FaqAdminController::class, 'update'])->name('update');
            Route::delete('/{faqItem}', [FaqAdminController::class, 'destroy'])->name('destroy');
        });
}
