<?php

namespace Tests\Feature\Resources;

use App\Filament\Resources\Clusters\Pages\CreateCluster;
use App\Filament\Resources\Clusters\Pages\EditCluster;
use App\Filament\Resources\Clusters\Pages\ListClusters;
use App\Filament\Resources\Clusters\Widgets\ClusterContentsWidget;
use App\Models\Category;
use App\Models\Cluster;
use App\Models\Content;
use Filament\Actions\DeleteAction;
use Livewire\Livewire;
use Tests\FilamentTestCase;

class ClusterResourceTest extends FilamentTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAsSuperAdmin();
    }

    // -------------------------------------------------------------------------
    // List Page
    // -------------------------------------------------------------------------

    public function test_can_load_list_page(): void
    {
        $clusters = Cluster::factory()->count(5)->create();

        Livewire::test(ListClusters::class)
            ->assertOk()
            ->assertCanSeeTableRecords($clusters);
    }

    public function test_can_search_clusters_by_title(): void
    {
        Cluster::factory()->create(['title' => 'Laravel Fundamentals']);
        Cluster::factory()->create(['title' => 'Frontend Essentials']);

        Livewire::test(ListClusters::class)
            ->searchTable('Laravel Fundamentals')
            ->assertCanSeeTableRecords(Cluster::where('title', 'Laravel Fundamentals')->get())
            ->assertCanNotSeeTableRecords(Cluster::where('title', 'Frontend Essentials')->get());
    }

    public function test_can_sort_clusters_by_title(): void
    {
        Cluster::factory()->count(5)->create();

        $sortedAsc  = Cluster::query()->orderBy('title')->get();
        $sortedDesc = Cluster::query()->orderBy('title', 'desc')->get();

        Livewire::test(ListClusters::class)
            ->sortTable('title')
            ->assertCanSeeTableRecords($sortedAsc, inOrder: true)
            ->sortTable('title', 'desc')
            ->assertCanSeeTableRecords($sortedDesc, inOrder: true);
    }

    // -------------------------------------------------------------------------
    // Create Page
    // -------------------------------------------------------------------------

    public function test_can_load_create_page(): void
    {
        Livewire::test(CreateCluster::class)
            ->assertOk();
    }

    public function test_can_create_cluster(): void
    {
        $category = Category::factory()->create();

        Livewire::test(CreateCluster::class)
            ->fillForm([
                'title'       => 'Developer Growth',
                'category_id' => $category->id,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('clusters', ['title' => 'Developer Growth']);
    }

    public function test_title_is_required(): void
    {
        Livewire::test(CreateCluster::class)
            ->fillForm(['title' => null])
            ->call('create')
            ->assertHasFormErrors(['title' => 'required']);
    }

    public function test_title_must_be_unique(): void
    {
        Cluster::factory()->create(['title' => 'Existing Cluster']);
        $category = Category::factory()->create();

        Livewire::test(CreateCluster::class)
            ->fillForm([
                'title'       => 'Existing Cluster',
                'category_id' => $category->id,
            ])
            ->call('create')
            ->assertHasFormErrors(['title' => 'unique']);
    }

    public function test_title_max_length_is_80(): void
    {
        Livewire::test(CreateCluster::class)
            ->fillForm(['title' => str_repeat('a', 81)])
            ->call('create')
            ->assertHasFormErrors(['title' => 'max']);
    }

    // -------------------------------------------------------------------------
    // Edit Page
    // -------------------------------------------------------------------------

    public function test_can_load_edit_page(): void
    {
        $cluster = Cluster::factory()->create();

        Livewire::test(EditCluster::class, ['record' => $cluster->getRouteKey()])
            ->assertOk()
            ->assertSchemaStateSet(['title' => $cluster->title]);
    }

    public function test_can_update_cluster(): void
    {
        $cluster = Cluster::factory()->create([
            'category_id' => Category::factory()->create()->id,
        ]);

        Livewire::test(EditCluster::class, ['record' => $cluster->getRouteKey()])
            ->fillForm(['title' => 'Updated Title'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('clusters', [
            'id'    => $cluster->id,
            'title' => 'Updated Title',
        ]);
    }

    public function test_title_must_be_unique_but_ignores_current_record(): void
    {
        $cluster = Cluster::factory()->create([
            'title'       => 'My Cluster',
            'category_id' => Category::factory()->create()->id,
        ]);

        Livewire::test(EditCluster::class, ['record' => $cluster->getRouteKey()])
            ->fillForm(['title' => 'My Cluster'])
            ->call('save')
            ->assertHasNoFormErrors();
    }

    public function test_title_must_be_unique_on_edit(): void
    {
        Cluster::factory()->create([
            'title'       => 'Taken Title',
            'category_id' => Category::factory()->create()->id,
        ]);
        $cluster = Cluster::factory()->create([
            'title'       => 'My Cluster',
            'category_id' => Category::factory()->create()->id,
        ]);

        Livewire::test(EditCluster::class, ['record' => $cluster->getRouteKey()])
            ->fillForm(['title' => 'Taken Title'])
            ->call('save')
            ->assertHasFormErrors(['title' => 'unique']);
    }

    // -------------------------------------------------------------------------
    // Delete
    // -------------------------------------------------------------------------

    public function test_can_delete_cluster(): void
    {
        $cluster = Cluster::factory()->create();

        Livewire::test(EditCluster::class, ['record' => $cluster->getRouteKey()])
            ->callAction(DeleteAction::class);

        $this->assertModelMissing($cluster);
    }

    // -------------------------------------------------------------------------
    // Related Clusters
    // -------------------------------------------------------------------------

    public function test_can_set_related_clusters(): void
    {
        $cluster = Cluster::factory()->create([
            'category_id' => Category::factory()->create()->id,
        ]);
        $relatedA = Cluster::factory()->create();
        $relatedB = Cluster::factory()->create();

        Livewire::test(EditCluster::class, ['record' => $cluster->getRouteKey()])
            ->fillForm(['relatedClusters' => [$relatedA->id, $relatedB->id]])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertCount(2, $cluster->fresh()->relatedClusters);
    }

    public function test_related_clusters_are_limited_to_three(): void
    {
        $cluster = Cluster::factory()->create();
        $related = Cluster::factory()->count(4)->create();

        Livewire::test(EditCluster::class, ['record' => $cluster->getRouteKey()])
            ->fillForm(['relatedClusters' => $related->pluck('id')->all()])
            ->call('save')
            ->assertHasFormErrors(['relatedClusters']);
    }

    public function test_related_clusters_pivot_is_deleted_when_cluster_is_deleted(): void
    {
        $clusterA = Cluster::factory()->create();
        $clusterB = Cluster::factory()->create();
        $clusterA->relatedClusters()->attach($clusterB->id);

        Livewire::test(EditCluster::class, ['record' => $clusterA->getRouteKey()])
            ->callAction(DeleteAction::class);

        $this->assertDatabaseMissing('cluster_related_cluster', ['cluster_id' => $clusterA->id]);
    }

    // -------------------------------------------------------------------------
    // ClusterContentsWidget
    // -------------------------------------------------------------------------

    public function test_contents_widget_is_visible_on_edit_page(): void
    {
        $cluster = Cluster::factory()->create();

        Livewire::test(EditCluster::class, ['record' => $cluster->getRouteKey()])
            ->assertSeeLivewire(ClusterContentsWidget::class);
    }

    public function test_contents_widget_shows_articles_belonging_to_cluster(): void
    {
        $cluster = Cluster::factory()->create();
        $article = Content::factory()->create(['cluster_id' => $cluster->id]);
        $other   = Content::factory()->create();

        Livewire::test(ClusterContentsWidget::class, ['record' => $cluster])
            ->assertCanSeeTableRecords([$article])
            ->assertCanNotSeeTableRecords([$other]);
    }

    public function test_contents_widget_shows_pillar_article(): void
    {
        $cluster = Cluster::factory()->create();
        $pillar  = Content::factory()->create(['cluster_id' => $cluster->id, 'is_pillar' => true]);

        Livewire::test(ClusterContentsWidget::class, ['record' => $cluster])
            ->assertCanSeeTableRecords([$pillar]);
    }

    public function test_contents_widget_is_empty_when_cluster_has_no_articles(): void
    {
        $cluster = Cluster::factory()->create();

        Livewire::test(ClusterContentsWidget::class, ['record' => $cluster])
            ->assertCountTableRecords(0);
    }
}
