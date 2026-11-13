<?php

namespace Tests\Feature\Frontend;

use App\Mail\ContactFormMessage;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\FilamentTestCase;

class ContactFormControllerTest extends FilamentTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(ThrottleRequests::class);

        Mail::fake();
    }

    // -------------------------------------------------------------------------
    // Valid submission
    // -------------------------------------------------------------------------

    public function test_valid_submission_returns_ok(): void
    {
        $this->fakeTurnstile();

        $this->postJson('/contact', $this->validPayload())
            ->assertOk();
    }

    public function test_valid_submission_sends_mail_to_configured_recipient(): void
    {
        $this->fakeTurnstile();
        config(['app.contact_mail_to' => 'owner@example.com']);

        $this->postJson('/contact', $this->validPayload());

        Mail::assertSent(ContactFormMessage::class, function (ContactFormMessage $mail) {
            return $mail->hasTo('owner@example.com');
        });
    }

    public function test_valid_submission_includes_email_in_mail(): void
    {
        $this->fakeTurnstile();

        $this->postJson('/contact', $this->validPayload(['email' => 'sender@example.com']));

        Mail::assertSent(ContactFormMessage::class, fn ($m) => $m->senderEmail === 'sender@example.com');
    }

    public function test_valid_submission_includes_message_in_mail(): void
    {
        $this->fakeTurnstile();

        $this->postJson('/contact', $this->validPayload(['message' => 'Hi, I have a question.']));

        Mail::assertSent(ContactFormMessage::class, fn ($m) => $m->senderMessage === 'Hi, I have a question.');
    }

    public function test_name_is_optional(): void
    {
        $this->fakeTurnstile();
        $payload = $this->validPayload();
        unset($payload['name']);

        $this->postJson('/contact', $payload)->assertOk();

        Mail::assertSent(ContactFormMessage::class, fn ($m) => $m->senderName === '');
    }

    // -------------------------------------------------------------------------
    // Validation
    // -------------------------------------------------------------------------

    public function test_missing_email_returns_422(): void
    {
        $payload = $this->validPayload();
        unset($payload['email']);

        $this->postJson('/contact', $payload)->assertUnprocessable();

        Mail::assertNotSent(ContactFormMessage::class);
    }

    public function test_invalid_email_returns_422(): void
    {
        $this->postJson('/contact', $this->validPayload(['email' => 'not-an-email']))
            ->assertUnprocessable();
    }

    public function test_missing_message_returns_422(): void
    {
        $payload = $this->validPayload();
        unset($payload['message']);

        $this->postJson('/contact', $payload)->assertUnprocessable();
    }

    public function test_message_too_short_returns_422(): void
    {
        $this->postJson('/contact', $this->validPayload(['message' => 'Short']))
            ->assertUnprocessable();
    }

    public function test_message_too_long_returns_422(): void
    {
        $this->postJson('/contact', $this->validPayload(['message' => str_repeat('a', 2001)]))
            ->assertUnprocessable();
    }

    // -------------------------------------------------------------------------
    // Security
    // -------------------------------------------------------------------------

    public function test_failed_turnstile_returns_422(): void
    {
        $this->fakeTurnstile(false);

        $this->postJson('/contact', $this->validPayload())->assertUnprocessable();

        Mail::assertNotSent(ContactFormMessage::class);
    }

    public function test_missing_turnstile_token_returns_422(): void
    {
        $payload = $this->validPayload();
        unset($payload['cf-turnstile-response']);

        $this->postJson('/contact', $payload)->assertUnprocessable();
    }

    // -------------------------------------------------------------------------
    // Rate limiting
    // -------------------------------------------------------------------------

    public function test_rate_limit_blocks_after_five_requests(): void
    {
        $this->withMiddleware(ThrottleRequests::class);
        $this->fakeTurnstile();

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/contact', $this->validPayload())->assertOk();
        }

        $this->postJson('/contact', $this->validPayload())->assertStatus(429);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function fakeTurnstile(bool $success = true): void
    {
        Http::fake([
            'https://challenges.cloudflare.com/*' => Http::response(['success' => $success]),
        ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'Hi, I would like more information about your services.',
            'cf-turnstile-response' => 'valid-token',
        ], $overrides);
    }
}
