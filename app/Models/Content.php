<?php

namespace App\Models;

use App\Enums\ContentStatus;
use App\Enums\ContentType;
use App\Enums\RedirectStatus;
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
use Filament\Forms\Components\RichEditor\Models\Concerns\InteractsWithRichContent;
use Filament\Forms\Components\RichEditor\Models\Contracts\HasRichContent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;

class Content extends Model implements HasRichContent, Feedable
{
    use HasFactory, InteractsWithRichContent;

    protected $fillable = [
        'title',
        'excerpt',
        'content',
        'feature_image_id',
        'is_featured',
        'type',
        'status',
        'published_at',
        'revised_at',
        'author_id',
        'category_id',
        'cluster_id',
        'cluster_position',
        'is_pillar',
        'sort',
        'hero_kicker',
        'hero_headline',
        'hero_headline_accent',
        'hero_text',
        'hero_image_id',
    ];

    protected $casts = [
        'status'       => ContentStatus::class,
        'type'         => ContentType::class,
        'published_at' => 'datetime',
        'revised_at'   => 'datetime',
    ];

    public function getContentAttribute(mixed $value): array|null
    {
        if (is_null($value)) return null;
        return is_array($value) ? $value : json_decode($value, true);
    }

    public function setContentAttribute(mixed $value): void
    {
        $this->attributes['content'] = is_array($value) ? json_encode($value) : $value;
    }

    public function hasAffiliateLinks(): bool
    {
        $content = $this->content;

        return $content !== null && str_contains(json_encode($content), '"affiliateLink"');
    }

    public function renderSafeRichContent(string $field): string
    {
        $originalRaw   = $this->attributes[$field] ?? null;
        $contentArray  = $this->getAttribute($field);

        if (is_array($contentArray)) {
            $processed = $this->preprocessInternalLinks($contentArray);
            $processed = $this->preprocessAffiliateLinks($processed);
            $this->attributes[$field] = json_encode($processed);
        }

        try {
            $html = (string) $this->renderRichContent($field);
        } finally {
            $this->attributes[$field] = $originalRaw;
        }

        $html = preg_replace_callback(
            '/<a\b([^>]*)>/i',
            function (array $matches) {
                $attrs = $matches[1];

                if (! preg_match('/target=["\']_blank["\']/i', $attrs)) {
                    return $matches[0];
                }

                if (preg_match('/\brel=/i', $attrs)) {
                    return $matches[0];
                }

                return '<a' . $attrs . ' rel="noopener noreferrer">';
            },
            $html
        );

        $arrowSvg = '<svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="internal-link-arrow" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>';

        $html = preg_replace_callback(
            '/<a\b([^>]*\binternal-link--with-arrow\b[^>]*)>(.*?)<\/a>/s',
            fn (array $m) => "<a{$m[1]}>{$arrowSvg}{$m[2]}</a>",
            $html
        );

        if ($this->id !== null) {
            $contentId = $this->id;
            $html = preg_replace_callback(
                '/(href=)(["\'])([^"\']*\/go\/[^"\'?]+)\2/i',
                fn (array $m) => "{$m[1]}{$m[2]}{$m[3]}?c={$contentId}{$m[2]}",
                $html
            );
        }

        return $html;
    }

    /**
     * @param  array<string, mixed>  $content
     * @return array<string, mixed>
     */
    protected function preprocessInternalLinks(array $content): array
    {
        $postCache = [];
        $processed = $this->walkInternalLinkNodes($content, $postCache);

        return $processed ?? $content;
    }

    /**
     * Walks the Tiptap JSON tree recursively. For text nodes carrying an
     * internalLink mark, replaces the link text with the current post title.
     * If the target post has been deleted, drops the text node (returns null);
     * paragraphs that become empty as a result are dropped as well.
     *
     * @param  array<string, mixed>  $node
     * @param  array<int, \App\Models\Content|null>  $postCache
     * @return array<string, mixed>|null
     */
    protected function walkInternalLinkNodes(array $node, array &$postCache): ?array
    {
        if (($node['type'] ?? null) === 'text' && ! empty($node['marks'])) {
            foreach ($node['marks'] as $mark) {
                if (($mark['type'] ?? null) !== 'internalLink') {
                    continue;
                }

                $postId = $mark['attrs']['postId'] ?? null;

                if (! $postId) {
                    return null;
                }

                if (! array_key_exists($postId, $postCache)) {
                    $postCache[$postId] = static::with('path')->find($postId);
                }

                $post = $postCache[$postId];

                if (! $post || ! $post->path?->url) {
                    return null;
                }

                $node['text'] = $post->title;

                return $node;
            }
        }

        if (isset($node['content']) && is_array($node['content'])) {
            $kept = [];
            foreach ($node['content'] as $child) {
                $processed = $this->walkInternalLinkNodes($child, $postCache);
                if ($processed !== null) {
                    $kept[] = $processed;
                }
            }
            $node['content'] = $kept;

            if (($node['type'] ?? null) === 'paragraph' && $this->isParagraphEmpty($node)) {
                return null;
            }
        }

        return $node;
    }

    /**
     * @param  array<string, mixed>  $content
     * @return array<string, mixed>
     */
    protected function preprocessAffiliateLinks(array $content): array
    {
        $linkCache = [];
        $processed = $this->walkAffiliateLinkNodes($content, $linkCache);

        return $processed ?? $content;
    }

    /**
     * @param  array<string, mixed>  $node
     * @param  array<int, \App\Models\AffiliateLink|null>  $linkCache
     * @return array<string, mixed>|null
     */
    protected function walkAffiliateLinkNodes(array $node, array &$linkCache): ?array
    {
        if (($node['type'] ?? null) === 'text' && ! empty($node['marks'])) {
            foreach ($node['marks'] as $mark) {
                if (($mark['type'] ?? null) !== 'affiliateLink') {
                    continue;
                }

                $linkId = $mark['attrs']['affiliateLinkId'] ?? null;

                if (! $linkId) {
                    return null;
                }

                if (! array_key_exists($linkId, $linkCache)) {
                    $linkCache[$linkId] = AffiliateLink::find($linkId);
                }

                $link = $linkCache[$linkId];

                if (! $link || ! $link->is_active) {
                    return null;
                }

                $node['text'] = $link->name;

                return $node;
            }
        }

        if (isset($node['content']) && is_array($node['content'])) {
            $kept = [];
            foreach ($node['content'] as $child) {
                $processed = $this->walkAffiliateLinkNodes($child, $linkCache);
                if ($processed !== null) {
                    $kept[] = $processed;
                }
            }
            $node['content'] = $kept;

            if (($node['type'] ?? null) === 'paragraph' && $this->isParagraphEmpty($node)) {
                return null;
            }
        }

        return $node;
    }

    /**
     * @param  array<string, mixed>  $node
     */
    protected function isParagraphEmpty(array $node): bool
    {
        if (empty($node['content'])) {
            return true;
        }

        foreach ($node['content'] as $child) {
            if (($child['type'] ?? null) !== 'text') {
                return false;
            }
            if (trim($child['text'] ?? '') !== '') {
                return false;
            }
        }

        return true;
    }

    public function setUpRichContent(): void
    {
        $this->registerRichContent('content')
            ->plugins([InternalLinkPlugin::make(), AffiliateLinkPlugin::make()])
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
            ]);
    }

    protected static function booted(): void
    {
        static::deleting(function (Content $model) {
            if ($model->type === ContentType::HOMEPAGE
                && (int) Setting::get('site.homepage_content') === (int) $model->id) {
                throw new \RuntimeException(__('resource.homepage_resource.validation.active_cannot_delete'));
            }

            $model->metaData()->delete();

            if ($model->path?->url) {
                PathRedirect::updateOrCreate(
                    ['old_path' => $model->path->url],
                    [
                        'new_path'    => null,
                        'status_code' => RedirectStatus::GONE,
                    ]
                );
            }

            $model->path()->delete();
        });

        static::saved(function (Content $model) {
            MenuItem::where('model_type', Content::class)
                ->where('model_id', $model->id)
                ->pluck('menu_id')
                ->each(fn($menuId) => Cache::forget("menu_{$menuId}"));

            if ($model->wasChanged('category_id')) {
                app(\App\Services\PathService::class)->generateForModel($model);
            }
        });
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function featureImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'feature_image_id');
    }

    public function heroImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'hero_image_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function cluster(): BelongsTo
    {
        return $this->belongsTo(Cluster::class, 'cluster_id');
    }

    public function metaData(): MorphOne
    {
        return $this->morphOne(MetaData::class, 'model');
    }

    public function path(): MorphOne
    {
        return $this->morphOne(Path::class, 'model');
    }

    public function toFeedItem(): FeedItem
    {
        $imageUrl = $this->featureImage?->url ?? '';
        if (!$imageUrl) {
            $defaultId = Setting::get('rss.default_image');
            $imageUrl = $defaultId ? (Media::find($defaultId)?->url ?? '') : '';
        }

        $item = FeedItem::create()
            ->id($this->id)
            ->title($this->title)
            ->summary($this->excerpt ?? '')
            ->updated($this->published_at ?? $this->updated_at)
            ->link(url($this->path?->url ?? ''))
            ->authorName($this->author?->name ?? Setting::get('site.name') ?? config('app.name'));

        if ($imageUrl) {
            $item->enclosure($imageUrl)
                ->enclosureLength(0)
                ->enclosureType($this->featureImage?->type ?? 'image/jpeg');
        }

        return $item;
    }

    public static function getAllFeedItems(): Collection
    {
        return static::with(['featureImage', 'path', 'author'])
            ->where('type', ContentType::POST)
            ->where('status', ContentStatus::PUBLISHED)
            ->where('published_at', '<=', now())
            ->orderByDesc('published_at')
            ->limit(20)
            ->get();
    }
}
