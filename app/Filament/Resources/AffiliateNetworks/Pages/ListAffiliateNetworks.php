<?php

namespace App\Filament\Resources\AffiliateNetworks\Pages;

use App\Filament\Resources\AffiliateNetworks\AffiliateNetworkResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAffiliateNetworks extends ListRecords
{
    protected static string $resource = AffiliateNetworkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
