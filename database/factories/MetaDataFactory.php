<?php

namespace Database\Factories;

use App\Enums\MetaDataRobots;
use App\Models\MetaData;
use Illuminate\Database\Eloquent\Factories\Factory;

class MetaDataFactory extends Factory
{
    protected $model = MetaData::class;

    public function definition(): array
    {
        return [
            'meta_title' => fake()->optional()->sentence(5),
            'meta_description' => fake()->optional()->text(150),
            'meta_keywords' => fake()->optional()->randomElements(['technology', 'laravel', 'filament', 'framework', 'development'], 3),
            'og_title' => fake()->optional()->sentence(5),
            'og_description' => fake()->optional()->text(190),
            'og_image_id' => null,
            'robots' => MetaDataRobots::INDEX_FOLLOW,
        ];
    }

    public function noIndex(): static
    {
        return $this->state(['robots' => MetaDataRobots::NOINDEX_NOFOLLOW]);
    }
}
