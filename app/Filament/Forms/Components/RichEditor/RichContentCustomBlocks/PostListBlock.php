<?php

namespace App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks;

use App\Enums\ContentType;
use App\Models\Category;
use App\Models\Cluster;
use App\Models\Content;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Enums\Width;

class PostListBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'post_list';
    }

    public static function getLabel(): string
    {
        return __('rich-editor-blocks.post_list.label');
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action
            ->modalWidth(Width::TwoExtraLarge)
            ->schema([
                TextInput::make('heading')
                    ->label(__('rich-editor-blocks.post_list.fields.heading'))
                    ->required()
                    ->maxLength(160),
                Textarea::make('intro')
                    ->label(__('rich-editor-blocks.post_list.fields.intro'))
                    ->rows(3)
                    ->maxLength(600),
                Select::make('count')
                    ->label(__('rich-editor-blocks.post_list.fields.count'))
                    ->options([2 => '2', 4 => '4', 6 => '6', 8 => '8'])
                    ->default(4)
                    ->required()
                    ->native(false)
                    ->live(),
                Repeater::make('items')
                    ->label(__('rich-editor-blocks.post_list.fields.items'))
                    ->schema([
                        Select::make('category_id')
                            ->label(__('rich-editor-blocks.post_list.fields.category'))
                            ->options(fn () => Category::orderBy('name')->pluck('name', 'id')->toArray())
                            ->native(false)
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set): void {
                                $set('cluster_id', null);
                                $set('post_id', null);
                            }),
                        Select::make('cluster_id')
                            ->label(__('rich-editor-blocks.post_list.fields.cluster'))
                            ->options(fn (Get $get): array => filled($get('category_id'))
                                ? Cluster::where('category_id', $get('category_id'))
                                    ->orderBy('title')
                                    ->pluck('title', 'id')
                                    ->toArray()
                                : [])
                            ->native(false)
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('post_id', null)),
                        Select::make('post_id')
                            ->label(__('rich-editor-blocks.post_list.fields.post'))
                            ->options(fn (Get $get): array => filled($get('cluster_id'))
                                ? Content::where('cluster_id', $get('cluster_id'))
                                    ->where('type', ContentType::POST)
                                    ->published()
                                    ->orderBy('cluster_position')
                                    ->get(['id', 'title', 'cluster_position'])
                                    ->mapWithKeys(fn ($c) => [
                                        $c->id => ($c->cluster_position ? $c->cluster_position.' – ' : '').$c->title,
                                    ])
                                    ->toArray()
                                : [])
                            ->native(false)
                            ->searchable()
                            ->required(),
                    ])
                    ->minItems(fn (Get $get): int => (int) ($get('count') ?? 4))
                    ->maxItems(fn (Get $get): int => (int) ($get('count') ?? 4))
                    ->reorderable()
                    ->collapsible()
                    ->addActionLabel(__('rich-editor-blocks.post_list.add_item'))
                    ->itemLabel(fn (array $state): ?string => self::labelForPost($state['post_id'] ?? null)),
            ]);
    }

    private static function labelForPost(mixed $postId): ?string
    {
        if (! $postId) {
            return null;
        }

        $post = Content::with(['category', 'cluster'])->find($postId);

        if (! $post) {
            return null;
        }

        $parts = [];

        if ($post->category?->name) {
            $parts[] = $post->category->name;
        }

        if ($post->cluster?->title) {
            $parts[] = $post->cluster->title;
        }

        $prefix = $parts ? implode(' · ', $parts).' — ' : '';

        return $prefix.$post->title;
    }

    /**
     * @param  array<string, mixed>  $config
     */
    public static function getPreviewLabel(array $config): string
    {
        $label = __('rich-editor-blocks.post_list.label');
        $count = (int) ($config['count'] ?? 0);

        if ($count > 0) {
            $label .= ' ('.$count.')';
        }

        if (! empty($config['heading'])) {
            $label .= ': '.$config['heading'];
        }

        return $label;
    }

    /**
     * @param  array<string, mixed>  $config
     */
    public static function toPreviewHtml(array $config): string
    {
        return view('filament.forms.components.rich-editor.rich-content-custom-blocks.post-list.preview', [
            'heading' => $config['heading'] ?? null,
            'intro' => $config['intro'] ?? null,
            'count' => (int) ($config['count'] ?? 4),
            'items' => $config['items'] ?? [],
        ])->render();
    }

    /**
     * @param  array<string, mixed>  $config
     * @param  array<string, mixed>  $data
     */
    public static function toHtml(array $config, array $data): string
    {
        $postIds = collect($config['items'] ?? [])
            ->pluck('post_id')
            ->filter()
            ->values();

        if ($postIds->isEmpty()) {
            return '';
        }

        $posts = Content::with(['featureImage', 'path', 'category', 'cluster'])
            ->whereIn('id', $postIds)
            ->get()
            ->keyBy('id');

        $orderedPosts = $postIds
            ->map(fn ($id) => $posts->get($id))
            ->filter()
            ->values();

        return view('filament.forms.components.rich-editor.rich-content-custom-blocks.post-list.index', [
            'heading' => $config['heading'] ?? null,
            'intro' => $config['intro'] ?? null,
            'posts' => $orderedPosts,
        ])->render();
    }
}
