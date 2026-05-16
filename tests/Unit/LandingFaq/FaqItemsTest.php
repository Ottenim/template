<?php

namespace Tests\Unit\LandingFaq;

use PHPUnit\Framework\TestCase;
use Template\LandingFaq\Support\FaqItems;

class FaqItemsTest extends TestCase
{
    public function test_it_normalizes_filters_sorts_and_limits_public_items(): void
    {
        $items = (new FaqItems)->publicItems([
            [
                'question' => ' Later question ',
                'answer' => ' Later answer ',
                'sort_order' => 20,
            ],
            [
                'question' => 'Hidden question',
                'answer' => 'Hidden answer',
                'sort_order' => 0,
                'is_active' => false,
            ],
            [
                'question' => ' First question ',
                'answer' => "First answer\nwith second line",
                'category' => ' Billing ',
                'sort_order' => 10,
            ],
            [
                'question' => '',
                'answer' => 'Missing question',
            ],
        ], 2);

        $this->assertCount(2, $items);
        $this->assertSame(['First question', 'Later question'], $items->pluck('question')->all());
        $this->assertSame('Billing', $items->first()['category']);
        $this->assertSame("First answer\nwith second line", $items->first()['answer']);
        $this->assertArrayNotHasKey('position', $items->first());
    }

    public function test_it_can_generate_script_safe_schema_json(): void
    {
        $json = (new FaqItems)->schemaJson(collect([
            [
                'question' => 'Is <script> escaped?',
                'answer' => 'Use </script><script>alert("x")</script>',
            ],
        ]));

        $this->assertIsString($json);
        $this->assertStringNotContainsString('</script>', $json);
        $this->assertStringContainsString('\u003C/script\u003E', $json);

        $decoded = json_decode($json, true);

        $this->assertSame('https://schema.org', $decoded['@context']);
        $this->assertSame('FAQPage', $decoded['@type']);
        $this->assertSame('Is <script> escaped?', $decoded['mainEntity'][0]['name']);
        $this->assertSame(
            'Use </script><script>alert("x")</script>',
            $decoded['mainEntity'][0]['acceptedAnswer']['text'],
        );
    }

    public function test_schema_json_returns_null_without_items(): void
    {
        $this->assertNull((new FaqItems)->schemaJson(collect()));
    }
}
