<?php

namespace App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks;

use App\Models\Media;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;

class ImageBlock extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'image';
    }

    public static function getLabel(): string
    {
        return __('rich-editor-blocks.image.label');
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action
            ->schema([
                CuratorPicker::make('image_id')
                    ->label(__('rich-editor-blocks.image.fields.image'))
                    ->required()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp']),
            ]);
    }

    /**
     * @param  array<string, mixed>  $config
     */
    public static function getPreviewLabel(array $config): string
    {
        return __('rich-editor-blocks.image.label');
    }

    /**
     * @param  array<string, mixed>  $config
     */
    public static function toPreviewHtml(array $config): string
    {
        $image = isset($config['image_id']) ? Media::find($config['image_id']) : null;

        return view('filament.forms.components.rich-editor.rich-content-custom-blocks.image.preview', [
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

        return view('filament.forms.components.rich-editor.rich-content-custom-blocks.image.index', [
            'image' => $image,
        ])->render();
    }
}
