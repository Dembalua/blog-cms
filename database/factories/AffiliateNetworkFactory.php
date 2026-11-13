<?php

namespace Database\Factories;

use App\Models\AffiliateNetwork;
use Illuminate\Database\Eloquent\Factories\Factory;

class AffiliateNetworkFactory extends Factory
{
    protected $model = AffiliateNetwork::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement(['Amazon', 'Awin', 'Digistore24', 'ShareASale', 'CJ Affiliate']),
        ];
    }
}
