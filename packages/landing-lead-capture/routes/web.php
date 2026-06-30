<?php

use Illuminate\Support\Facades\Route;
use Template\LandingLeadCapture\Config\LeadCaptureConfig;
use Template\LandingLeadCapture\Http\Controllers\LeadCaptureController;

$config = LeadCaptureConfig::fromConfig();

if ($config->enabled() && $config->routeEnabled()) {
    $middleware = $config->routeMiddleware();

    if ($config->rateLimitEnabled()) {
        $middleware[] = "throttle:{$config->rateLimitMaxAttempts()},{$config->rateLimitDecayMinutes()}";
    }

    Route::middleware($middleware)
        ->post($config->routeUri(), LeadCaptureController::class)
        ->name($config->routeName());
}
