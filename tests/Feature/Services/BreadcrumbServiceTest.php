<?php

namespace Tests\Feature\Services;

use App\Models\Category;
use App\Services\BreadcrumbService;
use Tests\FilamentTestCase;

class BreadcrumbServiceTest extends FilamentTestCase
{
    private BreadcrumbService $breadcrumbService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->breadcrumbService = app(BreadcrumbService::class);
    }

    public function test_returns_empty_collection_for_top_level_url(): void
    {
        $result = $this->breadcrumbService->fromUrl('web-development');

        $this->assertCount(0, $result);
    }

    public function test_returns_empty_collection_for_empty_string(): void
    {
        $result = $this->breadcrumbService->fromUrl('');

        $this->assertCount(0, $result);
    }

    public function test_returns_parent_category_for_second_level_url(): void
    {
        $parent = Category::factory()->create();
        $parent->path->update(['url' => 'web-development']);

        $result = $this->breadcrumbService->fromUrl('web-development/tools');

        $this->assertCount(1, $result);
        $this->assertEquals($parent->name, $result[0]['label']);
        $this->assertEquals(url('web-development'), $result[0]['url']);
    }

    public function test_returns_multiple_ancestors_for_deep_url(): void
    {
        $parent = Category::factory()->create();
        $parent->path->update(['url' => 'web-development']);

        $child = Category::factory()->create();
        $child->path->update(['url' => 'web-development/tools']);

        $result = $this->breadcrumbService->fromUrl('web-development/tools/some-post');

        $this->assertCount(2, $result);
        $this->assertEquals($parent->name, $result[0]['label']);
        $this->assertEquals(url('web-development'), $result[0]['url']);
        $this->assertEquals($child->name, $result[1]['label']);
        $this->assertEquals(url('web-development/tools'), $result[1]['url']);
    }

    public function test_ancestors_are_returned_in_order_from_root(): void
    {
        $parent = Category::factory()->create();
        $parent->path->update(['url' => 'a']);

        $child = Category::factory()->create();
        $child->path->update(['url' => 'a/b']);

        $result = $this->breadcrumbService->fromUrl('a/b/c');

        $this->assertEquals($parent->name, $result[0]['label']);
        $this->assertEquals($child->name, $result[1]['label']);
    }

    public function test_skips_prefix_with_no_matching_path(): void
    {
        $parent = Category::factory()->create();
        $parent->path->update(['url' => 'web-development']);

        $result = $this->breadcrumbService->fromUrl('web-development/missing/some-post');

        $this->assertCount(1, $result);
        $this->assertEquals($parent->name, $result[0]['label']);
    }

    public function test_returns_empty_collection_when_no_paths_exist(): void
    {
        $result = $this->breadcrumbService->fromUrl('does-not-exist/neither-does-this');

        $this->assertCount(0, $result);
    }
}
