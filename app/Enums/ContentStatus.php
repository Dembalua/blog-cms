<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum ContentStatus: string implements HasLabel, HasColor
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case SCHEDULED = 'scheduled';
    case ARCHIVED  = 'archived';

    public function getLabel(): string|htmlable|null
    {
        return mb_strtoupper(__('enums.content_status.' . $this->value));
    }

    public function getColor(): string|array|null
    {
        return match($this) {
            self::DRAFT     => 'warning',
            self::PUBLISHED => 'success',
            self::SCHEDULED => 'primary',
            self::ARCHIVED  => 'danger',
        };
    }
}
