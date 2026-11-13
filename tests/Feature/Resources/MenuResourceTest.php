<?php

namespace Tests\Feature\Resources;

use App\Filament\Resources\Menus\Pages\EditMenu;
use App\Filament\Resources\Menus\Pages\ListMenus;
use App\Filament\Resources\Menus\Widgets\MenuItemWidget;
use App\Models\Category;
use App\Models\Content;
use App\Models\Menu;
use App\Models\MenuItem;
use Livewire\Livewire;
use Tests\FilamentTestCase;

class MenuResourceTest extends FilamentTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAsSuperAdmin();
    }

    // -------------------------------------------------------------------------
    // List Page
    // -------------------------------------------------------------------------

    public function test_can_load_list_page(): void
    {
        $menus = Menu::factory()->count(3)->create();

        Livewire::test(ListMenus::class)
            ->assertOk()
            ->assertCanSeeTableRecords($menus);
    }

    public function test_can_search_menus_by_name(): void
    {
        Menu::factory()->create(['name' => 'Header Menu']);
        Menu::factory()->create(['name' => 'Footer Menu']);
        Menu::factory()->create(['name' => 'Sidebar Menu']);

        Livewire::test(ListMenus::class)
            ->searchTable('Header Menu')
            ->assertCanSeeTableRecords(Menu::where('name', 'Header Menu')->get())
            ->assertCanNotSeeTableRecords(Menu::whereIn('name', ['Footer Menu', 'Sidebar Menu'])->get());
    }

    // -------------------------------------------------------------------------
    // Create (Modal)
    // -------------------------------------------------------------------------

    public function test_can_create_menu(): void
    {
        Livewire::test(ListMenus::class)
            ->callAction('create', data: [
                'name' => 'Main Navigation',
            ])
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('menus', [
            'name' => 'Main Navigation',
        ]);
    }

    public function test_name_is_required_on_create(): void
    {
        Livewire::test(ListMenus::class)
            ->callAction('create', data: [
                'name' => null,
            ])
            ->assertHasFormErrors(['name' => 'required']);
    }

    public function test_name_must_be_unique_on_create(): void
    {
        Menu::factory()->create(['name' => 'Existing Menu']);

        Livewire::test(ListMenus::class)
            ->callAction('create', data: ['name' => 'Existing Menu'])
            ->assertHasFormErrors(['name' => 'unique']);
    }

    public function test_name_max_length_is_validated_on_create(): void
    {
        Livewire::test(ListMenus::class)
            ->callAction('create', data: [
                'name' => str_repeat('a', 81),
            ])
            ->assertHasFormErrors(['name' => 'max']);
    }

    // -------------------------------------------------------------------------
    // Edit Page
    // -------------------------------------------------------------------------

    public function test_can_load_edit_page(): void
    {
        $menu = Menu::factory()->create();

        Livewire::test(EditMenu::class, ['record' => $menu->getRouteKey()])
            ->assertOk()
            ->assertSchemaStateSet([
                'name' => $menu->name,
            ]);
    }

    public function test_can_update_menu_name(): void
    {
        $menu = Menu::factory()->create();

        Livewire::test(EditMenu::class, ['record' => $menu->getRouteKey()])
            ->fillForm(['name' => 'Updated Name'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('menus', [
            'id' => $menu->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_name_is_required_on_edit(): void
    {
        $menu = Menu::factory()->create();

        Livewire::test(EditMenu::class, ['record' => $menu->getRouteKey()])
            ->fillForm(['name' => null])
            ->call('save')
            ->assertHasFormErrors(['name' => 'required']);
    }

    public function test_name_can_be_unchanged_on_edit(): void
    {
        $menu = Menu::factory()->create(['name' => 'My Menu']);

        Livewire::test(EditMenu::class, ['record' => $menu->getRouteKey()])
            ->fillForm(['name' => 'My Menu'])
            ->call('save')
            ->assertHasNoFormErrors();
    }

    public function test_name_must_be_unique_on_edit(): void
    {
        Menu::factory()->create(['name' => 'Taken Name']);
        $menu = Menu::factory()->create(['name' => 'My Menu']);

        Livewire::test(EditMenu::class, ['record' => $menu->getRouteKey()])
            ->fillForm(['name' => 'Taken Name'])
            ->call('save')
            ->assertHasFormErrors(['name' => 'unique']);
    }

    // -------------------------------------------------------------------------
    // Widget
    // -------------------------------------------------------------------------

    public function test_widget_is_visible_on_edit_page(): void
    {
        $menu = Menu::factory()->create();

        Livewire::test(EditMenu::class, ['record' => $menu->getRouteKey()])
            ->assertSeeLivewire(MenuItemWidget::class);
    }

    public function test_widget_shows_only_items_for_current_menu(): void
    {
        $menu = Menu::factory()->create();
        $otherMenu = Menu::factory()->create();
        $ownContent = Content::factory()->page()->create();
        $foreignContent = Content::factory()->page()->create();
        $ownItem = MenuItem::factory()->forContent($ownContent)->create(['menu_id' => $menu->id]);
        $foreignItem = MenuItem::factory()->forContent($foreignContent)->create(['menu_id' => $otherMenu->id]);

        Livewire::test(MenuItemWidget::class, ['record' => $menu])
            ->assertSee($ownItem->title)
            ->assertDontSee($foreignItem->title);
    }

    public function test_can_create_menu_item_with_page(): void
    {
        $menu = Menu::factory()->create();
        $content = Content::factory()->page()->create();

        $item = MenuItem::create([
            'menu_id' => $menu->id,
            'title' => $content->title,
            'model_id' => $content->id,
            'model_type' => Content::class,
            'order' => 0,
        ]);

        $this->assertDatabaseHas('menu_items', [
            'menu_id' => $menu->id,
            'model_id' => $content->id,
            'model_type' => Content::class,
            'title' => $content->title,
        ]);
    }

    public function test_can_create_menu_item_with_category(): void
    {
        $menu = Menu::factory()->create();
        $category = Category::factory()->create();

        MenuItem::create([
            'menu_id' => $menu->id,
            'title' => $category->name,
            'model_id' => $category->id,
            'model_type' => Category::class,
            'order' => 0,
        ]);

        $this->assertDatabaseHas('menu_items', [
            'menu_id' => $menu->id,
            'model_id' => $category->id,
            'model_type' => Category::class,
        ]);
    }

    public function test_can_create_nested_menu_item(): void
    {
        $menu = Menu::factory()->create();
        $content = Content::factory()->page()->create();
        $parent = MenuItem::factory()->forContent($content)->create(['menu_id' => $menu->id]);
        $child = Content::factory()->page()->create();

        MenuItem::create([
            'menu_id' => $menu->id,
            'title' => $child->title,
            'model_id' => $child->id,
            'model_type' => Content::class,
            'parent_id' => $parent->id,
            'order' => 0,
        ]);

        $this->assertDatabaseHas('menu_items', [
            'menu_id' => $menu->id,
            'parent_id' => $parent->id,
            'title' => $child->title,
        ]);
    }

    public function test_can_delete_menu_item(): void
    {
        $menu = Menu::factory()->create();
        $content = Content::factory()->page()->create();
        $item = MenuItem::factory()->forContent($content)->create(['menu_id' => $menu->id]);

        $item->delete();

        $this->assertModelMissing($item);
    }

    public function test_deleting_menu_cascades_to_items(): void
    {
        $menu = Menu::factory()->create();
        $content = Content::factory()->page()->create();
        $item = MenuItem::factory()->forContent($content)->create(['menu_id' => $menu->id]);

        $menu->delete();

        $this->assertModelMissing($item);
    }

    public function test_deleting_parent_item_cascades_to_children(): void
    {
        $menu = Menu::factory()->create();
        $parentContent = Content::factory()->page()->create();
        $childContent = Content::factory()->page()->create();
        $parent = MenuItem::factory()->forContent($parentContent)->create(['menu_id' => $menu->id]);
        $child = MenuItem::factory()->withParent($parent)->forContent($childContent)->create();

        $parent->delete();

        $this->assertModelMissing($child);
    }
}
