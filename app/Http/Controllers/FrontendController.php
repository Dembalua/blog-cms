<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Content;
use App\Services\BreadcrumbService;
use App\Services\CategoryPageService;
use App\Services\MetaService;
use App\Services\PathService;
use Illuminate\Http\RedirectResponse;

class FrontendController extends Controller
{
    public function show(MetaService $metaService, BreadcrumbService $breadcrumbService, AffiliateRedirectController $affiliateRedirect, PathService $pathService, CategoryPageService $categoryPageService, string $url = ''): mixed
    {
        if (str_starts_with($url, 'go/')) {
            $response = $affiliateRedirect->handle(substr($url, 3), request('c'));

            if ($response) {
                return $response;
            }
        }

        $result = $pathService->resolve($url);

        if ($result instanceof RedirectResponse) {
            return $result;
        }

        $path = $result;
        $model = $path->model;

        view()->share('meta', $metaService->resolve($model));
        view()->share('pageTitle', $model instanceof Content ? $model->title : $model->name);
        view()->share('breadcrumbs', $breadcrumbService->fromUrl($path->url));

        if ($model instanceof Content) {
            if ($model->type->value === 'post') {
                return view('pages.post', ['content' => $model]);
            }

            if ($model->type->value === 'page') {
                return view('pages.page', ['content' => $model]);
            }
        }

        if ($model instanceof Category) {
            return view('pages.category', $categoryPageService->buildPageData($model));
        }

        abort(404);
    }
}
