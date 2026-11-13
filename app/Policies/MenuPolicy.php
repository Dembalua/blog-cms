<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Menu;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class MenuPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Menu');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Menu');
    }

    public function update(AuthUser $authUser, Menu $menu): bool
    {
        return $authUser->can('Update:Menu');
    }

    public function delete(AuthUser $authUser, Menu $menu): bool
    {
        return $authUser->can('Delete:Menu');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Menu');
    }
}
