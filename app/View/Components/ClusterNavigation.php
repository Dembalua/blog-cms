<?php

namespace App\View\Components;

use App\Enums\ContentStatus;
use App\Models\Content;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;

class ClusterNavigation extends Component
{
    public bool $isPillar;
    public ?Content $pillar;
    public Collection $subArticles;
    public ?string $clusterTitle;

    public function __construct(private readonly Content $content)
    {
        $related = new Collection();

        if ($content->cluster_id) {
            $related = Content::where('cluster_id', $content->cluster_id)
                ->where('id', '!=', $content->id)
                ->where('status', ContentStatus::PUBLISHED)
                ->with(['path', 'featureImage', 'category'])
                ->select(['id', 'title', 'excerpt', 'cluster_id', 'is_pillar', 'feature_image_id', 'category_id', 'published_at', 'revised_at'])
                ->get();
        }

        $this->isPillar     = (bool) $content->is_pillar;
        $this->pillar       = $this->isPillar ? null : $related->firstWhere('is_pillar', true);
        $this->subArticles  = $this->isPillar
            ? $related
            : $related->where('is_pillar', '!=', true)->values();
        $this->clusterTitle = $content->cluster?->title;
    }

    public function shouldRender(): bool
    {
        return $this->subArticles->isNotEmpty() || (! $this->isPillar && $this->pillar !== null);
    }

    public function render(): View
    {
        return view('components.cluster-navigation');
    }
}
