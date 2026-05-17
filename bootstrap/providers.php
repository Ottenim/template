<?php

use App\Providers\AppServiceProvider;
use Template\LandingContact\LandingContactServiceProvider;
use Template\LandingCore\LandingCoreServiceProvider;
use Template\LandingFaq\LandingFaqServiceProvider;
use Template\LandingLeadCapture\LandingLeadCaptureServiceProvider;
use Template\LandingTestimonials\LandingTestimonialsServiceProvider;
use Template\LandingWhatsapp\LandingWhatsappServiceProvider;

return [
    AppServiceProvider::class,
    LandingCoreServiceProvider::class,
    LandingContactServiceProvider::class,
    LandingFaqServiceProvider::class,
    LandingLeadCaptureServiceProvider::class,
    LandingTestimonialsServiceProvider::class,
    LandingWhatsappServiceProvider::class,
];
