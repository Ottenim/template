<?php

namespace Tests\Unit\LandingWhatsapp;

use PHPUnit\Framework\TestCase;
use Template\LandingWhatsapp\Support\WhatsappUrl;

class WhatsappUrlTest extends TestCase
{
    public function test_it_normalizes_phone_and_encodes_message(): void
    {
        $url = (new WhatsappUrl)->make('+55 (11) 99999-0000', 'Ola, quero saber mais & planos');

        $this->assertSame(
            'https://wa.me/5511999990000?text=Ola%2C%20quero%20saber%20mais%20%26%20planos',
            $url,
        );
    }

    public function test_it_returns_empty_url_without_phone_digits(): void
    {
        $whatsappUrl = new WhatsappUrl;

        $this->assertSame('', $whatsappUrl->make(null, 'Mensagem padrao'));
        $this->assertSame('', $whatsappUrl->make('sem telefone', 'Mensagem padrao'));
        $this->assertSame('', $whatsappUrl->normalizePhone('sem telefone'));
    }
}
