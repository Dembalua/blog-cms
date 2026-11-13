<?php

namespace Tests\Feature\Resources;

use App\Filament\Resources\Menus\MenuResource;
use Tests\FilamentTestCase;

class MenuResourceAccessTest extends FilamentTestCase
{
    public function test_guest_cannot_access_menu_resource(): void
    {
        $this->assertGuestIsRedirected(MenuResource::class);
    }

    public function test_user_without_roles_cannot_access_menu_resource(): void
    {
        $this->assertUserWithoutRolesIsForbidden(MenuResource::class);
    }

    public function test_user_role_can_access_menu_resource(): void
    {
        $this->assertUserRoleCanAccess(MenuResource::class);
    }

    public function test_super_admin_can_access_menu_resource(): void
    {
        $this->assertSuperAdminCanAccess(MenuResource::class);
    }
}
