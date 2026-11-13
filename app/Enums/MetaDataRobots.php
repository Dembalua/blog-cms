<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum MetaDataRobots: string implements HasColor, HasLabel
{
    case INDEX_FOLLOW = 'index, follow';
    case NOINDEX_FOLLOW = 'noindex, follow';
    case NOINDEX_NOFOLLOW = 'noindex, nofollow';
    case INDEX_NOFOLLOW = 'index, nofollow';

    public function getLabel(): string|Htmlable|null
    {
        return mb_strtoupper(__('enums.meta_data_robots.'.$this->value));
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::INDEX_FOLLOW => 'success',
            self::NOINDEX_FOLLOW => 'warning',
            self::NOINDEX_NOFOLLOW => 'danger',
            self::INDEX_NOFOLLOW => 'warning',
        };
    }
}
