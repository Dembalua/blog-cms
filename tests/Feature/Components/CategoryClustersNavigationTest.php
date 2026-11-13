<?php

namespace Tests\Feature\Components;

use App\Models\Cluster;
use App\Models\Content;
use App\View\Components\CategoryClustersNavigation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryClustersNavigationTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // shouldRender
    // -------------------------------------------------------------------------

    public function test_does_not_render_when_content_is_not_a_pillar(): void
    {
        $cluster  = Cluster::factory()->create();
        $content  = Content::factory()->published()->create(['cluster_id' => $cluster->id, 'is_pillar' => null]);
        $component = new CategoryClustersNavigation($content);

        $this->assertFalse($component->shouldRender());
    }

    public function test_does_not_render_when_content_has_no_cluster(): void
    {
        $content   = Content::factory()->published()->create(['cluster_id' => null, 'is_pillar' => true]);
        $component = new CategoryClustersNavigation($content);

        $this->assertFalse($component->shouldRender());
    }

    public function test_does_not_render_when_cluster_has_no_related_clusters(): void
    {
        $cluster  = Cluster::factory()->create();
        $pillar   = Content::factory()->published()->create(['cluster_id' => $cluster->id, 'is_pillar' => true]);
        $component = new CategoryClustersNavigation($pillar);

        $this->assertFalse($component->shouldRender());
    }

    public function test_does_not_render_when_related_cluster_has_no_published_pillar(): void
    {
        $clusterA = Cluster::factory()->create();
        $clusterB = Cluster::factory()->create();
        $clusterA->relatedClusters()->attach($clusterB->id);

        $pillarA  = Content::factory()->published()->create(['cluster_id' => $clusterA->id, 'is_pillar' => true]);
        Content::factory()->draft()->create(['cluster_id' => $clusterB->id, 'is_pillar' => true]);

        $component = new CategoryClustersNavigation($pillarA);

        $this->assertFalse($component->shouldRender());
    }

    public function test_renders_when_related_cluster_has_published_pillar(): void
    {
        $clusterA = Cluster::factory()->create();
        $clusterB = Cluster::factory()->create();
        $clusterA->relatedClusters()->attach($clusterB->id);

        $pillarA = Content::factory()->published()->create(['cluster_id' => $clusterA->id, 'is_pillar' => true]);
        Content::factory()->published()->create(['cluster_id' => $clusterB->id, 'is_pillar' => true]);

        $component = new CategoryClustersNavigation($pillarA);

        $this->assertTrue($component->shouldRender());
    }

    // -------------------------------------------------------------------------
    // Pillars data
    // -------------------------------------------------------------------------

    public function test_pillars_contains_pillar_articles_of_related_clusters(): void
    {
        $clusterA = Cluster::factory()->create();
        $clusterB = Cluster::factory()->create();
        $clusterC = Cluster::factory()->create();
        $clusterA->relatedClusters()->attach([$clusterB->id, $clusterC->id]);

        $pillarA = Content::factory()->published()->create(['cluster_id' => $clusterA->id, 'is_pillar' => true]);
        Content::factory()->published()->create(['cluster_id' => $clusterB->id, 'is_pillar' => true, 'title' => 'Pillar B']);
        Content::factory()->published()->create(['cluster_id' => $clusterC->id, 'is_pillar' => true, 'title' => 'Pillar C']);

        $component = new CategoryClustersNavigation($pillarA);
        $titles    = $component->pillars->pluck('title');

        $this->assertContains('Pillar B', $titles);
        $this->assertContains('Pillar C', $titles);
    }

    public function test_pillars_does_not_include_non_pillar_articles_from_related_clusters(): void
    {
        $clusterA = Cluster::factory()->create();
        $clusterB = Cluster::factory()->create();
        $clusterA->relatedClusters()->attach($clusterB->id);

        $pillarA = Content::factory()->published()->create(['cluster_id' => $clusterA->id, 'is_pillar' => true]);
        Content::factory()->published()->create(['cluster_id' => $clusterB->id, 'is_pillar' => true]);
        Content::factory()->published()->create(['cluster_id' => $clusterB->id, 'is_pillar' => null, 'title' => 'Sub Article B']);

        $component = new CategoryClustersNavigation($pillarA);

        $this->assertFalse($component->pillars->contains('title', 'Sub Article B'));
    }

    public function test_relationship_is_one_directional(): void
    {
        $clusterA = Cluster::factory()->create();
        $clusterB = Cluster::factory()->create();
        $clusterA->relatedClusters()->attach($clusterB->id);

        Content::factory()->published()->create(['cluster_id' => $clusterA->id, 'is_pillar' => true, 'title' => 'Pillar A']);
        $pillarB = Content::factory()->published()->create(['cluster_id' => $clusterB->id, 'is_pillar' => true]);

        $component = new CategoryClustersNavigation($pillarB);

        $this->assertFalse($component->shouldRender());
        $this->assertFalse($component->pillars->contains('title', 'Pillar A'));
    }
}
