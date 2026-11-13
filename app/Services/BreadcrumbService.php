<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Path;
use Illuminate\Support\Collection;

class BreadcrumbService
{
    public function fromUrl(string $url): Collection
    {
        $segments = explode('/', trim($url, '/'));

        if (count($segments) <= 1) {
            return collect();
        }

        $prefixes = [];
        for ($i = 1; $i < count($segments); $i++) {
            $prefixes[] = implode('/', array_slice($segments, 0, $i));
        }

        $paths = Path::with('model')->whereIn('url', $prefixes)->get()->keyBy('url');

        return collect($prefixes)->map(function (string $prefix) use ($paths) {
            $path = $paths->get($prefix);

            if (! $path?->model) {
                return null;
            }

            $model = $path->model;
            $label = $model instanceof Category ? $model->name : $model->title;

            return ['label' => $label, 'url' => url($prefix)];
        })->filter()->values();
    }
}
