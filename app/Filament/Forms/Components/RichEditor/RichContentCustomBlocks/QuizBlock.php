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
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Model;

class QuizBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'quiz';
    }

    public static function getLabel(): string
    {
        return __('rich-editor-blocks.quiz.label');
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action
            ->modalWidth(Width::TwoExtraLarge)
            ->schema([
                TextInput::make('heading')
                    ->label(__('rich-editor-blocks.quiz.fields.heading'))
                    ->maxLength(160),
                Textarea::make('intro')
                    ->label(__('rich-editor-blocks.quiz.fields.intro'))
                    ->rows(3)
                    ->maxLength(600),
                TextInput::make('cta_label')
                    ->label(__('rich-editor-blocks.quiz.fields.cta_label'))
                    ->default(__('rich-editor-blocks.quiz.defaults.cta_label'))
                    ->maxLength(80),
                Repeater::make('questions')
                    ->label(__('rich-editor-blocks.quiz.fields.questions'))
                    ->schema([
                        TextInput::make('question')
                            ->label(__('rich-editor-blocks.quiz.fields.question'))
                            ->required()
                            ->maxLength(300),
                        Repeater::make('answers')
                            ->label(__('rich-editor-blocks.quiz.fields.answers'))
                            ->columns(3)
                            ->schema([
                                TextInput::make('label')
                                    ->label(__('rich-editor-blocks.quiz.fields.answer_label'))
                                    ->required()
                                    ->maxLength(200)
                                    ->columnSpan(2),
                                TextInput::make('points')
                                    ->label(__('rich-editor-blocks.quiz.fields.answer_points'))
                                    ->required()
                                    ->integer()
                                    ->columnSpan(1),
                            ])
                            ->minItems(2)
                            ->addActionLabel(__('rich-editor-blocks.quiz.add_answer'))
                            ->itemLabel(fn (array $state): ?string => isset($state['label'], $state['points'])
                                ? $state['label'].' → '.$state['points']
                                : ($state['label'] ?? null)),
                    ])
                    ->minItems(1)
                    ->addActionLabel(__('rich-editor-blocks.quiz.add_question'))
                    ->collapsible()
                    ->itemLabel(fn (array $state): ?string => $state['question'] ?? null),
                Repeater::make('results')
                    ->label(__('rich-editor-blocks.quiz.fields.results'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('min_points')
                                    ->label(__('rich-editor-blocks.quiz.fields.min_points'))
                                    ->required()
                                    ->integer(),
                                TextInput::make('max_points')
                                    ->label(__('rich-editor-blocks.quiz.fields.max_points'))
                                    ->required()
                                    ->integer(),
                            ]),
                        TextInput::make('heading')
                            ->label(__('rich-editor-blocks.quiz.fields.result_heading'))
                            ->required()
                            ->maxLength(160),
                        Textarea::make('body')
                            ->label(__('rich-editor-blocks.quiz.fields.result_body'))
                            ->required()
                            ->rows(4)
                            ->maxLength(1500),
                        Section::make(__('rich-editor-blocks.quiz.fields.result_link'))
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
                                Toggle::make('link_with_arrow')
                                    ->label(__('rich-editor-blocks.internal_link.fields.with_arrow'))
                                    ->default(true),
                            ]),
                    ])
                    ->minItems(1)
                    ->addActionLabel(__('rich-editor-blocks.quiz.add_result'))
                    ->collapsible()
                    ->itemLabel(fn (array $state): ?string => isset($state['min_points'], $state['max_points'])
                        ? ($state['heading'] ?? '').' ('.$state['min_points'].'–'.$state['max_points'].')'
                        : ($state['heading'] ?? null)),
            ]);
    }

    /**
     * @param  array<string, mixed>  $config
     */
    public static function getPreviewLabel(array $config): string
    {
        $label = __('rich-editor-blocks.quiz.label');
        $count = count($config['questions'] ?? []);

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
        return view('filament.forms.components.rich-editor.rich-content-custom-blocks.quiz.preview', [
            'heading' => $config['heading'] ?? null,
            'intro' => $config['intro'] ?? null,
            'questions' => $config['questions'] ?? [],
            'results' => $config['results'] ?? [],
        ])->render();
    }

    /**
     * @param  array<string, mixed>  $config
     * @param  array<string, mixed>  $data
     */
    public static function toHtml(array $config, array $data): string
    {
        $results = array_map(fn (array $result) => [
            ...$result,
            'link_html' => self::buildLinkHtml($result),
        ], $config['results'] ?? []);

        return view('filament.forms.components.rich-editor.rich-content-custom-blocks.quiz.index', [
            'heading' => $config['heading'] ?? null,
            'intro' => $config['intro'] ?? null,
            'cta_label' => $config['cta_label'] ?? __('rich-editor-blocks.quiz.defaults.cta_label'),
            'questions' => $config['questions'] ?? [],
            'results' => $results,
        ])->render();
    }

    /**
     * @param  array<string, mixed>  $result
     */
    private static function buildLinkHtml(array $result): string
    {
        $postId = $result['link_post_id'] ?? null;

        if (! $postId) {
            return '';
        }

        $post = Content::with('path')->find($postId);

        if (! $post || ! $post->path?->url) {
            return '';
        }

        $href = url($post->path->url);
        $withArrow = (bool) ($result['link_with_arrow'] ?? true);
        $class = 'internal-link'.($withArrow ? ' internal-link--with-arrow' : '');
        $arrow = $withArrow
            ? '<svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="internal-link-arrow" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>'
            : '';

        return '<a href="'.e($href).'" class="'.$class.'">'.$arrow.e($post->title).'</a>';
    }
}
