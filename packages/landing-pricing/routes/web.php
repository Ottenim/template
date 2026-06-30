<?php

use Illuminate\Support\Facades\Route;
use Template\LandingPricing\Config\PricingConfig;
use Template\LandingPricing\Http\Controllers\PricingPlanAdminController;

$config = PricingConfig::fromConfig();

if ($config->enabled() && $config->adminEnabled()) {
    Route::middleware($config->adminMiddleware())
        ->prefix($config->adminPrefix())
        ->name('pricing.admin.')
        ->group(function () {
            Route::get('/', [PricingPlanAdminController::class, 'index'])->name('index');
            Route::get('/create', [PricingPlanAdminController::class, 'create'])->name('create');
            Route::post('/', [PricingPlanAdminController::class, 'store'])->name('store');
            Route::get('/{pricingPlan}/edit', [PricingPlanAdminController::class, 'edit'])->name('edit');
            Route::put('/{pricingPlan}', [PricingPlanAdminController::class, 'update'])->name('update');
            Route::delete('/{pricingPlan}', [PricingPlanAdminController::class, 'destroy'])->name('destroy');
        });
}
