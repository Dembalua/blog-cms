<?php

namespace Tests\Feature\Resources;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Livewire\Livewire;
use Tests\FilamentTestCase;

class UserResourceAccessTest extends FilamentTestCase
{
    /**
     * A guest should be redirected to the login page.
     */
    public function test_guest_cannot_access_user_resource(): void
    {
        $this->assertGuestIsRedirected(UserResource::class);
    }

    /**
     * A user without any roles should receive a 403.
     */
    public function test_user_without_roles_cannot_access_user_resource(): void
    {
        $this->assertUserWithoutRolesIsForbidden(UserResource::class);
    }

    /**
     * The user role should not be able to access the user resource
     * as it is reserved for super_admin only.
     */
    public function test_user_role_cannot_access_user_resource(): void
    {
        $this->assertUserRoleIsForbidden(UserResource::class);
    }

    /**
     * A super_admin should have full access to the user resource.
     */
    public function test_super_admin_can_access_user_resource(): void
    {
        $this->assertSuperAdminCanAccess(UserResource::class);
    }

    // -------------------------------------------------------------------------
    // UserResource specific access tests
    // -------------------------------------------------------------------------

    /**
     * The user role should not be able to see the user list via Livewire.
     */
    public function test_user_role_cannot_see_user_list(): void
    {
        $this->actingAsUser();

        Livewire::test(ListUsers::class)
            ->assertForbidden();
    }

    /**
     * The user role should not be able to access the create user page.
     */
    public function test_user_role_cannot_access_create_page(): void
    {
        $this->actingAsUser();

        Livewire::test(CreateUser::class)
            ->assertForbidden();
    }

    /**
     * The user role should not be able to access the edit page of any user.
     */
    public function test_user_role_cannot_access_edit_page(): void
    {
        $this->actingAsUser();

        $user = User::factory()->create();

        Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
            ->assertForbidden();
    }

    /**
     * A super_admin should be able to see all users in the list table.
     */
    public function test_super_admin_can_see_user_list(): void
    {
        $this->actingAsSuperAdmin();

        $users = User::factory()->count(5)->create();

        Livewire::test(ListUsers::class)
            ->assertOk()
            ->assertCanSeeTableRecords($users);
    }

    /**
     * A super_admin should be able to access the create user page.
     */
    public function test_super_admin_can_access_create_page(): void
    {
        $this->actingAsSuperAdmin();

        Livewire::test(CreateUser::class)
            ->assertOk();
    }

    /**
     * A super_admin should be able to access the edit page of any user.
     */
    public function test_super_admin_can_access_edit_page(): void
    {
        $this->actingAsSuperAdmin();

        $user = User::factory()->create();

        Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
            ->assertOk();
    }
}
