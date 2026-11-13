<?php

namespace App\Filament\Resources\AffiliateLinks\Pages;

use App\Filament\Resources\AffiliateLinks\AffiliateLinkResource;
use App\Filament\Resources\AffiliateLinks\Widgets\AffiliateLinkUsageWidget;
use App\Models\Content;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditAffiliateLink extends EditRecord
{
    protected static string $resource = AffiliateLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function (DeleteAction $action, $record) {
                    if (Content::where('content', 'like', '%"affiliateLinkId":'.$record->id.'%')->exists()) {
                        Notification::make()
                            ->danger()
                            ->title(__('resource.affiliate_link_resource.delete_blocked_title'))
                            ->body(__('resource.affiliate_link_resource.delete_blocked_body'))
                            ->send();

                        $action->halt();
                    }
                }),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            AffiliateLinkUsageWidget::class,
        ];
    }
}
