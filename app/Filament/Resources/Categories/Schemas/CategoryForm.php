<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Filament\Forms\MetaDataFields;
use App\Models\Path;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make(__('resource.category_resource.tabs.general'))
                            ->schema([
                                Section::make(__('resource.category_resource.form.category_section_title'))
                                    ->description(__('resource.category_resource.form.category_section_description'))
                                    ->schema([
                                        Section::make()
                                            ->schema([
                                                Toggle::make('active')
                                                    ->label(__('resource.category_resource.form.active'))
                                                    ->required()
                                                    ->helperText(__('resource.category_resource.form.active_helper_text')),
                                            ])
                                            ->columns(2)
                                            ->columnSpanFull(),
                                        Section::make()
                                            ->schema([
                                                TextInput::make('name')
                                                    ->label(__('resource.default_form_fields.name'))
                                                    ->required()
                                                    ->unique(ignoreRecord: true)
                                                    ->maxLength(80)
                                                    ->helperText(fn ($state) => strlen($state ?? '').' / 80 '.__('resource.default_form_fields.signs'))
                                                    ->live(onBlur: true)
                                                    ->afterStateUpdated(
                                                        function (Set $set, ?string $state, string $operation) {
                                                            if ($operation === 'create') {
                                                                $set('path.slug', Str::slug($state, '-', 'de'));
                                                            }
                                                        }
                                                    ),
                                                Group::make()
                                                    ->relationship('path')
                                                    ->schema([
                                                        Hidden::make('edit_slug')
                                                            ->dehydrated(false),
                                                        TextInput::make('slug')
                                                            ->label(__('resource.caregory_resource.form.path'))
                                                            ->disabled(fn (Get $get) => ! $get('edit_slug'))
                                                            ->required()
                                                            ->dehydrated()
                                                            ->maxLength(100)
                                                            ->rules([Rule::notIn(Path::RESERVED_SLUGS)])
                                                            ->validationMessages(['not_in' => __('resource.default_form_fields.slug_reserved')])
                                                            ->live(onBlur: true)
                                                            ->helperText(fn ($state) => strlen($state ?? '').' / 100 '.__('resource.default_form_fields.signs'))
                                                            ->hintIcon('heroicon-m-question-mark-circle')
                                                            ->hintIconTooltip(__('resource.caregory_resource.form.path_hint'))
                                                            ->prefixAction(
                                                                Action::make('edit_slug')
                                                                    ->icon('heroicon-m-pencil')
                                                                    ->color('danger')
                                                                    ->tooltip(__('resource.caregory_resource.form.path_tooltip'))
                                                                    ->action(fn (Set $set, Get $get) => $set('edit_slug', ! $get('edit_slug')))
                                                            ),
                                                    ]),
                                                Textarea::make('description')
                                                    ->label(__('resource.category_resource.form.description'))
                                                    ->columnSpanFull()
                                                    ->rows(6),
                                            ])
                                            ->columns(2)
                                            ->columnSpanFull(),
                                        Section::make()
                                            ->schema([
                                                CuratorPicker::make('image_id')
                                                    ->columnSpanFull()
                                                    ->label(__('resource.category_resource.form.category_image_label'))
                                                    ->buttonLabel(__('resource.category_resource.form.category_image_button_label'))
                                                    ->relationship('image', 'id')
                                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp']),
                                            ])
                                            ->columns(2)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2)
                                    ->columnSpan(2),
                            ]),
                        Tab::make(__('resource.category_resource.tabs.seo'))
                            ->schema([
                                MetaDataFields::make(withOgImage: false),
                            ]),
                    ]),
            ]);
    }
}
