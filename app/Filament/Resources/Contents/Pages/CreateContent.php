<?php

namespace App\Filament\Resources\Contents\Pages;

use App\Filament\Resources\Contents\ContentResource;
use App\Models\Category;
use App\Models\Path;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateContent extends CreateRecord
{
    protected static string $resource = ContentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['author_id'] = auth()->id();
        return $data;
    }

    protected function beforeSave(): void
    {
        $slug       = $this->data['path']['slug'] ?? null;
        $categoryId = $this->data['category_id'] ?? null;

        if (! $slug || ! $categoryId) {
            return;
        }

        $categoryUrl = Category::find($categoryId)?->path?->url;

        if (! $categoryUrl) {
            return;
        }

        $potentialUrl = $categoryUrl . '/' . $slug;

        if (Path::where('url', $potentialUrl)->exists()) {
            Notification::make()
                ->danger()
                ->title(__('resource.content_resource.validation.url_exists', ['url' => $potentialUrl]))
                ->send();

            $this->halt();
        }
    }
}
