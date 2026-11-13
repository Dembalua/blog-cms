<?php

namespace App\Services;

use App\Enums\ContentType;
use App\Models\Category;
use App\Models\Content;
use App\Models\Setting;

class MetaService
{
    private static ?object $globalMeta = null;

    private function globalMeta(): ?object
    {
        if (self::$globalMeta === null) {
            self::$globalMeta = Setting::where('group', 'seo')
                ->where('key', 'meta')
                ->first()
                ?->metaData ?? new \stdClass;
        }

        return self::$globalMeta instanceof \stdClass ? null : self::$globalMeta;
    }

    public function resolve(mixed $model): array
    {
        $meta = $model->metaData;
        $global = $this->globalMeta();

        return [
            'meta_title' => $meta?->meta_title ?: $global?->meta_title,
            'meta_description' => $meta?->meta_description ?: $global?->meta_description,
            'meta_keywords' => $meta?->meta_keywords ?: $global?->meta_keywords,
            'robots' => $meta?->robots ?: $global?->robots,
            'og_title' => $meta?->og_title ?: $global?->og_title,
            'og_description' => $meta?->og_description ?: $global?->og_description,
            'og_image' => $this->resolveOgImage($model) ?? $global?->ogImage?->og_url,
            'og_url' => request()->url(),
            'og_type' => $this->resolveOgType($model),
            'author' => $model instanceof Content ? $model->author?->name : null,
            'canonical_url' => request()->url(),
        ];
    }

    private function resolveOgImage(mixed $model): ?string
    {
        return match (true) {
            $model instanceof Content => $model->featureImage?->og_url,
            $model instanceof Category => $model->image?->og_url,
            default => null,
        };
    }

    private function resolveOgType(mixed $model): string
    {
        if ($model instanceof Content && $model->type === ContentType::POST) {
            return 'article';
        }

        return 'website';
    }

    public function resolveGlobal(): array
    {
        $global = $this->globalMeta();

        return [
            'meta_title' => $global?->meta_title,
            'meta_description' => $global?->meta_description,
            'meta_keywords' => $global?->meta_keywords,
            'robots' => $global?->robots,
            'og_title' => $global?->og_title,
            'og_description' => $global?->og_description,
            'og_image' => $global?->ogImage?->og_url,
            'og_url' => request()->url(),
            'og_type' => 'website',
            'canonical_url' => request()->url(),
        ];
    }
}
