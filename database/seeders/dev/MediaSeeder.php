<?php

namespace Database\Seeders\dev;

use App\Models\Media;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class MediaSeeder extends Seeder
{
    public function run(): void
    {
        $images = [
            [
                'filename' => 'web-development.webp',
                'name'     => 'web-development',
                'alt'      => 'Web Development',
                'title'    => 'Web Development',
                'type'     => 'image/webp',
                'ext'      => 'webp',
            ],
            [
                'filename' => 'design-ux.webp',
                'name'     => 'design-ux',
                'alt'      => 'Design & UX',
                'title'    => 'Design & UX',
                'type'     => 'image/webp',
                'ext'      => 'webp',
            ],
            [
                'filename' => 'devops.webp',
                'name'     => 'devops',
                'alt'      => 'DevOps',
                'title'    => 'DevOps',
                'type'     => 'image/webp',
                'ext'      => 'webp',
            ],
            [
                'filename' => 'tools-software.webp',
                'name'     => 'tools-software',
                'alt'      => 'Tools & Software',
                'title'    => 'Tools & Software',
                'type'     => 'image/webp',
                'ext'      => 'webp',
            ],
            [
                'filename' => 'career-learning.webp',
                'name'     => 'career-learning',
                'alt'      => 'Career & Learning',
                'title'    => 'Career & Learning',
                'type'     => 'image/webp',
                'ext'      => 'webp',
            ],
            [
                'filename' => 'logo-light.png',
                'name'     => 'logo-light',
                'alt'      => 'Logo',
                'title'    => 'Logo',
                'type'     => 'image/png',
                'ext'      => 'png',
            ],
            [
                'filename' => 'logo-dark.png',
                'name'     => 'logo-dark',
                'alt'      => 'Logo Dark',
                'title'    => 'Logo Dark',
                'type'     => 'image/png',
                'ext'      => 'png',
            ],
            [
                'filename' => 'favicon.svg',
                'name'     => 'favicon',
                'alt'      => 'Favicon',
                'title'    => 'Favicon',
                'type'     => 'image/svg+xml',
                'ext'      => 'svg',
            ],
        ];

        foreach ($images as $image) {
            $sourcePath = database_path('seeders/media/' . $image['filename']);
            $targetPath = 'seeder/' . $image['filename'];

            Storage::disk('public')->put(
                $targetPath,
                file_get_contents($sourcePath)
            );

            $imageSize = @getimagesize($sourcePath);

            Media::firstOrCreate(
                ['name' => $image['name']],
                [
                    'disk'       => 'public',
                    'directory'  => 'seeder',
                    'visibility' => 'public',
                    'path'       => $targetPath,
                    'width'      => $imageSize[0] ?? null,
                    'height'     => $imageSize[1] ?? null,
                    'size'       => filesize($sourcePath),
                    'type'       => $image['type'],
                    'ext'        => $image['ext'],
                    'alt'        => $image['alt'],
                    'title'      => $image['title'],
                ]
            );
        }
    }
}
