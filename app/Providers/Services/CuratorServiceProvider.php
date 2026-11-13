<?php

namespace App\Providers\Services;

use App\Models\Media;
use App\Policies\MediaPolicy;
use Awcodes\Curator\Curations\CurationPreset;
use Awcodes\Curator\Facades\Curation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class CuratorServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Curation::presets([
            CurationPreset::make('og')
                ->width(1200)
                ->height(630)
                ->format('webp')
                ->quality(85),

            CurationPreset::make('card')
                ->width(560)
                ->height(240)
                ->format('webp')
                ->quality(80),

            CurationPreset::make('hero')
                ->width(1920)
                ->height(480)
                ->format('webp')
                ->quality(85),

            CurationPreset::make('homepage_hero')
                ->width(1100)
                ->height(700)
                ->format('webp')
                ->quality(85),

            CurationPreset::make('pinterest')
                ->width(1000)
                ->height(750)
                ->format('webp')
                ->quality(85),

        ]);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Media::class, MediaPolicy::class);
    }
}
