<?php

namespace Database\Seeders\dev;

use App\Enums\ContentType;
use App\Enums\MetaDataRobots;
use App\Models\Content;
use App\Models\Media;
use App\Models\Menu;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $media = Media::all()->keyBy('name');
        $header = Menu::where('name', 'Main Navigation')->first();
        $footer = Menu::where('name', 'Footer Navigation')->first();

        $homepageId = Content::where('type', ContentType::HOMEPAGE)->value('id');
        $privacyPageId = Content::where('title', 'Privacy Policy')->value('id');

        $settings = [
            ['group' => 'site',   'key' => 'name',             'value' => 'My Blog',                   'type' => 'string'],
            ['group' => 'site',   'key' => 'logo',             'value' => $media->get('logo-light')?->id,    'type' => 'integer'],
            ['group' => 'site',   'key' => 'logo_dark',        'value' => $media->get('logo-dark')?->id,    'type' => 'integer'],
            ['group' => 'site',   'key' => 'favicon',          'value' => $media->get('favicon')?->id, 'type' => 'integer'],
            ['group' => 'site',   'key' => 'homepage_content', 'value' => $homepageId,                 'type' => 'integer'],
            ['group' => 'site',   'key' => 'privacy_page',     'value' => $privacyPageId,              'type' => 'integer'],
            ['group' => 'menu',   'key' => 'header',           'value' => $header?->id,                'type' => 'integer'],
            ['group' => 'menu',   'key' => 'footer',           'value' => $footer?->id,                'type' => 'integer'],
            ['group' => 'menu',   'key' => 'max_depth',        'value' => 3,                           'type' => 'integer'],
            ['group' => 'footer', 'key' => 'slogan',           'value' => 'Making the web better.',    'type' => 'string'],
            ['group' => 'social', 'key' => 'instagram',        'value' => 'https://www.instagram.com',  'type' => 'string'],
            ['group' => 'social', 'key' => 'youtube',          'value' => 'https://www.youtube.com',   'type' => 'string'],
            ['group' => 'social', 'key' => 'pinterest',        'value' => 'https://www.pinterest.com', 'type' => 'string'],
            ['group' => 'theme', 'key' => 'light_primary',     'value' => '#7C3AED', 'type' => 'string'],
            ['group' => 'theme', 'key' => 'dark_primary',      'value' => '#A78BFA', 'type' => 'string'],
            ['group' => 'theme', 'key' => 'light_secondary',   'value' => '#8B5CF6', 'type' => 'string'],
            ['group' => 'theme', 'key' => 'dark_secondary',    'value' => '#C4B5FD', 'type' => 'string'],
            ['group' => 'theme', 'key' => 'light_background',  'value' => '#FAF5FF', 'type' => 'string'],
            ['group' => 'theme', 'key' => 'dark_background',   'value' => '#100D1A', 'type' => 'string'],
            ['group' => 'theme', 'key' => 'light_text',        'value' => '#0F172A', 'type' => 'string'],
            ['group' => 'theme', 'key' => 'dark_text',         'value' => '#EDE9F6', 'type' => 'string'],
            ['group' => 'theme', 'key' => 'light_surface',     'value' => '#FAF5FF', 'type' => 'string'],
            ['group' => 'theme', 'key' => 'dark_surface',      'value' => '#1A1625', 'type' => 'string'],
            ['group' => 'theme', 'key' => 'light_text_muted',  'value' => '#374151', 'type' => 'string'],
            ['group' => 'theme', 'key' => 'dark_text_muted',   'value' => '#B8B0CC', 'type' => 'string'],
            ['group' => 'theme', 'key' => 'light_text_subtle', 'value' => '#6B7280', 'type' => 'string'],
            ['group' => 'theme', 'key' => 'dark_text_subtle',  'value' => '#8B80A0', 'type' => 'string'],
            ['group' => 'theme',   'key' => 'light_border',        'value' => '#EFE7FC', 'type' => 'string'],
            ['group' => 'theme',   'key' => 'dark_border',         'value' => '#2D2640', 'type' => 'string'],
            ['group' => 'content',     'key' => 'category_page_size', 'value' => 12,  'type' => 'integer'],
            ['group' => 'maintenance', 'key' => 'enabled',            'value' => 0,    'type' => 'boolean'],
            ['group' => 'maintenance', 'key' => 'allowed_ips',        'value' => '',   'type' => 'string'],
            ['group' => 'maintenance', 'key' => 'pages',              'value' => '[]', 'type' => 'string'],
            ['group' => 'rss', 'key' => 'title',         'value' => 'My Blog – Latest Articles',          'type' => 'string'],
            ['group' => 'rss', 'key' => 'description',   'value' => 'Articles and tutorials about web development and more.', 'type' => 'string'],
            ['group' => 'rss', 'key' => 'default_image', 'value' => $media->get('web-development')?->id,  'type' => 'integer'],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['group' => $setting['group'], 'key' => $setting['key']],
                ['value' => $setting['value'], 'type' => $setting['type']]
            );
        }

        $seoAnchor = Setting::firstOrCreate(
            ['group' => 'seo', 'key' => 'meta'],
            ['value' => null, 'type' => 'string']
        );

        if (! $seoAnchor->metaData) {
            $seoAnchor->metaData()->create([
                'meta_title' => 'My Blog – Articles & Tutorials',
                'meta_description' => 'Discover articles and tutorials about web development and more.',
                'meta_keywords' => ['blog', 'web development', 'tutorials'],
                'og_title' => 'My Blog – Articles & Tutorials',
                'og_description' => 'Discover articles and tutorials about web development and more.',
                'robots' => MetaDataRobots::INDEX_FOLLOW,
            ]);
        }
    }
}
