<?php

declare(strict_types=1);

namespace App\Filament\Forms\Components\RichEditor\TipTapExtensions;

use App\Models\AffiliateLink;
use Tiptap\Core\Mark;

class AffiliateLinkExtension extends Mark
{
    public static $name = 'affiliateLink';

    private array $linkCache = [];

    public function addAttributes(): array
    {
        return [
            'affiliateLinkId' => [
                'rendered' => false,
            ],
        ];
    }

    public function parseHTML(): array
    {
        return [
            ['tag' => 'a[data-affiliate-id]'],
        ];
    }

    public function renderHTML($mark, $HTMLAttributes = []): ?array
    {
        $id = $mark->attrs->affiliateLinkId ?? null;

        if ($id !== null && ! array_key_exists((string) $id, $this->linkCache)) {
            $this->linkCache[(string) $id] = AffiliateLink::find($id);
        }

        $link = $id !== null ? ($this->linkCache[(string) $id] ?? null) : null;

        if ($link === null || ! $link->is_active) {
            return null;
        }

        return [
            'a',
            array_merge($HTMLAttributes, [
                'href' => url('go/'.$link->slug),
                'rel' => 'nofollow sponsored noopener',
                'target' => '_blank',
                'class' => 'affiliate-link',
            ]),
            0,
        ];
    }
}
