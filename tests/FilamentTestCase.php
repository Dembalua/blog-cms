<?php

namespace Tests;

use App\Models\Media;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Filament\Resources\Resource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;

abstract class FilamentTestCase extends TestCase
{
    use RefreshDatabase;

    /**
     * Set up the test environment.
     * Seeds roles and permissions and clears the permission cache.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $this->app->make(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Create and authenticate a user with the super_admin role.
     * Super admins have unrestricted access to all resources.
     */
    protected function actingAsSuperAdmin(): User
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');
        $this->actingAs($user);

        return $user;
    }

    /**
     * Create and authenticate a user with the user role.
     * Users have access to all resources except User and Role management.
     */
    protected function actingAsUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        $this->actingAs($user);

        return $user;
    }

    /**
     * Create and authenticate a user with no roles or permissions.
     * Used to test that unauthenticated or unpermissioned users are blocked.
     */
    protected function actingAsUserWithoutRoles(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        return $user;
    }

    /**
     * Create a fake media record in the database without requiring
     * an actual file upload. Bypasses Curator's own factory.
     */
    protected function createMedia(array $attributes = []): Media
    {
        return Media::create(array_merge([
            'disk' => 'public',
            'directory' => 'test',
            'visibility' => 'public',
            'name' => fake()->word(),
            'path' => 'test/'.fake()->word().'.jpg',
            'width' => 800,
            'height' => 600,
            'size' => 1024,
            'type' => 'image/jpeg',
            'ext' => 'jpg',
            'alt' => fake()->sentence(),
            'title' => fake()->sentence(),
        ], $attributes));
    }

    // -------------------------------------------------------------------------
    // Standard Access Control Assertions
    // These cover the common access patterns shared across all resources.
    // Resource-specific behaviour should be tested in the resource's own test.
    // -------------------------------------------------------------------------

    /**
     * Assert that a guest is redirected to the login page
     * when trying to access a resource.
     *
     * @param  class-string<resource>  $resource
     */
    protected function assertGuestIsRedirected(string $resource): void
    {
        $this->get($resource::getUrl('index'))
            ->assertRedirect('/admin/login');
    }

    /**
     * Assert that a user without any roles receives a 403
     * when trying to access a resource.
     *
     * @param  class-string<resource>  $resource
     */
    protected function assertUserWithoutRolesIsForbidden(string $resource): void
    {
        $this->actingAsUserWithoutRoles();

        $this->get($resource::getUrl('index'))
            ->assertForbidden();
    }

    /**
     * Assert that a user with the user role receives a 403
     * when trying to access a resource reserved for super_admin.
     *
     * @param  class-string<resource>  $resource
     */
    protected function assertUserRoleIsForbidden(string $resource): void
    {
        $this->actingAsUser();

        $this->get($resource::getUrl('index'))
            ->assertForbidden();
    }

    /**
     * Assert that a user with the user role can access a resource.
     *
     * @param  class-string<resource>  $resource
     */
    protected function assertUserRoleCanAccess(string $resource): void
    {
        $this->actingAsUser();

        $this->get($resource::getUrl('index'))
            ->assertOk();
    }

    /**
     * Assert that a super_admin can access a resource.
     *
     * @param  class-string<resource>  $resource
     */
    protected function assertSuperAdminCanAccess(string $resource): void
    {
        $this->actingAsSuperAdmin();

        $this->get($resource::getUrl('index'))
            ->assertOk();
    }
}
