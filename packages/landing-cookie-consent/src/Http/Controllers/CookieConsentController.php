<?php

namespace Template\LandingCookieConsent\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Template\LandingCookieConsent\Config\CookieConsentConfig;
use Template\LandingCookieConsent\Http\Requests\StoreConsentRequest;
use Template\LandingCookieConsent\Models\CookieConsent;

class CookieConsentController extends Controller
{
    public function __invoke(StoreConsentRequest $request, CookieConsentConfig $config): JsonResponse
    {
        if (! $config->loggingEnabled() || ! $config->loggingDatabaseEnabled()) {
            return response()->json(['recorded' => false]);
        }

        $data = $request->validatedConsentData();

        $consent = CookieConsent::query()->create([
            ...$data,
            'ip_address' => $config->loggingStoreIp() ? $request->ip() : null,
            'user_agent' => $config->loggingStoreUserAgent()
                ? Str::limit((string) $request->userAgent(), 1000, '')
                : null,
            'created_at' => now(),
        ]);

        return response()->json([
            'recorded' => true,
            'id' => $consent->getKey(),
        ]);
    }
}
