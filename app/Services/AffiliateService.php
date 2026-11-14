<?php

namespace App\Services;

use App\Models\AffiliateLink;
use App\Models\AffiliateLinkClick;
use App\Models\Content;
use Illuminate\Support\Facades\DB;

class AffiliateService
{
    public function trackClick(AffiliateLink $link, mixed $contentIdInput): void
    {
        $link->increment('clicks');

        $contentId = (int) $contentIdInput;

        if ($contentId <= 0 || ! Content::whereKey($contentId)->exists()) {
            return;
        }

        $affected = AffiliateLinkClick::where('affiliate_link_id', $link->id)
            ->where('content_id', $contentId)
            ->update([
                'clicks' => DB::raw('clicks + 1'),
                'last_clicked_at' => now(),
            ]);

        if ($affected === 0) {
            AffiliateLinkClick::create([
                'affiliate_link_id' => $link->id,
                'content_id' => $contentId,
                'clicks' => 1,
                'last_clicked_at' => now(),
            ]);
        }
    }
}
