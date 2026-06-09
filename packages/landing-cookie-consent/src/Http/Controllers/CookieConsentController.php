<?php

namespace Template\LandingCookieConsent\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Template\LandingCookieConsent\Http\Requests\StoreConsentRequest;
use Template\LandingCookieConsent\Models\CookieConsent;

class CookieConsentController extends Controller
{
    public function __invoke(StoreConsentRequest $request): JsonResponse
    {
        if (! (bool) config('landing-cookie-consent.logging.enabled', true)
            || ! (bool) config('landing-cookie-consent.logging.database.enabled', true)) {
            return response()->json(['recorded' => false]);
        }

        $data = $request->validatedConsentData();

        $consent = CookieConsent::query()->create([
            ...$data,
            'ip_address' => (bool) config('landing-cookie-consent.logging.store_ip', false) ? $request->ip() : null,
            'user_agent' => (bool) config('landing-cookie-consent.logging.store_user_agent', true)
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
