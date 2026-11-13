<?php

namespace App\Console\Commands;

use App\Enums\ContentStatus;
use App\Enums\MetaDataRobots;
use App\Models\Category;
use App\Models\Content;
use App\Services\IndexNowService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SubmitIndexNow extends Command
{
    protected $signature = 'indexnow:submit {--since=24 : Hours to look back}';

    protected $description = 'Submit recently changed URLs to IndexNow';

    public function handle(IndexNowService $service): int
    {
        $service->publishKeyFile();

        if (! $service->isEnabled()) {
            $this->info('IndexNow disabled (no INDEXNOW_KEY) — skipping.');

            return self::SUCCESS;
        }

        $since = Carbon::now()->subHours((int) $this->option('since'));
        $base  = rtrim(config('app.url'), '/');

        $noIndex = [MetaDataRobots::NOINDEX_FOLLOW, MetaDataRobots::NOINDEX_NOFOLLOW];

        $categoryUrls = Category::active()
            ->with('path', 'metaData')
            ->where('updated_at', '>=', $since)
            ->get()
            ->filter(fn(Category $c) => $c->path?->url)
            ->filter(fn(Category $c) => ! in_array($c->metaData?->robots, $noIndex))
            ->map(fn(Category $c) => $base . '/' . $c->path->url);

        $contentUrls = Content::where('status', ContentStatus::PUBLISHED)
            ->with('path', 'metaData')
            ->where(function ($q) use ($since) {
                $q->where('revised_at', '>=', $since)
                    ->orWhere('published_at', '>=', $since)
                    ->orWhere('updated_at', '>=', $since);
            })
            ->get()
            ->filter(fn(Content $c) => $c->path?->url)
            ->filter(fn(Content $c) => ! in_array($c->metaData?->robots, $noIndex))
            ->map(fn(Content $c) => $base . '/' . $c->path->url);

        $urls = collect()
            ->concat($categoryUrls)
            ->concat($contentUrls)
            ->unique()
            ->values()
            ->all();

        if (empty($urls)) {
            $this->info('No URLs to submit.');

            return self::SUCCESS;
        }

        foreach (array_chunk($urls, 10000) as $chunk) {
            $service->submit($chunk);
        }

        $this->info('Submitted ' . count($urls) . ' URL(s) to IndexNow.');

        return self::SUCCESS;
    }
}
