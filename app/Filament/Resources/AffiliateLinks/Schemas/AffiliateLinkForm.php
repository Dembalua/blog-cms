<?php

namespace App\Filament\Resources\AffiliateLinks\Schemas;

use App\Models\AffiliateNetwork;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class AffiliateLinkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('resource.affiliate_link_resource.form.section_title'))
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Select::make('affiliate_network_id')
                            ->label(__('resource.affiliate_link_resource.form.network'))
                            ->options(AffiliateNetwork::orderBy('name')->pluck('name', 'id'))
                            ->required()
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get, ?string $state, string $operation) {
                                if ($operation !== 'create') {
                                    return;
                                }
                                $name        = $get('name') ?? '';
                                $networkName = $state ? AffiliateNetwork::find($state)?->name ?? '' : '';
                                $set('slug', Str::slug($networkName . '-' . $name, '-', 'de'));
                            }),

                        TextInput::make('url')
                            ->label(__('resource.affiliate_link_resource.form.url'))
                            ->required()
                            ->url()
                            ->maxLength(2048),

                        TextInput::make('name')
                            ->label(__('resource.affiliate_link_resource.form.name'))
                            ->required()
                            ->maxLength(150)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, Get $get, ?string $state, string $operation) {
                                if ($operation !== 'create') {
                                    return;
                                }
                                $networkId   = $get('affiliate_network_id');
                                $networkName = $networkId ? AffiliateNetwork::find($networkId)?->name ?? '' : '';
                                $set('slug', Str::slug($networkName . '-' . $state, '-', 'de'));
                            }),

                        TextInput::make('slug')
                            ->label(__('resource.affiliate_link_resource.form.slug'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(100)
                            ->prefix('go/')
                            ->disabled(fn(Get $get) => ! $get('edit_slug'))
                            ->dehydrated()
                            ->hintIcon('heroicon-m-question-mark-circle')
                            ->hintIconTooltip(__('resource.affiliate_link_resource.form.slug_tooltip'))
                            ->prefixAction(
                                Action::make('edit_slug')
                                    ->icon('heroicon-m-pencil')
                                    ->color('danger')
                                    ->tooltip(__('resource.affiliate_link_resource.form.slug_tooltip'))
                                    ->action(fn(Set $set, Get $get) => $set('edit_slug', ! $get('edit_slug')))
                            ),

                        TextInput::make('edit_slug')
                            ->hidden()
                            ->dehydrated(false),

                        Toggle::make('is_active')
                            ->label(__('resource.affiliate_link_resource.form.is_active'))
                            ->default(true)
                            ->inline(false),
                    ]),
            ]);
    }
}
