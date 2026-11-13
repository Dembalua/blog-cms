<?php

namespace App\Http\Controllers;

use App\Enums\ContentStatus;
use App\Enums\ContentType;
use App\Models\Category;
use App\Models\Content;
use App\Models\Setting;
use Illuminate\View\View;
use Spatie\Feed\Feed;

class FeedController extends Controller
{
    public function info(): View
    {
        view()->share('meta', [
            'meta_description' => __('Subscribe to the RSS feed and never miss a new article.'),
        ]);

        return view('feed.info', [
            'feedUrl' => route('feed.main'),
            'feedTitle' => Setting::get('rss.title') ?? config('app.name'),
        ]);
    }

    public function main(): Feed
    {
        $items = Content::getAllFeedItems();

        return new Feed(
            title: Setting::get('rss.title') ?? config('app.name'),
            items: $items,
            url: route('feed.main'),
            view: 'feed.rss',
            description: Setting::get('rss.description') ?? '',
            language: 'de',
            format: 'rss',
        );
    }

    public function category(string $slug): Feed
    {
        $category = Category::whereHas('path', fn ($q) => $q->where('slug', $slug))->firstOrFail();

        $items = Content::with(['featureImage', 'path', 'author'])
            ->where('type', ContentType::POST)
            ->where('status', ContentStatus::PUBLISHED)
            ->where('published_at', '<=', now())
            ->where('category_id', $category->id)
            ->orderByDesc('published_at')
            ->limit(20)
            ->get();

        $feedItems = $items->map(function (Content $content) {
            $item = $content->toFeedItem();
            $curation = $content->featureImage?->getCuration('pinterest');
            if (! empty($curation['url'])) {
                $item->enclosure($curation['url'])
                    ->enclosureLength(0)
                    ->enclosureType('image/webp');
            }

            return $item;
        });

        return new Feed(
            title: $category->name,
            items: $feedItems,
            url: route('feed.category', $slug),
            view: 'feed.rss',
            description: $category->description ?? '',
            language: 'de',
            format: 'rss',
        );
    }
}
