<?php

namespace App\Console\Commands;

use App\Enums\ContentStatus;
use App\Mail\ScheduledContentSkipped;
use App\Models\Content;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class PublishScheduledContent extends Command
{
    protected $signature = 'content:publish-scheduled';

    protected $description = 'Publish scheduled content whose published_at date has been reached';

    public function handle(): void
    {
        $contents = Content::where('status', ContentStatus::SCHEDULED)
            ->where('published_at', '<=', now())
            ->with('path')
            ->get();

        $publishable = $contents->filter(fn (Content $c) => $c->path?->url);
        $skipped = $contents->filter(fn (Content $c) => ! $c->path?->url);

        foreach ($publishable as $content) {
            $content->update(['status' => ContentStatus::PUBLISHED]);
        }

        if ($skipped->isNotEmpty()) {
            Mail::to(config('app.log_mail_to'))->send(new ScheduledContentSkipped($skipped));
            $this->warn("Skipped {$skipped->count()} content(s) without reachable URL — notification sent.");
        }

        $this->info("Published {$publishable->count()} content(s).");
    }
}
