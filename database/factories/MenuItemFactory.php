<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Content;
use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class MenuItemFactory extends Factory
{
    protected $model = MenuItem::class;

    public function definition(): array
    {
        $model = fake()->randomElement([
            Content::factory()->create(),
            Category::factory()->create(),
        ]);

        return [
            'menu_id' => Menu::factory(),
            'title' => fake()->words(2, true),
            'model_id' => $model->id,
            'model_type' => $model::class,
            'parent_id' => null,
            'order' => fake()->numberBetween(0, 10),
        ];
    }

    public function withParent(MenuItem $parent): static
    {
        return $this->state([
            'parent_id' => $parent->id,
            'menu_id' => $parent->menu_id,
        ]);
    }

    public function forContent(Content $content): static
    {
        return $this->state([
            'model_id' => $content->id,
            'model_type' => Content::class,
        ]);
    }

    public function forCategory(Category $category): static
    {
        return $this->state([
            'model_id' => $category->id,
            'model_type' => Category::class,
        ]);
    }
}
