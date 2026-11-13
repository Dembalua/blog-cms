<?php

namespace App\Filament\Resources\Contents\Pages;

use App\Filament\Resources\Contents\ContentResource;
use App\Models\Category;
use App\Models\Path;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditContent extends EditRecord
{
    protected static string $resource = ContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function beforeSave(): void
    {
        $this->validateUrlUniqueness($this->record->path?->id);
    }

    private function validateUrlUniqueness(?int $excludePathId): void
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

        $exists = Path::where('url', $potentialUrl)
            ->when($excludePathId, fn($q, $id) => $q->where('id', '!=', $id))
            ->exists();

        if ($exists) {
            Notification::make()
                ->danger()
                ->title(__('resource.content_resource.validation.url_exists', ['url' => $potentialUrl]))
                ->send();

            $this->halt();
        }
    }
}
