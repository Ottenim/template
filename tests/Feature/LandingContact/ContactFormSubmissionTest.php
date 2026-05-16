<?php

namespace Tests\Feature\LandingContact;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Template\LandingContact\Mail\ContactMessageReceived;
use Template\LandingContact\Models\ContactMessage;
use Tests\TestCase;

class ContactFormSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_accepts_valid_submission_saves_message_and_sends_notification(): void
    {
        Mail::fake();

        config()->set('landing-contact.send_email.to', 'sales@example.test');
        config()->set('landing-contact.tracking.enabled', true);

        $response = $this->from('/origem')->post(route('landing-contact.submit'), [
            'name' => 'Maria Silva',
            'email' => 'maria@example.test',
            'phone' => '11999990000',
            'message' => 'Ola, quero receber uma proposta.',
            'privacy_consent' => '1',
            'source_page' => 'home',
            'source_url' => 'https://example.test/home',
        ]);

        $response->assertRedirect('/origem');
        $response->assertSessionHas('landing_contact_success', 'Mensagem enviada com sucesso. Retornaremos em breve.');
        $response->assertSessionHas('landing_contact_conversion', [
            'event' => 'contact_form_submit',
        ]);

        $this->assertDatabaseHas('lp_contact_messages', [
            'name' => 'Maria Silva',
            'email' => 'maria@example.test',
            'phone' => '11999990000',
            'message' => 'Ola, quero receber uma proposta.',
            'privacy_consent' => true,
            'source_page' => 'home',
            'source_url' => 'https://example.test/home',
        ]);

        $contactMessage = ContactMessage::query()->firstOrFail();

        $this->assertSame('contact_form_submit', $contactMessage->metadata['tracking_event']);
        $this->assertNotEmpty($contactMessage->ip_address);

        Mail::assertSent(
            ContactMessageReceived::class,
            fn (ContactMessageReceived $mail) => $mail->data['email'] === 'maria@example.test'
                && $mail->contactMessage?->is($contactMessage) === true,
        );
    }

    public function test_it_rejects_invalid_required_disabled_and_honeypot_fields(): void
    {
        Mail::fake();

        config()->set('landing-contact.fields.company', false);

        $response = $this->from('/contact')->post(route('landing-contact.submit'), [
            'name' => '',
            'email' => 'not-an-email',
            'company' => 'Acme',
            'message' => '',
            'privacy_consent' => '0',
            'website' => 'spam',
        ]);

        $response->assertRedirect('/contact');
        $response->assertSessionHasErrors([
            'name',
            'email',
            'company',
            'message',
            'privacy_consent',
            'website',
        ]);

        $this->assertDatabaseCount('lp_contact_messages', 0);
        Mail::assertNothingSent();
    }

    public function test_it_can_skip_database_and_email_and_redirect_to_configured_url(): void
    {
        Mail::fake();

        config()->set('landing-contact.save_to_database', false);
        config()->set('landing-contact.send_email.enabled', false);
        config()->set('landing-contact.privacy_consent.enabled', false);
        config()->set('landing-contact.redirect_after_submit', '/obrigado');

        $response = $this->post(route('landing-contact.submit'), [
            'name' => 'Joao',
            'email' => 'joao@example.test',
            'message' => 'Preciso de contato.',
        ]);

        $response->assertRedirect('/obrigado');
        $response->assertSessionHas('landing_contact_success');

        $this->assertDatabaseCount('lp_contact_messages', 0);
        Mail::assertNothingSent();
    }
}
