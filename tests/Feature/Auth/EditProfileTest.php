<?php

namespace Tests\Feature\Auth;

use App\Filament\Pages\Auth\EditProfile;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\FilamentTestCase;

class EditProfileTest extends FilamentTestCase
{
    /**
     * Every profile test runs as a regular authenticated user
     * since profile editing is available to all roles.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAsUser();
    }

    /**
     * The profile page should load and pre-fill the form
     * with the currently authenticated user's data.
     */
    public function test_can_load_profile_page(): void
    {
        Livewire::test(EditProfile::class)
            ->assertOk()
            ->assertFormSet([
                'name' => auth()->user()->name,
                'lastname' => auth()->user()->lastname,
                'email' => auth()->user()->email,
            ]);
    }

    /**
     * A user should be able to update their name and lastname
     * without affecting other profile fields.
     */
    public function test_can_update_profile_data(): void
    {
        Livewire::test(EditProfile::class)
            ->fillForm([
                'name' => 'NewName',
                'lastname' => 'NewLastName',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertEquals('NewName', auth()->user()->fresh()->name);
        $this->assertEquals('NewLastName', auth()->user()->fresh()->lastname);
    }

    /**
     * Password rules should be enforced on the profile page.
     * Weak passwords that don't meet requirements should fail validation.
     */
    public function test_password_rules_are_validated_on_profile(): void
    {
        Livewire::test(EditProfile::class)
            ->fillForm([
                'password' => '123',
                'passwordConfirmation' => '123',
            ])
            ->call('save')
            ->assertHasFormErrors(['password']);
    }

    /**
     * When a user provides a new password, the confirmation field
     * must also be filled in and must match.
     */
    public function test_password_confirmation_is_required_when_changing_password(): void
    {
        Livewire::test(EditProfile::class)
            ->fillForm([
                'password' => 'NewSecurePassword123!',
                'passwordConfirmation' => '',
            ])
            ->call('save')
            ->assertHasFormErrors(['passwordConfirmation' => 'required']);
    }

    /**
     * A valid password change with matching confirmation should
     * succeed and the new password should be persisted correctly.
     */
    public function test_can_change_password_with_valid_confirmation(): void
    {
        $newPassword = 'NewSecurePassword123!';

        Livewire::test(EditProfile::class)
            ->fillForm([
                'password' => $newPassword,
                'passwordConfirmation' => $newPassword,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertTrue(
            Hash::check($newPassword, auth()->user()->fresh()->password)
        );
    }
}
