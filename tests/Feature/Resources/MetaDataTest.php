<?php

namespace Tests\Feature\Resources;

use App\Enums\MetaDataRobots;
use App\Filament\Resources\Categories\Pages\EditCategory;
use App\Filament\Resources\Contents\Pages\EditContent;
use App\Models\Category;
use App\Models\Content;
use App\Models\MetaData;
use Livewire\Livewire;
use Tests\FilamentTestCase;

class MetaDataTest extends FilamentTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAsSuperAdmin();
    }

    // -------------------------------------------------------------------------
    // Content
    // -------------------------------------------------------------------------

    public function test_can_save_meta_data_for_content(): void
    {
        $content = Content::factory()->create();

        Livewire::test(EditContent::class, ['record' => $content->getRouteKey()])
            ->fillForm([
                'metaData.meta_title' => 'Test Meta Title',
                'metaData.meta_description' => 'Test Meta Description',
                'metaData.robots' => MetaDataRobots::INDEX_FOLLOW,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('meta_data', [
            'model_type' => Content::class,
            'model_id' => $content->id,
            'meta_title' => 'Test Meta Title',
            'meta_description' => 'Test Meta Description',
        ]);
    }

    public function test_meta_data_is_created_as_morph_relation_for_content(): void
    {
        $content = Content::factory()->create();

        Livewire::test(EditContent::class, ['record' => $content->getRouteKey()])
            ->fillForm([
                'metaData.meta_title' => 'Morph Test',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertNotNull($content->fresh()->metaData);
        $this->assertEquals('Morph Test', $content->fresh()->metaData->meta_title);
    }

    public function test_can_update_meta_data_for_content(): void
    {
        $content = Content::factory()->create();
        $content->metaData()->create(
            MetaData::factory()->make()->toArray()
        );

        Livewire::test(EditContent::class, ['record' => $content->getRouteKey()])
            ->fillForm([
                'metaData.meta_title' => 'Updated Meta Title',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertEquals('Updated Meta Title', $content->fresh()->metaData->meta_title);
    }

    public function test_meta_title_max_length_for_content(): void
    {
        $content = Content::factory()->create();

        Livewire::test(EditContent::class, ['record' => $content->getRouteKey()])
            ->fillForm([
                'metaData.meta_title' => str_repeat('a', 61),
            ])
            ->call('save')
            ->assertHasFormErrors(['metaData.meta_title']);
    }

    public function test_meta_description_max_length_for_content(): void
    {
        $content = Content::factory()->create();

        Livewire::test(EditContent::class, ['record' => $content->getRouteKey()])
            ->fillForm([
                'metaData.meta_description' => str_repeat('a', 161),
            ])
            ->call('save')
            ->assertHasFormErrors(['metaData.meta_description']);
    }

    // -------------------------------------------------------------------------
    // Category
    // -------------------------------------------------------------------------

    public function test_can_save_meta_data_for_category(): void
    {
        $category = Category::factory()->create();

        Livewire::test(EditCategory::class, ['record' => $category->getRouteKey()])
            ->fillForm([
                'metaData.meta_title' => 'Category Meta Title',
                'metaData.meta_description' => 'Category Meta Description',
                'metaData.robots' => MetaDataRobots::INDEX_FOLLOW,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('meta_data', [
            'model_type' => Category::class,
            'model_id' => $category->id,
            'meta_title' => 'Category Meta Title',
        ]);
    }

    public function test_meta_data_is_created_as_morph_relation_for_category(): void
    {
        $category = Category::factory()->create();

        Livewire::test(EditCategory::class, ['record' => $category->getRouteKey()])
            ->fillForm([
                'metaData.meta_title' => 'Category Morph Test',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertNotNull($category->fresh()->metaData);
        $this->assertEquals('Category Morph Test', $category->fresh()->metaData->meta_title);
    }
}
