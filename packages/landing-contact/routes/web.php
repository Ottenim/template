<?php

use Illuminate\Support\Facades\Route;
use Template\LandingContact\Http\Controllers\ContactFormController;

if ((bool) config('landing-contact.enabled', true) && (bool) config('landing-contact.route.enabled', true)) {
    $middleware = (array) config('landing-contact.route.middleware', ['web']);

    if ((bool) config('landing-contact.anti_spam.rate_limit', true)) {
        $maxAttempts = (int) config('landing-contact.anti_spam.rate_limit_max_attempts', 5);
        $decayMinutes = (int) config('landing-contact.anti_spam.rate_limit_decay_minutes', 1);

        $middleware[] = "throttle:{$maxAttempts},{$decayMinutes}";
    }

    Route::middleware(array_filter($middleware))
        ->post(config('landing-contact.route.uri', 'contact'), ContactFormController::class)
        ->name(config('landing-contact.route.name', 'landing-contact.submit'));
}
