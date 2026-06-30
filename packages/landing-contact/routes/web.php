<?php

use Illuminate\Support\Facades\Route;
use Template\LandingContact\Config\ContactConfig;
use Template\LandingContact\Http\Controllers\ContactFormController;

$config = ContactConfig::fromConfig();

if ($config->enabled() && $config->routeEnabled()) {
    $middleware = $config->routeMiddleware();

    if ($config->rateLimitEnabled()) {
        $middleware[] = "throttle:{$config->rateLimitMaxAttempts()},{$config->rateLimitDecayMinutes()}";
    }

    Route::middleware($middleware)
        ->post($config->routeUri(), ContactFormController::class)
        ->name($config->routeName());
}
