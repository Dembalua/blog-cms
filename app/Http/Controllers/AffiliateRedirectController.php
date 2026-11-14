<?php

namespace App\Http\Controllers;

use App\Models\AffiliateLink;
use App\Services\AffiliateService;
use Illuminate\Http\RedirectResponse;

class AffiliateRedirectController extends Controller
{
    public function __construct(private readonly AffiliateService $affiliate) {}

    public function handle(string $slug, mixed $contentIdInput): ?RedirectResponse
    {
        $link = AffiliateLink::where('slug', $slug)->first();

        if (! $link) {
            return null;
        }

        if (! $link->is_active) {
            return redirect('/');
        }

        $this->affiliate->trackClick($link, $contentIdInput);

        return redirect($link->url, 302);
    }
}
