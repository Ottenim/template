<?php

use Illuminate\Support\Facades\Route;
use Template\LandingLeadCapture\Http\Controllers\LeadCaptureController;

if ((bool) config('landing-lead-capture.enabled', true) && (bool) config('landing-lead-capture.route.enabled', true)) {
    $middleware = (array) config('landing-lead-capture.route.middleware', ['web']);

    if ((bool) config('landing-lead-capture.anti_spam.rate_limit', true)) {
        $maxAttempts = (int) config('landing-lead-capture.anti_spam.rate_limit_max_attempts', 5);
        $decayMinutes = (int) config('landing-lead-capture.anti_spam.rate_limit_decay_minutes', 1);

        $middleware[] = "throttle:{$maxAttempts},{$decayMinutes}";
    }

    Route::middleware(array_filter($middleware))
        ->post(config('landing-lead-capture.route.uri', 'lead-capture'), LeadCaptureController::class)
        ->name(config('landing-lead-capture.route.name', 'landing-lead-capture.submit'));
}
