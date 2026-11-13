<?php

namespace Database\Seeders\dev;

use App\Models\Category;
use App\Models\Content;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Services\PathService;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all()->keyBy('name');
        $contents   = Content::all()->keyBy('title');

        // ---------------------------------------------------------------------
        // Main Navigation
        // ---------------------------------------------------------------------

        $main = Menu::firstOrCreate(['name' => 'Main Navigation']);

        $webDev = MenuItem::firstOrCreate(
            [
                'model_id'   => $categories->get('Web Development')?->id,
                'model_type' => Category::class,
            ],
            [
                'menu_id'   => $main->id,
                'title'     => 'Web Development',
                'parent_id' => null,
                'order'     => 1,
            ]
        );

        MenuItem::firstOrCreate(
            [
                'model_id'   => $categories->get('Design & UX')?->id,
                'model_type' => Category::class,
            ],
            [
                'menu_id'   => $main->id,
                'title'     => 'Design & UX',
                'parent_id' => null,
                'order'     => 2,
            ]
        );

        MenuItem::firstOrCreate(
            [
                'model_id'   => $categories->get('Career & Learning')?->id,
                'model_type' => Category::class,
            ],
            [
                'menu_id'   => $main->id,
                'title'     => 'Career & Learning',
                'parent_id' => null,
                'order'     => 3,
            ]
        );

        // DevOps as subcategory of Web Development
        MenuItem::firstOrCreate(
            [
                'model_id'   => $categories->get('DevOps')?->id,
                'model_type' => Category::class,
            ],
            [
                'menu_id'   => $main->id,
                'title'     => 'DevOps',
                'parent_id' => $webDev->id,
                'order'     => 1,
            ]
        );

        // Tools & Software as subcategory of Web Development
        MenuItem::firstOrCreate(
            [
                'model_id'   => $categories->get('Tools & Software')?->id,
                'model_type' => Category::class,
            ],
            [
                'menu_id'   => $main->id,
                'title'     => 'Tools & Software',
                'parent_id' => $webDev->id,
                'order'     => 2,
            ]
        );

        MenuItem::firstOrCreate(
            [
                'model_id'   => $contents->get('About Us')?->id,
                'model_type' => Content::class,
            ],
            [
                'menu_id'   => $main->id,
                'title'     => 'About Us',
                'parent_id' => null,
                'order'     => 4,
            ]
        );

        MenuItem::firstOrCreate(
            [
                'model_id'   => $contents->get('Contact')?->id,
                'model_type' => Content::class,
            ],
            [
                'menu_id'   => $main->id,
                'title'     => 'Contact',
                'parent_id' => null,
                'order'     => 5,
            ]
        );

        // ---------------------------------------------------------------------
        // Footer Navigation
        // ---------------------------------------------------------------------

        $footer = Menu::firstOrCreate(['name' => 'Footer Navigation']);

        MenuItem::firstOrCreate(
            [
                'model_id'   => $contents->get('Privacy Policy')?->id,
                'model_type' => Content::class,
            ],
            [
                'menu_id'   => $footer->id,
                'title'     => 'Privacy Policy',
                'parent_id' => null,
                'order'     => 1,
            ]
        );

        MenuItem::firstOrCreate(
            [
                'model_id'   => $contents->get('Terms of Service')?->id,
                'model_type' => Content::class,
            ],
            [
                'menu_id'   => $footer->id,
                'title'     => 'Terms of Service',
                'parent_id' => null,
                'order'     => 2,
            ]
        );

        MenuItem::firstOrCreate(
            [
                'model_id'   => $contents->get('Newsletter')?->id,
                'model_type' => Content::class,
            ],
            [
                'menu_id'   => $footer->id,
                'title'     => 'Newsletter',
                'parent_id' => null,
                'order'     => 3,
            ]
        );

        // ---------------------------------------------------------------------
        // Generate hierarchical paths for all menu items
        // ---------------------------------------------------------------------

        $pathService = app(PathService::class);

        MenuItem::whereNull('parent_id')->get()->each(
            fn(MenuItem $item) => $pathService->generateForMenuItem($item)
        );

        Category::all()->each(
            fn(Category $category) => $pathService->generateForCategory($category)
        );
    }
}
