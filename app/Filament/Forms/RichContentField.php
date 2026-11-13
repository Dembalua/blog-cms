<?php

namespace App\Filament\Forms;

use App\Filament\Forms\Components\RichEditor\Plugins\AffiliateLinkPlugin;
use App\Filament\Forms\Components\RichEditor\Plugins\InternalLinkPlugin;
use App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\ContactFormBlock;
use App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\FaqBlock;
use App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\ImageBlock;
use App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\ImageTextBlock;
use App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\PostCardsBigBlock;
use App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\PostCardsSmallBlock;
use App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\PostListBlock;
use App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\QuizBlock;
use App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\SectionsBlock;
use Awcodes\Curator\Components\Forms\RichEditor\AttachCuratorMediaPlugin;
use Filament\Forms\Components\RichEditor;

class RichContentField
{
    public static function make(string $name = 'content'): RichEditor
    {
        return RichEditor::make($name)
            ->label(__('resource.content_resource.form.content'))
            ->required()
            ->json()
            ->extraInputAttributes(['style' => 'min-height: 300px'])
            ->columnSpanFull()
            ->resizableImages(true)
            ->toolbarButtons([
                ['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link', 'internalLink', 'affiliateLink'],
                ['h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd'],
                ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                ['table', 'attachFiles', 'attachCuratorMedia', 'customBlocks'],
                ['undo', 'redo'],
            ])
            ->customBlocks([
                ImageBlock::class,
                ImageTextBlock::class,
                SectionsBlock::class,
                FaqBlock::class,
                QuizBlock::class,
                ContactFormBlock::class,
                PostCardsBigBlock::class,
                PostCardsSmallBlock::class,
                PostListBlock::class,
            ])
            ->plugins([
                AttachCuratorMediaPlugin::make(),
                InternalLinkPlugin::make(),
                AffiliateLinkPlugin::make(),
            ]);
    }
}
