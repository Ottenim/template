<?php

use Illuminate\Support\Facades\Route;
use Template\LandingTestimonials\Http\Controllers\TestimonialAdminController;

if ((bool) config('landing-testimonials.enabled', true) && (bool) config('landing-testimonials.admin.enabled', false)) {
    Route::middleware(array_filter((array) config('landing-testimonials.admin.middleware', ['web', 'auth'])))
        ->prefix(config('landing-testimonials.admin.prefix', 'admin/testimonials'))
        ->name('testimonials.admin.')
        ->group(function () {
            Route::get('/', [TestimonialAdminController::class, 'index'])->name('index');
            Route::get('/create', [TestimonialAdminController::class, 'create'])->name('create');
            Route::post('/', [TestimonialAdminController::class, 'store'])->name('store');
            Route::get('/{testimonial}/edit', [TestimonialAdminController::class, 'edit'])->name('edit');
            Route::put('/{testimonial}', [TestimonialAdminController::class, 'update'])->name('update');
            Route::delete('/{testimonial}', [TestimonialAdminController::class, 'destroy'])->name('destroy');
        });
}
