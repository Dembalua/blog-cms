<?php

namespace App\Filament\Resources\Clusters\Pages;

use App\Filament\Resources\Clusters\ClusterResource;
use App\Filament\Resources\Clusters\Widgets\ClusterContentsWidget;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCluster extends EditRecord
{
    protected static string $resource = ClusterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            ClusterContentsWidget::class,
        ];
    }
}
