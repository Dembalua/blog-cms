<?php

namespace Tests\Feature\Components;

use App\Models\Cluster;
use App\Models\Content;
use App\View\Components\ClusterNavigation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClusterNavigationTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // shouldRender
    // -------------------------------------------------------------------------

    public function test_does_not_render_when_content_has_no_cluster(): void
    {
        $content = Content::factory()->published()->create(['cluster_id' => null]);
        $component = new ClusterNavigation($content);

        $this->assertFalse($component->shouldRender());
    }

    public function test_does_not_render_when_no_published_related_articles_exist(): void
    {
        $cluster = Cluster::factory()->create();
        $content = Content::factory()->published()->create(['cluster_id' => $cluster->id, 'is_pillar' => true]);
        $component = new ClusterNavigation($content);

        $this->assertFalse($component->shouldRender());
    }

    public function test_does_not_render_when_related_articles_are_not_published(): void
    {
        $cluster = Cluster::factory()->create();
        $pillar = Content::factory()->published()->create(['cluster_id' => $cluster->id, 'is_pillar' => true]);
        Content::factory()->draft()->create(['cluster_id' => $cluster->id]);

        $component = new ClusterNavigation($pillar);

        $this->assertFalse($component->shouldRender());
    }

    public function test_renders_when_published_related_articles_exist(): void
    {
        $cluster = Cluster::factory()->create();
        $pillar = Content::factory()->published()->create(['cluster_id' => $cluster->id, 'is_pillar' => true]);
        Content::factory()->published()->create(['cluster_id' => $cluster->id]);

        $component = new ClusterNavigation($pillar);

        $this->assertTrue($component->shouldRender());
    }

    // -------------------------------------------------------------------------
    // Pillar context
    // -------------------------------------------------------------------------

    public function test_is_pillar_is_true_for_pillar_content(): void
    {
        $cluster = Cluster::factory()->create();
        $pillar = Content::factory()->published()->create(['cluster_id' => $cluster->id, 'is_pillar' => true]);
        Content::factory()->published()->create(['cluster_id' => $cluster->id]);

        $component = new ClusterNavigation($pillar);

        $this->assertTrue($component->isPillar);
    }

    public function test_sub_articles_are_populated_for_pillar(): void
    {
        $cluster = Cluster::factory()->create();
        $pillar = Content::factory()->published()->create(['cluster_id' => $cluster->id, 'is_pillar' => true]);
        Content::factory()->published()->create(['cluster_id' => $cluster->id, 'title' => 'Sub One']);
        Content::factory()->published()->create(['cluster_id' => $cluster->id, 'title' => 'Sub Two']);

        $component = new ClusterNavigation($pillar);
        $titles = $component->subArticles->pluck('title');

        $this->assertContains('Sub One', $titles);
        $this->assertContains('Sub Two', $titles);
    }

    public function test_pillar_is_null_when_current_article_is_pillar(): void
    {
        $cluster = Cluster::factory()->create();
        $pillar = Content::factory()->published()->create(['cluster_id' => $cluster->id, 'is_pillar' => true]);
        Content::factory()->published()->create(['cluster_id' => $cluster->id]);

        $component = new ClusterNavigation($pillar);

        $this->assertNull($component->pillar);
    }

    // -------------------------------------------------------------------------
    // Sub-article context
    // -------------------------------------------------------------------------

    public function test_is_pillar_is_false_for_sub_article(): void
    {
        $cluster = Cluster::factory()->create();
        Content::factory()->published()->create(['cluster_id' => $cluster->id, 'is_pillar' => true]);
        $sub = Content::factory()->published()->create(['cluster_id' => $cluster->id]);

        $component = new ClusterNavigation($sub);

        $this->assertFalse($component->isPillar);
    }

    public function test_pillar_is_set_for_sub_article(): void
    {
        $cluster = Cluster::factory()->create();
        Content::factory()->published()->create(['cluster_id' => $cluster->id, 'is_pillar' => true, 'title' => 'The Main Guide']);
        $sub = Content::factory()->published()->create(['cluster_id' => $cluster->id]);

        $component = new ClusterNavigation($sub);

        $this->assertNotNull($component->pillar);
        $this->assertSame('The Main Guide', $component->pillar->title);
    }

    public function test_current_article_is_not_in_sub_articles(): void
    {
        $cluster = Cluster::factory()->create();
        Content::factory()->published()->create(['cluster_id' => $cluster->id, 'is_pillar' => true]);
        $sub = Content::factory()->published()->create(['cluster_id' => $cluster->id, 'title' => 'Current Article']);

        $component = new ClusterNavigation($sub);

        $this->assertFalse($component->subArticles->contains('title', 'Current Article'));
    }

    public function test_sub_articles_exclude_the_pillar(): void
    {
        $cluster = Cluster::factory()->create();
        $pillar = Content::factory()->published()->create(['cluster_id' => $cluster->id, 'is_pillar' => true, 'title' => 'Pillar Article']);
        $sub = Content::factory()->published()->create(['cluster_id' => $cluster->id]);

        $component = new ClusterNavigation($sub);

        $this->assertFalse($component->subArticles->contains('title', 'Pillar Article'));
    }
}
