<?php

namespace Tests\Feature\LandingCore;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RequestIdMiddlewareTest extends TestCase
{
    protected function defineWebProbeRoute(): void
    {
        Route::middleware('web')->get('/_test/request-id', fn () => 'ok');
    }

    public function test_it_adds_a_request_id_header_to_web_responses(): void
    {
        $this->defineWebProbeRoute();

        $response = $this->get('/_test/request-id');

        $response->assertOk();
        $response->assertHeader('X-Request-Id');
        $this->assertNotEmpty($response->headers->get('X-Request-Id'));
    }

    public function test_it_reuses_a_safe_incoming_request_id(): void
    {
        $this->defineWebProbeRoute();

        $response = $this->withHeader('X-Request-Id', 'trace-abc-123')
            ->get('/_test/request-id');

        $response->assertOk();
        $response->assertHeader('X-Request-Id', 'trace-abc-123');
    }

    public function test_it_replaces_an_unsafe_incoming_request_id(): void
    {
        $this->defineWebProbeRoute();

        $response = $this->withHeader('X-Request-Id', 'not allowed value!')
            ->get('/_test/request-id');

        $response->assertOk();
        $this->assertNotSame('not allowed value!', $response->headers->get('X-Request-Id'));
        $this->assertNotEmpty($response->headers->get('X-Request-Id'));
    }
}
