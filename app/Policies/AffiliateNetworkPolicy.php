<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\AffiliateNetwork;
use Illuminate\Auth\Access\HandlesAuthorization;

class AffiliateNetworkPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AffiliateNetwork');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AffiliateNetwork');
    }

    public function update(AuthUser $authUser, AffiliateNetwork $affiliateNetwork): bool
    {
        return $authUser->can('Update:AffiliateNetwork');
    }

    public function delete(AuthUser $authUser, AffiliateNetwork $affiliateNetwork): bool
    {
        return $authUser->can('Delete:AffiliateNetwork');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AffiliateNetwork');
    }

}