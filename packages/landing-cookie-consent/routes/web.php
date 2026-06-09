<?php

use Illuminate\Support\Facades\Route;
use Template\LandingCookieConsent\Http\Controllers\CookieConsentController;

if ((bool) config('landing-cookie-consent.enabled', true)
    && (bool) config('landing-cookie-consent.logging.enabled', true)
    && (bool) config('landing-cookie-consent.logging.route.enabled', true)) {
    $middleware = (array) config('landing-cookie-consent.logging.route.middleware', ['web']);

    if ((bool) config('landing-cookie-consent.logging.route.rate_limit', true)) {
        $maxAttempts = (int) config('landing-cookie-consent.logging.route.rate_limit_max_attempts', 30);
        $decayMinutes = (int) config('landing-cookie-consent.logging.route.rate_limit_decay_minutes', 1);

        $middleware[] = "throttle:{$maxAttempts},{$decayMinutes}";
    }

    Route::middleware(array_filter($middleware))
        ->post(config('landing-cookie-consent.logging.route.uri', 'cookie-consent'), CookieConsentController::class)
        ->name(config('landing-cookie-consent.logging.route.name', 'landing-cookie-consent.store'));
}
