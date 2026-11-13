<?php

namespace Database\Seeders;

use Database\Seeders\dev\AffiliateLinkClickSeeder;
use Database\Seeders\dev\AffiliateLinkSeeder;
use Database\Seeders\dev\AffiliateNetworkSeeder;
use Database\Seeders\dev\CategorySeeder;
use Database\Seeders\dev\ClusterSeeder;
use Database\Seeders\dev\ContentSeeder;
use Database\Seeders\dev\MediaSeeder;
use Database\Seeders\dev\MenuSeeder;
use Database\Seeders\dev\SettingSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DevSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            MediaSeeder::class,
            CategorySeeder::class,
            ClusterSeeder::class,
            AffiliateNetworkSeeder::class,
            AffiliateLinkSeeder::class,
            ContentSeeder::class,
            MenuSeeder::class,
            SettingSeeder::class,
            AffiliateLinkClickSeeder::class,
        ]);
    }
}
