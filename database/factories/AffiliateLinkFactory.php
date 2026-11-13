<?php

namespace Database\Factories;

use App\Models\AffiliateLink;
use App\Models\AffiliateNetwork;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AffiliateLinkFactory extends Factory
{
    protected $model = AffiliateLink::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'affiliate_network_id' => AffiliateNetwork::factory(),
            'name'                 => ucwords($name),
            'slug'                 => Str::slug($name, '-', 'de'),
            'url'                  => fake()->url(),
            'is_active'            => true,
            'clicks'               => 0,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
