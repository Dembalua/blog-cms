<?php

namespace Database\Seeders\dev;

use App\Models\AffiliateNetwork;
use Illuminate\Database\Seeder;

class AffiliateNetworkSeeder extends Seeder
{
    public function run(): void
    {
        $networks = ['Amazon', 'Awin', 'Digistore24'];

        foreach ($networks as $name) {
            AffiliateNetwork::firstOrCreate(['name' => $name]);
        }
    }
}
