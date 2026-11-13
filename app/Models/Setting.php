<?php

namespace App\Models;

use App\Services\ThemeService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
    ];

    public function metaData(): MorphOne
    {
        return $this->morphOne(MetaData::class, 'model');
    }

    public static function get(string $groupKey): mixed
    {
        return Cache::rememberForever(
            "setting.{$groupKey}",
            function () use ($groupKey) {
                [$group, $key] = explode('.', $groupKey, 2);

                $record = static::where('group', $group)
                    ->where('key', $key)
                    ->first();

                return $record?->getCastedValue();
            }
        );
    }

    public static function set(string $groupKey, mixed $value, string $type = 'string'): void
    {
        [$group, $key] = explode('.', $groupKey, 2);

        static::updateOrCreate(
            ['group' => $group, 'key' => $key],
            ['value' => $value, 'type' => $type]
        );
    }

    public static function clearCache(string $groupKey): void
    {
        Cache::forget("setting.{$groupKey}");
    }

    public function getCastedValue(): mixed
    {
        if ($this->value === null) {
            return null;
        }

        return match ($this->type) {
            'integer' => (int) $this->value,
            'boolean' => (bool) $this->value,
            default => $this->value,
        };
    }

    protected static function booted(): void
    {
        static::saved(function (Setting $setting) {
            if ($setting->group && $setting->key) {
                static::clearCache("{$setting->group}.{$setting->key}");
            }
            if ($setting->group === 'theme') {
                app(ThemeService::class)->generateVariables();
            }
        });

        static::deleted(function (Setting $setting) {
            if ($setting->group && $setting->key) {
                static::clearCache("{$setting->group}.{$setting->key}");
            }
        });
    }
}
