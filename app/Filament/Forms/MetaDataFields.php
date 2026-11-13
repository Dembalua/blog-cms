<?php

namespace App\Filament\Forms;

use App\Enums\MetaDataRobots;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;

class MetaDataFields
{
    public static function make(bool $withOgImage = true): Section
    {
        $ogSchema = [
            TextInput::make('og_title')
                ->label(__('meta-data.fields.og_title'))
                ->maxLength(95)
                ->helperText(fn($state) => strlen($state ?? '') . ' / 95 ' . __('resource.default_form_fields.signs'))
                ->live(onBlur: true),
            Textarea::make('og_description')
                ->label(__('meta-data.fields.og_description'))
                ->rows(3)
                ->maxLength(200)
                ->helperText(fn($state) => strlen($state ?? '') . ' / 200 ' . __('resource.default_form_fields.signs'))
                ->live(onBlur: true),
        ];

        if ($withOgImage) {
            $ogSchema[] = CuratorPicker::make('og_image_id')
                ->label(__('meta-data.fields.og_image'))
                ->buttonLabel(__('meta-data.fields.og_image_button_label'))
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp']);
        }

        return Section::make(__('meta-data.section.title'))
            ->relationship('metaData')
            ->columns(2)
            ->schema([
                Select::make('robots')
                    ->label(__('meta-data.fields.robots'))
                    ->columnSpanFull()
                    ->options(MetaDataRobots::class)
                    ->default(MetaDataRobots::INDEX_FOLLOW)
                    ->native(false),
                Section::make(__('meta-data.section.meta'))
                    ->schema([
                        TextInput::make('meta_title')
                            ->label(__('meta-data.fields.meta_title'))
                            ->maxLength(60)
                            ->helperText(fn($state) => strlen($state ?? '') . ' / 60 ' . __('resource.default_form_fields.signs'))
                            ->live(onBlur: true),
                        Textarea::make('meta_description')
                            ->label(__('meta-data.fields.meta_description'))
                            ->rows(3)
                            ->maxLength(160)
                            ->helperText(fn($state) => strlen($state ?? '') . ' / 160 ' . __('resource.default_form_fields.signs'))
                            ->live(onBlur: true),
                        TagsInput::make('meta_keywords')
                            ->label(__('meta-data.fields.meta_keywords'))
                            ->reorderable()
                            ->trim()
                            ->splitKeys([','])
                            ->nestedRecursiveRules([
                                'min:3',
                                'max:50',
                            ]),
                    ]),
                Section::make(__('meta-data.section.og'))
                    ->schema($ogSchema),
            ]);
    }
}
