<?php

namespace Tests\Feature\Console;

use App\Models\Category;
use App\Models\Content;
use Illuminate\Support\Facades\Artisan;
use Tests\FilamentTestCase;

class GenerateSitemapTest extends FilamentTestCase
{
    protected function tearDown(): void
    {
        if (file_exists(public_path('sitemap.xml'))) {
            unlink(public_path('sitemap.xml'));
        }

        parent::tearDown();
    }

    public function test_command_creates_sitemap_file(): void
    {
        Artisan::call('sitemap:generate');

        $this->assertFileExists(public_path('sitemap.xml'));
    }

    public function test_sitemap_contains_published_content(): void
    {
        $content = Content::factory()->post()->published()->create();

        Artisan::call('sitemap:generate');

        $this->assertStringContainsString(
            $content->path->url,
            file_get_contents(public_path('sitemap.xml'))
        );
    }

    public function test_sitemap_excludes_draft_content(): void
    {
        $content = Content::factory()->post()->draft()->create();

        Artisan::call('sitemap:generate');

        $this->assertStringNotContainsString(
            $content->path->url,
            file_get_contents(public_path('sitemap.xml'))
        );
    }

    public function test_sitemap_excludes_noindex_content(): void
    {
        $content = Content::factory()->post()->published()->create();
        $content->metaData()->create(
            \Database\Factories\MetaDataFactory::new()->noIndex()->make()->toArray()
        );

        Artisan::call('sitemap:generate');

        $this->assertStringNotContainsString(
            $content->path->url,
            file_get_contents(public_path('sitemap.xml'))
        );
    }

    public function test_sitemap_contains_active_categories(): void
    {
        $category = Category::factory()->active()->create();

        Artisan::call('sitemap:generate');

        $this->assertStringContainsString(
            $category->path->url,
            file_get_contents(public_path('sitemap.xml'))
        );
    }

    public function test_sitemap_excludes_inactive_categories(): void
    {
        $category = Category::factory()->inactive()->create();

        Artisan::call('sitemap:generate');

        $this->assertStringNotContainsString(
            $category->path->url,
            file_get_contents(public_path('sitemap.xml'))
        );
    }

    public function test_sitemap_excludes_noindex_categories(): void
    {
        $category = Category::factory()->active()->create();
        $category->metaData()->create(
            \Database\Factories\MetaDataFactory::new()->noIndex()->make()->toArray()
        );

        Artisan::call('sitemap:generate');

        $this->assertStringNotContainsString(
            $category->path->url,
            file_get_contents(public_path('sitemap.xml'))
        );
    }
}
