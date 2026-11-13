<?php

namespace Tests\Feature\Resources;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Livewire\Livewire;
use Tests\FilamentTestCase;

class UserResourceTest extends FilamentTestCase
{
    /**
     * Every CRUD test runs as super_admin since this class
     * only tests functionality, not access control.
     * Access control is covered in UserResourceAccessTest.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAsSuperAdmin();
    }

    /**
     * A guest who is not logged in should be redirected
     * to the login page when accessing the user resource.
     */
    public function test_guest_cannot_access_panel(): void
    {
        auth()->logout();

        $this->get(UserResource::getUrl('index'))
            ->assertRedirect('/admin/login');
    }

    /**
     * The create user page should load successfully
     * and render the form.
     */
    public function test_can_load_create_page(): void
    {
        Livewire::test(CreateUser::class)
            ->assertOk();
    }

    /**
     * Submitting valid data should create a new user
     * and persist it to the database.
     */
    public function test_can_create_user(): void
    {
        $newUserData = User::factory()->make();

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => $newUserData->name,
                'lastname' => $newUserData->lastname,
                'email' => $newUserData->email,
                'password' => 'Password123!',
                'passwordConfirmation' => 'Password123!',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('users', [
            'name' => $newUserData->name,
            'email' => $newUserData->email,
        ]);
    }

    /**
     * The name field is required and should fail
     * validation when left empty.
     */
    public function test_name_is_required(): void
    {
        Livewire::test(CreateUser::class)
            ->fillForm(['name' => null])
            ->call('create')
            ->assertHasFormErrors(['name' => 'required']);
    }

    /**
     * The lastname field is optional and should not produce
     * a validation error when left empty.
     */
    public function test_lastname_is_optional(): void
    {
        Livewire::test(CreateUser::class)
            ->fillForm(['lastname' => null])
            ->call('create')
            ->assertHasNoFormErrors(['lastname']);
    }

    /**
     * The email field must be unique across all users.
     * Submitting an already existing email should fail validation.
     */
    public function test_email_must_be_unique(): void
    {
        $existingUser = User::factory()->create();

        Livewire::test(CreateUser::class)
            ->fillForm(['email' => $existingUser->email])
            ->call('create')
            ->assertHasFormErrors(['email' => 'unique']);
    }

    /**
     * The email field must contain a valid email format.
     * Invalid formats should fail validation.
     */
    public function test_email_must_be_valid_format(): void
    {
        Livewire::test(CreateUser::class)
            ->fillForm(['email' => 'not-an-email'])
            ->call('create')
            ->assertHasFormErrors(['email' => 'email']);
    }

    /**
     * The edit page should load with the correct user data
     * pre-filled in the form fields.
     */
    public function test_can_load_edit_page(): void
    {
        $user = User::factory()->create();

        Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
            ->assertOk()
            ->assertSchemaStateSet([
                'name' => $user->name,
                'email' => $user->email,
            ]);
    }

    /**
     * Submitting valid updated data should persist the changes
     * to the database correctly.
     */
    public function test_can_update_user(): void
    {
        $user = User::factory()->create();

        Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
            ->fillForm(['name' => 'Updated Name'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
        ]);
    }

    /**
     * Updating a user without providing a new password should
     * leave the existing password unchanged in the database.
     */
    public function test_can_update_user_without_changing_password(): void
    {
        $user = User::factory()->create(['name' => 'Old Name']);

        Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
            ->fillForm([
                'name' => 'New Name',
                'password' => null,
                'passwordConfirmation' => null,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
        ]);
    }

    /**
     * Deleting a user from the edit page should remove
     * the record from the database entirely.
     */
    public function test_can_delete_user(): void
    {
        $user = User::factory()->create();

        Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
            ->callAction(DeleteAction::class);

        $this->assertModelMissing($user);
    }

    /**
     * Password confirmation must match the password field.
     * Mismatching confirmations should fail validation.
     * Passwords that don't meet complexity rules should also fail.
     */
    public function test_password_validation_rules(): void
    {
        Livewire::test(CreateUser::class)
            ->fillForm([
                'password' => 'Password123!',
                'passwordConfirmation' => 'Mismatch123!',
            ])
            ->call('create')
            ->assertHasFormErrors(['passwordConfirmation' => 'same']);

        Livewire::test(CreateUser::class)
            ->fillForm([
                'password' => '123',
                'passwordConfirmation' => '123',
            ])
            ->call('create')
            ->assertHasFormErrors(['password']);
    }

    /**
     * When changing a password on the edit page, the confirmation
     * field must be filled in and should not be left empty.
     */
    public function test_password_confirmation_is_required_on_edit(): void
    {
        $user = User::factory()->create();

        Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
            ->fillForm([
                'password' => 'NewPassword123!',
                'passwordConfirmation' => '',
            ])
            ->call('save')
            ->assertHasFormErrors(['passwordConfirmation']);
    }
}
