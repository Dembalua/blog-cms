<?php

namespace App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks;

use App\Enums\ContentStatus;
use App\Enums\ContentType;
use App\Models\Category;
use App\Models\Cluster;
use App\Models\Content;
use App\Models\Media;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Database\Eloquent\Model;

class ImageTextBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'image-text';
    }

    public static function getLabel(): string
    {
        return __('rich-editor-blocks.image_text.label');
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action
            ->schema([
                CuratorPicker::make('image_id')
                    ->label(__('rich-editor-blocks.image_text.fields.image'))
                    ->required()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp']),
                TextInput::make('heading')
                    ->label(__('rich-editor-blocks.image_text.fields.heading'))
                    ->maxLength(120),
                Textarea::make('body')
                    ->label(__('rich-editor-blocks.image_text.fields.body'))
                    ->required()
                    ->rows(4)
                    ->maxLength(800)
                    ->live(onBlur: true)
                    ->helperText(fn ($state) => strlen($state ?? '').' / 800 '.__('resource.default_form_fields.signs').' — '.__('rich-editor-blocks.image_text.fields.body_link_hint')),
                Select::make('image_position')
                    ->label(__('rich-editor-blocks.image_text.fields.image_position'))
                    ->options([
                        'right' => __('rich-editor-blocks.image_text.fields.image_position_right'),
                        'left' => __('rich-editor-blocks.image_text.fields.image_position_left'),
                    ])
                    ->default('right')
                    ->native(false),
                Section::make(__('rich-editor-blocks.image_text.fields.link_section'))
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Select::make('link_category_id')
                            ->label(__('rich-editor-blocks.internal_link.fields.category'))
                            ->options(Category::orderBy('name')->pluck('name', 'id'))
                            ->live()
                            ->afterStateUpdated(function (Set $set): void {
                                $set('link_cluster_id', null);
                                $set('link_post_id', null);
                            })
                            ->nullable(),
                        Select::make('link_cluster_id')
                            ->label(__('rich-editor-blocks.internal_link.fields.cluster'))
                            ->options(fn (Get $get): array => Cluster::query()
                                ->when($get('link_category_id'), fn ($q, $catId) => $q->where('category_id', $catId))
                                ->orderBy('title')
                                ->pluck('title', 'id')
                                ->toArray()
                            )
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('link_post_id', null))
                            ->nullable(),
                        Select::make('link_post_id')
                            ->label(__('rich-editor-blocks.internal_link.fields.article'))
                            ->options(fn (Get $get, ?Model $record): array => filled($get('link_cluster_id'))
                                ? Content::where('cluster_id', $get('link_cluster_id'))
                                    ->where('status', ContentStatus::PUBLISHED)
                                    ->when($record?->id, fn ($q, $id) => $q->where('id', '!=', $id))
                                    ->orderBy('cluster_position')
                                    ->get(['id', 'title', 'cluster_position'])
                                    ->mapWithKeys(fn ($c) => [
                                        $c->id => ($c->cluster_position ? $c->cluster_position.' – ' : '').$c->title,
                                    ])
                                    ->toArray()
                                : []
                            )
                            ->nullable()
                            ->searchable(),
                        Select::make('link_page_id')
                            ->label(__('rich-editor-blocks.image_text.fields.link_page'))
                            ->options(fn (?Model $record): array => Content::where('type', ContentType::PAGE)
                                ->where('status', ContentStatus::PUBLISHED)
                                ->when($record?->id, fn ($q, $id) => $q->where('id', '!=', $id))
                                ->orderBy('title')
                                ->pluck('title', 'id')
                                ->toArray()
                            )
                            ->nullable()
                            ->searchable(),
                        Toggle::make('link_with_arrow')
                            ->label(__('rich-editor-blocks.internal_link.fields.with_arrow'))
                            ->default(true),
                    ]),
            ]);
    }

    /**
     * @param  array<string, mixed>  $config
     */
    public static function getPreviewLabel(array $config): string
    {
        $label = __('rich-editor-blocks.image_text.label');

        return $label.(! empty($config['heading']) ? ': '.$config['heading'] : '');
    }

    /**
     * @param  array<string, mixed>  $config
     */
    public static function toPreviewHtml(array $config): string
    {
        $image = isset($config['image_id']) ? Media::find($config['image_id']) : null;

        return view('filament.forms.components.rich-editor.rich-content-custom-blocks.image-text.preview', [
            'heading' => $config['heading'] ?? null,
            'body' => str_replace('{link}', '…', $config['body'] ?? ''),
            'imagePosition' => $config['image_position'] ?? 'right',
            'image' => $image,
        ])->render();
    }

    /**
     * @param  array<string, mixed>  $config
     * @param  array<string, mixed>  $data
     */
    public static function toHtml(array $config, array $data): string
    {
        $image = isset($config['image_id']) ? Media::find($config['image_id']) : null;

        return view('filament.forms.components.rich-editor.rich-content-custom-blocks.image-text.index', [
            'heading' => $config['heading'] ?? null,
            'body' => self::renderBody($config),
            'imagePosition' => $config['image_position'] ?? 'right',
            'image' => $image,
        ])->render();
    }

    /**
     * @param  array<string, mixed>  $config
     */
    private static function renderBody(array $config): string
    {
        $body = $config['body'] ?? '';
        $linkHtml = self::buildLinkHtml($config);
        $bodyHtml = nl2br(e($body));

        if ($linkHtml && str_contains($body, '{link}')) {
            return '<p>'.str_replace('{link}', $linkHtml, $bodyHtml).'</p>';
        }

        if ($linkHtml) {
            return '<p>'.$bodyHtml.'</p><p>'.$linkHtml.'</p>';
        }

        return '<p>'.$bodyHtml.'</p>';
    }

    /**
     * @param  array<string, mixed>  $config
     */
    private static function buildLinkHtml(array $config): string
    {
        $targetId = $config['link_post_id'] ?? $config['link_page_id'] ?? null;

        if (! $targetId) {
            return '';
        }

        $target = Content::with('path')->find($targetId);

        if (! $target || ! $target->path?->url) {
            return '';
        }

        $href = url($target->path->url);
        $withArrow = (bool) ($config['link_with_arrow'] ?? true);
        $class = 'internal-link'.($withArrow ? ' internal-link--with-arrow' : '');

        return '<a href="'.e($href).'" class="'.$class.'">'.e($target->title).'</a>';
    }
}
