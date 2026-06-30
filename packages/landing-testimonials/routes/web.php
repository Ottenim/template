<?php

use Illuminate\Support\Facades\Route;
use Template\LandingTestimonials\Config\TestimonialsConfig;
use Template\LandingTestimonials\Http\Controllers\TestimonialAdminController;

$config = TestimonialsConfig::fromConfig();

if ($config->enabled() && $config->adminEnabled()) {
    Route::middleware($config->adminMiddleware())
        ->prefix($config->adminPrefix())
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
