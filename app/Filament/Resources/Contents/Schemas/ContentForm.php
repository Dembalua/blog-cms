<?php

namespace App\Filament\Resources\Contents\Schemas;

use App\Enums\ContentStatus;
use App\Enums\ContentType;
use App\Filament\Forms\MetaDataFields;
use App\Filament\Forms\RichContentField;
use App\Models\Cluster;
use App\Models\Content;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use App\Models\Path;
use Illuminate\Validation\Rule;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;

class ContentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make(__('resource.content_resource.tabs.general'))
                            ->schema([
                                Section::make(__('resource.content_resource.form.content_section_title'))
                                    ->description(__('resource.content_resource.form.content_section_description'))
                                    ->columnSpanFull()
                                    ->schema([
                                        Section::make()
                                            ->columns(3)
                                            ->columnSpanFull()
                                            ->schema([
                                                Select::make('type')
                                                    ->label(__('resource.content_resource.form.type'))
                                                    ->columnSpan(2)
                                                    ->default(ContentType::POST)
                                                    ->native(false)
                                                    ->options(collect(ContentType::cases())
                                                        ->reject(fn ($case) => $case === ContentType::HOMEPAGE)
                                                        ->mapWithKeys(fn ($case) => [$case->value => $case->getLabel()])
                                                        ->toArray()),
                                                Select::make('status')
                                                    ->label(__('resource.content_resource.form.status'))
                                                    ->columnSpan(1)
                                                    ->native(false)
                                                    ->options(ContentStatus::class)
                                                    ->default(ContentStatus::DRAFT),
                                                TextInput::make('title')
                                                    ->label(__('resource.content_resource.form.title'))
                                                    ->columnSpan(2)
                                                    ->required()
                                                    ->maxLength(80)
                                                    ->helperText(fn($state) => strlen($state ?? '') . ' / 80 ' . __('resource.default_form_fields.signs'))
                                                    ->live(onBlur: true)
                                                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                                        if (blank($get('metaData.meta_title'))) {
                                                            $set('metaData.meta_title', $state);
                                                        }
                                                        if (blank($get('metaData.og_title'))) {
                                                            $set('metaData.og_title', $state);
                                                        }
                                                    }),
                                                Group::make()
                                                    ->relationship('path')
                                                    ->schema([
                                                        Hidden::make('edit_slug')
                                                            ->dehydrated(false),
                                                        TextInput::make('slug')
                                                            ->label(__('resource.content_resource.form.path'))
                                                            ->disabled(fn(Get $get, string $operation) => $operation === 'edit' && !$get('edit_slug'))
                                                            ->required()
                                                            ->dehydrated()
                                                            ->maxLength(100)
                                                            ->rules([Rule::notIn(Path::RESERVED_SLUGS)])
                                                            ->validationMessages(['not_in' => __('resource.default_form_fields.slug_reserved')])
                                                            ->live(onBlur: true)
                                                            ->helperText(fn($state) => strlen($state ?? '') . ' / 100 ' . __('resource.default_form_fields.signs'))
                                                            ->prefixAction(
                                                                Action::make('edit_slug')
                                                                    ->icon('heroicon-m-pencil')
                                                                    ->color('danger')
                                                                    ->tooltip(__('resource.content_resource.form.path_tooltip'))
                                                                    ->visible(fn(string $operation) => $operation === 'edit')
                                                                    ->action(fn(Set $set, Get $get) => $set('edit_slug', !$get('edit_slug')))
                                                            ),
                                                    ]),
                                            ]),
                                        Section::make()
                                            ->columns(6)
                                            ->columnSpanFull()
                                            ->schema([
                                                DateTimePicker::make('published_at')
                                                    ->label(__('resource.content_resource.form.published_at'))
                                                    ->default(now()->setTimezone(config('app.timezone')))
                                                    ->native(false)
                                                    ->columnSpan(3),
                                                DatePicker::make('revised_at')
                                                    ->label(__('resource.content_resource.form.revised_at'))
                                                    ->native(false)
                                                    ->maxDate(now())
                                                    ->columnSpan(3),
                                                Select::make('author_id')
                                                    ->label(__('resource.content_resource.form.author'))
                                                    ->native(false)
                                                    ->relationship('author', 'name')
                                                    ->default(fn() => auth()->id())
                                                    ->disabled(fn(string $operation) => $operation === 'create')
                                                    ->dehydrated(false)
                                                    ->columnSpan(3),
                                                Select::make('category_id')
                                                    ->label(__('resource.content_resource.form.category'))
                                                    ->native(false)
                                                    ->preload()
                                                    ->live()
                                                    ->afterStateUpdated(fn(Set $set) => $set('cluster_id', null))
                                                    ->getOptionLabelFromRecordUsing(fn($record) => $record->active
                                                        ? $record->name
                                                        : '<span class="text-danger-400">' . $record->name . ' (' . __('resource.default_form_fields.inactive') . ')</span>'
                                                    )
                                                    ->allowHtml()
                                                    ->relationship('category', 'name')
                                                    ->disabled(fn($record) => $record && \App\Models\MenuItem::where('model_type', \App\Models\Content::class)->where('model_id', $record->id)->exists())
                                                    ->helperText(fn($record) => $record && \App\Models\MenuItem::where('model_type', \App\Models\Content::class)->where('model_id', $record->id)->exists()
                                                        ? __('resource.content_resource.form.category_disabled_menu')
                                                        : null)
                                                    ->columnSpan(3),
                                                Toggle::make('is_featured')
                                                    ->label(__('resource.content_resource.form.is_featured'))
                                                    ->default(false)
                                                    ->inline(false)
                                                    ->columnSpan(fn(Get $get) => $get('cluster_id') ? 1 : 3)
                                                    ->onIcon(Heroicon::Star)
                                                    ->offIcon(Heroicon::Star),
                                                Toggle::make('is_pillar')
                                                    ->label(__('resource.content_resource.form.is_pillar'))
                                                    ->inline(false)
                                                    ->live()
                                                    ->columnSpan(1)
                                                    ->visible(fn(Get $get) => (bool) $get('cluster_id'))
                                                    ->afterStateHydrated(fn($state, $component) => $component->state($state === true))
                                                    ->dehydrateStateUsing(fn($state) => $state ?: null)
                                                    ->onIcon(Heroicon::Fire)
                                                    ->offIcon(Heroicon::Fire)
                                                    ->rules([
                                                        fn(Get $get, ?Model $record): Closure => static function (string $attribute, mixed $value, Closure $fail) use ($get, $record): void {
                                                            if (! $value || ! $get('cluster_id')) {
                                                                return;
                                                            }
                                                            $exists = Content::where('cluster_id', $get('cluster_id'))
                                                                ->whereNotNull('is_pillar')
                                                                ->when($record?->id, fn($q, $id) => $q->where('id', '!=', $id))
                                                                ->exists();
                                                            if ($exists) {
                                                                $fail(__('resource.content_resource.validation.pillar_exists'));
                                                            }
                                                        },
                                                    ]),
                                                Select::make('cluster_position')
                                                    ->label(__('resource.content_resource.form.cluster_position'))
                                                    ->native(false)
                                                    ->placeholder(__('resource.content_resource.form.cluster_position_placeholder'))
                                                    ->options(array_combine(range(0, 15), range(0, 15)))
                                                    ->columnSpan(1)
                                                    ->visible(fn(Get $get) => (bool) $get('cluster_id')),
                                                Select::make('cluster_id')
                                                    ->label(__('resource.content_resource.form.cluster'))
                                                    ->native(false)
                                                    ->preload()
                                                    ->searchable()
                                                    ->live()
                                                    ->disabled(fn(Get $get) => ! $get('category_id'))
                                                    ->relationship(
                                                        name: 'cluster',
                                                        titleAttribute: 'title',
                                                        modifyQueryUsing: fn(Builder $query, Get $get) => $query->where('category_id', $get('category_id')),
                                                    )
                                                    ->columnSpan(3),
                                            ]),
                                        Section::make(__('resource.content_resource.form.preview_title'))
                                            ->description(__('resource.content_resource.form.preview_description'))
                                            ->columns(2)
                                            ->columnSpanFull()
                                            ->schema([
                                                Textarea::make('excerpt')
                                                    ->label(__('resource.content_resource.form.excerpt'))
                                                    ->rows(10)
                                                    ->maxLength(160)
                                                    ->live(onBlur: true)
                                                    ->helperText(fn($state) => strlen($state ?? '') . ' / 160 ' . __('resource.default_form_fields.signs')),
                                                CuratorPicker::make('feature_image_id')
                                                    ->label(__('resource.content_resource.form.feature_image'))
                                                    ->hintIcon('heroicon-m-information-circle')
                                                    ->hintIconTooltip(__('resource.content_resource.form.feature_image_hint'))
                                                    ->buttonLabel(__('resource.content_resource.form.feature_image_button_label'))
                                                    ->relationship('featureImage', 'id')
                                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp']),
                                            ]),
                                        Section::make()
                                            ->columnSpanFull()
                                            ->schema([
                                                RichContentField::make('content'),
                                            ]),
                                    ]),
                            ]),
                        Tab::make(__('resource.content_resource.tabs.seo'))
                            ->schema([
                                MetaDataFields::make(withOgImage: false),
                            ]),
                        Tab::make(__('resource.content_resource.tabs.links'))
                            ->schema([
                                Placeholder::make('inbound_links')
                                    ->label('')
                                    ->content(fn(?Model $record) => $record
                                        ? new HtmlString(view('filament.resources.contents.inbound-links', [
                                            'articles' => Content::where('content', 'like', '%"postId":' . $record->id . '%')
                                                ->with('category')
                                                ->select(['id', 'title', 'status', 'is_pillar', 'category_id'])
                                                ->get(),
                                        ])->render())
                                        : ''
                                    ),
                            ]),
                    ]),
            ]);
    }
}
