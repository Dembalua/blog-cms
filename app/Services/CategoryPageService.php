<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Content;
use App\Models\MenuItem;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class CategoryPageService
{
    public function buildPageData(Category $category): array
    {
        [$categoryIds, $directContentIds, $subCategoryIds, $subCategoryChildIds] = Cache::rememberForever(
            "category_menu_ids_v3_{$category->id}",
            function () use ($category) {
                $menuItem = MenuItem::with('children.children')
                    ->where('model_type', Category::class)
                    ->where('model_id', $category->id)
                    ->first();

                $categoryIds = [$category->id];
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
            ->published()
            ->select('id', 'title', 'excerpt', 'category_id', 'feature_image_id', 'published_at', 'revised_at', 'is_featured')
            ->orderByDesc('published_at')
            ->paginate(Setting::get('content.category_page_size') ?? 12);

        $categoryMap = collect([$category])
            ->merge($subCategories)
            ->merge($grandchildren)
            ->keyBy('id');

        return [
            'category' => $category,
            'contents' => $contents,
            'subCategories' => $subCategories,
            'navTree' => $navTree,
            'categoryMap' => $categoryMap,
        ];
    }
}
