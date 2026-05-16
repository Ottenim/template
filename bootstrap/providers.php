<?php

use App\Providers\AppServiceProvider;
use Template\LandingCore\LandingCoreServiceProvider;
use Template\LandingWhatsapp\LandingWhatsappServiceProvider;

return [
    AppServiceProvider::class,
    LandingCoreServiceProvider::class,
    LandingWhatsappServiceProvider::class,
];
