<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Media;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class MediaPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Media');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Media');
    }

    public function update(AuthUser $authUser, Media $media): bool
    {
        return $authUser->can('Update:Media');
    }

    public function delete(AuthUser $authUser, Media $media): bool
    {
        return $authUser->can('Delete:Media');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Media');
    }

    public function download(AuthUser $authUser, Media $media): bool
    {
        return $authUser->can('Download:Media');
    }
}
