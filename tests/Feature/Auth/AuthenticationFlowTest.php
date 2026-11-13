<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Tests\FilamentTestCase;

class AuthenticationFlowTest extends FilamentTestCase
{
    /**
     * Set up the test environment.
     * Enables email verification requirement for all auth flow tests.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('filament.auth.pages.email_verification.required', true);
    }

    /**
     * Unverified users should be redirected to the email verification prompt
     * instead of being allowed into the panel.
     */
    public function test_unverified_user_is_redirected_to_email_verification_page(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get('/admin')
            ->assertRedirectContains('/email-verification/prompt');
    }

    /**
     * Verified users who have not set up 2FA should be redirected
     * to the 2FA setup page before accessing the panel.
     */
//    public function test_verified_user_without_2fa_is_redirected_to_2fa_setup_page(): void
//    {
//        $user = User::factory()->without2fa()->create();
//
//        $this->actingAs($user)
//            ->get('/admin')
//            ->assertRedirectContains('/multi-factor-authentication/set-up');
//    }

    /**
     * A fully authenticated user (verified email + 2FA configured)
     * should be able to access the panel without any redirects.
     */
    public function test_fully_authenticated_user_can_access_panel(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk();
    }
}
