<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum ContentType: string implements HasLabel, HasColor
{
    case POST = 'post';
    case PAGE = 'page';
    case HOMEPAGE = 'homepage';

    public function getLabel(): string|htmlable|null
    {
        return mb_strtoupper(__('enums.content_type.' . $this->value));
    }

    public function getColor(): string|array|null
    {
        return match($this) {
            self::POST     => 'primary',
            self::PAGE     => 'success',
            self::HOMEPAGE => 'warning',
        };
    }
}
