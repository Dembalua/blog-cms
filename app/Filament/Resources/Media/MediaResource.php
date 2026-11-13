<?php

declare(strict_types=1);

namespace App\Filament\Resources\Media;


use App\Filament\Resources\Media\Tables\MediaTable;
use Awcodes\Curator\Resources\Media\MediaResource as BaseMediaResource;
use Filament\Tables\Table;

class MediaResource extends BaseMediaResource
{
    public static function table(Table $table): Table
    {
        return MediaTable::configure($table);
    }
}
