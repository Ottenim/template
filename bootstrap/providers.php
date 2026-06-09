<?php

use App\Providers\AppServiceProvider;
use Template\LandingAnalytics\LandingAnalyticsServiceProvider;
use Template\LandingContact\LandingContactServiceProvider;
use Template\LandingCookieConsent\LandingCookieConsentServiceProvider;
use Template\LandingCore\LandingCoreServiceProvider;
use Template\LandingFaq\LandingFaqServiceProvider;
use Template\LandingLeadCapture\LandingLeadCaptureServiceProvider;
use Template\LandingPricing\LandingPricingServiceProvider;
use Template\LandingSeo\LandingSeoServiceProvider;
use Template\LandingTestimonials\LandingTestimonialsServiceProvider;
use Template\LandingWhatsapp\LandingWhatsappServiceProvider;

return [
    AppServiceProvider::class,
    LandingCoreServiceProvider::class,
    LandingCookieConsentServiceProvider::class,
    LandingAnalyticsServiceProvider::class,
    LandingContactServiceProvider::class,
    LandingFaqServiceProvider::class,
    LandingLeadCaptureServiceProvider::class,
    LandingPricingServiceProvider::class,
    LandingSeoServiceProvider::class,
    LandingTestimonialsServiceProvider::class,
    LandingWhatsappServiceProvider::class,
];
