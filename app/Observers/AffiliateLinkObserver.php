<?php

namespace App\Observers;

use App\Enums\RedirectStatus;
use App\Models\AffiliateLink;
use App\Models\PathRedirect;

class AffiliateLinkObserver
{
    public function updating(AffiliateLink $link): void
    {
        if (! $link->isDirty('slug')) {
            return;
        }

        $oldPath = 'go/' . $link->getOriginal('slug');
        $newPath = 'go/' . $link->slug;

        PathRedirect::where('new_path', $oldPath)->update(['new_path' => $newPath]);

        PathRedirect::updateOrCreate(
            ['old_path' => $oldPath],
            [
                'new_path'    => $newPath,
                'status_code' => RedirectStatus::MOVED_PERMANENTLY,
            ]
        );

        PathRedirect::where('old_path', $newPath)->delete();
    }

    public function created(AffiliateLink $link): void
    {
        PathRedirect::where('old_path', 'go/' . $link->slug)->delete();
    }

    public function deleted(AffiliateLink $link): void
    {
        PathRedirect::updateOrCreate(
            ['old_path' => 'go/' . $link->slug],
            ['new_path' => null, 'status_code' => RedirectStatus::GONE]
        );
    }
}
