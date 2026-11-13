<?php

namespace Database\Factories;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Factories\Factory;

class SettingFactory extends Factory
{
    protected $model = Setting::class;

    public function definition(): array
    {
        return [
            'group' => 'site',
            'key'   => 'name',
            'value' => fake()->word(),
            'type'  => 'string',
        ];
    }

    public function siteName(): static
    {
        return $this->state([
            'group' => 'site',
            'key'   => 'name',
            'value' => fake()->company(),
            'type'  => 'string',
        ]);
    }

    public function siteLogo(): static
    {
        return $this->state([
            'group' => 'site',
            'key'   => 'logo',
            'value' => null,
            'type'  => 'integer',
        ]);
    }

    public function siteLogoDark(): static
    {
        return $this->state([
            'group' => 'site',
            'key'   => 'logo_dark',
            'value' => null,
            'type'  => 'integer',
        ]);
    }

    public function siteFavicon(): static
    {
        return $this->state([
            'group' => 'site',
            'key'   => 'favicon',
            'value' => null,
            'type'  => 'integer',
        ]);
    }

    public function menuHeader(): static
    {
        return $this->state([
            'group' => 'menu',
            'key'   => 'header',
            'value' => null,
            'type'  => 'integer',
        ]);
    }

    public function menuFooter(): static
    {
        return $this->state([
            'group' => 'menu',
            'key'   => 'footer',
            'value' => null,
            'type'  => 'integer',
        ]);
    }

    public function footerSlogan(string $slogan = 'Your Slogan'): static
    {
        return $this->state([
            'group' => 'footer',
            'key'   => 'slogan',
            'value' => $slogan,
            'type'  => 'string',
        ]);
    }

    public function seoMeta(): static
    {
        return $this->state([
            'group' => 'seo',
            'key'   => 'meta',
            'value' => null,
            'type'  => 'string',
        ]);
    }

    public function menuMaxDepth(int $depth = 3): static
    {
        return $this->state([
            'group' => 'menu',
            'key'   => 'max_depth',
            'value' => $depth,
            'type'  => 'integer',
        ]);
    }

    public function themeLightPrimary(string $color = '#7C3AED'): static
    {
        return $this->state(['group' => 'theme', 'key' => 'light_primary', 'value' => $color, 'type' => 'string']);
    }

    public function themeDarkPrimary(string $color = '#A78BFA'): static
    {
        return $this->state(['group' => 'theme', 'key' => 'dark_primary', 'value' => $color, 'type' => 'string']);
    }

    public function themeLightBackground(string $color = '#FAF5FF'): static
    {
        return $this->state(['group' => 'theme', 'key' => 'light_background', 'value' => $color, 'type' => 'string']);
    }

    public function themeDarkBackground(string $color = '#100D1A'): static
    {
        return $this->state(['group' => 'theme', 'key' => 'dark_background', 'value' => $color, 'type' => 'string']);
    }

    public function maintenanceEnabled(bool $enabled = true): static
    {
        return $this->state([
            'group' => 'maintenance',
            'key'   => 'enabled',
            'value' => $enabled ? 1 : 0,
            'type'  => 'boolean',
        ]);
    }

    public function maintenanceAllowedIps(string $ips = ''): static
    {
        return $this->state([
            'group' => 'maintenance',
            'key'   => 'allowed_ips',
            'value' => $ips,
            'type'  => 'string',
        ]);
    }

    public function maintenancePages(array $contentIds = []): static
    {
        return $this->state([
            'group' => 'maintenance',
            'key'   => 'pages',
            'value' => json_encode($contentIds),
            'type'  => 'string',
        ]);
    }

    public function categoryPageSize(int $size = 12): static
    {
        return $this->state([
            'group' => 'content',
            'key'   => 'category_page_size',
            'value' => $size,
            'type'  => 'integer',
        ]);
    }

    public function rssTitle(string $title = 'My Feed'): static
    {
        return $this->state([
            'group' => 'rss',
            'key'   => 'title',
            'value' => $title,
            'type'  => 'string',
        ]);
    }

    public function rssDescription(string $description = 'My description.'): static
    {
        return $this->state([
            'group' => 'rss',
            'key'   => 'description',
            'value' => $description,
            'type'  => 'string',
        ]);
    }
}
