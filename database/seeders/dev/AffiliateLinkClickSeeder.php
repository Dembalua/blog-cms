<?php

namespace Database\Seeders\dev;

use App\Models\AffiliateLink;
use App\Models\AffiliateLinkClick;
use App\Models\Content;
use Illuminate\Database\Seeder;

class AffiliateLinkClickSeeder extends Seeder
{
    public function run(): void
    {
        $clicks = [
            ['link' => 'amazon-clean-code-by-robert-c-martin', 'content' => 'Writing Clean Code Without Losing Your Mind', 'clicks' => 42, 'last_clicked_at' => now()->subDays(1)],
            ['link' => 'amazon-clean-code-by-robert-c-martin', 'content' => 'How to Stay Relevant as a Developer',         'clicks' => 15, 'last_clicked_at' => now()->subDays(2)],
            ['link' => 'amazon-the-pragmatic-programmer',      'content' => 'How to Stay Relevant as a Developer',         'clicks' => 17, 'last_clicked_at' => now()->subDays(3)],
            ['link' => 'amazon-mechanical-keyboard-for-devs',  'content' => 'How to Stay Relevant as a Developer',         'clicks' => 9,  'last_clicked_at' => now()->subWeek()],
            ['link' => 'awin-jetbrains-all-products-pack',     'content' => 'Filament Admin Panels Made Easy',             'clicks' => 23, 'last_clicked_at' => now()->subDays(2)],
            ['link' => 'awin-udemy-web-dev-bootcamp',          'content' => 'Getting Started with Laravel 12',             'clicks' => 54, 'last_clicked_at' => now()->subHours(6)],
            ['link' => 'digistore24-laravel-mastery-course',   'content' => 'Getting Started with Laravel 12',             'clicks' => 31, 'last_clicked_at' => now()->subDays(4)],
            ['link' => 'digistore24-laravel-mastery-course',   'content' => 'Building REST APIs with Laravel',             'clicks' => 12, 'last_clicked_at' => now()->subDays(5)],
        ];

        $links = AffiliateLink::whereIn('slug', array_unique(array_column($clicks, 'link')))->get()->keyBy('slug');
        $contents = Content::whereIn('title', array_unique(array_column($clicks, 'content')))->get()->keyBy('title');

        $totals = [];

        foreach ($clicks as $row) {
            $link = $links->get($row['link']);
            $content = $contents->get($row['content']);

            if (! $link || ! $content) {
                continue;
            }

            AffiliateLinkClick::firstOrCreate(
                [
                    'affiliate_link_id' => $link->id,
                    'content_id' => $content->id,
                ],
                [
                    'clicks' => $row['clicks'],
                    'last_clicked_at' => $row['last_clicked_at'],
                ]
            );

            $totals[$link->id] = ($totals[$link->id] ?? 0) + $row['clicks'];
        }

        foreach ($totals as $linkId => $sum) {
            AffiliateLink::where('id', $linkId)->update(['clicks' => $sum]);
        }
    }
}
