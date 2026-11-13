<?php

namespace Database\Seeders\dev;

use App\Models\AffiliateLink;
use App\Models\AffiliateNetwork;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AffiliateLinkSeeder extends Seeder
{
    public function run(): void
    {
        $links = [
            'Amazon' => [
                ['name' => 'Clean Code by Robert C. Martin', 'url' => 'https://www.amazon.de/dp/0132350882'],
                ['name' => 'The Pragmatic Programmer',       'url' => 'https://www.amazon.de/dp/0135957052'],
                ['name' => 'Mechanical Keyboard for Devs',   'url' => 'https://www.amazon.de/dp/B09SV7QJGD'],
            ],
            'Awin' => [
                ['name' => 'JetBrains All Products Pack', 'url' => 'https://www.jetbrains.com/all/'],
                ['name' => 'Udemy Web Dev Bootcamp',      'url' => 'https://www.udemy.com/course/the-web-developer-bootcamp/'],
            ],
            'Digistore24' => [
                ['name' => 'Laravel Mastery Course', 'url' => 'https://www.digistore24.com'],
                ['name' => 'Vue.js Complete Guide',  'url' => 'https://www.digistore24.com'],
            ],
        ];

        foreach ($links as $networkName => $items) {
            $network = AffiliateNetwork::where('name', $networkName)->first();

            if (! $network) {
                continue;
            }

            foreach ($items as $item) {
                $slug = Str::slug($network->name.'-'.$item['name'], '-', 'de');

                AffiliateLink::firstOrCreate(
                    ['slug' => $slug],
                    [
                        'affiliate_network_id' => $network->id,
                        'name' => $item['name'],
                        'url' => $item['url'],
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
