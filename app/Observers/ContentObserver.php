<?php

namespace App\Observers;

use App\Models\Content;
use App\Models\MenuItem;
use Illuminate\Validation\ValidationException;

class ContentObserver
{
    public function creating(Content $content): void
    {
        $this->guardAgainstMenuConflict($content);
    }

    public function updating(Content $content): void
    {
        if ($content->isDirty('category_id') && $content->category_id !== null) {
            $this->guardAgainstMenuConflict($content);
        }
    }

    private function guardAgainstMenuConflict(Content $content): void
    {
        if ($content->category_id === null) {
            return;
        }

        $inMenu = MenuItem::where('model_type', Content::class)
            ->where('model_id', $content->id)
            ->exists();

        if ($inMenu) {
            throw ValidationException::withMessages([
                'category_id' => __('validation.custom.content.menu_category_conflict'),
            ]);
        }
    }
}
