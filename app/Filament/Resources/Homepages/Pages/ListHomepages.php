<?php

namespace App\Filament\Resources\Homepages\Pages;

use App\Filament\Resources\Homepages\HomepageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHomepages extends ListRecords
{
    protected static string $resource = HomepageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
