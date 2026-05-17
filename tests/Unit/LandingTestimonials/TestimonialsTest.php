<?php

namespace Tests\Unit\LandingTestimonials;

use PHPUnit\Framework\TestCase;
use Template\LandingTestimonials\Support\Testimonials;

class TestimonialsTest extends TestCase
{
    public function test_it_normalizes_filters_sorts_and_limits_public_items(): void
    {
        $items = (new Testimonials)->publicItems([
            [
                'name' => ' Later client ',
                'text' => ' Later testimonial ',
                'company' => ' Later Company ',
                'rating' => 7,
                'sort_order' => 20,
            ],
            [
                'name' => 'Hidden client',
                'text' => 'Hidden testimonial',
                'sort_order' => 0,
                'is_active' => false,
            ],
            [
                'name' => ' Featured client ',
                'text' => "Featured testimonial\nwith second line",
                'role' => ' CEO ',
                'company' => ' Featured Company ',
                'rating' => '5',
                'sort_order' => 30,
                'is_featured' => true,
            ],
            [
                'name' => ' First client ',
                'text' => 'First testimonial',
                'rating' => '4',
                'sort_order' => 10,
            ],
            [
                'name' => '',
                'text' => 'Missing name',
            ],
        ], 3);

        $this->assertCount(3, $items);
        $this->assertSame(['Featured client', 'First client', 'Later client'], $items->pluck('name')->all());
        $this->assertSame("Featured testimonial\nwith second line", $items->first()['text']);
        $this->assertSame('CEO', $items->first()['role']);
        $this->assertSame('Featured Company', $items->first()['company']);
        $this->assertSame(5, $items->first()['rating']);
        $this->assertNull($items->last()['rating']);
        $this->assertArrayNotHasKey('position', $items->first());
    }

    public function test_it_can_include_inactive_items_when_requested(): void
    {
        $items = (new Testimonials)->publicItems([
            [
                'name' => 'Inactive client',
                'text' => 'Inactive testimonial',
                'is_active' => false,
            ],
        ], activeOnly: false);

        $this->assertCount(1, $items);
        $this->assertFalse($items->first()['is_active']);
    }
}
