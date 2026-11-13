<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Cluster;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class ClusterPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Cluster');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Cluster');
    }

    public function update(AuthUser $authUser, Cluster $cluster): bool
    {
        return $authUser->can('Update:Cluster');
    }

    public function delete(AuthUser $authUser, Cluster $cluster): bool
    {
        return $authUser->can('Delete:Cluster');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Cluster');
    }
}
