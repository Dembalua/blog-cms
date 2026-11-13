<?php

namespace App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks;

use App\Enums\ContentStatus;
use App\Models\Category;
use App\Models\Cluster;
use App\Models\Content;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Model;

class SectionsBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'sections';
    }

    public static function getLabel(): string
    {
        return __('rich-editor-blocks.sections.label');
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action
            ->modalWidth(Width::TwoExtraLarge)
            ->schema([
                TextInput::make('heading')
                    ->label(__('rich-editor-blocks.sections.fields.heading'))
                    ->maxLength(160),
                Textarea::make('intro')
                    ->label(__('rich-editor-blocks.sections.fields.intro'))
                    ->rows(3)
                    ->maxLength(600),
                Repeater::make('items')
                    ->label(__('rich-editor-blocks.sections.fields.items'))
                    ->schema([
                        TextInput::make('heading')
                            ->label(__('rich-editor-blocks.sections.fields.item_heading'))
                            ->required()
                            ->maxLength(160),
                        Textarea::make('body')
                            ->label(__('rich-editor-blocks.sections.fields.item_body'))
                            ->required()
                            ->rows(4)
                            ->maxLength(1500),
                        Section::make(__('rich-editor-blocks.sections.fields.item_link'))
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
                                                $c->id => ($c->cluster_position ? $c->cluster_position . ' – ' : '') . $c->title,
                                            ])
                                            ->toArray()
                                        : []
                                    )
                                    ->nullable()
                                    ->searchable(),
                                Toggle::make('link_with_arrow')
                                    ->label(__('rich-editor-blocks.internal_link.fields.with_arrow'))
                                    ->default(true),
                            ]),
                    ])
                    ->minItems(1)
                    ->addActionLabel(__('rich-editor-blocks.sections.add_item'))
                    ->collapsible()
                    ->itemLabel(fn (array $state): ?string => $state['heading'] ?? null),
            ]);
    }

    /**
     * @param  array<string, mixed>  $config
     */
    public static function getPreviewLabel(array $config): string
    {
        $label = __('rich-editor-blocks.sections.label');
        $count = count($config['items'] ?? []);

        if ($count > 0) {
            $label .= ' (' . $count . ')';
        }

        if (! empty($config['heading'])) {
            $label .= ': ' . $config['heading'];
        }

        return $label;
    }

    /**
     * @param  array<string, mixed>  $config
     */
    public static function toPreviewHtml(array $config): string
    {
        return view('filament.forms.components.rich-editor.rich-content-custom-blocks.sections.preview', [
            'heading' => $config['heading'] ?? null,
            'intro'   => $config['intro'] ?? null,
            'items'   => $config['items'] ?? [],
        ])->render();
    }

    /**
     * @param  array<string, mixed>  $config
     * @param  array<string, mixed>  $data
     */
    public static function toHtml(array $config, array $data): string
    {
        $items = array_map(fn (array $item) => [
            ...$item,
            'link_html' => self::buildLinkHtml($item),
        ], $config['items'] ?? []);

        return view('filament.forms.components.rich-editor.rich-content-custom-blocks.sections.index', [
            'heading' => $config['heading'] ?? null,
            'intro'   => $config['intro'] ?? null,
            'items'   => $items,
        ])->render();
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private static function buildLinkHtml(array $item): string
    {
        $postId = $item['link_post_id'] ?? null;

        if (! $postId) {
            return '';
        }

        $post = Content::with('path')->find($postId);

        if (! $post || ! $post->path?->url) {
            return '';
        }

        $href      = url($post->path->url);
        $withArrow = (bool) ($item['link_with_arrow'] ?? true);
        $class     = 'internal-link' . ($withArrow ? ' internal-link--with-arrow' : '');

        return '<a href="' . e($href) . '" class="' . $class . '">' . e($post->title) . '</a>';
    }
}
