<?php

namespace Database\Factories;

use App\Enums\ContentStatus;
use App\Enums\ContentType;
use App\Models\Content;
use App\Models\User;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ContentFactory extends Factory
{
    protected $model = Content::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(4);

        return [
            'title' => $title,
            'excerpt' => fake()->optional()->text(100),
            'content' => RichContentRenderer::make('<p>' . implode('</p><p>', fake()->paragraphs(3)) . '</p>')->toArray(),
            'feature_image_id' => null,
            'is_featured' => false,
            'type' => ContentType::POST->value,
            'status' => ContentStatus::DRAFT->value,
            'published_at' => null,
            'author_id' => User::factory(),
            'sort' => fake()->numberBetween(0, 100),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Content $content) {
            $content->path()->create([
                'slug' => Str::slug($content->title, '-', 'de'),
                'url'  => Str::slug($content->title, '-', 'de'),
            ]);
        });
    }

    public function post(): static
    {
        return $this->state(['type' => ContentType::POST->value]);
    }

    public function page(): static
    {
        return $this->state(['type' => ContentType::PAGE->value]);
    }

    public function draft(): static
    {
        return $this->state(['status' => ContentStatus::DRAFT->value]);
    }

    public function published(): static
    {
        return $this->state([
            'status' => ContentStatus::PUBLISHED->value,
            'published_at' => now(),
        ]);
    }

    public function scheduled(): static
    {
        return $this->state([
            'status'       => ContentStatus::SCHEDULED->value,
            'published_at' => now()->subHour(),
        ]);
    }

    public function archived(): static
    {
        return $this->state(['status' => ContentStatus::ARCHIVED->value]);
    }

    public function featured(): static
    {
        return $this->state(['is_featured' => true]);
    }

    public function withAuthor(User $author): static
    {
        return $this->state(['author_id' => $author->id]);
    }
}
