<?php

namespace Tests\Feature\Resources;

use App\Filament\Resources\Categories\Pages\CreateCategory;
use App\Filament\Resources\Categories\Pages\EditCategory;
use App\Filament\Resources\Categories\Pages\ListCategories;
use App\Models\Category;
use Filament\Actions\DeleteAction;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\FilamentTestCase;

class CategoryResourceTest extends FilamentTestCase
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
        $categories = Category::factory()->count(5)->create();

        Livewire::test(ListCategories::class)
            ->assertOk()
            ->assertCanSeeTableRecords($categories);
    }

    public function test_can_search_categories_by_name(): void
    {
        $target = Category::factory()->create(['name' => 'JavaScript Frameworks']);
        $others = collect([
            Category::factory()->create(['name' => 'Database Design']),
            Category::factory()->create(['name' => 'Cloud Services']),
            Category::factory()->create(['name' => 'Mobile Apps']),
            Category::factory()->create(['name' => 'Testing Tools']),
        ]);

        Livewire::test(ListCategories::class)
            ->searchTable('JavaScript Frameworks')
            ->assertCanSeeTableRecords([$target])
            ->assertCanNotSeeTableRecords($others);
    }

    public function test_can_filter_categories_by_active(): void
    {
        $active = Category::factory()->active()->count(3)->create();
        $inactive = Category::factory()->inactive()->count(3)->create();

        Livewire::test(ListCategories::class)
            ->filterTable('active', '1')
            ->assertCanSeeTableRecords($active)
            ->assertCanNotSeeTableRecords($inactive);
    }

    public function test_can_sort_categories_by_name(): void
    {
        Category::factory()->count(5)->create();

        $sortedAsc = Category::query()->orderBy('name')->get();
        $sortedDesc = Category::query()->orderBy('name', 'desc')->get();

        Livewire::test(ListCategories::class)
            ->sortTable('name')
            ->assertCanSeeTableRecords($sortedAsc, inOrder: true)
            ->sortTable('name', 'desc')
            ->assertCanSeeTableRecords($sortedDesc, inOrder: true);
    }

    // -------------------------------------------------------------------------
    // Create Page
    // -------------------------------------------------------------------------

    public function test_can_load_create_page(): void
    {
        Livewire::test(CreateCategory::class)
            ->assertOk();
    }

    public function test_can_create_category(): void
    {
        $data = Category::factory()->make();

        Livewire::test(CreateCategory::class)
            ->fillForm([
                'name'      => $data->name,
                'path.slug' => Str::slug($data->name),
                'active'    => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('categories', [
            'name' => $data->name,
        ]);

        $category = Category::where('name', $data->name)->first();

        $this->assertDatabaseHas('paths', [
            'model_type' => Category::class,
            'model_id'   => $category->id,
            'slug'       => Str::slug($data->name),
        ]);
    }

    public function test_name_is_required(): void
    {
        Livewire::test(CreateCategory::class)
            ->fillForm(['name' => null])
            ->call('create')
            ->assertHasFormErrors(['name' => 'required']);
    }

    public function test_name_must_be_unique(): void
    {
        Category::factory()->create(['name' => 'Existing Category']);

        Livewire::test(CreateCategory::class)
            ->fillForm(['name' => 'Existing Category', 'path.slug' => 'existing-category'])
            ->call('create')
            ->assertHasFormErrors(['name' => 'unique']);
    }

    public function test_name_can_be_unchanged_on_edit(): void
    {
        $category = Category::factory()->create(['name' => 'My Category']);

        Livewire::test(EditCategory::class, ['record' => $category->getRouteKey()])
            ->fillForm(['name' => 'My Category'])
            ->call('save')
            ->assertHasNoFormErrors();
    }

    public function test_name_must_be_unique_on_edit(): void
    {
        Category::factory()->create(['name' => 'Taken Name']);
        $category = Category::factory()->create(['name' => 'My Category']);

        Livewire::test(EditCategory::class, ['record' => $category->getRouteKey()])
            ->fillForm(['name' => 'Taken Name'])
            ->call('save')
            ->assertHasFormErrors(['name' => 'unique']);
    }

    public function test_slug_is_required(): void
    {
        Livewire::test(CreateCategory::class)
            ->fillForm([
                'name'      => 'Some Category',
                'path.slug' => null,
            ])
            ->call('create')
            ->assertHasFormErrors(['path.slug' => 'required']);
    }

    public function test_slug_is_auto_generated_from_name(): void
    {
        $name = 'My Test Category';

        Livewire::test(CreateCategory::class)
            ->fillForm(['name' => $name])
            ->assertSchemaStateSet([
                'path.slug' => Str::slug($name),
            ]);
    }

    // -------------------------------------------------------------------------
    // Edit Page
    // -------------------------------------------------------------------------

    public function test_can_load_edit_page(): void
    {
        $category = Category::factory()->create();

        Livewire::test(EditCategory::class, ['record' => $category->getRouteKey()])
            ->assertOk()
            ->assertSchemaStateSet([
                'name'      => $category->name,
                'path.slug' => $category->path->slug,
            ]);
    }

    public function test_can_update_category(): void
    {
        $category = Category::factory()->create();

        Livewire::test(EditCategory::class, ['record' => $category->getRouteKey()])
            ->fillForm(['name' => 'Updated Name'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('categories', [
            'id'   => $category->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_can_delete_category(): void
    {
        $category = Category::factory()->create();
        $url = $category->path->url;

        Livewire::test(EditCategory::class, ['record' => $category->getRouteKey()])
            ->callAction(DeleteAction::class);

        $this->assertModelMissing($category);

        $this->assertDatabaseMissing('paths', [
            'model_type' => Category::class,
            'model_id'   => $category->id,
        ]);

        $this->assertDatabaseHas('path_redirects', [
            'old_path'    => $url,
            'new_path'    => null,
            'status_code' => 410,
        ]);
    }
}
