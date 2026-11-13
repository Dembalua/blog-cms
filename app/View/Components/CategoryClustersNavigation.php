<?php

namespace App\View\Components;

use App\Enums\ContentStatus;
use App\Models\Content;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;

class CategoryClustersNavigation extends Component
{
    public Collection $pillars;

    public function __construct(private readonly Content $content)
    {
        $this->pillars = new Collection();

        if (! $content->is_pillar || ! $content->cluster_id) {
            return;
        }

        $content->loadMissing('cluster.relatedClusters');

        $relatedClusterIds = $content->cluster->relatedClusters->pluck('id');

        if ($relatedClusterIds->isEmpty()) {
            return;
        }

        $this->pillars = Content::whereIn('cluster_id', $relatedClusterIds)
            ->where('is_pillar', true)
            ->where('status', ContentStatus::PUBLISHED)
            ->with(['path', 'cluster', 'featureImage'])
            ->select(['id', 'title', 'cluster_id', 'is_pillar', 'feature_image_id'])
            ->get();
    }

    public function shouldRender(): bool
    {
        return $this->pillars->isNotEmpty();
    }

    public function render(): View
    {
        return view('components.category-clusters-navigation');
    }
}
