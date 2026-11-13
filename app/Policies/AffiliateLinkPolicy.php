<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AffiliateLink;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class AffiliateLinkPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AffiliateLink');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AffiliateLink');
    }

    public function update(AuthUser $authUser, AffiliateLink $affiliateLink): bool
    {
        return $authUser->can('Update:AffiliateLink');
    }

    public function delete(AuthUser $authUser, AffiliateLink $affiliateLink): bool
    {
        return $authUser->can('Delete:AffiliateLink');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AffiliateLink');
    }
}
