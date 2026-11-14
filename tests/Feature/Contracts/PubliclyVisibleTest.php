<?php

namespace Tests\Feature\Contracts;

use App\Contracts\PubliclyVisible;
use App\Enums\ContentStatus;
use App\Models\Category;
use App\Models\Content;
use Tests\FilamentTestCase;

class PubliclyVisibleTest extends FilamentTestCase
{
    public function test_content_is_publicly_visible_when_published(): void
    {
        $content = Content::factory()->create(['status' => ContentStatus::PUBLISHED]);

        $this->assertInstanceOf(PubliclyVisible::class, $content);
        $this->assertTrue($content->isPubliclyVisible());
    }

    public function test_content_is_not_publicly_visible_when_draft(): void
    {
        $content = Content::factory()->create(['status' => ContentStatus::DRAFT]);

        $this->assertFalse($content->isPubliclyVisible());
    }

    public function test_content_is_not_publicly_visible_when_scheduled(): void
    {
        $content = Content::factory()->create(['status' => ContentStatus::SCHEDULED]);

        $this->assertFalse($content->isPubliclyVisible());
    }

    public function test_content_is_not_publicly_visible_when_archived(): void
    {
        $content = Content::factory()->create(['status' => ContentStatus::ARCHIVED]);

        $this->assertFalse($content->isPubliclyVisible());
    }

    public function test_category_is_publicly_visible_when_active(): void
    {
        $category = Category::factory()->create(['active' => true]);

        $this->assertInstanceOf(PubliclyVisible::class, $category);
        $this->assertTrue($category->isPubliclyVisible());
    }

    public function test_category_is_not_publicly_visible_when_inactive(): void
    {
        $category = Category::factory()->create(['active' => false]);

        $this->assertFalse($category->isPubliclyVisible());
    }

    public function test_published_scope_includes_published_content(): void
    {
        $published = Content::factory()->create(['status' => ContentStatus::PUBLISHED]);

        $this->assertTrue(Content::published()->whereKey($published->id)->exists());
    }

    public function test_published_scope_excludes_draft_scheduled_and_archived(): void
    {
        $draft = Content::factory()->create(['status' => ContentStatus::DRAFT]);
        $scheduled = Content::factory()->create(['status' => ContentStatus::SCHEDULED]);
        $archived = Content::factory()->create(['status' => ContentStatus::ARCHIVED]);

        $ids = Content::published()->pluck('id')->all();

        $this->assertNotContains($draft->id, $ids);
        $this->assertNotContains($scheduled->id, $ids);
        $this->assertNotContains($archived->id, $ids);
    }
}
