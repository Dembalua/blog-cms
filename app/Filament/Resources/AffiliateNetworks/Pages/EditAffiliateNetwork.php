<?php

namespace App\Filament\Resources\AffiliateNetworks\Pages;

use App\Filament\Resources\AffiliateNetworks\AffiliateNetworkResource;
use App\Filament\Resources\AffiliateNetworks\Widgets\AffiliateNetworkLinksWidget;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditAffiliateNetwork extends EditRecord
{
    protected static string $resource = AffiliateNetworkResource::class;

    protected function getFooterWidgets(): array
    {
        return [
            AffiliateNetworkLinksWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function (DeleteAction $action, $record) {
                    if ($record->affiliateLinks()->exists()) {
                        Notification::make()
                            ->danger()
                            ->title(__('resource.affiliate_network_resource.delete_blocked_title'))
                            ->body(__('resource.affiliate_network_resource.delete_blocked_body'))
                            ->send();

                        $action->halt();
                    }
                }),
        ];
    }
}
