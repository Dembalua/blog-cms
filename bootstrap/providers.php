<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Services\CuratorServiceProvider;
use App\Providers\Services\LanguageServiceProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    LanguageServiceProvider::class,
    CuratorServiceProvider::class,
];
