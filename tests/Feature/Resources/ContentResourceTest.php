<?php

namespace Tests\Feature\Resources;

use App\Enums\ContentStatus;
use App\Enums\ContentType;
use App\Filament\Resources\Contents\Pages\CreateContent;
use App\Filament\Resources\Contents\Pages\EditContent;
use App\Filament\Resources\Contents\Pages\ListContents;
use App\Models\Category;
use App\Models\Cluster;
use App\Models\Content;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\FilamentTestCase;

class ContentResourceTest extends FilamentTestCase
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
        $contents = Content::factory()->count(5)->create();

        Livewire::test(ListContents::class)
            ->assertOk()
            ->assertCanSeeTableRecords($contents);
    }

    public function test_can_search_contents_by_title(): void
    {
        $contents = Content::factory()->count(5)->create();

        Livewire::test(ListContents::class)
            ->searchTable($contents->first()->title)
            ->assertCanSeeTableRecords($contents->where('title', $contents->first()->title))
            ->assertCanNotSeeTableRecords($contents->where('title', '!=', $contents->first()->title));
    }

    public function test_can_filter_by_type_post(): void
    {
        $posts = Content::factory()->post()->count(3)->create();
        $pages = Content::factory()->page()->count(3)->create();

        Livewire::test(ListContents::class)
            ->filterTable('type', ContentType::POST->value)
            ->assertCanSeeTableRecords($posts)
            ->assertCanNotSeeTableRecords($pages);
    }

    public function test_can_filter_by_type_page(): void
    {
        $posts = Content::factory()->post()->count(3)->create();
        $pages = Content::factory()->page()->count(3)->create();

        Livewire::test(ListContents::class)
            ->filterTable('type', ContentType::PAGE->value)
            ->assertCanSeeTableRecords($pages)
            ->assertCanNotSeeTableRecords($posts);
    }

    public function test_can_filter_by_status_published(): void
    {
        $published = Content::factory()->published()->count(3)->create();
        $drafts = Content::factory()->draft()->count(3)->create();

        Livewire::test(ListContents::class)
            ->filterTable('status', ContentStatus::PUBLISHED->value)
            ->assertCanSeeTableRecords($published)
            ->assertCanNotSeeTableRecords($drafts);
    }

    public function test_can_filter_by_author(): void
    {
        $author = User::factory()->create();
        $contents = Content::factory()->withAuthor($author)->count(3)->create();
        $others = Content::factory()->count(3)->create();

        Livewire::test(ListContents::class)
            ->filterTable('author_id', $author->id)
            ->assertCanSeeTableRecords($contents)
            ->assertCanNotSeeTableRecords($others);
    }

    public function test_can_filter_featured_contents(): void
    {
        $featured = Content::factory()->featured()->count(3)->create();
        $normal = Content::factory()->count(3)->create();

        Livewire::test(ListContents::class)
            ->filterTable('is_featured', true)
            ->assertCanSeeTableRecords($featured)
            ->assertCanNotSeeTableRecords($normal);
    }

    public function test_can_sort_contents_by_title(): void
    {
        Content::factory()->count(5)->create();

        $sortedAsc = Content::query()->orderBy('title')->get();
        $sortedDesc = Content::query()->orderBy('title', 'desc')->get();

        Livewire::test(ListContents::class)
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
        Livewire::test(CreateContent::class)
            ->assertOk();
    }

    public function test_can_create_post(): void
    {
        $data = Content::factory()->post()->make();

        Livewire::test(CreateContent::class)
            ->fillForm([
                'title'     => $data->title,
                'path.slug' => Str::slug($data->title),
                'content'   => $data->content,
                'type'      => ContentType::POST->value,
                'status'    => ContentStatus::DRAFT->value,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $content = Content::where('title', $data->title)->first();

        $this->assertDatabaseHas('contents', [
            'title' => $data->title,
            'type'  => ContentType::POST->value,
        ]);

        $this->assertDatabaseHas('paths', [
            'model_type' => Content::class,
            'model_id'   => $content->id,
            'slug'       => Str::slug($data->title),
        ]);
    }

    public function test_can_create_page(): void
    {
        $data = Content::factory()->page()->make();

        Livewire::test(CreateContent::class)
            ->fillForm([
                'title'     => $data->title,
                'path.slug' => Str::slug($data->title),
                'content'   => $data->content,
                'type'      => ContentType::PAGE->value,
                'status'    => ContentStatus::DRAFT->value,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('contents', [
            'title' => $data->title,
            'type'  => ContentType::PAGE->value,
        ]);
    }

    public function test_title_is_required(): void
    {
        Livewire::test(CreateContent::class)
            ->fillForm(['title' => null])
            ->call('create')
            ->assertHasFormErrors(['title' => 'required']);
    }

    public function test_slug_is_required(): void
    {
        Livewire::test(CreateContent::class)
            ->fillForm([
                'title'     => 'Some Content',
                'path.slug' => null,
            ])
            ->call('create')
            ->assertHasFormErrors(['path.slug' => 'required']);
    }

    public function test_content_is_required(): void
    {
        Livewire::test(CreateContent::class)
            ->fillForm(['content' => null])
            ->call('create')
            ->assertHasFormErrors(['content']);
    }

    public function test_meta_title_and_og_title_are_filled_from_title_when_empty(): void
    {
        Livewire::test(CreateContent::class)
            ->fillForm(['title' => 'My Title'])
            ->assertSchemaStateSet([
                'metaData.meta_title' => 'My Title',
                'metaData.og_title'   => 'My Title',
            ]);
    }

    public function test_meta_title_and_og_title_are_not_overwritten_when_already_set(): void
    {
        Livewire::test(CreateContent::class)
            ->fillForm([
                'metaData.meta_title' => 'Custom Meta',
                'metaData.og_title'   => 'Custom OG',
            ])
            ->fillForm(['title' => 'My Title'])
            ->assertSchemaStateSet([
                'metaData.meta_title' => 'Custom Meta',
                'metaData.og_title'   => 'Custom OG',
            ]);
    }

    public function test_meta_title_and_og_title_are_not_overwritten_on_edit(): void
    {
        $content = Content::factory()->create();
        $content->metaData()->create([
            'meta_title' => 'Existing Meta',
            'og_title'   => 'Existing OG',
        ]);

        Livewire::test(EditContent::class, ['record' => $content->getRouteKey()])
            ->fillForm(['title' => 'Changed Title'])
            ->assertSchemaStateSet([
                'metaData.meta_title' => 'Existing Meta',
                'metaData.og_title'   => 'Existing OG',
            ]);
    }

    public function test_author_is_set_from_authenticated_user(): void
    {
        $data = Content::factory()->make();

        Livewire::test(CreateContent::class)
            ->fillForm([
                'title'     => $data->title,
                'path.slug' => Str::slug($data->title),
                'content'   => $data->content,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('contents', [
            'title'     => $data->title,
            'author_id' => auth()->id(),
        ]);
    }

    // -------------------------------------------------------------------------
    // Edit Page
    // -------------------------------------------------------------------------

    public function test_can_load_edit_page(): void
    {
        $content = Content::factory()->create();

        Livewire::test(EditContent::class, ['record' => $content->getRouteKey()])
            ->assertOk()
            ->assertSchemaStateSet([
                'title'     => $content->title,
                'path.slug' => $content->path->slug,
            ]);
    }

    public function test_can_update_content(): void
    {
        $content = Content::factory()->create();

        Livewire::test(EditContent::class, ['record' => $content->getRouteKey()])
            ->fillForm(['title' => 'Updated Title'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('contents', [
            'id'    => $content->id,
            'title' => 'Updated Title',
        ]);
    }

    public function test_can_update_status(): void
    {
        $content = Content::factory()->draft()->create();

        Livewire::test(EditContent::class, ['record' => $content->getRouteKey()])
            ->fillForm(['status' => ContentStatus::PUBLISHED->value])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('contents', [
            'id'     => $content->id,
            'status' => ContentStatus::PUBLISHED->value,
        ]);
    }

    public function test_can_delete_content(): void
    {
        $content = Content::factory()->create();
        $url = $content->path->url;

        Livewire::test(EditContent::class, ['record' => $content->getRouteKey()])
            ->callAction(DeleteAction::class);

        $this->assertModelMissing($content);

        $this->assertDatabaseMissing('paths', [
            'model_type' => Content::class,
            'model_id'   => $content->id,
        ]);

        $this->assertDatabaseHas('path_redirects', [
            'old_path'    => $url,
            'new_path'    => null,
            'status_code' => 410,
        ]);
    }

    // -------------------------------------------------------------------------
    // Category Relation
    // -------------------------------------------------------------------------

    public function test_can_set_category_on_content(): void
    {
        $category = Category::factory()->create();
        $data = Content::factory()->post()->make();

        Livewire::test(CreateContent::class)
            ->fillForm([
                'title'       => $data->title,
                'path.slug'   => Str::slug($data->title),
                'content'     => $data->content,
                'type'        => ContentType::POST->value,
                'status'      => ContentStatus::DRAFT->value,
                'category_id' => $category->id,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $content = Content::where('title', $data->title)->first();

        $this->assertEquals($category->id, $content->category_id);
    }

    public function test_can_update_content_category(): void
    {
        $content = Content::factory()->create();
        $newCategory = Category::factory()->create();

        Livewire::test(EditContent::class, ['record' => $content->getRouteKey()])
            ->fillForm(['category_id' => $newCategory->id])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertEquals($newCategory->id, $content->fresh()->category_id);
    }

    // -------------------------------------------------------------------------
    // Cluster Filters
    // -------------------------------------------------------------------------

    public function test_can_filter_by_cluster(): void
    {
        $cluster       = Cluster::factory()->create();
        $inCluster     = Content::factory()->count(2)->create(['cluster_id' => $cluster->id]);
        $notInCluster  = Content::factory()->count(2)->create(['cluster_id' => null]);

        Livewire::test(ListContents::class)
            ->filterTable('cluster_id', $cluster->id)
            ->assertCanSeeTableRecords($inCluster)
            ->assertCanNotSeeTableRecords($notInCluster);
    }

    public function test_can_filter_by_is_pillar(): void
    {
        $cluster  = Cluster::factory()->create();
        $pillar   = Content::factory()->create(['cluster_id' => $cluster->id, 'is_pillar' => true]);
        $nonPilar = Content::factory()->create(['cluster_id' => $cluster->id]);

        Livewire::test(ListContents::class)
            ->filterTable('is_pillar')
            ->assertCanSeeTableRecords(collect([$pillar]))
            ->assertCanNotSeeTableRecords(collect([$nonPilar]));
    }

    // -------------------------------------------------------------------------
    // Pillar Validation
    // -------------------------------------------------------------------------

    public function test_pillar_validation_fails_when_cluster_already_has_pillar(): void
    {
        $category = Category::factory()->create();
        $cluster  = Cluster::factory()->create(['category_id' => $category->id]);
        Content::factory()->create(['cluster_id' => $cluster->id, 'is_pillar' => true]);

        $data = Content::factory()->post()->make();

        Livewire::test(CreateContent::class)
            ->fillForm([
                'title'       => $data->title,
                'path.slug'   => Str::slug($data->title),
                'content'     => $data->content,
                'category_id' => $category->id,
                'cluster_id'  => $cluster->id,
                'is_pillar'   => true,
            ])
            ->call('create')
            ->assertHasFormErrors(['is_pillar']);
    }

    public function test_pillar_validation_passes_when_no_pillar_exists_in_cluster(): void
    {
        $category = Category::factory()->create();
        $cluster  = Cluster::factory()->create(['category_id' => $category->id]);

        $data = Content::factory()->post()->make();

        Livewire::test(CreateContent::class)
            ->fillForm([
                'title'       => $data->title,
                'path.slug'   => Str::slug($data->title),
                'content'     => $data->content,
                'category_id' => $category->id,
                'cluster_id'  => $cluster->id,
                'is_pillar'   => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('contents', [
            'title'     => $data->title,
            'is_pillar' => true,
        ]);
    }

    public function test_pillar_validation_ignores_current_record_on_edit(): void
    {
        $cluster = Cluster::factory()->create();
        $pillar  = Content::factory()->create(['cluster_id' => $cluster->id, 'is_pillar' => true]);

        Livewire::test(EditContent::class, ['record' => $pillar->getRouteKey()])
            ->fillForm(['is_pillar' => true])
            ->call('save')
            ->assertHasNoFormErrors();
    }

    public function test_is_pillar_stored_as_null_when_toggled_off(): void
    {
        $cluster = Cluster::factory()->create();
        $pillar  = Content::factory()->create(['cluster_id' => $cluster->id, 'is_pillar' => true]);

        Livewire::test(EditContent::class, ['record' => $pillar->getRouteKey()])
            ->fillForm(['is_pillar' => false])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('contents', [
            'id'       => $pillar->id,
            'is_pillar' => null,
        ]);
    }

    // -------------------------------------------------------------------------
    // Cluster depends on Category
    // -------------------------------------------------------------------------

    public function test_cluster_field_is_disabled_until_category_is_selected(): void
    {
        $component = Livewire::test(CreateContent::class);

        $component->assertFormFieldIsDisabled('cluster_id');

        $category = Category::factory()->create();
        $component->fillForm(['category_id' => $category->id])
            ->assertFormFieldIsEnabled('cluster_id');
    }

    public function test_cluster_options_are_filtered_by_selected_category(): void
    {
        $categoryA = Category::factory()->create();
        $categoryB = Category::factory()->create();
        $clusterB  = Cluster::factory()->create(['category_id' => $categoryB->id]);

        $data = Content::factory()->post()->make();

        Livewire::test(CreateContent::class)
            ->fillForm([
                'title'       => $data->title,
                'path.slug'   => Str::slug($data->title),
                'content'     => $data->content,
                'category_id' => $categoryA->id,
                'cluster_id'  => $clusterB->id,
            ])
            ->call('create')
            ->assertHasFormErrors(['cluster_id']);
    }

    public function test_cluster_is_reset_when_category_changes(): void
    {
        $categoryA = Category::factory()->create();
        $categoryB = Category::factory()->create();
        $clusterA  = Cluster::factory()->create(['category_id' => $categoryA->id]);

        Livewire::test(CreateContent::class)
            ->fillForm([
                'category_id' => $categoryA->id,
                'cluster_id'  => $clusterA->id,
            ])
            ->assertSchemaStateSet(['cluster_id' => $clusterA->id])
            ->fillForm(['category_id' => $categoryB->id])
            ->assertSchemaStateSet(['cluster_id' => null]);
    }

    // -------------------------------------------------------------------------
    // URL Uniqueness
    // -------------------------------------------------------------------------

    public function test_two_contents_in_same_category_cannot_share_the_same_url(): void
    {
        $category = Category::factory()->create();
        $first    = Content::factory()->create(['category_id' => $category->id]);

        $this->expectException(\Illuminate\Database\UniqueConstraintViolationException::class);

        \Illuminate\Support\Facades\DB::table('paths')->insert([
            'model_type' => Content::class,
            'model_id'   => 999,
            'slug'       => $first->path->slug,
            'url'        => $first->path->url,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    // -------------------------------------------------------------------------
    // Inbound Links Tab
    // -------------------------------------------------------------------------

    public function test_inbound_links_tab_shows_empty_state_when_no_articles_link_to_content(): void
    {
        $target = Content::factory()->create();

        Livewire::test(EditContent::class, ['record' => $target->getRouteKey()])
            ->assertSee(__('resource.content_resource.inbound_links.empty'));
    }

    public function test_inbound_links_tab_shows_linking_article_title(): void
    {
        $target  = Content::factory()->create();
        $linking = Content::factory()->create([
            'content' => $this->makeContentWithInternalLink($target->id),
        ]);

        Livewire::test(EditContent::class, ['record' => $target->getRouteKey()])
            ->assertSee($linking->title)
            ->assertDontSee(__('resource.content_resource.inbound_links.empty'));
    }

    public function test_inbound_links_tab_shows_correct_count(): void
    {
        $target   = Content::factory()->create();
        $linkingA = Content::factory()->create([
            'content' => $this->makeContentWithInternalLink($target->id),
        ]);
        $linkingB = Content::factory()->create([
            'content' => $this->makeContentWithInternalLink($target->id),
        ]);

        Livewire::test(EditContent::class, ['record' => $target->getRouteKey()])
            ->assertSee(__('resource.content_resource.inbound_links.count', ['count' => 2]))
            ->assertSee($linkingA->title)
            ->assertSee($linkingB->title);
    }

    public function test_inbound_links_tab_does_not_show_unrelated_articles(): void
    {
        $target      = Content::factory()->create();
        $unrelated   = Content::factory()->create();
        $linking     = Content::factory()->create([
            'content' => $this->makeContentWithInternalLink($target->id),
        ]);

        Livewire::test(EditContent::class, ['record' => $target->getRouteKey()])
            ->assertSee($linking->title)
            ->assertDontSee($unrelated->title);
    }

    public function test_inbound_links_tab_shows_category_badge_when_present(): void
    {
        $category = Category::factory()->create();
        $target   = Content::factory()->create();
        Content::factory()->create([
            'category_id' => $category->id,
            'content'     => $this->makeContentWithInternalLink($target->id),
        ]);

        Livewire::test(EditContent::class, ['record' => $target->getRouteKey()])
            ->assertSee($category->name);
    }

    private function makeContentWithInternalLink(int $postId): array
    {
        return [
            'type'    => 'doc',
            'content' => [
                [
                    'type'    => 'paragraph',
                    'attrs'   => ['textAlign' => 'start'],
                    'content' => [
                        [
                            'type'  => 'text',
                            'text'  => 'See also this article.',
                            'marks' => [
                                ['type' => 'internalLink', 'attrs' => ['postId' => $postId]],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
