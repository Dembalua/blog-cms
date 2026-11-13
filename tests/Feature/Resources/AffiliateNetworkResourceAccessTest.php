<?php

namespace Tests\Feature\Resources;

use App\Filament\Resources\AffiliateNetworks\AffiliateNetworkResource;
use Tests\FilamentTestCase;

class AffiliateNetworkResourceAccessTest extends FilamentTestCase
{
    public function test_guest_cannot_access_affiliate_network_resource(): void
    {
        $this->assertGuestIsRedirected(AffiliateNetworkResource::class);
    }

    public function test_user_without_roles_cannot_access_affiliate_network_resource(): void
    {
        $this->assertUserWithoutRolesIsForbidden(AffiliateNetworkResource::class);
    }

    public function test_user_role_can_access_affiliate_network_resource(): void
    {
        $this->assertUserRoleCanAccess(AffiliateNetworkResource::class);
    }

    public function test_super_admin_can_access_affiliate_network_resource(): void
    {
        $this->assertSuperAdminCanAccess(AffiliateNetworkResource::class);
    }
}
