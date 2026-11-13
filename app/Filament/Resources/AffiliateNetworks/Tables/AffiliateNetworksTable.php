<?php

namespace App\Filament\Resources\AffiliateNetworks\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AffiliateNetworksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('resource.default_form_fields.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('affiliate_links_count')
                    ->label(__('resource.affiliate_network_resource.table.links_count'))
                    ->counts('affiliateLinks')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('resource.default_form_fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->striped()
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
