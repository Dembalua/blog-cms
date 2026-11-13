<?php

namespace App\Http\Middleware;

use App\Models\Content;
use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('admin*') || $request->is('livewire*') || $request->is('up')) {
            return $next($request);
        }

        if (! Setting::get('maintenance.enabled')) {
            return $next($request);
        }

        $allowedIps = array_filter(
            array_map('trim', explode("\n", Setting::get('maintenance.allowed_ips') ?? ''))
        );

        if (in_array($request->ip(), $allowedIps)) {
            return $next($request);
        }

        $allowedContentIds = json_decode(Setting::get('maintenance.pages') ?? '[]', true);

        if (! empty($allowedContentIds)) {
            $currentPath = ltrim($request->path(), '/');

            $allowedPaths = Content::whereIn('id', $allowedContentIds)
                ->with('path')
                ->get()
                ->map(fn ($c) => ltrim($c->path?->url ?? '', '/'))
                ->filter()
                ->values();

            if ($allowedPaths->contains($currentPath)) {
                view()->share('maintenanceModeActive', true);

                return $next($request);
            }
        }

        view()->share('maintenanceModeActive', true);

        return response()->view('maintenance', [], 503);
    }
}
