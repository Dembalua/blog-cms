<?php

namespace Tests\Feature\Services;

use App\Models\Category;
use App\Models\Content;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Services\CategoryPageService;
use Illuminate\Support\Facades\Cache;
use Tests\FilamentTestCase;

class CategoryPageServiceTest extends FilamentTestCase
{
    private CategoryPageService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new CategoryPageService;
    }

    public function test_returns_direct_contents_via_category_id(): void
    {
        $category = Category::factory()->active()->create();
        $post = Content::factory()->post()->published()->create(['category_id' => $category->id]);

        $data = $this->service->buildPageData($category);

        $this->assertTrue($data['contents']->contains('id', $post->id));
    }

    public function test_sub_categories_are_loaded_from_menu_tree(): void
    {
        $category = Category::factory()->active()->create();
        $subA = Category::factory()->active()->create();
        $subB = Category::factory()->active()->create();

        $menu = Menu::factory()->create();
        $root = MenuItem::factory()->forCategory($category)->create(['menu_id' => $menu->id]);
        MenuItem::factory()->forCategory($subA)->withParent($root)->create();
        MenuItem::factory()->forCategory($subB)->withParent($root)->create();

        $data = $this->service->buildPageData($category);

        $this->assertCount(2, $data['subCategories']);
        $this->assertTrue($data['subCategories']->contains('id', $subA->id));
        $this->assertTrue($data['subCategories']->contains('id', $subB->id));
    }

    public function test_grandchildren_are_resolved_in_nav_tree(): void
    {
        $category = Category::factory()->active()->create();
        $sub = Category::factory()->active()->create();
        $grandchildA = Category::factory()->active()->create();
        $grandchildB = Category::factory()->active()->create();

        $menu = Menu::factory()->create();
        $root = MenuItem::factory()->forCategory($category)->create(['menu_id' => $menu->id]);
        $subItem = MenuItem::factory()->forCategory($sub)->withParent($root)->create();
        MenuItem::factory()->forCategory($grandchildA)->withParent($subItem)->create();
        MenuItem::factory()->forCategory($grandchildB)->withParent($subItem)->create();

        $data = $this->service->buildPageData($category);

        $navNode = $data['navTree']->first();
        $this->assertSame($sub->id, $navNode['category']->id);
        $this->assertCount(2, $navNode['children']);
        $this->assertTrue($navNode['children']->contains('id', $grandchildA->id));
        $this->assertTrue($navNode['children']->contains('id', $grandchildB->id));
    }

    public function test_direct_content_in_menu_is_included_via_direct_ids(): void
    {
        $category = Category::factory()->active()->create();
        $directPage = Content::factory()->page()->published()->create();

        $menu = Menu::factory()->create();
        $root = MenuItem::factory()->forCategory($category)->create(['menu_id' => $menu->id]);
        MenuItem::factory()->forContent($directPage)->withParent($root)->create();

        $data = $this->service->buildPageData($category);

        $this->assertTrue($data['contents']->contains('id', $directPage->id));
    }

    public function test_grandchild_content_in_menu_is_included(): void
    {
        $category = Category::factory()->active()->create();
        $sub = Category::factory()->active()->create();
        $grandchildPage = Content::factory()->page()->published()->create();

        $menu = Menu::factory()->create();
        $root = MenuItem::factory()->forCategory($category)->create(['menu_id' => $menu->id]);
        $subItem = MenuItem::factory()->forCategory($sub)->withParent($root)->create();
        MenuItem::factory()->forContent($grandchildPage)->withParent($subItem)->create();

        $data = $this->service->buildPageData($category);

        $this->assertTrue($data['contents']->contains('id', $grandchildPage->id));
    }

    public function test_category_map_contains_root_sub_and_grandchildren(): void
    {
        $category = Category::factory()->active()->create();
        $sub = Category::factory()->active()->create();
        $grandchild = Category::factory()->active()->create();

        $menu = Menu::factory()->create();
        $root = MenuItem::factory()->forCategory($category)->create(['menu_id' => $menu->id]);
        $subItem = MenuItem::factory()->forCategory($sub)->withParent($root)->create();
        MenuItem::factory()->forCategory($grandchild)->withParent($subItem)->create();

        $data = $this->service->buildPageData($category);

        $this->assertArrayHasKey($category->id, $data['categoryMap']);
        $this->assertArrayHasKey($sub->id, $data['categoryMap']);
        $this->assertArrayHasKey($grandchild->id, $data['categoryMap']);
    }

    public function test_menu_ids_are_cached_after_first_call(): void
    {
        $category = Category::factory()->active()->create();

        $this->assertFalse(Cache::has("category_menu_ids_v3_{$category->id}"));

        $this->service->buildPageData($category);

        $this->assertTrue(Cache::has("category_menu_ids_v3_{$category->id}"));
    }
}
