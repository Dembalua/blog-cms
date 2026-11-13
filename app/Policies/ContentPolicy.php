<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Content;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContentPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Content');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Content');
    }

    public function update(AuthUser $authUser, Content $content): bool
    {
        return $authUser->can('Update:Content');
    }

    public function delete(AuthUser $authUser, Content $content): bool
    {
        return $authUser->can('Delete:Content');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Content');
    }

}