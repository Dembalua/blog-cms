<?php

namespace App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks;

use App\Enums\ContentStatus;
use App\Enums\ContentType;
use App\Models\Category;
use App\Models\Cluster;
use App\Models\Content;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Enums\Width;

class PostCardsBigBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'post_cards_big';
    }

    public static function getLabel(): string
    {
        return __('rich-editor-blocks.post_cards_big.label');
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action
            ->modalWidth(Width::TwoExtraLarge)
            ->schema([
                TextInput::make('heading')
                    ->label(__('rich-editor-blocks.post_cards_big.fields.heading'))
                    ->required()
                    ->maxLength(160),
                Textarea::make('intro')
                    ->label(__('rich-editor-blocks.post_cards_big.fields.intro'))
                    ->rows(3)
                    ->maxLength(600),
                Select::make('count')
                    ->label(__('rich-editor-blocks.post_cards_big.fields.count'))
                    ->options([3 => '3', 6 => '6', 9 => '9', 12 => '12'])
                    ->default(3)
                    ->required()
                    ->native(false),
                Select::make('source')
                    ->label(__('rich-editor-blocks.post_cards_big.fields.source'))
                    ->options([
                        'latest'   => __('rich-editor-blocks.post_cards_big.fields.source_latest'),
                        'category' => __('rich-editor-blocks.post_cards_big.fields.source_category'),
                        'cluster'  => __('rich-editor-blocks.post_cards_big.fields.source_cluster'),
                    ])
                    ->default('latest')
                    ->required()
                    ->native(false)
                    ->live(),
                Select::make('category_id')
                    ->label(__('rich-editor-blocks.post_cards_big.fields.category'))
                    ->options(fn () => Category::orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->native(false)
                    ->required()
                    ->visible(fn (Get $get): bool => $get('source') === 'category'),
                Select::make('cluster_id')
                    ->label(__('rich-editor-blocks.post_cards_big.fields.cluster'))
                    ->options(fn () => Cluster::orderBy('title')->pluck('title', 'id'))
                    ->searchable()
                    ->native(false)
                    ->required()
                    ->visible(fn (Get $get): bool => $get('source') === 'cluster'),
                Toggle::make('show_category_link')
                    ->label(__('rich-editor-blocks.post_cards_big.fields.show_category_link'))
                    ->default(false)
                    ->visible(fn (Get $get): bool => $get('source') === 'category'),
            ]);
    }

    /**
     * @param  array<string, mixed>  $config
     */
    public static function getPreviewLabel(array $config): string
    {
        $label = __('rich-editor-blocks.post_cards_big.label');
        $count = (int) ($config['count'] ?? 0);

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
        return view('filament.forms.components.rich-editor.rich-content-custom-blocks.post-cards-big.preview', [
            'heading' => $config['heading'] ?? null,
            'intro'   => $config['intro'] ?? null,
            'count'   => (int) ($config['count'] ?? 3),
            'source'  => $config['source'] ?? 'latest',
        ])->render();
    }

    /**
     * @param  array<string, mixed>  $config
     * @param  array<string, mixed>  $data
     */
    public static function toHtml(array $config, array $data): string
    {
        $count  = in_array((int) ($config['count'] ?? 3), [3, 6, 9, 12], true) ? (int) $config['count'] : 3;
        $source = $config['source'] ?? 'latest';

        $query = Content::with(['featureImage', 'path', 'category'])
            ->where('type', ContentType::POST)
            ->where('status', ContentStatus::PUBLISHED);

        if ($source === 'category' && ! empty($config['category_id'])) {
            $query->where('category_id', $config['category_id']);
        } elseif ($source === 'cluster' && ! empty($config['cluster_id'])) {
            $query->where('cluster_id', $config['cluster_id']);
        }

        $posts = $query
            ->orderByDesc('published_at')
            ->take($count)
            ->get();

        $categoryUrl = null;
        $categoryName = null;

        if (($config['show_category_link'] ?? false) && $source === 'category' && ! empty($config['category_id'])) {
            $category = Category::with('path')->find($config['category_id']);

            if ($category && $category->path?->url) {
                $categoryUrl = url($category->path->url);
                $categoryName = $category->name;
            }
        }

        return view('filament.forms.components.rich-editor.rich-content-custom-blocks.post-cards-big.index', [
            'heading'      => $config['heading'] ?? null,
            'intro'        => $config['intro'] ?? null,
            'posts'        => $posts,
            'categoryUrl'  => $categoryUrl,
            'categoryName' => $categoryName,
        ])->render();
    }
}
