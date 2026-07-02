<?php

use Illuminate\Support\Facades\Route;
use Template\LandingCookieConsent\Config\CookieConsentConfig;
use Template\LandingCookieConsent\Http\Controllers\CookieConsentController;

$config = CookieConsentConfig::fromConfig();

if ($config->enabled() && $config->loggingEnabled() && $config->loggingRouteEnabled()) {
    $middleware = $config->loggingRouteMiddleware();

    if ($config->loggingRouteRateLimit()) {
        $maxAttempts = $config->loggingRouteRateLimitMaxAttempts();
        $decayMinutes = $config->loggingRouteRateLimitDecayMinutes();

        $middleware[] = "throttle:{$maxAttempts},{$decayMinutes}";
    }

    Route::middleware($middleware)
        ->post($config->loggingRouteUri(), CookieConsentController::class)
        ->name($config->loggingRouteName());
}
