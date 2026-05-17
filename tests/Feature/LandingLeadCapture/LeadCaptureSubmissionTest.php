<?php

namespace Tests\Feature\LandingLeadCapture;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Template\LandingLeadCapture\Mail\LeadCaptured;
use Template\LandingLeadCapture\Models\Lead;
use Tests\TestCase;

class LeadCaptureSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_accepts_valid_submission_saves_lead_and_sends_notification(): void
    {
        Mail::fake();

        config()->set('landing-lead-capture.fields.phone', true);
        config()->set('landing-lead-capture.fields.company', true);
        config()->set('landing-lead-capture.fields.interest', [
            'enabled' => true,
            'required' => true,
            'options' => [
                'catalog' => 'Receber catalogo',
            ],
        ]);
        config()->set('landing-lead-capture.send_email.enabled', true);
        config()->set('landing-lead-capture.send_email.to', 'sales@example.test');
        config()->set('landing-lead-capture.tracking.enabled', true);

        $response = $this->from('/origem')->post(route('landing-lead-capture.submit'), [
            'name' => 'Maria Silva',
            'email' => 'maria@example.test',
            'phone' => '11999990000',
            'company' => 'Acme',
            'interest' => 'catalog',
            'privacy_consent' => '1',
            'source_page' => 'home',
            'source_url' => 'https://example.test/home',
            'source' => 'ebook',
            'campaign' => 'lancamento',
            'tag' => 'catalogo',
        ]);

        $response->assertRedirect('/origem');
        $response->assertSessionHas('landing_lead_capture_success', 'Cadastro realizado com sucesso.');
        $response->assertSessionHas('landing_lead_capture_conversion', [
            'event' => 'lead_capture_submit',
            'source' => 'ebook',
            'campaign' => 'lancamento',
            'tag' => 'catalogo',
        ]);

        $this->assertDatabaseHas('lp_leads', [
            'name' => 'Maria Silva',
            'email' => 'maria@example.test',
            'phone' => '11999990000',
            'company' => 'Acme',
            'interest' => 'catalog',
            'privacy_consent' => true,
            'source_page' => 'home',
            'source_url' => 'https://example.test/home',
            'source' => 'ebook',
            'campaign' => 'lancamento',
            'tag' => 'catalogo',
        ]);

        $lead = Lead::query()->firstOrFail();

        $this->assertSame('lead_capture_submit', $lead->metadata['tracking_event']);
        $this->assertTrue($lead->metadata['tracking_enabled']);
        $this->assertNotEmpty($lead->ip_address);

        Mail::assertSent(
            LeadCaptured::class,
            fn (LeadCaptured $mail) => $mail->data['email'] === 'maria@example.test'
                && $mail->lead?->is($lead) === true,
        );
    }

    public function test_it_rejects_invalid_required_disabled_interest_and_honeypot_fields(): void
    {
        Mail::fake();

        config()->set('landing-lead-capture.fields.company', false);
        config()->set('landing-lead-capture.fields.interest', [
            'enabled' => true,
            'required' => true,
            'options' => [
                'catalog' => 'Receber catalogo',
            ],
        ]);

        $response = $this->from('/lead')->post(route('landing-lead-capture.submit'), [
            'name' => '',
            'email' => 'not-an-email',
            'company' => 'Acme',
            'interest' => 'invalid',
            'privacy_consent' => '0',
            'website' => 'spam',
        ]);

        $response->assertRedirect('/lead');
        $response->assertSessionHasErrors([
            'name',
            'email',
            'company',
            'interest',
            'privacy_consent',
            'website',
        ]);

        $this->assertDatabaseCount('lp_leads', 0);
        Mail::assertNothingSent();
    }

    public function test_it_can_skip_database_and_email_and_redirect_to_configured_url(): void
    {
        Mail::fake();

        config()->set('landing-lead-capture.save_to_database', false);
        config()->set('landing-lead-capture.send_email.enabled', false);
        config()->set('landing-lead-capture.privacy_consent.enabled', false);
        config()->set('landing-lead-capture.redirect_after_submit', '/obrigado');

        $response = $this->post(route('landing-lead-capture.submit'), [
            'name' => 'Joao',
            'email' => 'joao@example.test',
        ]);

        $response->assertRedirect('/obrigado');
        $response->assertSessionHas('landing_lead_capture_success');

        $this->assertDatabaseCount('lp_leads', 0);
        Mail::assertNothingSent();
    }
}
