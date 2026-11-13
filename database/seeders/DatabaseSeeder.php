<?php

namespace Database\Seeders;

use App\Services\ThemeService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
        ]);

        if (app()->isLocal()) {
            $this->call([
                DevSeeder::class,
            ]);
        }

        Cache::flush();
        app(ThemeService::class)->generateVariables();
    }
}
