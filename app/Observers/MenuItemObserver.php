<?php

namespace App\Observers;

use App\Models\Content;
use App\Models\MenuItem;
use App\Services\PathService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class MenuItemObserver
{
    public function creating(MenuItem $menuItem): void
    {
        $this->guardAgainstCategorizedContent($menuItem);
    }

    public function updating(MenuItem $menuItem): void
    {
        if ($menuItem->isDirty(['model_type', 'model_id'])) {
            $this->guardAgainstCategorizedContent($menuItem);
        }
    }

    private function guardAgainstCategorizedContent(MenuItem $menuItem): void
    {
        if ($menuItem->model_type !== Content::class) {
            return;
        }

        $content = Content::find($menuItem->model_id);

        if ($content && $content->category_id !== null) {
            throw ValidationException::withMessages([
                'model_id' => __('validation.custom.menu_item.categorized_content'),
            ]);
        }
    }

    public function created(MenuItem $menuItem): void
    {
        app(PathService::class)->generateForMenuItem($menuItem);
    }

    public function updated(MenuItem $menuItem): void
    {
        app(PathService::class)->generateForMenuItem($menuItem);

        // If the parent changed, siblings and their descendants may be affected
        if ($menuItem->wasChanged('parent_id')) {
            $oldParentId = $menuItem->getOriginal('parent_id');

            if ($oldParentId) {
                $oldParent = MenuItem::find($oldParentId);

                if ($oldParent) {
                    app(PathService::class)->generateForMenuItem($oldParent);
                }
            }
        }
    }

    public function deleted(MenuItem $menuItem): void
    {
        // Remove the url from the path when the menu item is deleted
        // The model itself and its path record remain, but url becomes null
        if ($menuItem->model && $menuItem->model->path) {
            $menuItem->model->path->update(['url' => null]);
        }

        // Regenerate paths for all children (they will also lose their url
        // since their parent no longer exists in the menu)
        foreach ($menuItem->children as $child) {
            app(PathService::class)->generateForMenuItem($child);
        }
    }
}
