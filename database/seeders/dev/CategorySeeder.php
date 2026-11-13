<?php

namespace Database\Seeders\dev;

use App\Enums\MetaDataRobots;
use App\Models\Category;
use App\Models\Media;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $media = Media::all()->keyBy('name');

        $categories = [
            [
                'name' => 'Web Development',
                'sort' => 1,
                'active' => true,
                'description' => 'Articles and tutorials about web development, frameworks, and best practices.',
                'image' => 'web-development',
                'path' => 'web-development',
                'meta' => [
                    'meta_title' => 'Web Development – Articles & Tutorials',
                    'meta_description' => 'Discover articles and tutorials about web development, frameworks and best practices.',
                    'meta_keywords' => ['web development', 'programming', 'frameworks', 'tutorials', 'coding'],
                    'og_title' => 'Web Development – Articles & Tutorials',
                    'og_description' => 'Discover articles and tutorials about web development, frameworks and best practices.',
                    'robots' => MetaDataRobots::INDEX_FOLLOW,
                ],
            ],
            [
                'name' => 'Design & UX',
                'sort' => 2,
                'active' => true,
                'description' => 'Insights and inspiration for designers and UX professionals.',
                'image' => 'design-ux',
                'path' => 'design-ux',
                'meta' => [
                    'meta_title' => 'Design & UX – Insights & Inspiration',
                    'meta_description' => 'Insights and inspiration for designers and UX professionals.',
                    'meta_keywords' => ['design', 'ux', 'user experience', 'interface', 'inspiration'],
                    'og_title' => 'Design & UX – Insights & Inspiration',
                    'og_description' => 'Insights and inspiration for designers and UX professionals.',
                    'robots' => MetaDataRobots::INDEX_FOLLOW,
                ],
            ],
            [
                'name' => 'DevOps',
                'sort' => 3,
                'active' => true,
                'description' => 'Everything about DevOps, CI/CD, infrastructure and automation.',
                'image' => 'devops',
                'path' => 'devops',
                'meta' => [
                    'meta_title' => 'DevOps – Infrastructure & Automation',
                    'meta_description' => 'Everything about DevOps, CI/CD, infrastructure and automation.',
                    'meta_keywords' => ['devops', 'infrastructure', 'automation', 'cicd', 'deployment'],
                    'og_title' => 'DevOps – Infrastructure & Automation',
                    'og_description' => 'Everything about DevOps, CI/CD, infrastructure and automation.',
                    'robots' => MetaDataRobots::INDEX_FOLLOW,
                ],
            ],
            [
                'name' => 'Tools & Software',
                'sort' => 4,
                'active' => true,
                'description' => 'Reviews and guides for developer tools and software.',
                'image' => 'tools-software',
                'path' => 'tools-software',
                'meta' => [
                    'meta_title' => 'Tools & Software – Reviews & Guides',
                    'meta_description' => 'Reviews and guides for developer tools and software.',
                    'meta_keywords' => ['tools', 'software', 'reviews', 'developer', 'productivity'],
                    'og_title' => 'Tools & Software – Reviews & Guides',
                    'og_description' => 'Reviews and guides for developer tools and software.',
                    'robots' => MetaDataRobots::INDEX_FOLLOW,
                ],
            ],
            [
                'name' => 'Career & Learning',
                'sort' => 5,
                'active' => true,
                'description' => 'Tips and resources for growing your tech career and learning new skills.',
                'image' => 'career-learning',
                'path' => 'career-learning',
                'meta' => [
                    'meta_title' => 'Career & Learning – Grow Your Skills',
                    'meta_description' => 'Tips and resources for growing your tech career and learning new skills.',
                    'meta_keywords' => ['career', 'learning', 'skills', 'growth', 'education'],
                    'og_title' => 'Career & Learning – Grow Your Skills',
                    'og_description' => 'Tips and resources for growing your tech career and learning new skills.',
                    'robots' => MetaDataRobots::INDEX_FOLLOW,
                ],
            ],
        ];

        foreach ($categories as $data) {
            $category = Category::firstOrCreate(
                ['name' => $data['name']],
                [
                    'sort' => $data['sort'],
                    'active' => $data['active'],
                    'description' => $data['description'],
                    'image_id' => $media->get($data['image'])?->id,
                ]
            );

            if (! $category->path) {
                $category->path()->create([
                    'slug' => $data['path'],
                ]);
            }

            if (! $category->metaData) {
                $metaData = $data['meta'];
                if (isset($media->get($data['image'])?->id)) {
                    $metaData['og_image_id'] = $media->get($data['image'])->id;
                }
                $category->metaData()->create($metaData);
            }
        }
    }
}
