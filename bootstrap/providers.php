<?php

use App\Providers\AppServiceProvider;
use Template\LandingContact\LandingContactServiceProvider;
use Template\LandingCore\LandingCoreServiceProvider;
use Template\LandingWhatsapp\LandingWhatsappServiceProvider;

return [
    AppServiceProvider::class,
    LandingCoreServiceProvider::class,
    LandingContactServiceProvider::class,
    LandingWhatsappServiceProvider::class,
];
