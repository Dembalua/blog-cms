<?php

namespace Tests\Feature\Resources;

use App\Filament\Resources\AffiliateLinks\AffiliateLinkResource;
use Tests\FilamentTestCase;

class AffiliateLinkResourceAccessTest extends FilamentTestCase
{
    public function test_guest_cannot_access_affiliate_link_resource(): void
    {
        $this->assertGuestIsRedirected(AffiliateLinkResource::class);
    }

    public function test_user_without_roles_cannot_access_affiliate_link_resource(): void
    {
        $this->assertUserWithoutRolesIsForbidden(AffiliateLinkResource::class);
    }

    public function test_user_role_can_access_affiliate_link_resource(): void
    {
        $this->assertUserRoleCanAccess(AffiliateLinkResource::class);
    }

    public function test_super_admin_can_access_affiliate_link_resource(): void
    {
        $this->assertSuperAdminCanAccess(AffiliateLinkResource::class);
    }
}
