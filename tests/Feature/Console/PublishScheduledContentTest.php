<?php

namespace Tests\Feature\Console;

use App\Enums\ContentStatus;
use App\Mail\ScheduledContentSkipped;
use App\Models\Content;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Tests\FilamentTestCase;

class PublishScheduledContentTest extends FilamentTestCase
{
    public function test_publishes_scheduled_content_with_past_published_at(): void
    {
        $content = Content::factory()->post()->scheduled()->create();

        Artisan::call('content:publish-scheduled');

        $this->assertEquals(ContentStatus::PUBLISHED, $content->fresh()->status);
    }

    public function test_ignores_scheduled_content_with_future_published_at(): void
    {
        $content = Content::factory()->post()->scheduled()->create([
            'published_at' => now()->addDay(),
        ]);

        Artisan::call('content:publish-scheduled');

        $this->assertEquals(ContentStatus::SCHEDULED, $content->fresh()->status);
    }

    public function test_ignores_draft_content(): void
    {
        $content = Content::factory()->post()->draft()->create([
            'published_at' => now()->subHour(),
        ]);

        Artisan::call('content:publish-scheduled');

        $this->assertEquals(ContentStatus::DRAFT, $content->fresh()->status);
    }

    public function test_skips_scheduled_content_without_url_and_sends_mail(): void
    {
        Mail::fake();

        $content = Content::factory()->post()->scheduled()->create();
        $content->path()->update(['url' => null]);

        Artisan::call('content:publish-scheduled');

        $this->assertEquals(ContentStatus::SCHEDULED, $content->fresh()->status);
        Mail::assertSent(ScheduledContentSkipped::class);
    }
}
