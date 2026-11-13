<?php

namespace Tests\Feature\Resources;

use App\Filament\Resources\Clusters\ClusterResource;
use Tests\FilamentTestCase;

class ClusterResourceAccessTest extends FilamentTestCase
{
    public function test_guest_cannot_access_cluster_resource(): void
    {
        $this->assertGuestIsRedirected(ClusterResource::class);
    }

    public function test_user_without_roles_cannot_access_cluster_resource(): void
    {
        $this->assertUserWithoutRolesIsForbidden(ClusterResource::class);
    }

    public function test_user_role_can_access_cluster_resource(): void
    {
        $this->assertUserRoleCanAccess(ClusterResource::class);
    }

    public function test_super_admin_can_access_cluster_resource(): void
    {
        $this->assertSuperAdminCanAccess(ClusterResource::class);
    }
}
