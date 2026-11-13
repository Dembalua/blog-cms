<?php

namespace App\View;

use App\Enums\ContentStatus;
use App\Models\Category;
use App\Models\Content;
use App\Models\MenuItem;
use App\Models\Setting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class MenuComposer
{
    private static array $requestCache = [];

    public static function flushRequestCache(): void
    {
        self::$requestCache = [];
    }

    public function compose(View $view): void
    {
        $maintenanceModeActive = view()->shared('maintenanceModeActive', false);

        $view->with([
            'headerMenuItems' => $this->getMenuBySetting('menu.header'),
            'footerMenuItems' => $maintenanceModeActive
                ? $this->getMaintenancePages()
                : $this->getMenuBySetting('menu.footer'),
        ]);
    }

    private function getMaintenancePages(): Collection
    {
        $ids = json_decode(Setting::get('maintenance.pages') ?? '[]', true);

        if (empty($ids)) {
            return collect();
        }

        return Content::whereIn('id', $ids)
            ->with('path')
            ->get()
            ->map(fn ($content) => (object) [
                'title' => $content->title,
                'model' => $content,
            ]);
    }

    public function getMenuBySetting(string $settingKey)
    {
        if (array_key_exists($settingKey, self::$requestCache)) {
            return self::$requestCache[$settingKey];
        }

        $menuId = Setting::get($settingKey);

        if (empty($menuId)) {
            return self::$requestCache[$settingKey] = collect();
        }

        return self::$requestCache[$settingKey] = Cache::rememberForever("menu_{$menuId}", function () use ($menuId) {
            $withChildren = function ($query) use (&$withChildren) {
                $query->where(function ($q) {
                    $q->whereHasMorph('model', [Content::class], fn ($q) => $q->where('status', ContentStatus::PUBLISHED))
                        ->orWhereHasMorph('model', [Category::class]);
                })
                    ->orderBy('order')
                    ->with(['children' => $withChildren, 'model', 'model.path']);
            };

            return MenuItem::whereNull('parent_id')
                ->with(['children' => $withChildren, 'model', 'model.path'])
                ->where('menu_id', $menuId)
                ->where(function ($query) {
                    $query->whereHasMorph('model', [Content::class], fn ($q) => $q->where('status', ContentStatus::PUBLISHED))
                        ->orWhereHasMorph('model', [Category::class], fn ($q) => $q->where('active', true));
                })
                ->orderBy('order')
                ->get();
        });
    }
}
