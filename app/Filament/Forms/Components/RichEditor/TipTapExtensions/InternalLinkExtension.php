<?php

declare(strict_types=1);

namespace App\Filament\Forms\Components\RichEditor\TipTapExtensions;

use App\Models\Content;
use Tiptap\Core\Mark;

class InternalLinkExtension extends Mark
{
    public static $name = 'internalLink';

    private array $urlCache = [];

    public function addAttributes(): array
    {
        return [
            'postId' => [],
            'withArrow' => ['default' => false],
        ];
    }

    public function parseHTML(): array
    {
        return [
            ['tag' => 'a[data-post-id]'],
        ];
    }

    public function renderHTML($mark, $HTMLAttributes = []): array
    {
        $postId = $mark->attrs->postId ?? null;
        $withArrow = $mark->attrs->withArrow ?? false;

        if ($postId !== null && ! array_key_exists((string) $postId, $this->urlCache)) {
            $post = Content::with('path')->find($postId);
            $this->urlCache[(string) $postId] = $post?->path?->url
                ? url($post->path->url)
                : null;
        }

        $href = ($postId !== null ? ($this->urlCache[(string) $postId] ?? null) : null) ?? '#';

        $class = 'internal-link' . ($withArrow ? ' internal-link--with-arrow' : '');

        return ['a', array_merge($HTMLAttributes, ['href' => $href, 'class' => $class]), 0];
    }
}
