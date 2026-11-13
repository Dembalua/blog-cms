<?php

namespace App\Filament\Resources\Media\Pages;

use App\Filament\Resources\Media\MediaResource;
use Awcodes\Curator\Resources\Media\Pages\ListMedia as BaseListMedia;

class ListMedia extends BaseListMedia
{
    protected static string $resource = MediaResource::class;

    public function getHeaderActions(): array
    {
        $baseActions = parent::getHeaderActions();
        $toggleAction = array_shift($baseActions);

        return auth()->user()->can('Create:Media')
            ? [$toggleAction, ...$baseActions]
            : [$toggleAction];
    }
}
