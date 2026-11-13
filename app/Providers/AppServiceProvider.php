<?php

namespace App\Providers;

use App\Models\AffiliateLink;
use App\Models\Content;
use App\Models\MenuItem;
use App\Models\Path;
use App\Observers\AffiliateLinkObserver;
use App\Observers\ContentObserver;
use App\Observers\MenuItemObserver;
use App\Observers\PathObserver;
use App\View\MenuComposer;
use App\View\SiteComposer;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use App\Mail\CmsError;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SiteComposer::class);

        $this->app->extend(
            HtmlSanitizerConfig::class,
            fn (HtmlSanitizerConfig $config) => $config
                ->allowElement('svg',      ['fill', 'stroke', 'stroke-width', 'width', 'height', 'aria-hidden', 'role'])
                ->allowElement('path',     ['fill', 'stroke', 'stroke-width', 'stroke-linecap', 'stroke-linejoin', 'fill-rule', 'clip-rule'])
                ->allowElement('circle',   ['cx', 'cy', 'r', 'fill', 'stroke', 'stroke-width'])
                ->allowElement('rect',     ['x', 'y', 'width', 'height', 'rx', 'ry', 'fill', 'stroke'])
                ->allowElement('line',     ['x1', 'y1', 'x2', 'y2', 'stroke', 'stroke-width', 'stroke-linecap'])
                ->allowElement('polyline', ['points', 'fill', 'stroke', 'stroke-width', 'stroke-linecap', 'stroke-linejoin'])
                ->allowElement('polygon',  ['points', 'fill', 'stroke'])
                ->allowElement('g',        ['fill', 'stroke', 'transform', 'opacity'])
                ->allowElement('defs',     [])
                ->allowAttribute('viewbox',     allowedElements: 'svg')
                ->allowAttribute('d',           allowedElements: 'path')
                ->allowAttribute('aria-hidden', allowedElements: '*'),
        );
    }

    public function boot(): void
    {
        FilamentAsset::register([
            Js::make('internal-link-extension', rescue(
                fn () => Vite::asset('resources/js/filament/internal-link-extension.js'),
                '',
                false,
            )),
            Js::make('affiliate-link-extension', rescue(
                fn () => Vite::asset('resources/js/filament/affiliate-link-extension.js'),
                '',
                false,
            )),
        ]);

        Path::observe(PathObserver::class);
        MenuItem::observe(MenuItemObserver::class);
        Content::observe(ContentObserver::class);
        AffiliateLink::observe(AffiliateLinkObserver::class);

        View::composer([
            'components.navigation.footer',
            'maintenance',
        ], MenuComposer::class);

        View::composer([
            'components.layouts.app',
            'components.navigation.footer',
            'components.navigation.header',
            'maintenance',
        ], SiteComposer::class);

        if (app()->isProduction()) {
            Event::listen(MessageLogged::class, function ($event) {
                if (in_array($event->level, ['error', 'critical', 'alert', 'emergency'])) {
                    Mail::to(config('app.log_mail_to'))->send(new CmsError($event->message, $event->context ?? []));
                }
            });
        }
    }
}
