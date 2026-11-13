<?php

namespace Tests\Feature\Services;

use App\Models\Category;
use App\Models\MenuItem;
use App\Services\PathService;
use Tests\FilamentTestCase;

class PathServiceTest extends FilamentTestCase
{
    private PathService $pathService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pathService = app(PathService::class);
    }

    public function test_generates_url_for_top_level_menu_item(): void
    {
        $category = Category::factory()->create();
        $menuItem = MenuItem::factory()->forCategory($category)->create(['parent_id' => null]);

        $this->pathService->generateForMenuItem($menuItem);

        $this->assertDatabaseHas('paths', [
            'model_type' => Category::class,
            'model_id'   => $category->id,
            'url'        => $category->path->slug,
        ]);
    }

    public function test_generates_hierarchical_url_for_nested_menu_item(): void
    {
        $parent = Category::factory()->create();
        $child = Category::factory()->create();

        $parentItem = MenuItem::factory()->forCategory($parent)->create(['parent_id' => null]);
        $childItem  = MenuItem::factory()->forCategory($child)->withParent($parentItem)->create();

        $this->pathService->generateForMenuItem($childItem);

        $this->assertDatabaseHas('paths', [
            'model_type' => Category::class,
            'model_id'   => $child->id,
            'url'        => $parent->path->slug . '/' . $child->path->slug,
        ]);
    }

    public function test_generates_urls_recursively_for_all_descendants(): void
    {
        $parent     = Category::factory()->create();
        $child      = Category::factory()->create();
        $grandchild = Category::factory()->create();

        $parentItem     = MenuItem::factory()->forCategory($parent)->create(['parent_id' => null]);
        $childItem      = MenuItem::factory()->forCategory($child)->withParent($parentItem)->create();
        $grandchildItem = MenuItem::factory()->forCategory($grandchild)->withParent($childItem)->create();

        $this->pathService->generateForMenuItem($parentItem);

        $this->assertDatabaseHas('paths', [
            'model_type' => Category::class,
            'model_id'   => $child->id,
            'url'        => $parent->path->slug . '/' . $child->path->slug,
        ]);

        $this->assertDatabaseHas('paths', [
            'model_type' => Category::class,
            'model_id'   => $grandchild->id,
            'url'        => $parent->path->slug . '/' . $child->path->slug . '/' . $grandchild->path->slug,
        ]);
    }

    public function test_does_nothing_when_model_has_no_menu_item(): void
    {
        $category    = Category::factory()->create();
        $originalUrl = $category->path->url;

        $this->pathService->generateForModel($category);

        $this->assertDatabaseHas('paths', [
            'model_type' => Category::class,
            'model_id'   => $category->id,
            'url'        => $originalUrl,
        ]);
    }
}
