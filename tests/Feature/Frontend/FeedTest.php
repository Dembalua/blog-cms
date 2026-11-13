<?php

namespace Tests\Feature\Frontend;

use App\Models\Category;
use App\Models\Content;
use App\Models\Setting;
use Tests\FilamentTestCase;

class FeedTest extends FilamentTestCase
{
    // -------------------------------------------------------------------------
    // Main Feed
    // -------------------------------------------------------------------------

    public function test_main_feed_returns_rss_xml(): void
    {
        Content::factory()->post()->published()->create();

        $this->get('/rss')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml;charset=UTF-8');
    }

    public function test_main_feed_only_contains_published_posts(): void
    {
        Content::factory()->post()->published()->create(['title' => 'Published Post']);
        Content::factory()->post()->draft()->create(['title' => 'Draft Post']);

        $this->get('/rss')
            ->assertSee('Published Post')
            ->assertDontSee('Draft Post');
    }

    public function test_main_feed_excludes_pages(): void
    {
        Content::factory()->post()->published()->create(['title' => 'A Post']);
        Content::factory()->page()->published()->create(['title' => 'Legal Notice']);

        $this->get('/rss')
            ->assertSee('A Post')
            ->assertDontSee('Legal Notice');
    }

    public function test_main_feed_respects_limit_of_20(): void
    {
        Content::factory()->post()->published()->count(25)->create();

        $response = $this->get('/rss');

        $this->assertEquals(20, substr_count($response->getContent(), '<item>'));
    }

    // -------------------------------------------------------------------------
    // Category Feed
    // -------------------------------------------------------------------------

    public function test_category_feed_returns_rss_xml(): void
    {
        $category = Category::factory()->create();
        Content::factory()->post()->published()->create(['category_id' => $category->id]);

        $this->get('/rss/category/' . $category->path->slug)
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml;charset=UTF-8');
    }

    public function test_category_feed_only_contains_posts_from_that_category(): void
    {
        $category      = Category::factory()->create();
        $otherCategory = Category::factory()->create();

        Content::factory()->post()->published()->create([
            'title'       => 'Matching Post',
            'category_id' => $category->id,
        ]);
        Content::factory()->post()->published()->create([
            'title'       => 'Other Category Post',
            'category_id' => $otherCategory->id,
        ]);

        $this->get('/rss/category/' . $category->path->slug)
            ->assertSee('Matching Post')
            ->assertDontSee('Other Category Post');
    }

    public function test_category_feed_returns_404_for_invalid_slug(): void
    {
        $this->get('/rss/category/does-not-exist')
            ->assertNotFound();
    }

    // -------------------------------------------------------------------------
    // Feed Content
    // -------------------------------------------------------------------------

    public function test_main_feed_uses_title_from_settings(): void
    {
        Setting::factory()->rssTitle('Software Development Feed')->create();
        Content::factory()->post()->published()->create();

        $this->get('/rss')
            ->assertSee('Software Development Feed');
    }

    public function test_feed_item_includes_enclosure_when_post_has_image(): void
    {
        $media = $this->createMedia();
        Content::factory()->post()->published()->create(['feature_image_id' => $media->id]);

        $this->get('/rss')
            ->assertSee('<enclosure', false);
    }

    public function test_feed_item_has_no_enclosure_without_image_or_default(): void
    {
        Content::factory()->post()->published()->create(['feature_image_id' => null]);

        $this->get('/rss')
            ->assertDontSee('<enclosure', false);
    }
}
