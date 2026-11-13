<?php

namespace Database\Seeders\dev;

use App\Models\Category;
use App\Models\Cluster;
use Illuminate\Database\Seeder;

class ClusterSeeder extends Seeder
{
    public function run(): void
    {
        $clusters = [
            'Laravel Fundamentals' => 'Web Development',
            'Frontend Essentials'  => 'Design & UX',
            'Developer Growth'     => 'Career & Learning',
        ];

        $categories = Category::whereIn('name', $clusters)->pluck('id', 'name');

        foreach ($clusters as $title => $categoryName) {
            Cluster::firstOrCreate(
                ['title' => $title],
                ['category_id' => $categories[$categoryName] ?? null]
            );
        }

        $laravel   = Cluster::where('title', 'Laravel Fundamentals')->first();
        $frontend  = Cluster::where('title', 'Frontend Essentials')->first();
        $developer = Cluster::where('title', 'Developer Growth')->first();

        $laravel->relatedClusters()->syncWithoutDetaching([$frontend->id]);
        $frontend->relatedClusters()->syncWithoutDetaching([$laravel->id, $developer->id]);
    }
}
