<?php

namespace App\View;

use App\Models\Content;
use App\Models\Media;
use Illuminate\View\View;

class SiteComposer
{
    public function compose(View $view): void
    {
        $view->with(once(function () {
            $logoId = setting('site.logo');
            $logoDarkId = setting('site.logo_dark');
            $faviconId = setting('site.favicon');

            $media = Media::whereIn('id', array_filter([$logoId, $logoDarkId, $faviconId]))
                ->get()
                ->keyBy('id');

            $privacyPageId = setting('site.privacy_page');
            $sitePrivacyUrl = $privacyPageId
                ? Content::find($privacyPageId)?->path?->url
                : null;

            return [
                'siteName' => setting('site.name'),
                'siteLogo' => $media->get($logoId),
                'siteLogoDark' => $media->get($logoDarkId),
                'siteFavicon' => $media->get($faviconId),
                'socialInstagram' => setting('social.instagram'),
                'socialYoutube' => setting('social.youtube'),
                'socialPinterest' => setting('social.pinterest'),
                'footerSlogan' => setting('footer.slogan'),
                'sitePrivacyUrl' => $sitePrivacyUrl,
            ];
        }));
    }
}
