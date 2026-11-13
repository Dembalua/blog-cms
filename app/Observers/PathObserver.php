<?php

namespace App\Observers;

use App\Enums\RedirectStatus;
use App\Models\Category;
use App\Models\Path;
use App\Models\PathRedirect;
use App\Services\PathService;

class PathObserver
{
    public function updating(Path $path): void
    {
        if ($path->isDirty('url')) {
            $oldPath = $path->getOriginal('url');
            $newPath = $path->url;

            if ($oldPath === null) {
                if ($newPath !== null) {
                    PathRedirect::where('old_path', $newPath)->delete();
                }

                return;
            }

            if ($newPath === null) {
                PathRedirect::updateOrCreate(
                    ['old_path' => $oldPath],
                    ['new_path' => null, 'status_code' => RedirectStatus::GONE]
                );

                return;
            }

            PathRedirect::where('new_path', $oldPath)->update(['new_path' => $newPath]);

            PathRedirect::updateOrCreate(
                ['old_path' => $oldPath],
                [
                    'new_path' => $newPath,
                    'status_code' => RedirectStatus::MOVED_PERMANENTLY,
                ]
            );

            PathRedirect::where('old_path', $newPath)->delete();
        }
    }

    public function updated(Path $path): void
    {
        $service = app(PathService::class);

        if ($path->wasChanged('slug')) {
            $service->generateForModel($path->model);
        }

        if ($path->wasChanged('url') && $path->model instanceof Category) {
            $service->generateForCategory($path->model);
        }
    }

    public function created(Path $path): void
    {
        if ($path->url) {
            PathRedirect::where('old_path', $path->url)->delete();
        }

        app(PathService::class)->generateForModel($path->model);
    }
}
