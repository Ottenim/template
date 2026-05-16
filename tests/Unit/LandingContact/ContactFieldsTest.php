<?php

namespace Tests\Unit\LandingContact;

use PHPUnit\Framework\TestCase;
use Template\LandingContact\Support\ContactFields;

class ContactFieldsTest extends TestCase
{
    public function test_it_normalizes_configured_fields(): void
    {
        $fields = new ContactFields([
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
                    'proposal' => 'Receber proposta',
                    'support' => 'Falar com suporte',
                ],
            ],
            'message' => [
                'enabled' => true,
                'rows' => 3,
            ],
        ]);

        $this->assertSame(['name', 'email', 'interest', 'message'], $fields->enabledNames());
        $this->assertTrue($fields->isEnabled('name'));
        $this->assertFalse($fields->isRequired('name'));
        $this->assertFalse($fields->isEnabled('phone'));
        $this->assertTrue($fields->isRequired('interest'));

        $this->assertSame('Nome completo', $fields->get('name')['label']);
        $this->assertSame('Seu nome', $fields->get('name')['placeholder']);
        $this->assertSame([
            [
                'value' => 'proposal',
                'label' => 'Receber proposta',
            ],
            [
                'value' => 'support',
                'label' => 'Falar com suporte',
            ],
        ], $fields->get('interest')['options']);
        $this->assertSame(3, $fields->get('message')['rows']);
    }

    public function test_it_falls_back_to_field_defaults_for_invalid_configuration(): void
    {
        $fields = new ContactFields([
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

        $this->assertTrue($fields->isRequired('name'));
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
