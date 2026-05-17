<?php

namespace Tests\Unit\LandingLeadCapture;

use PHPUnit\Framework\TestCase;
use Template\LandingLeadCapture\Support\LeadCaptureFields;

class LeadCaptureFieldsTest extends TestCase
{
    public function test_it_normalizes_configured_fields(): void
    {
        $fields = new LeadCaptureFields([
            'name' => [
                'enabled' => 'true',
                'required' => 'false',
                'label' => ' Nome completo ',
                'placeholder' => ' Seu nome ',
            ],
            'email' => true,
            'phone' => false,
            'interest' => [
                'enabled' => true,
                'required' => true,
                'options' => [
                    'catalog' => 'Receber catalogo',
                    'newsletter' => 'Newsletter',
                ],
            ],
        ]);

        $this->assertSame(['name', 'email', 'interest'], $fields->enabledNames());
        $this->assertTrue($fields->isEnabled('name'));
        $this->assertFalse($fields->get('name')['required']);
        $this->assertFalse($fields->isEnabled('phone'));
        $this->assertTrue($fields->get('interest')['required']);

        $this->assertSame('Nome completo', $fields->get('name')['label']);
        $this->assertSame('Seu nome', $fields->get('name')['placeholder']);
        $this->assertSame([
            [
                'value' => 'catalog',
                'label' => 'Receber catalogo',
            ],
            [
                'value' => 'newsletter',
                'label' => 'Newsletter',
            ],
        ], $fields->get('interest')['options']);
    }

    public function test_it_falls_back_to_field_defaults_for_invalid_configuration(): void
    {
        $fields = new LeadCaptureFields([
            'name' => null,
            'company' => 'invalid',
            'interest' => [
                'enabled' => true,
                'options' => [
                    '',
                    'valid-option',
                ],
            ],
        ]);

        $this->assertTrue($fields->get('name')['required']);
        $this->assertSame('Nome', $fields->get('name')['label']);
        $this->assertFalse($fields->isEnabled('company'));
        $this->assertSame([
            [
                'value' => 'valid-option',
                'label' => 'valid-option',
            ],
        ], $fields->get('interest')['options']);
    }
}
