<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use App\Models\Category;
use App\Models\MenuItem;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function (DeleteAction $action, $record) {
                    if (MenuItem::where('model_type', Category::class)->where('model_id', $record->id)->exists()) {
                        Notification::make()
                            ->danger()
                            ->title(__('resource.category_resource.delete_blocked_title'))
                            ->body(__('resource.category_resource.delete_blocked_menu_body'))
                            ->send();

                        $action->halt();
                    }

                    if ($record->contents()->exists()) {
                        Notification::make()
                            ->danger()
                            ->title(__('resource.category_resource.delete_blocked_title'))
                            ->body(__('resource.category_resource.delete_blocked_contents_body'))
                            ->send();

                        $action->halt();
                    }
                }),
        ];
    }
}
