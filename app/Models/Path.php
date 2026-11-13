<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Path extends Model
{
    const RESERVED_SLUGS = ['rss', 'feeds', 'contact'];

    protected $fillable = [
        'slug',
        'url',
    ];

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    protected function slug(): Attribute
    {
        return Attribute::make(
            set: fn(?string $value) => $value !== null ? trim($value, '/') : null,
        );
    }

    protected function url(): Attribute
    {
        return Attribute::make(
            set: fn(?string $value) => $value !== null ? trim($value, '/') : null,
        );
    }
}

