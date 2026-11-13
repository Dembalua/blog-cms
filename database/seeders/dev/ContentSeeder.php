<?php

namespace Database\Seeders\dev;

use App\Enums\ContentStatus;
use App\Enums\ContentType;
use App\Enums\MetaDataRobots;
use App\Models\AffiliateLink;
use App\Models\Category;
use App\Models\Cluster;
use App\Models\Content;
use App\Models\Media;
use App\Models\User;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $media = Media::all()->keyBy('name');
        $categories = Category::all()->keyBy('name');
        $clusters = Cluster::all()->keyBy('title');
        $author = User::first();

        $nodes = fn (string $html) => RichContentRenderer::make($html)->toArray()['content'] ?? [];

        // ---------------------------------------------------------------------
        // Pages
        // ---------------------------------------------------------------------

        $pages = [
            [
                'title' => 'About Us',
                'path' => 'about-us',
                'excerpt' => 'Learn more about who we are, what we do and why we are passionate about technology.',
                'content' => RichContentRenderer::make('<h2>Who We Are</h2><p>We are a team of passionate developers and designers who love building things for the web. Our mission is to share knowledge and inspire others to create amazing digital experiences.</p><p>Founded in 2024, we have been writing about web development, design, DevOps and everything in between.</p>')->toArray(),
                'image' => 'career-learning',
                'category' => null,
                'is_featured' => false,
                'meta_title' => 'About Us – Our Story & Mission',
                'meta_description' => 'Learn more about who we are, what we do and why we are passionate about technology and the web.',
                'og_title' => 'About Us – Our Story & Mission',
                'og_description' => 'Learn more about who we are, what we do and why we are passionate about technology and the web.',
            ],
            [
                'title' => 'Contact',
                'path' => 'contact',
                'excerpt' => 'Get in touch with us. We are happy to answer your questions and hear your feedback.',
                'content' => RichContentRenderer::make('<h2>Get In Touch</h2><p>Have a question, suggestion or just want to say hello? We would love to hear from you. Use the form below or reach out to us directly via email.</p><p>We typically respond within one business day.</p>')->toArray(),
                'image' => 'tools-software',
                'category' => null,
                'is_featured' => false,
                'meta_title' => 'Contact – Get In Touch With Us',
                'meta_description' => 'Have a question or suggestion? Get in touch with us. We are happy to answer your questions.',
                'og_title' => 'Contact – Get In Touch With Us',
                'og_description' => 'Have a question or suggestion? Get in touch with us. We are happy to answer your questions.',
            ],
            [
                'title' => 'Privacy Policy',
                'path' => 'privacy-policy',
                'excerpt' => 'Read our privacy policy to understand how we collect, use and protect your personal data.',
                'content' => RichContentRenderer::make('<h2>Privacy Policy</h2><p>We take your privacy seriously. This policy explains what data we collect, how we use it and what rights you have regarding your personal information.</p><p>We do not sell your data to third parties and we only collect what is necessary to provide our services.</p>')->toArray(),
                'image' => 'design-ux',
                'category' => null,
                'is_featured' => false,
                'meta_title' => 'Privacy Policy – How We Handle Your Data',
                'meta_description' => 'Read our privacy policy to understand how we collect, use and protect your personal data.',
                'og_title' => 'Privacy Policy – How We Handle Your Data',
                'og_description' => 'Read our privacy policy to understand how we collect, use and protect your personal data.',
            ],
            [
                'title' => 'Terms of Service',
                'path' => 'terms-of-service',
                'excerpt' => 'Read our terms of service to understand the rules and guidelines for using our platform.',
                'content' => RichContentRenderer::make('<h2>Terms of Service</h2><p>By using our platform you agree to these terms. Please read them carefully before using our services.</p><p>We reserve the right to update these terms at any time. Continued use of the platform after changes constitutes acceptance of the new terms.</p>')->toArray(),
                'image' => 'devops',
                'category' => null,
                'is_featured' => false,
                'meta_title' => 'Terms of Service – Rules & Guidelines',
                'meta_description' => 'Read our terms of service to understand the rules and guidelines for using our platform.',
                'og_title' => 'Terms of Service – Rules & Guidelines',
                'og_description' => 'Read our terms of service to understand the rules and guidelines for using our platform.',
            ],
            [
                'title' => 'Newsletter',
                'path' => 'newsletter',
                'excerpt' => 'Subscribe to our newsletter and stay up to date with the latest articles and tutorials.',
                'content' => RichContentRenderer::make('<h2>Stay Up To Date</h2><p>Subscribe to our newsletter and never miss a new article, tutorial or announcement. We send out a digest every two weeks with the best content from our blog.</p><p>No spam, unsubscribe at any time.</p>')->toArray(),
                'image' => 'web-development',
                'category' => null,
                'is_featured' => false,
                'meta_title' => 'Newsletter – Stay Up To Date',
                'meta_description' => 'Subscribe to our newsletter and stay up to date with the latest articles and tutorials.',
                'og_title' => 'Newsletter – Stay Up To Date',
                'og_description' => 'Subscribe to our newsletter and stay up to date with the latest articles and tutorials.',
            ],
        ];

        foreach ($pages as $data) {
            $content = Content::firstOrCreate(
                ['title' => $data['title']],
                [
                    'excerpt' => $data['excerpt'],
                    'content' => $data['content'],
                    'feature_image_id' => $media->get($data['image'])?->id,
                    'is_featured' => $data['is_featured'],
                    'type' => ContentType::PAGE,
                    'status' => ContentStatus::PUBLISHED,
                    'published_at' => now(),
                    'author_id' => $author->id,
                    'category_id' => $data['category'] ? $categories->get($data['category'])?->id : null,
                    'cluster_id' => null,
                    'is_pillar' => null,
                    'sort' => 0,
                ]
            );

            if (! $content->path) {
                $content->path()->create([
                    'slug' => $data['path'],
                ]);
            }

            if (! $content->metaData) {
                $content->metaData()->create([
                    'meta_title' => $data['meta_title'],
                    'meta_description' => $data['meta_description'],
                    'og_title' => $data['og_title'],
                    'og_description' => $data['og_description'],
                    'og_image_id' => $media->get($data['image'])?->id,
                    'meta_keywords' => ['technology', 'blog', 'web'],
                    'robots' => MetaDataRobots::INDEX_FOLLOW,
                ]);
            }
        }

        // ---------------------------------------------------------------------
        // Posts
        // ---------------------------------------------------------------------

        $posts = [
            // Web Development – 5 posts
            [
                'title' => 'Getting Started with Laravel 12',
                'path' => 'getting-started-with-laravel-12',
                'cluster' => 'Laravel Fundamentals',
                'is_pillar' => true,
                'excerpt' => 'A comprehensive guide to getting started with Laravel 12 and its new features.',
                'content' => [
                    'type' => 'doc',
                    'content' => array_merge(
                        $nodes("<h2>Getting Started with Laravel 12</h2><p>Laravel 12 brings <strong>powerful new features</strong>, <em>elegant syntax</em>, <u>improved tooling</u> and <s>removed legacy baggage</s>. You can also add <a href=\"https://laravel.com\">links</a>, use <sub>subscript</sub> or <sup>superscript</sup> where needed.</p><h3>Installation</h3><p>Get started with a single command in your terminal:</p><pre><code>composer create-project laravel/laravel my-app\ncd my-app\nphp artisan serve</code></pre><h3>What's New</h3><ul><li>Streamlined application structure</li><li>Enhanced Eloquent features</li><li>Improved starter kits with React, Vue and Livewire</li><li>Better security defaults out of the box</li></ul><h3>Migration Steps</h3><ol><li>Update your composer.json to require Laravel 12</li><li>Run composer update</li><li>Review the upgrade guide for breaking changes</li><li>Run your full test suite</li></ol><blockquote>Laravel is a web application framework with expressive, elegant syntax. It takes the pain out of development by making common tasks easy.</blockquote>"),
                        [
                            [
                                'type' => 'customBlock',
                                'attrs' => [
                                    'id' => 'image-text',
                                    'label' => 'Image & Text',
                                    'preview' => '',
                                    'config' => [
                                        'heading' => 'Setting Up Your Local Environment',
                                        'body' => 'A solid local setup is the foundation of every productive Laravel project. Tools like Laravel Herd or Sail make it easy to get started without wrestling with system configuration.',
                                        'image_position' => 'right',
                                        'image_id' => $media->get('tools-software')?->id,
                                    ],
                                ],
                            ],
                        ],
                        $nodes('<h3>Version Comparison</h3><table><thead><tr><th>Feature</th><th>Laravel 11</th><th>Laravel 12</th></tr></thead><tbody><tr><td>PHP minimum</td><td>8.2</td><td>8.2</td></tr><tr><td>New app structure</td><td>Yes</td><td>Yes</td></tr><tr><td>Starter kits</td><td>Breeze, Jetstream</td><td>React, Vue, Livewire</td></tr><tr><td>Config files</td><td>Many</td><td>Minimal</td></tr></tbody></table><p>Check out the <a href="https://laravel.com/docs">official Laravel documentation</a> for the complete upgrade guide.</p>'),
                    ),
                ],
                'image' => 'web-development',
                'category' => 'Web Development',
                'is_featured' => true,
                'meta_title' => 'Getting Started with Laravel 12',
                'meta_description' => 'A comprehensive guide to getting started with Laravel 12 and its exciting new features.',
                'og_title' => 'Getting Started with Laravel 12',
                'og_description' => 'A comprehensive guide to getting started with Laravel 12 and its exciting new features.',
            ],
            [
                'title' => 'Building REST APIs with Laravel',
                'path' => 'building-rest-apis-with-laravel',
                'cluster' => 'Laravel Fundamentals',
                'is_pillar' => null,
                'excerpt' => 'Learn how to build robust and scalable REST APIs using Laravel and best practices.',
                'content' => RichContentRenderer::make('<h2>Building REST APIs with Laravel</h2><p>REST APIs are the backbone of modern web applications. In this article we explore how to build robust and scalable APIs using Laravel, including authentication, versioning and error handling.</p>')->toArray(),
                'image' => 'web-development',
                'category' => 'Web Development',
                'is_featured' => false,
                'meta_title' => 'Building REST APIs with Laravel',
                'meta_description' => 'Learn how to build robust and scalable REST APIs using Laravel and best practices.',
                'og_title' => 'Building REST APIs with Laravel',
                'og_description' => 'Learn how to build robust and scalable REST APIs using Laravel and best practices.',
            ],
            [
                'title' => 'Understanding Eloquent Relationships',
                'path' => 'understanding-eloquent-relationships',
                'cluster' => 'Laravel Fundamentals',
                'is_pillar' => null,
                'excerpt' => 'A deep dive into Eloquent relationships and how to use them effectively in your projects.',
                'content' => RichContentRenderer::make('<h2>Understanding Eloquent Relationships</h2><p>Eloquent makes working with database relationships simple and expressive. In this article we cover all relationship types from hasOne to morphMany and show you how to use them effectively.</p>')->toArray(),
                'image' => 'web-development',
                'category' => 'Web Development',
                'is_featured' => false,
                'meta_title' => 'Understanding Eloquent Relationships',
                'meta_description' => 'A deep dive into Eloquent relationships and how to use them effectively in your projects.',
                'og_title' => 'Understanding Eloquent Relationships',
                'og_description' => 'A deep dive into Eloquent relationships and how to use them effectively in your projects.',
            ],
            [
                'title' => 'Filament Admin Panels Made Easy',
                'path' => 'filament-admin-panels-made-easy',
                'cluster' => 'Laravel Fundamentals',
                'is_pillar' => null,
                'excerpt' => 'Discover how Filament makes building beautiful admin panels for Laravel applications a breeze.',
                'content' => RichContentRenderer::make('<h2>Filament Admin Panels Made Easy</h2><p>Filament is the most elegant way to build admin panels for Laravel. With its rich set of components and intuitive API you can build complex backends in a fraction of the time.</p>')->toArray(),
                'image' => 'web-development',
                'category' => 'Web Development',
                'is_featured' => true,
                'meta_title' => 'Filament Admin Panels Made Easy',
                'meta_description' => 'Discover how Filament makes building beautiful admin panels for Laravel a breeze.',
                'og_title' => 'Filament Admin Panels Made Easy',
                'og_description' => 'Discover how Filament makes building beautiful admin panels for Laravel a breeze.',
            ],
            [
                'title' => 'Mastering Tailwind CSS',
                'path' => 'mastering-tailwind-css',
                'cluster' => 'Frontend Essentials',
                'is_pillar' => true,
                'excerpt' => 'Take your Tailwind CSS skills to the next level with advanced techniques and patterns.',
                'content' => RichContentRenderer::make('<h2>Mastering Tailwind CSS</h2><p>Tailwind CSS has changed the way we write styles. In this article we go beyond the basics and explore advanced techniques, custom configurations and design patterns that will take your UI skills to the next level.</p>')->toArray(),
                'image' => 'design-ux',
                'category' => 'Design & UX',
                'is_featured' => false,
                'meta_title' => 'Mastering Tailwind CSS',
                'meta_description' => 'Take your Tailwind CSS skills to the next level with advanced techniques and patterns.',
                'og_title' => 'Mastering Tailwind CSS',
                'og_description' => 'Take your Tailwind CSS skills to the next level with advanced techniques and patterns.',
            ],

            // Design & UX – 3 posts
            [
                'title' => 'Design Systems That Scale',
                'path' => 'design-systems-that-scale',
                'cluster' => 'Frontend Essentials',
                'is_pillar' => null,
                'excerpt' => 'How to build and maintain design systems that grow with your product and team.',
                'content' => RichContentRenderer::make('<h2>Design Systems That Scale</h2><p>A well-built design system is one of the most valuable investments a product team can make. In this article we explore how to create and maintain design systems that grow with your product.</p>')->toArray(),
                'image' => 'design-ux',
                'category' => 'Design & UX',
                'is_featured' => false,
                'meta_title' => 'Design Systems That Scale',
                'meta_description' => 'How to build and maintain design systems that grow with your product and team.',
                'og_title' => 'Design Systems That Scale',
                'og_description' => 'How to build and maintain design systems that grow with your product and team.',
            ],
            [
                'title' => 'UX Writing for Developers',
                'path' => 'ux-writing-for-developers',
                'cluster' => 'Frontend Essentials',
                'is_pillar' => null,
                'excerpt' => 'Good UX writing can make or break a product. Learn the fundamentals every developer should know.',
                'content' => RichContentRenderer::make('<h2>UX Writing for Developers</h2><p>UX writing is often overlooked by developers but it can have a huge impact on how users experience your product. In this article we cover the fundamentals of UX writing that every developer should know.</p>')->toArray(),
                'image' => 'design-ux',
                'category' => 'Design & UX',
                'is_featured' => false,
                'meta_title' => 'UX Writing for Developers',
                'meta_description' => 'Good UX writing can make or break a product. Learn the fundamentals every developer should know.',
                'og_title' => 'UX Writing for Developers',
                'og_description' => 'Good UX writing can make or break a product. Learn the fundamentals every developer should know.',
            ],
            [
                'title' => 'Accessibility in Modern Web Apps',
                'path' => 'accessibility-in-modern-web-apps',
                'cluster' => 'Frontend Essentials',
                'is_pillar' => null,
                'excerpt' => 'Building accessible web applications is not optional. Here is how to get it right from the start.',
                'content' => RichContentRenderer::make('<h2>Accessibility in Modern Web Apps</h2><p>Accessibility is not a feature, it is a requirement. In this article we cover the most important accessibility principles and how to implement them in your modern web applications.</p>')->toArray(),
                'image' => 'design-ux',
                'category' => 'Design & UX',
                'is_featured' => true,
                'meta_title' => 'Accessibility in Modern Web Apps',
                'meta_description' => 'Building accessible web applications is not optional. Here is how to get it right.',
                'og_title' => 'Accessibility in Modern Web Apps',
                'og_description' => 'Building accessible web applications is not optional. Here is how to get it right.',
            ],

            // No feature image – for styling fallback
            [
                'title' => 'Writing Clean Code Without Losing Your Mind',
                'path' => 'writing-clean-code-without-losing-your-mind',
                'excerpt' => 'Clean code is not about perfection. It is about writing code that others can read and maintain.',
                'content' => RichContentRenderer::make('<h2>Writing Clean Code Without Losing Your Mind</h2><p>Every developer wants to write clean code, but few agree on what that actually means. In this article we cut through the noise and focus on practical habits that make a real difference.</p>')->toArray(),
                'image' => null,
                'category' => 'Web Development',
                'is_featured' => false,
                'meta_title' => 'Writing Clean Code Without Losing Your Mind',
                'meta_description' => 'Clean code is not about perfection. Practical habits that make a real difference.',
                'og_title' => 'Writing Clean Code Without Losing Your Mind',
                'og_description' => 'Clean code is not about perfection. Practical habits that make a real difference.',
            ],

            // FAQ block demo
            [
                'title' => 'Laravel FAQ: Common Questions Answered',
                'path' => 'laravel-faq-common-questions-answered',
                'excerpt' => 'Answers to the most common questions developers have when learning Laravel.',
                'content' => [
                    'type' => 'doc',
                    'content' => array_merge(
                        $nodes('<h2>Laravel Questions Answered</h2><p>Whether you are just getting started or coming from another framework, these are the questions we see most often in the Laravel community.</p>'),
                        [
                            [
                                'type' => 'customBlock',
                                'attrs' => [
                                    'id' => 'faq',
                                    'label' => 'FAQ',
                                    'preview' => '',
                                    'config' => [
                                        'heading' => 'Getting Started with Laravel',
                                        'items' => [
                                            [
                                                'question' => 'What PHP version does Laravel 12 require?',
                                                'answer' => 'Laravel 12 requires PHP 8.2 or higher. It takes advantage of typed properties, enums and other modern PHP features.',
                                            ],
                                            [
                                                'question' => 'Do I need to know PHP before learning Laravel?',
                                                'answer' => 'A solid understanding of PHP fundamentals — classes, functions, arrays — will help a lot. Laravel abstracts a lot of complexity but knowing what is underneath makes you a better developer.',
                                            ],
                                            [
                                                'question' => 'What is the difference between Eloquent and the Query Builder?',
                                                'answer' => 'Eloquent is an ORM that maps database rows to model objects with relationships. The Query Builder offers a lower-level fluent interface for constructing SQL. Both are available in every Laravel project.',
                                            ],
                                            [
                                                'question' => 'How do I run database migrations?',
                                                'answer' => 'Run php artisan migrate to apply pending migrations. Use php artisan migrate:fresh to drop all tables and re-run every migration from scratch.',
                                            ],
                                            [
                                                'question' => 'Where should I store application logic — controllers or services?',
                                                'answer' => 'Keep controllers thin: they should receive a request, delegate to a service or action class, and return a response. Heavy business logic belongs in dedicated service classes.',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ),
                ],
                'image' => 'web-development',
                'category' => 'Web Development',
                'is_featured' => false,
                'meta_title' => 'Laravel FAQ: Common Questions Answered',
                'meta_description' => 'Answers to the most common questions developers have when learning Laravel — PHP version, Eloquent, migrations and more.',
                'og_title' => 'Laravel FAQ: Common Questions Answered',
                'og_description' => 'Answers to the most common questions developers have when learning Laravel.',
            ],

            // Career & Learning – 1 post
            [
                'title' => 'How to Stay Relevant as a Developer',
                'path' => 'how-to-stay-relevant-as-a-developer',
                'cluster' => 'Developer Growth',
                'is_pillar' => true,
                'excerpt' => 'The tech landscape changes fast. Here are practical strategies to keep your skills sharp and stay relevant.',
                'content' => RichContentRenderer::make('<h2>How to Stay Relevant as a Developer</h2><p>Technology evolves at a rapid pace. What was cutting edge two years ago may be obsolete today. In this article we share practical strategies to keep learning, stay motivated and remain relevant in a fast-changing industry.</p>')->toArray(),
                'image' => 'career-learning',
                'category' => 'Career & Learning',
                'is_featured' => false,
                'meta_title' => 'How to Stay Relevant as a Developer',
                'meta_description' => 'The tech landscape changes fast. Practical strategies to keep your skills sharp.',
                'og_title' => 'How to Stay Relevant as a Developer',
                'og_description' => 'The tech landscape changes fast. Practical strategies to keep your skills sharp.',
            ],
        ];

        $clusterPositions = [];

        foreach ($posts as $data) {
            $clusterName = $data['cluster'] ?? null;
            $clusterPosition = null;
            if ($clusterName) {
                $clusterPositions[$clusterName] = ($clusterPositions[$clusterName] ?? -1) + 1;
                $clusterPosition = $clusterPositions[$clusterName];
            }

            $content = Content::firstOrCreate(
                ['title' => $data['title']],
                [
                    'excerpt' => $data['excerpt'],
                    'content' => $data['content'],
                    'feature_image_id' => $media->get($data['image'])?->id,
                    'is_featured' => $data['is_featured'],
                    'type' => ContentType::POST,
                    'status' => ContentStatus::PUBLISHED,
                    'published_at' => now(),
                    'author_id' => $author->id,
                    'category_id' => $data['category'] ? $categories->get($data['category'])?->id : null,
                    'cluster_id' => $clusterName ? $clusters->get($clusterName)?->id : null,
                    'cluster_position' => $clusterPosition,
                    'is_pillar' => $data['is_pillar'] ?? null,
                    'sort' => 0,
                ]
            );

            if (! $content->path) {
                $content->path()->create([
                    'slug' => $data['path'],
                ]);
            }

            if (! $content->metaData) {
                $content->metaData()->create([
                    'meta_title' => $data['meta_title'],
                    'meta_description' => $data['meta_description'],
                    'og_title' => $data['og_title'],
                    'og_description' => $data['og_description'],
                    'og_image_id' => $media->get($data['image'])?->id,
                    'meta_keywords' => ['technology', 'blog', 'web'],
                    'robots' => MetaDataRobots::INDEX_FOLLOW,
                ]);
            }
        }

        // Affiliate link examples: embed product mentions into selected articles.
        $affiliateInserts = [
            [
                'article' => 'Writing Clean Code Without Losing Your Mind',
                'link' => 'amazon-clean-code-by-robert-c-martin',
                'intro' => 'If you want to go deeper, ',
                'anchor' => 'Clean Code by Robert C. Martin',
                'outro' => ' is the standard reference for writing readable and maintainable code.',
            ],
            [
                'article' => 'How to Stay Relevant as a Developer',
                'link' => 'amazon-the-pragmatic-programmer',
                'intro' => 'Books like ',
                'anchor' => 'The Pragmatic Programmer',
                'outro' => ' still hold up after two decades — the habits they describe translate directly into today\'s workflows.',
            ],
            [
                'article' => 'How to Stay Relevant as a Developer',
                'link' => 'amazon-mechanical-keyboard-for-devs',
                'intro' => 'Small things help too — a good ',
                'anchor' => 'mechanical keyboard',
                'outro' => ' makes long coding days noticeably less tiring.',
            ],
            [
                'article' => 'How to Stay Relevant as a Developer',
                'link' => 'amazon-clean-code-by-robert-c-martin',
                'intro' => 'If you want a solid foundation, ',
                'anchor' => 'Clean Code',
                'outro' => ' is still the reference for writing readable, maintainable code.',
            ],
            [
                'article' => 'Filament Admin Panels Made Easy',
                'link' => 'awin-jetbrains-all-products-pack',
                'intro' => 'PhpStorm from the ',
                'anchor' => 'JetBrains All Products Pack',
                'outro' => ' pays for itself the first time you refactor a Filament resource across dozens of files.',
            ],
            [
                'article' => 'Getting Started with Laravel 12',
                'link' => 'awin-udemy-web-dev-bootcamp',
                'intro' => 'If you like learning by video, the ',
                'anchor' => 'Udemy Web Dev Bootcamp',
                'outro' => ' is a solid, project-based intro.',
            ],
            [
                'article' => 'Getting Started with Laravel 12',
                'link' => 'digistore24-laravel-mastery-course',
                'intro' => 'For a deeper dive, the ',
                'anchor' => 'Laravel Mastery Course',
                'outro' => ' walks through building a full production app step by step.',
            ],
            [
                'article' => 'Building REST APIs with Laravel',
                'link' => 'digistore24-laravel-mastery-course',
                'intro' => 'The ',
                'anchor' => 'Laravel Mastery Course',
                'outro' => ' covers REST API design and auth in detail if you want a guided path.',
            ],
        ];

        foreach ($affiliateInserts as $insert) {
            $article = Content::where('title', $insert['article'])->first();
            $link = AffiliateLink::where('slug', $insert['link'])->first();

            if (! $article || ! $link) {
                continue;
            }

            $existingLinkIds = collect(data_get($article->content, 'content', []))
                ->flatMap(fn ($node) => data_get($node, 'content', []))
                ->flatMap(fn ($inline) => data_get($inline, 'marks', []))
                ->where('type', 'affiliateLink')
                ->pluck('attrs.affiliateLinkId')
                ->all();

            if (in_array($link->id, $existingLinkIds, true)) {
                continue;
            }

            $articleContent = $article->content;
            $articleContent['content'][] = [
                'type' => 'paragraph',
                'attrs' => ['textAlign' => 'start'],
                'content' => [
                    ['type' => 'text', 'text' => $insert['intro']],
                    [
                        'type' => 'text',
                        'text' => $insert['anchor'],
                        'marks' => [
                            ['type' => 'affiliateLink', 'attrs' => ['affiliateLinkId' => $link->id]],
                        ],
                    ],
                    ['type' => 'text', 'text' => $insert['outro']],
                ],
            ];
            $article->update(['content' => $articleContent]);
        }

        // Add an internal link example: REST APIs article links back to the pillar
        $pillar = Content::where('title', 'Getting Started with Laravel 12')->first();
        $article = Content::where('title', 'Building REST APIs with Laravel')->first();

        if ($pillar && $article) {
            $hasInternalLink = collect(data_get($article->content, 'content', []))
                ->flatMap(fn ($node) => data_get($node, 'content', []))
                ->flatMap(fn ($inline) => data_get($inline, 'marks', []))
                ->contains('type', 'internalLink');

            if (! $hasInternalLink) {
                $articleContent = $article->content;
                array_unshift($articleContent['content'], [
                    'type' => 'paragraph',
                    'attrs' => ['textAlign' => 'start'],
                    'content' => [
                        ['type' => 'text', 'text' => 'This article is part of the '],
                        [
                            'type' => 'text',
                            'text' => $pillar->title,
                            'marks' => [
                                ['type' => 'internalLink', 'attrs' => ['postId' => $pillar->id]],
                            ],
                        ],
                        ['type' => 'text', 'text' => ' series.'],
                    ],
                ]);
                $article->update(['content' => $articleContent]);
            }
        }

        $linkingArticles = [
            'Understanding Eloquent Relationships' => 'This guide builds on the foundation covered in ',
            'Filament Admin Panels Made Easy' => 'Recommended reading before this article: ',
        ];

        foreach ($linkingArticles as $title => $prefix) {
            $article = Content::where('title', $title)->first();

            if (! $pillar || ! $article) {
                continue;
            }

            $hasInternalLink = collect(data_get($article->content, 'content', []))
                ->flatMap(fn ($node) => data_get($node, 'content', []))
                ->flatMap(fn ($inline) => data_get($inline, 'marks', []))
                ->contains('type', 'internalLink');

            if (! $hasInternalLink) {
                $articleContent = $article->content;
                array_unshift($articleContent['content'], [
                    'type' => 'paragraph',
                    'attrs' => ['textAlign' => 'start'],
                    'content' => [
                        ['type' => 'text', 'text' => $prefix],
                        [
                            'type' => 'text',
                            'text' => $pillar->title,
                            'marks' => [
                                ['type' => 'internalLink', 'attrs' => ['postId' => $pillar->id]],
                            ],
                        ],
                        ['type' => 'text', 'text' => '.'],
                    ],
                ]);
                $article->update(['content' => $articleContent]);
            }
        }

        // ---------------------------------------------------------------------
        // Homepage
        // ---------------------------------------------------------------------

        $laravelPillar = Content::where('title', 'Getting Started with Laravel 12')->first();
        $tailwindPillar = Content::where('title', 'Mastering Tailwind CSS')->first();
        $careerPillar = Content::where('title', 'How to Stay Relevant as a Developer')->first();
        $webDevCategory = $categories->get('Web Development');

        $homepageContent = [
            'type' => 'doc',
            'content' => array_merge(
                $nodes('<h2>What you will find here</h2><p>This blog is a long-running notebook about building things for the web. Expect practical articles about Laravel, frontend tooling, design systems, DevOps workflows and the career side of being a developer.</p>'),
                [
                    [
                        'type' => 'customBlock',
                        'attrs' => [
                            'id' => 'post_cards_big',
                            'label' => 'Post Cards Big',
                            'preview' => '',
                            'config' => [
                                'heading' => 'Fresh from the editor',
                                'intro' => 'The latest articles, tutorials and notes from the blog.',
                                'count' => 3,
                                'source' => 'latest',
                            ],
                        ],
                    ],
                    [
                        'type' => 'customBlock',
                        'attrs' => [
                            'id' => 'sections',
                            'label' => 'Sections',
                            'preview' => '',
                            'config' => [
                                'heading' => 'Pick a topic',
                                'intro' => 'Three areas we write about — from shipping your first Laravel app to staying sane as a developer over the long run.',
                                'items' => [
                                    [
                                        'heading' => 'Web Development',
                                        'body' => 'Laravel, Eloquent, APIs and the tooling around them — the practical side of building web apps.',
                                        'link_category_id' => $laravelPillar?->category_id,
                                        'link_cluster_id' => $laravelPillar?->cluster_id,
                                        'link_post_id' => $laravelPillar?->id,
                                        'link_with_arrow' => true,
                                    ],
                                    [
                                        'heading' => 'Design & UX',
                                        'body' => 'Design systems, UX writing and accessibility — because good software is also good to use.',
                                        'link_category_id' => $tailwindPillar?->category_id,
                                        'link_cluster_id' => $tailwindPillar?->cluster_id,
                                        'link_post_id' => $tailwindPillar?->id,
                                        'link_with_arrow' => true,
                                    ],
                                    [
                                        'heading' => 'Career & Learning',
                                        'body' => 'Habits, mindsets and resources that keep developers growing long after the first job.',
                                        'link_category_id' => $careerPillar?->category_id,
                                        'link_cluster_id' => $careerPillar?->cluster_id,
                                        'link_post_id' => $careerPillar?->id,
                                        'link_with_arrow' => true,
                                    ],
                                ],
                            ],
                        ],
                    ],
                    [
                        'type' => 'customBlock',
                        'attrs' => [
                            'id' => 'image-text',
                            'label' => 'Image & Text',
                            'preview' => '',
                            'config' => [
                                'heading' => 'DevOps without the hype',
                                'body' => "DevOps isn't about shiny dashboards or chasing the newest tool. It's about pipelines that just work, deployments you don't dread and infrastructure you barely need to think about. More articles on this are on the way.",
                                'image_position' => 'left',
                                'image_id' => $media->get('devops')?->id,
                            ],
                        ],
                    ],
                    [
                        'type' => 'customBlock',
                        'attrs' => [
                            'id' => 'post_cards_small',
                            'label' => 'Post Cards Small',
                            'preview' => '',
                            'config' => [
                                'heading' => 'More from Web Development',
                                'intro' => 'Deep dives, how-tos and opinions — all about building for the web.',
                                'count' => 4,
                                'source' => 'category',
                                'category_id' => $webDevCategory?->id,
                                'show_category_link' => true,
                            ],
                        ],
                    ],
                ],
            ),
        ];

        Content::firstOrCreate(
            ['title' => 'Home'],
            [
                'excerpt' => null,
                'content' => $homepageContent,
                'feature_image_id' => null,
                'is_featured' => false,
                'type' => ContentType::HOMEPAGE,
                'status' => ContentStatus::PUBLISHED,
                'published_at' => now(),
                'author_id' => $author->id,
                'category_id' => null,
                'cluster_id' => null,
                'cluster_position' => null,
                'is_pillar' => null,
                'sort' => 0,
                'hero_kicker' => 'A blog for curious developers',
                'hero_headline' => 'Build for the web',
                'hero_headline_accent' => 'with confidence',
                'hero_text' => "Deep dives into web development, design, DevOps and the tools that make our daily work better.\n\nFresh content every week — no fluff, no filler, just hands-on knowledge for people who build things.",
                'hero_image_id' => $media->get('tools-software')?->id,
            ]
        );
    }
}
