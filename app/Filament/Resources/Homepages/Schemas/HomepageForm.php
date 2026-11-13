<?php

namespace App\Filament\Resources\Homepages\Schemas;

use App\Filament\Forms\RichContentField;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class HomepageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('resource.homepage_resource.form.general_section_title'))
                    ->description(__('resource.homepage_resource.form.general_section_description'))
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('title')
                            ->label(__('resource.homepage_resource.form.title'))
                            ->helperText(__('resource.homepage_resource.form.title_hint'))
                            ->required()
                            ->maxLength(80)
                            ->columnSpan(2),
                        Select::make('author_id')
                            ->label(__('resource.homepage_resource.form.author'))
                            ->native(false)
                            ->relationship('author', 'name')
                            ->default(fn () => auth()->id())
                            ->required()
                            ->columnSpan(2),
                        DateTimePicker::make('published_at')
                            ->label(__('resource.homepage_resource.form.published_at'))
                            ->default(now()->setTimezone(config('app.timezone')))
                            ->native(false)
                            ->required(),
                        DatePicker::make('revised_at')
                            ->label(__('resource.homepage_resource.form.revised_at'))
                            ->native(false)
                            ->maxDate(now()),
                    ]),

                Section::make(__('resource.homepage_resource.form.hero_section_title'))
                    ->description(__('resource.homepage_resource.form.hero_section_description'))
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('hero_kicker')
                            ->label(__('resource.homepage_resource.form.hero_kicker'))
                            ->required()
                            ->maxLength(60)
                            ->columnSpan(2),
                        TextInput::make('hero_headline')
                            ->label(__('resource.homepage_resource.form.hero_headline'))
                            ->required()
                            ->maxLength(25)
                            ->live(onBlur: true)
                            ->helperText(fn ($state) => strlen($state ?? '').' / 25 '.__('resource.default_form_fields.signs')),
                        TextInput::make('hero_headline_accent')
                            ->label(__('resource.homepage_resource.form.hero_headline_accent'))
                            ->required()
                            ->maxLength(25)
                            ->live(onBlur: true)
                            ->helperText(fn ($state) => strlen($state ?? '').' / 25 '.__('resource.default_form_fields.signs')),
                        Textarea::make('hero_text')
                            ->label(__('resource.homepage_resource.form.hero_text'))
                            ->hint(__('resource.homepage_resource.form.hero_text_hint'))
                            ->required()
                            ->rows(6)
                            ->maxLength(350)
                            ->live(onBlur: true)
                            ->helperText(fn ($state) => strlen($state ?? '').' / 350 '.__('resource.default_form_fields.signs'))
                            ->columnSpan(2),
                        CuratorPicker::make('hero_image_id')
                            ->label(__('resource.homepage_resource.form.hero_image'))
                            ->buttonLabel(__('resource.homepage_resource.form.hero_image_button_label'))
                            ->relationship('heroImage', 'id')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->columnSpanFull(),
                    ]),

                Section::make(__('resource.homepage_resource.form.content_section_title'))
                    ->description(__('resource.homepage_resource.form.content_section_description'))
                    ->columnSpanFull()
                    ->schema([
                        RichContentField::make('content'),
                    ]),
            ]);
    }
}
