<?php

namespace App\Filament\Resources\AffiliateLinks\Tables;

use App\Models\Content;
use Filament\Actions\EditAction;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class AffiliateLinksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('resource.affiliate_link_resource.form.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('network.name')
                    ->label(__('resource.affiliate_link_resource.form.network'))
                    ->badge()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('slug')
                    ->label(__('resource.affiliate_link_resource.form.slug'))
                    ->formatStateUsing(fn($state) => 'go/' . $state)
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->label(__('resource.affiliate_link_resource.form.is_active'))
                    ->boolean()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('clicks')
                    ->label(__('resource.affiliate_link_resource.table.clicks'))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('articles_count')
                    ->label(__('resource.affiliate_link_resource.table.articles_count'))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('last_status_code')
                    ->label(__('resource.affiliate_link_resource.table.last_status_code'))
                    ->badge()
                    ->color(fn($state) => match (true) {
                        $state === null        => 'gray',
                        $state >= 200 && $state < 300 => 'success',
                        default                => 'danger',
                    })
                    ->formatStateUsing(fn($state) => $state ?? '—')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('last_checked_at')
                    ->label(__('resource.affiliate_link_resource.table.last_checked_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('resource.default_form_fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->striped()
            ->modifyQueryUsing(fn($query) => $query->addSelect([
                'articles_count' => Content::selectRaw('COUNT(*)')
                    ->whereRaw("content LIKE CONCAT('%\"affiliateLinkId\":', affiliate_links.id, '%')"),
            ]))
            ->filters([
                SelectFilter::make('affiliate_network_id')
                    ->label(__('resource.affiliate_link_resource.form.network'))
                    ->relationship('network', 'name')
                    ->native(false),
                TernaryFilter::make('is_active')
                    ->label(__('resource.affiliate_link_resource.form.is_active')),
                Filter::make('unused')
                    ->label(__('resource.affiliate_link_resource.filters.unused'))
                    ->query(fn($query) => $query->whereNotExists(function ($sub) {
                        $sub->select(DB::raw(1))
                            ->from('contents')
                            ->whereRaw("content LIKE CONCAT('%\"affiliateLinkId\":', affiliate_links.id, '%')");
                    }))
                    ->toggle(),
            ], layout: FiltersLayout::AboveContentCollapsible)
            ->filtersFormColumns(2)
            ->filtersFormSchema(fn(array $filters): array => [
                Section::make()
                    ->schema([
                        $filters['affiliate_network_id'],
                        $filters['is_active'],
                        $filters['unused']->columnStart(1),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
