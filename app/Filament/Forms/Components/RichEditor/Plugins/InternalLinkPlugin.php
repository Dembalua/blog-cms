<?php

declare(strict_types=1);

namespace App\Filament\Forms\Components\RichEditor\Plugins;

use App\Enums\ContentStatus;
use App\Filament\Forms\Components\RichEditor\TipTapExtensions\InternalLinkExtension;
use App\Models\Category;
use App\Models\Cluster;
use App\Models\Content;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\EditorCommand;
use Filament\Forms\Components\RichEditor\Plugins\Contracts\RichContentPlugin;
use Filament\Forms\Components\RichEditor\RichEditorTool;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Enums\Width;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Database\Eloquent\Model;

class InternalLinkPlugin implements RichContentPlugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getTipTapPhpExtensions(): array
    {
        return [
            app(InternalLinkExtension::class),
        ];
    }

    public function getTipTapJsExtensions(): array
    {
        return [
            FilamentAsset::getScriptSrc('internal-link-extension'),
        ];
    }

    public function getEditorTools(): array
    {
        return [
            RichEditorTool::make('internalLink')
                ->label(__('rich-editor-blocks.internal_link.label'))
                ->icon('heroicon-m-link')
                ->action('insertInternalLink')
                ->activeKey('internalLink')
                ->extraAttributes(['class' => 'fi-internal-link-tool']),
        ];
    }

    public function getEditorActions(): array
    {
        return [
            Action::make('insertInternalLink')
                ->modalWidth(Width::Large)
                ->modalHeading(__('rich-editor-blocks.internal_link.modal_heading'))
                ->schema([
                    Select::make('category_id')
                        ->label(__('rich-editor-blocks.internal_link.fields.category'))
                        ->options(Category::orderBy('name')->pluck('name', 'id'))
                        ->live()
                        ->afterStateUpdated(function (Set $set): void {
                            $set('cluster_id', null);
                            $set('post_id', null);
                        }),
                    Select::make('cluster_id')
                        ->label(__('rich-editor-blocks.internal_link.fields.cluster'))
                        ->options(fn (Get $get): array => Cluster::query()
                            ->when($get('category_id'), fn ($q, $catId) => $q->where('category_id', $catId))
                            ->orderBy('title')
                            ->pluck('title', 'id')
                            ->toArray()
                        )
                        ->live()
                        ->afterStateUpdated(fn (Set $set) => $set('post_id', null))
                        ->required(),
                    Select::make('post_id')
                        ->label(__('rich-editor-blocks.internal_link.fields.article'))
                        ->options(fn (Get $get, ?Model $record): array => filled($get('cluster_id'))
                            ? Content::where('cluster_id', $get('cluster_id'))
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
                        ->required()
                        ->searchable(),
                    Toggle::make('with_arrow')
                        ->label(__('rich-editor-blocks.internal_link.fields.with_arrow'))
                        ->default(true),
                ])
                ->action(function (array $arguments, array $data, RichEditor $component): void {
                    $editorSelection = $arguments['editorSelection'] ?? null;
                    $withArrow = (bool) ($data['with_arrow'] ?? false);
                    $post = Content::find($data['post_id']);

                    $commands = [
                        EditorCommand::make('insertContent', [[
                            'type' => 'text',
                            'text' => $post?->title ?? '',
                            'marks' => [[
                                'type' => 'internalLink',
                                'attrs' => ['postId' => (int) $data['post_id'], 'withArrow' => $withArrow],
                            ]],
                        ]]),
                    ];

                    $component->runCommands($commands, editorSelection: $editorSelection);
                }),
        ];
    }
}
