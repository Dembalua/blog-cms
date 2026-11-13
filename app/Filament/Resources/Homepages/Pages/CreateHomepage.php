<?php

namespace App\Filament\Resources\Homepages\Pages;

use App\Enums\ContentStatus;
use App\Enums\ContentType;
use App\Filament\Resources\Homepages\HomepageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHomepage extends CreateRecord
{
    protected static string $resource = HomepageResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['type'] = ContentType::HOMEPAGE;
        $data['status'] = ContentStatus::PUBLISHED;
        $data['author_id'] = $data['author_id'] ?? auth()->id();

        return $data;
    }
}
