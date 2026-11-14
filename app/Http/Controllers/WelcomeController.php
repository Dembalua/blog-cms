<?php

namespace App\Http\Controllers;

use App\Enums\ContentType;
use App\Models\Content;
use App\Models\Setting;
use App\Services\MetaService;

class WelcomeController extends Controller
{
    public function __invoke(MetaService $metaService): mixed
    {
        $meta = $metaService->resolveGlobal();

        $slogan = Setting::get('footer.slogan');
        if ($slogan) {
            view()->share('pageTitleFull', Setting::get('site.name').' | '.$slogan);
        }

        $homepageId = Setting::get('site.homepage_content');

        if ($homepageId) {
            $homepage = Content::with(['heroImage', 'author'])
                ->where('id', $homepageId)
                ->where('type', ContentType::HOMEPAGE)
                ->first();

            if ($homepage) {
                $meta['author'] = $homepage->author?->name;
                view()->share('meta', $meta);

                return view('pages.homepage', ['content' => $homepage]);
            }
        }

        view()->share('meta', $meta);

        $latestPosts = Content::with(['featureImage', 'path', 'category'])
            ->published()
            ->where('type', ContentType::POST)
            ->select('id', 'title', 'excerpt', 'category_id', 'feature_image_id', 'published_at', 'revised_at', 'is_featured')
            ->orderByDesc('published_at')
            ->take(3)
            ->get();

        return view('welcome', compact('latestPosts'));
    }
}
