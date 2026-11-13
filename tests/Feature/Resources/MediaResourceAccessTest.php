<?php

namespace Tests\Feature\Resources;

use App\Filament\Resources\Media\MediaResource;
use App\Filament\Resources\Media\Pages\ListMedia;
use Livewire\Livewire;
use Tests\FilamentTestCase;

class MediaResourceAccessTest extends FilamentTestCase
{
    /**
     * A guest should be redirected to the login page.
     */
    public function test_guest_cannot_access_media_resource(): void
    {
        $this->assertGuestIsRedirected(MediaResource::class);
    }

    /**
     * A user without any roles should receive a 403.
     */
    public function test_user_without_roles_cannot_access_media_resource(): void
    {
        $this->assertUserWithoutRolesIsForbidden(MediaResource::class);
    }

    /**
     * The user role should be able to access the media resource
     * as they have all media permissions.
     */
    public function test_user_role_can_access_media_resource(): void
    {
        $this->assertUserRoleCanAccess(MediaResource::class);
    }

    /**
     * A super_admin should have full access to the media resource.
     */
    public function test_super_admin_can_access_media_resource(): void
    {
        $this->assertSuperAdminCanAccess(MediaResource::class);
    }

    // -------------------------------------------------------------------------
    // MediaResource specific access tests
    // -------------------------------------------------------------------------

    /**
     * A user with Create:Media permission should see the upload button.
     */
    public function test_user_role_can_see_upload_button(): void
    {
        $this->actingAsUser();

        Livewire::test(ListMedia::class)
            ->assertOk()
            ->assertSeeHtml('upload');
    }

    /**
     * A user without Create:Media permission should not see the upload button.
     */
    public function test_user_without_create_permission_cannot_see_upload_button(): void
    {
        $user = $this->actingAsUserWithoutRoles();
        $user->givePermissionTo('ViewAny:Media');

        Livewire::test(ListMedia::class)
            ->assertOk()
            ->assertDontSeeHtml('upload');
    }

    /**
     * A user with the user role should be able to see all media records.
     */
    public function test_user_role_can_see_media_list(): void
    {
        $this->actingAsUser();

        $mediaItems = collect([
            $this->createMedia(),
            $this->createMedia(),
            $this->createMedia(),
        ]);

        Livewire::test(ListMedia::class)
            ->assertOk()
            ->assertCanSeeTableRecords($mediaItems);
    }

    /**
     * A user with Download:Media permission should see the download action.
     */
    public function test_user_role_can_download_media(): void
    {
        $this->actingAsUser();

        $media = $this->createMedia();

        Livewire::test(ListMedia::class)
            ->assertTableActionExists('download')
            ->assertTableActionVisible('download', $media);
    }

    /**
     * A user without Download:Media permission should not see the download action.
     */
    public function test_user_without_download_permission_cannot_download_media(): void
    {
        $user = $this->actingAsUserWithoutRoles();
        $user->givePermissionTo('ViewAny:Media');

        $media = $this->createMedia();

        Livewire::test(ListMedia::class)
            ->assertTableActionHidden('download', $media);
    }

    /**
     * A super_admin should see the upload button.
     */
    public function test_super_admin_can_see_upload_button(): void
    {
        $this->actingAsSuperAdmin();

        Livewire::test(ListMedia::class)
            ->assertOk()
            ->assertSeeHtml('upload');
    }
}
