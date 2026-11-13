<?php

namespace App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks;

use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Forms\Components\TextInput;
use Filament\Support\Enums\Width;

class ContactFormBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'contact-form';
    }

    public static function getLabel(): string
    {
        return __('rich-editor-blocks.contact_form.label');
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action
            ->modalWidth(Width::Medium)
            ->schema([
                TextInput::make('button_text')
                    ->label(__('rich-editor-blocks.contact_form.fields.button_text'))
                    ->required()
                    ->maxLength(80)
                    ->default(__('rich-editor-blocks.contact_form.default_button_text')),
            ]);
    }

    /**
     * @param  array<string, mixed>  $config
     */
    public static function getPreviewLabel(array $config): string
    {
        $label = __('rich-editor-blocks.contact_form.label');

        if (! empty($config['button_text'])) {
            $label .= ': ' . $config['button_text'];
        }

        return $label;
    }

    /**
     * @param  array<string, mixed>  $config
     */
    public static function toPreviewHtml(array $config): string
    {
        return view('filament.forms.components.rich-editor.rich-content-custom-blocks.contact-form.preview', [
            'buttonText' => $config['button_text'] ?? __('rich-editor-blocks.contact_form.default_button_text'),
        ])->render();
    }

    /**
     * @param  array<string, mixed>  $config
     * @param  array<string, mixed>  $data
     */
    public static function toHtml(array $config, array $data): string
    {
        return view('filament.forms.components.rich-editor.rich-content-custom-blocks.contact-form.index', [
            'buttonText' => $config['button_text'] ?? __('rich-editor-blocks.contact_form.default_button_text'),
        ])->render();
    }
}
