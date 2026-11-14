<?php

namespace App\Http\Controllers;

use App\Enums\ContentStatus;
use App\Enums\RedirectStatus;
use App\Models\Category;
use App\Models\Content;
use App\Models\MenuItem;
use App\Models\Path;
use App\Models\PathRedirect;
use App\Models\Setting;
use App\Services\BreadcrumbService;
use App\Services\MetaService;
use Illuminate\Support\Facades\Cache;

class FrontendController extends Controller
{
    public function show(MetaService $metaService, BreadcrumbService $breadcrumbService, AffiliateRedirectController $affiliateRedirect, string $url = ''): mixed
    {
        if (str_starts_with($url, 'go/')) {
            $response = $affiliateRedirect->handle(substr($url, 3), request('c'));

            if ($response) {
                return $response;
            }
        }

        $path = Path::with([
            'model' => fn ($q) => $q->morphWith([
                Category::class => ['metaData', 'image'],
                Content::class => ['metaData', 'featureImage', 'author'],
            ]),
        ])->where('url', $url)->first();

        if (! $path) {
            $redirect = PathRedirect::where('old_path', $url)->first();

            if (! $redirect) {
                abort(404);
            }

            if ($redirect->status_code === RedirectStatus::GONE || $redirect->new_path === null) {
                abort(410);
            }

            return redirect($redirect->new_path, $redirect->status_code->value);
        }

        $model = $path->model;

        view()->share('meta', $metaService->resolve($model));
        view()->share('pageTitle', $model instanceof Content ? $model->title : $model->name);
        view()->share('breadcrumbs', $breadcrumbService->fromUrl($path->url));

        if ($model instanceof Content) {
            if ($model->status !== ContentStatus::PUBLISHED) {
                abort(404);
            }

            if ($model->type->value === 'post') {
                return view('pages.post', ['content' => $model]);
            }

            if ($model->type->value === 'page') {
                return view('pages.page', ['content' => $model]);
            }
        }

        if ($model instanceof Category) {
            if (! $model->active) {
                abort(404);
            }

            [$categoryIds, $directContentIds, $subCategoryIds, $subCategoryChildIds] = Cache::rememberForever(
                "category_menu_ids_v3_{$model->id}",
                function () use ($model) {
                    $menuItem = MenuItem::with('children.children')
                        ->where('model_type', Category::class)
                        ->where('model_id', $model->id)
                        ->first();

                    $categoryIds = [$model->id];
                    $directContentIds = [];
                    $subCategoryIds = [];
                    $subCategoryChildIds = [];

                    if ($menuItem) {
                        foreach ($menuItem->children as $child) {
                            if ($child->model_type === Category::class) {
                                $categoryIds[] = $child->model_id;
                                $subCategoryIds[] = $child->model_id;
                                $subCategoryChildIds[$child->model_id] = [];
                            } elseif ($child->model_type === Content::class) {
                                $directContentIds[] = $child->model_id;
                            }
                            foreach ($child->children as $grandchild) {
                                if ($grandchild->model_type === Category::class) {
                                    $categoryIds[] = $grandchild->model_id;
                                    if (array_key_exists($child->model_id, $subCategoryChildIds)) {
                                        $subCategoryChildIds[$child->model_id][] = $grandchild->model_id;
                                    }
                                } elseif ($grandchild->model_type === Content::class) {
                                    $directContentIds[] = $grandchild->model_id;
                                }
                            }
                        }
                    }

                    return [$categoryIds, $directContentIds, $subCategoryIds, $subCategoryChildIds];
                }
            );

            $subCategories = $subCategoryIds
                ? Category::whereIn('id', $subCategoryIds)->with('path')->active()->get()
                : collect();

            $allGrandchildIds = $subCategoryChildIds ? array_unique(array_merge(...array_values($subCategoryChildIds))) : [];
            $grandchildren = $allGrandchildIds
                ? Category::whereIn('id', $allGrandchildIds)->with('path')->active()->get()->keyBy('id')
                : collect();

            $navTree = $subCategories->map(fn ($sub) => [
                'category' => $sub,
                'children' => collect($subCategoryChildIds[$sub->id] ?? [])
                    ->map(fn ($id) => $grandchildren->get($id))
                    ->filter()
                    ->values(),
            ]);

            $contents = Content::with('featureImage', 'path')
                ->where(function ($query) use ($categoryIds, $directContentIds) {
                    $query->whereIn('category_id', $categoryIds);
                    if (! empty($directContentIds)) {
                        $query->orWhereIn('id', $directContentIds);
                    }
                })
                ->where('status', ContentStatus::PUBLISHED)
                ->select('id', 'title', 'excerpt', 'category_id', 'feature_image_id', 'published_at', 'revised_at', 'is_featured')
                ->orderByDesc('published_at')
                ->paginate(Setting::get('content.category_page_size') ?? 12);

            $categoryMap = collect([$model])
                ->merge($subCategories)
                ->merge($grandchildren)
                ->keyBy('id');

            return view('pages.category', [
                'category' => $model,
                'contents' => $contents,
                'subCategories' => $subCategories,
                'navTree' => $navTree,
                'categoryMap' => $categoryMap,
            ]);
        }

        abort(404);
    }
}
