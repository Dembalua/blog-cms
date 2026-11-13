<?php

namespace App\Filament\Resources\AffiliateNetworks;

use App\Filament\Resources\AffiliateNetworks\Pages\CreateAffiliateNetwork;
use App\Filament\Resources\AffiliateNetworks\Pages\EditAffiliateNetwork;
use App\Filament\Resources\AffiliateNetworks\Pages\ListAffiliateNetworks;
use App\Filament\Resources\AffiliateNetworks\Schemas\AffiliateNetworkForm;
use App\Filament\Resources\AffiliateNetworks\Tables\AffiliateNetworksTable;
use App\Models\AffiliateNetwork;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AffiliateNetworkResource extends Resource
{
    protected static ?string $model = AffiliateNetwork::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;
    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return AffiliateNetworkForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AffiliateNetworksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListAffiliateNetworks::route('/'),
            'create' => CreateAffiliateNetwork::route('/create'),
            'edit'   => EditAffiliateNetwork::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('resource.affiliate_network_resource.navigation.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('resource.affiliate_network_resource.navigation.plural_model_label');
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('resource.navigation_groups.affiliate');
    }
}
