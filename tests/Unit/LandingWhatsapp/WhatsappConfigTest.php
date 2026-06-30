<?php

namespace Tests\Unit\LandingWhatsapp;

use PHPUnit\Framework\TestCase;
use Template\LandingCore\Analytics\LandingEvent;
use Template\LandingWhatsapp\Config\WhatsappConfig;

class WhatsappConfigTest extends TestCase
{
    public function test_it_exposes_safe_defaults_when_config_is_empty(): void
    {
        $config = WhatsappConfig::fromArray([]);

        $this->assertTrue($config->enabled());
        $this->assertNull($config->phone());
        $this->assertSame('all', $config->visibility());
        $this->assertSame('Falar no WhatsApp', $config->buttonLabel());
        $this->assertTrue($config->buttonShowText());
        $this->assertTrue($config->floatingEnabled());
        $this->assertSame('bottom-right', $config->floatingPosition());
        $this->assertFalse($config->floatingShowText());
        $this->assertTrue($config->sectionCard());
        $this->assertFalse($config->styleUseBrandColor());
        $this->assertSame('#25D366', $config->styleBrandColor());
        $this->assertFalse($config->trackingEnabled());
    }

    public function test_tracking_event_default_comes_from_the_canonical_enum(): void
    {
        $config = WhatsappConfig::fromArray([]);

        $this->assertSame(LandingEvent::WhatsappClick->value, $config->trackingEventName());
        $this->assertSame('whatsapp_click', $config->trackingEventName());
    }

    public function test_it_reads_and_coerces_configured_values(): void
    {
        $config = WhatsappConfig::fromArray([
            'enabled' => 'false',
            'phone' => '  +55 11 99999-0000  ',
            'button' => [
                'show_text' => '0',
                'aria_label' => 'Fale conosco',
            ],
            'floating' => [
                'enabled' => 'false',
                'position' => 'top-left',
                'mobile_bar' => 1,
            ],
            'style' => [
                'use_brand_color' => 'true',
                'brand_color' => '#abcdef',
            ],
            'tracking' => [
                'enabled' => 1,
                'event_name' => 'wa_custom',
            ],
        ]);

        $this->assertFalse($config->enabled());
        $this->assertSame('+55 11 99999-0000', $config->phone());
        $this->assertFalse($config->buttonShowText());
        $this->assertSame('Fale conosco', $config->buttonAriaLabel());
        $this->assertFalse($config->floatingEnabled());
        $this->assertSame('top-left', $config->floatingPosition());
        $this->assertTrue($config->floatingMobileBar());
        $this->assertTrue($config->styleUseBrandColor());
        $this->assertSame('#abcdef', $config->styleBrandColor());
        $this->assertTrue($config->trackingEnabled());
        $this->assertSame('wa_custom', $config->trackingEventName());
    }
}
