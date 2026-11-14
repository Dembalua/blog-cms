<?php

namespace App\Services;

use App\Contracts\PubliclyVisible;
use App\Enums\RedirectStatus;
use App\Models\Category;
use App\Models\Content;
use App\Models\MenuItem;
use App\Models\Path;
use App\Models\PathRedirect;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;

class PathService
{
    public function resolve(string $url): Path|RedirectResponse
    {
        $path = Path::with([
            'model' => fn ($q) => $q->morphWith([
                Category::class => ['metaData', 'image'],
                Content::class => ['metaData', 'featureImage', 'author'],
            ]),
        ])->where('url', $url)->first();

        if ($path) {
            if ($path->model instanceof PubliclyVisible && ! $path->model->isPubliclyVisible()) {
                abort(404);
            }

            return $path;
        }

        $redirect = PathRedirect::where('old_path', $url)->first();

        if (! $redirect) {
            abort(404);
        }

        if ($redirect->status_code === RedirectStatus::GONE || $redirect->new_path === null) {
            abort(410);
        }

        return redirect($redirect->new_path, $redirect->status_code->value);
    }

    public function generateForModel(Model $model): void
    {
        $menuItem = MenuItem::where('model_type', get_class($model))
            ->where('model_id', $model->id)
            ->first();

        if ($menuItem) {
            $url = $this->buildUrl($menuItem);
        } elseif ($model instanceof Content && $model->category_id) {
            $model->loadMissing('category.path');
            $categoryUrl = $model->category?->path?->url;
            $slug = $model->path?->slug;

            if (! $categoryUrl || ! $slug) {
                return;
            }

            $url = $categoryUrl.'/'.$slug;
        } else {
            return;
        }

        $path = $model->path;

        if (! $path) {
            return;
        }

        if ($path->url !== $url) {
            $path->update(['url' => $url]);
        }
    }

    public function generateForMenuItem(MenuItem $menuItem): void
    {
        $this->generateForModel($menuItem->model);

        foreach ($menuItem->children as $child) {
            $this->generateForMenuItem($child);
        }
    }

    // When a category URL changes, regenerate URLs for all its content
    public function generateForCategory(Model $category): void
    {
        Content::with('path')
            ->where('category_id', $category->id)
            ->get()
            ->each(fn ($content) => $this->generateForModel($content));
    }

    private function buildUrl(MenuItem $menuItem): string
    {
        $segments = [];
        $current = $menuItem;

        while ($current !== null) {
            $model = $current->model;

            if (! $model || ! $model->path) {
                break;
            }

            array_unshift($segments, $model->path->slug);
            $current = $current->parent_id ? $current->parent : null;
        }

        return implode('/', array_filter($segments));
    }
}
