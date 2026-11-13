<?php

namespace App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks;

use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Support\Enums\Width;

class FaqBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'faq';
    }

    public static function getLabel(): string
    {
        return __('rich-editor-blocks.faq.label');
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action
            ->modalWidth(Width::TwoExtraLarge)
            ->schema([
                TextInput::make('heading')
                    ->label(__('rich-editor-blocks.faq.fields.heading')),
                Repeater::make('items')
                    ->label(__('rich-editor-blocks.faq.fields.items'))
                    ->schema([
                        TextInput::make('question')
                            ->label(__('rich-editor-blocks.faq.fields.question'))
                            ->required()
                            ->maxLength(200),
                        Textarea::make('answer')
                            ->label(__('rich-editor-blocks.faq.fields.answer'))
                            ->required()
                            ->rows(3)
                            ->maxLength(1000),
                    ])
                    ->minItems(1)
                    ->addActionLabel(__('rich-editor-blocks.faq.add_item'))
                    ->collapsible()
                    ->itemLabel(fn(array $state): ?string => $state['question'] ?? null),
            ]);
    }

    /**
     * @param  array<string, mixed>  $config
     */
    public static function getPreviewLabel(array $config): string
    {
        $label = __('rich-editor-blocks.faq.label');
        $count = count($config['items'] ?? []);

        if ($count > 0) {
            $label .= ' (' . $count . ')';
        }

        if (!empty($config['heading'])) {
            $label .= ': ' . $config['heading'];
        }

        return $label;
    }

    /**
     * @param  array<string, mixed>  $config
     */
    public static function toPreviewHtml(array $config): string
    {
        return view('filament.forms.components.rich-editor.rich-content-custom-blocks.faq.preview', [
            'heading' => $config['heading'] ?? null,
            'items'   => $config['items'] ?? [],
        ])->render();
    }

    /**
     * @param  array<string, mixed>  $config
     * @param  array<string, mixed>  $data
     */
    public static function toHtml(array $config, array $data): string
    {
        return view('filament.forms.components.rich-editor.rich-content-custom-blocks.faq.index', [
            'heading' => $config['heading'] ?? null,
            'items'   => $config['items'] ?? [],
        ])->render();
    }

    /**
     * Extracts all FAQ items from a Tiptap content document for JSON-LD generation.
     *
     * @param  array<string, mixed>|string|null  $contentJson
     * @return array<int, array{question: string, answer: string}>
     */
    public static function extractItems(array|string|null $contentJson): array
    {
        if (is_string($contentJson)) {
            $contentJson = json_decode($contentJson, true);
        }

        if (empty($contentJson)) {
            return [];
        }

        $faqItems = [];
        $nodes = $contentJson['content'] ?? [];

        if (!is_array($nodes)) {
            return [];
        }

        foreach ($nodes as $node) {
            if (!is_array($node)) {
                continue;
            }

            if (($node['type'] ?? '') === 'customBlock') {
                $attrs = $node['attrs'] ?? [];
                if (($attrs['id'] ?? '') === static::getId()) {
                    $blockData = $attrs['config'] ?? [];
                    foreach ($blockData['items'] ?? [] as $item) {
                        if (!empty($item['question']) && !empty($item['answer'])) {
                            $faqItems[] = $item;
                        }
                    }
                }
            }

            if (!empty($node['content'])) {
                $faqItems = array_merge($faqItems, static::extractItems($node));
            }
        }

        return $faqItems;
    }
}
