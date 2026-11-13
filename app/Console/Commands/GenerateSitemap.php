<?php

namespace App\Console\Commands;

use App\Enums\ContentStatus;
use App\Enums\ContentType;
use App\Enums\MetaDataRobots;
use App\Models\Category;
use App\Models\Content;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Generate the sitemap.xml file';

    public function handle(): void
    {
        $sitemap = Sitemap::create();

        $homeUrl = Url::create('/');
        $homepageId = Setting::get('site.homepage_content');
        $homepage = $homepageId
            ? Content::where('id', $homepageId)->where('type', ContentType::HOMEPAGE)->first()
            : null;

        if ($homepage) {
            $lastmod = $homepage->updated_at;

            if ($this->homepageShowsLatestPosts($homepage)) {
                $maxPost = Content::where('status', ContentStatus::PUBLISHED)
                    ->where('type', ContentType::POST)
                    ->max('updated_at');

                if ($maxPost && $maxPost > $lastmod) {
                    $lastmod = Carbon::parse($maxPost);
                }
            }

            $homeUrl->setLastModificationDate($lastmod);
        } else {
            $latestPostUpdate = Content::where('status', ContentStatus::PUBLISHED)
                ->where('type', ContentType::POST)
                ->max('updated_at');

            if ($latestPostUpdate) {
                $homeUrl->setLastModificationDate(Carbon::parse($latestPostUpdate));
            }
        }

        $sitemap->add($homeUrl);

        Category::active()->with('path', 'metaData')->get()
            ->filter(fn (Category $category) => $category->path?->url)
            ->filter(fn (Category $category) => ! in_array($category->metaData?->robots, [
                MetaDataRobots::NOINDEX_FOLLOW,
                MetaDataRobots::NOINDEX_NOFOLLOW,
            ]))
            ->each(fn (Category $category) => $sitemap->add(
                Url::create('/'.$category->path->url)
                    ->setLastModificationDate($category->updated_at)
            ));

        Content::where('status', ContentStatus::PUBLISHED)
            ->with('path', 'metaData')
            ->get()
            ->filter(fn (Content $content) => $content->path?->url)
            ->filter(fn (Content $content) => ! in_array($content->metaData?->robots, [
                MetaDataRobots::NOINDEX_FOLLOW,
                MetaDataRobots::NOINDEX_NOFOLLOW,
            ]))
            ->each(fn (Content $content) => $sitemap->add(
                Url::create('/'.$content->path->url)
                    ->setLastModificationDate($content->updated_at)
            ));

        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated.');
    }

    private function homepageShowsLatestPosts(Content $homepage): bool
    {
        $content = $homepage->getAttributes()['content'] ?? null;
        if (! $content) {
            return false;
        }

        $json = is_string($content) ? json_decode($content, true) : $content;
        if (! is_array($json)) {
            return false;
        }

        return $this->walkFindsLatestBlock($json, ['post_cards_big', 'post_cards_small', 'post_list']);
    }

    private function walkFindsLatestBlock(array $node, array $ids): bool
    {
        if (($node['type'] ?? null) === 'customBlock'
            && in_array($node['attrs']['id'] ?? null, $ids, true)
            && ($node['attrs']['config']['source'] ?? null) === 'latest') {
            return true;
        }

        foreach ($node['content'] ?? [] as $child) {
            if (is_array($child) && $this->walkFindsLatestBlock($child, $ids)) {
                return true;
            }
        }

        return false;
    }
}
