<?php

namespace App\Filament\Resources\AffiliateNetworks\Widgets;

use App\Models\AffiliateLink;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AffiliateNetworkLinksWidget extends TableWidget
{
    public ?Model $record = null;

    protected int | string | array $columnSpan = 'full';

    protected function getTableHeading(): string
    {
        return __('resource.affiliate_network_resource.links_widget.heading');
    }

    protected function getTableQuery(): Builder
    {
        return AffiliateLink::query()
            ->where('affiliate_network_id', $this->record?->id ?? 0)
            ->select('id', 'name', 'slug', 'is_active', 'clicks');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->heading($this->getTableHeading())
            ->columns([
                TextColumn::make('name')
                    ->label(__('resource.affiliate_link_resource.form.name'))
                    ->sortable(),
                TextColumn::make('slug')
                    ->label(__('resource.affiliate_link_resource.form.slug'))
                    ->formatStateUsing(fn($state) => 'go/' . $state),
                IconColumn::make('is_active')
                    ->label(__('resource.affiliate_link_resource.form.is_active'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('clicks')
                    ->label(__('resource.affiliate_link_resource.table.clicks'))
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
