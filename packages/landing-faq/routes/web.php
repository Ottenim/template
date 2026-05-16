<?php

use Illuminate\Support\Facades\Route;
use Template\LandingFaq\Http\Controllers\FaqAdminController;

if ((bool) config('landing-faq.enabled', true) && (bool) config('landing-faq.admin.enabled', false)) {
    Route::middleware(array_filter((array) config('landing-faq.admin.middleware', ['web', 'auth'])))
        ->prefix(config('landing-faq.admin.prefix', 'admin/faq'))
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
