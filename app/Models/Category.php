<?php

namespace App\Models;

use App\Enums\RedirectStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\Cache;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sort',
        'active',
        'description',
        'image_id',
    ];

    protected static function booted(): void
    {
        static::deleting(function (Category $model) {
            $model->metaData()->delete();

            if ($model->path && $model->path->url) {
                PathRedirect::updateOrCreate(
                    ['old_path' => $model->path->url],
                    [
                        'new_path'    => null,
                        'status_code' => RedirectStatus::GONE,
                    ]
                );
            }

            $model->path()->delete();
        });

        static::saved(function (Category $model) {
            MenuItem::where('model_type', Category::class)
                ->where('model_id', $model->id)
                ->pluck('menu_id')
                ->each(fn($menuId) => Cache::forget("menu_{$menuId}"));
        });
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'image_id');
    }

    public function contents(): HasMany
    {
        return $this->hasMany(Content::class, 'category_id');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function metaData(): MorphOne
    {
        return $this->morphOne(MetaData::class, 'model');
    }

    public function path(): MorphOne
    {
        return $this->morphOne(Path::class, 'model');
    }
}
