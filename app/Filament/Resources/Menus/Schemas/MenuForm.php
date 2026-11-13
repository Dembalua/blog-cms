<?php

namespace App\Filament\Resources\Menus\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MenuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('resource.menu_resource.form.section_title'))
                    ->description(__('resource.menu_resource.form.section_description'))
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->label(__('resource.default_form_fields.name'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(80)
                            ->helperText(fn ($state) => strlen($state ?? '').' / 80 '.__('resource.default_form_fields.signs'))
                            ->live(onBlur: true),
                    ]),
            ]);
    }
}
