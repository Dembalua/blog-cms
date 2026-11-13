<?php

namespace Tests\Feature\Console;

use App\Models\Category;
use App\Models\Content;
use Database\Factories\MetaDataFactory;
use Illuminate\Support\Facades\Http;
use Tests\FilamentTestCase;

class SubmitIndexNowTest extends FilamentTestCase
{
    private const KEY = 'testkey1234567890abcdef111111111';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.indexnow.key'      => self::KEY,
            'services.indexnow.endpoint' => 'https://api.indexnow.org/indexnow',
            'app.url'                    => 'https://example.test',
        ]);
    }

    protected function tearDown(): void
    {
        $file = public_path(self::KEY . '.txt');
        if (is_file($file)) {
            unlink($file);
        }

        $state = storage_path('app/indexnow-published-key.txt');
        if (is_file($state)) {
            unlink($state);
        }

        parent::tearDown();
    }

    public function test_sends_nothing_when_key_is_not_configured(): void
    {
        config(['services.indexnow.key' => null]);
        Content::factory()->post()->published()->create();
        Http::fake();

        $this->artisan('indexnow:submit')->assertSuccessful();

        Http::assertNothingSent();
    }

    public function test_sends_nothing_when_no_recent_urls(): void
    {
        Http::fake();

        $this->artisan('indexnow:submit')->assertSuccessful();

        Http::assertNothingSent();
    }

    public function test_submits_recently_published_content(): void
    {
        $content = Content::factory()->post()->published()->create();
        Http::fake(['https://api.indexnow.org/*' => Http::response('', 200)]);

        $this->artisan('indexnow:submit')->assertSuccessful();

        Http::assertSent(function ($request) use ($content) {
            return $request->url() === 'https://api.indexnow.org/indexnow'
                && $request['host'] === 'example.test'
                && $request['key'] === 'testkey1234567890abcdef111111111'
                && $request['keyLocation'] === 'https://example.test/testkey1234567890abcdef111111111.txt'
                && in_array('https://example.test/' . $content->path->url, $request['urlList']);
        });
    }

    public function test_excludes_draft_content(): void
    {
        Content::factory()->post()->draft()->create();
        Http::fake();

        $this->artisan('indexnow:submit')->assertSuccessful();

        Http::assertNothingSent();
    }

    public function test_excludes_noindex_content(): void
    {
        $content = Content::factory()->post()->published()->create();
        $content->metaData()->create(
            MetaDataFactory::new()->noIndex()->make()->toArray()
        );
        Http::fake(['https://api.indexnow.org/*' => Http::response('', 200)]);

        $this->artisan('indexnow:submit')->assertSuccessful();

        Http::assertNothingSent();
    }

    public function test_submits_active_categories(): void
    {
        $category = Category::factory()->active()->create();
        Http::fake(['https://api.indexnow.org/*' => Http::response('', 200)]);

        $this->artisan('indexnow:submit')->assertSuccessful();

        Http::assertSent(fn($request) => in_array(
            'https://example.test/' . $category->path->url,
            $request['urlList']
        ));
    }

    public function test_excludes_inactive_categories(): void
    {
        Category::factory()->inactive()->create();
        Http::fake();

        $this->artisan('indexnow:submit')->assertSuccessful();

        Http::assertNothingSent();
    }

    public function test_ignores_content_updated_outside_since_window(): void
    {
        $old = Content::factory()->post()->published()->create();
        Content::withoutTimestamps(fn() => $old->forceFill([
            'updated_at'   => now()->subDays(3),
            'published_at' => now()->subDays(3),
            'revised_at'   => null,
        ])->save());

        Http::fake();

        $this->artisan('indexnow:submit')->assertSuccessful();

        Http::assertNothingSent();
    }
}
