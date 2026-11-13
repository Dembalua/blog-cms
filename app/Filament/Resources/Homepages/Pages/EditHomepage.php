<?php

namespace App\Filament\Resources\Homepages\Pages;

use App\Filament\Resources\Homepages\HomepageResource;
use App\Models\Setting;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditHomepage extends EditRecord
{
    protected static string $resource = HomepageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function (DeleteAction $action) {
                    if ((int) Setting::get('site.homepage_content') === (int) $this->record->id) {
                        Notification::make()
                            ->danger()
                            ->title(__('resource.homepage_resource.validation.active_cannot_delete'))
                            ->send();
                        $action->halt();
                    }
                }),
        ];
    }
}
