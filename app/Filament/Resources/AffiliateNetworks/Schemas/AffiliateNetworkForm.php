<?php

namespace App\Filament\Resources\AffiliateNetworks\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AffiliateNetworkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('resource.affiliate_network_resource.form.section_title'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('resource.default_form_fields.name'))
                            ->required()
                            ->maxLength(100)
                            ->unique(ignoreRecord: true),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
