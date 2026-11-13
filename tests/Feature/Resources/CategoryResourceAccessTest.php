<?php

namespace Tests\Feature\Resources;

use App\Filament\Resources\Categories\CategoryResource;
use Tests\FilamentTestCase;

class CategoryResourceAccessTest extends FilamentTestCase
{
    public function test_guest_cannot_access_category_resource(): void
    {
        $this->assertGuestIsRedirected(CategoryResource::class);
    }

    public function test_user_without_roles_cannot_access_category_resource(): void
    {
        $this->assertUserWithoutRolesIsForbidden(CategoryResource::class);
    }

    public function test_user_role_can_access_category_resource(): void
    {
        $this->assertUserRoleCanAccess(CategoryResource::class);
    }

    public function test_super_admin_can_access_category_resource(): void
    {
        $this->assertSuperAdminCanAccess(CategoryResource::class);
    }
}
