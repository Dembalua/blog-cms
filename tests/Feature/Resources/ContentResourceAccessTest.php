<?php

namespace Tests\Feature\Resources;

use App\Filament\Resources\Contents\ContentResource;
use Tests\FilamentTestCase;

class ContentResourceAccessTest extends FilamentTestCase
{
    public function test_guest_cannot_access_content_resource(): void
    {
        $this->assertGuestIsRedirected(ContentResource::class);
    }

    public function test_user_without_roles_cannot_access_content_resource(): void
    {
        $this->assertUserWithoutRolesIsForbidden(ContentResource::class);
    }

    public function test_user_role_can_access_content_resource(): void
    {
        $this->assertUserRoleCanAccess(ContentResource::class);
    }

    public function test_super_admin_can_access_content_resource(): void
    {
        $this->assertSuperAdminCanAccess(ContentResource::class);
    }
}
