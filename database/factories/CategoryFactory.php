<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => $name,
            'sort' => fake()->numberBetween(0, 100),
            'active' => fake()->boolean(),
            'description' => fake()->optional()->paragraph(),
            'image_id' => null,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Category $category) {
            $category->path()->create([
                'slug' => Str::slug($category->name, '-', 'de'),
                'url'  => Str::slug($category->name, '-', 'de'),
            ]);
        });
    }

    public function active(): static
    {
        return $this->state(['active' => true]);
    }

    public function inactive(): static
    {
        return $this->state(['active' => false]);
    }
}
