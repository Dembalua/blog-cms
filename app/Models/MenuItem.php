<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Cache;
use SolutionForest\FilamentTree\Concern\ModelTree;

class MenuItem extends Model
{
    use HasFactory;
    use ModelTree;
    protected $fillable = [
        'menu_id',
        'title',
        'model_id',
        'model_type',
        'parent_id',
        'order',
    ];

    protected static function booted(): void
    {
        static::saved(function (MenuItem $item) {
            Cache::forget('menu_' . $item->menu_id);
            static::clearCategoryMenuCache($item);
        });

        static::deleted(function (MenuItem $item) {
            Cache::forget('menu_' . $item->menu_id);
            static::clearCategoryMenuCache($item);
        });
    }

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    public function parent():BelongsTo
    {
        return $this->belongsTo(self::class,'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class,'parent_id');
    }

    private static function clearCategoryMenuCache(MenuItem $item): void
    {
        if ($item->model_type === Category::class) {
            Cache::forget("category_menu_ids_{$item->model_id}");
        }

        $current = $item->parent;
        while ($current !== null) {
            if ($current->model_type === Category::class) {
                Cache::forget("category_menu_ids_{$current->model_id}");
            }
            $current = $current->parent;
        }
    }

    public function getDepth(): int
    {
        $depth = 0;
        $item = $this;

        while ($item->parent_id !== null) {
            $depth++;
            $item = $item->parent;
        }

        return $depth;
    }
}
