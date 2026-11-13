<?php

namespace Tests\Feature\Observers;

use App\Models\Category;
use App\Models\Content;
use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Validation\ValidationException;
use Tests\FilamentTestCase;

class ContentMenuConstraintTest extends FilamentTestCase
{
    public function test_categorized_content_cannot_be_added_to_menu(): void
    {
        $category = Category::factory()->create();
        $content = Content::factory()->create(['category_id' => $category->id]);
        $menu = Menu::factory()->create();

        $this->expectException(ValidationException::class);

        MenuItem::factory()->forContent($content)->create(['menu_id' => $menu->id]);
    }

    public function test_content_without_category_can_be_added_to_menu(): void
    {
        $content = Content::factory()->create(['category_id' => null]);
        $menu = Menu::factory()->create();

        $item = MenuItem::factory()->forContent($content)->create(['menu_id' => $menu->id]);

        $this->assertDatabaseHas('menu_items', ['id' => $item->id]);
    }

    public function test_category_cannot_be_assigned_to_menu_linked_content(): void
    {
        $content = Content::factory()->create(['category_id' => null]);
        MenuItem::factory()->forContent($content)->create();
        $category = Category::factory()->create();

        $this->expectException(ValidationException::class);

        $content->update(['category_id' => $category->id]);
    }

    public function test_category_can_be_assigned_to_content_not_in_menu(): void
    {
        $content = Content::factory()->create(['category_id' => null]);
        $category = Category::factory()->create();

        $content->update(['category_id' => $category->id]);

        $this->assertDatabaseHas('contents', [
            'id'          => $content->id,
            'category_id' => $category->id,
        ]);
    }

    public function test_removing_category_from_menu_linked_content_is_allowed(): void
    {
        $category = Category::factory()->create();
        $content = Content::factory()->create(['category_id' => $category->id]);

        $content->update(['category_id' => null]);

        MenuItem::factory()->forContent($content)->create();

        $this->assertDatabaseHas('contents', [
            'id'          => $content->id,
            'category_id' => null,
        ]);
    }
}
