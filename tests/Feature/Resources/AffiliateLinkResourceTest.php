<?php

namespace Tests\Feature\Resources;

use App\Filament\Resources\AffiliateLinks\Pages\CreateAffiliateLink;
use App\Filament\Resources\AffiliateLinks\Pages\EditAffiliateLink;
use App\Filament\Resources\AffiliateLinks\Pages\ListAffiliateLinks;
use App\Filament\Resources\AffiliateLinks\Widgets\AffiliateLinkUsageWidget;
use App\Models\AffiliateLink;
use App\Models\AffiliateLinkClick;
use App\Models\AffiliateNetwork;
use App\Models\Content;
use Filament\Actions\DeleteAction;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\FilamentTestCase;

class AffiliateLinkResourceTest extends FilamentTestCase
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
        $links = AffiliateLink::factory()->count(3)->create();

        Livewire::test(ListAffiliateLinks::class)
            ->assertOk()
            ->assertCanSeeTableRecords($links);
    }

    public function test_can_search_links_by_name(): void
    {
        AffiliateLink::factory()->create(['name' => 'Laravel Mastery Course']);
        AffiliateLink::factory()->create(['name' => 'Vue Complete Guide']);

        Livewire::test(ListAffiliateLinks::class)
            ->searchTable('Laravel Mastery Course')
            ->assertCanSeeTableRecords(AffiliateLink::where('name', 'Laravel Mastery Course')->get())
            ->assertCanNotSeeTableRecords(AffiliateLink::where('name', 'Vue Complete Guide')->get());
    }

    public function test_can_sort_links_by_name(): void
    {
        AffiliateLink::factory()->count(3)->create();

        $sortedAsc  = AffiliateLink::orderBy('name')->get();
        $sortedDesc = AffiliateLink::orderBy('name', 'desc')->get();

        Livewire::test(ListAffiliateLinks::class)
            ->sortTable('name')
            ->assertCanSeeTableRecords($sortedAsc, inOrder: true)
            ->sortTable('name', 'desc')
            ->assertCanSeeTableRecords($sortedDesc, inOrder: true);
    }

    public function test_can_sort_links_by_clicks(): void
    {
        AffiliateLink::factory()->create(['clicks' => 10]);
        AffiliateLink::factory()->create(['clicks' => 50]);
        AffiliateLink::factory()->create(['clicks' => 5]);

        $sortedDesc = AffiliateLink::orderBy('clicks', 'desc')->get();

        Livewire::test(ListAffiliateLinks::class)
            ->sortTable('clicks', 'desc')
            ->assertCanSeeTableRecords($sortedDesc, inOrder: true);
    }

    public function test_can_filter_links_by_network(): void
    {
        $amazon = AffiliateNetwork::factory()->create(['name' => 'Amazon']);
        $awin   = AffiliateNetwork::factory()->create(['name' => 'Awin']);

        $amazonLinks = AffiliateLink::factory()->count(2)->create(['affiliate_network_id' => $amazon->id]);
        $awinLinks   = AffiliateLink::factory()->count(2)->create(['affiliate_network_id' => $awin->id]);

        Livewire::test(ListAffiliateLinks::class)
            ->filterTable('affiliate_network_id', $amazon->id)
            ->assertCanSeeTableRecords($amazonLinks)
            ->assertCanNotSeeTableRecords($awinLinks);
    }

    public function test_can_filter_active_links(): void
    {
        $active   = AffiliateLink::factory()->count(2)->create();
        $inactive = AffiliateLink::factory()->inactive()->count(2)->create();

        Livewire::test(ListAffiliateLinks::class)
            ->filterTable('is_active', '1')
            ->assertCanSeeTableRecords($active)
            ->assertCanNotSeeTableRecords($inactive);
    }

    public function test_can_filter_unused_links(): void
    {
        $unused = AffiliateLink::factory()->create();
        $used   = AffiliateLink::factory()->create();

        Content::factory()->create([
            'content' => $this->makeContentWithAffiliateLink($used->id),
        ]);

        Livewire::test(ListAffiliateLinks::class)
            ->filterTable('unused', true)
            ->assertCanSeeTableRecords([$unused])
            ->assertCanNotSeeTableRecords([$used]);
    }

    // -------------------------------------------------------------------------
    // Create Page
    // -------------------------------------------------------------------------

    public function test_can_load_create_page(): void
    {
        Livewire::test(CreateAffiliateLink::class)
            ->assertOk();
    }

    public function test_can_create_affiliate_link(): void
    {
        $network = AffiliateNetwork::factory()->create();

        Livewire::test(CreateAffiliateLink::class)
            ->fillForm([
                'affiliate_network_id' => $network->id,
                'name'                 => 'Clean Code Book',
                'slug'                 => 'amazon-clean-code-book',
                'url'                  => 'https://example.com/clean-code',
                'is_active'            => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('affiliate_links', [
            'name' => 'Clean Code Book',
            'slug' => 'amazon-clean-code-book',
        ]);
    }

    public function test_name_is_required(): void
    {
        $network = AffiliateNetwork::factory()->create();

        Livewire::test(CreateAffiliateLink::class)
            ->fillForm(['affiliate_network_id' => $network->id, 'name' => null])
            ->call('create')
            ->assertHasFormErrors(['name' => 'required']);
    }

    public function test_url_is_required(): void
    {
        $network = AffiliateNetwork::factory()->create();

        Livewire::test(CreateAffiliateLink::class)
            ->fillForm(['affiliate_network_id' => $network->id, 'name' => 'Test', 'url' => null])
            ->call('create')
            ->assertHasFormErrors(['url' => 'required']);
    }

    public function test_url_must_be_valid(): void
    {
        $network = AffiliateNetwork::factory()->create();

        Livewire::test(CreateAffiliateLink::class)
            ->fillForm([
                'affiliate_network_id' => $network->id,
                'name'                 => 'Test',
                'slug'                 => 'test-slug',
                'url'                  => 'not-a-url',
            ])
            ->call('create')
            ->assertHasFormErrors(['url' => 'url']);
    }

    public function test_slug_must_be_unique(): void
    {
        $network = AffiliateNetwork::factory()->create();
        AffiliateLink::factory()->create(['slug' => 'existing-slug']);

        Livewire::test(CreateAffiliateLink::class)
            ->fillForm([
                'affiliate_network_id' => $network->id,
                'name'                 => 'Test',
                'slug'                 => 'existing-slug',
                'url'                  => 'https://example.com',
            ])
            ->call('create')
            ->assertHasFormErrors(['slug' => 'unique']);
    }

    public function test_slug_is_auto_generated_from_network_and_name(): void
    {
        $network = AffiliateNetwork::factory()->create(['name' => 'Amazon']);

        Livewire::test(CreateAffiliateLink::class)
            ->fillForm([
                'affiliate_network_id' => $network->id,
                'name'                 => 'Clean Code Book',
            ])
            ->assertSchemaStateSet([
                'slug' => Str::slug('Amazon-Clean Code Book'),
            ]);
    }

    // -------------------------------------------------------------------------
    // Edit Page
    // -------------------------------------------------------------------------

    public function test_can_load_edit_page(): void
    {
        $link = AffiliateLink::factory()->create();

        Livewire::test(EditAffiliateLink::class, ['record' => $link->getRouteKey()])
            ->assertOk()
            ->assertSchemaStateSet([
                'name' => $link->name,
                'slug' => $link->slug,
                'url'  => $link->url,
            ]);
    }

    public function test_can_update_affiliate_link(): void
    {
        $link = AffiliateLink::factory()->create();

        Livewire::test(EditAffiliateLink::class, ['record' => $link->getRouteKey()])
            ->fillForm(['name' => 'Updated Name'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('affiliate_links', [
            'id'   => $link->id,
            'name' => 'Updated Name',
        ]);
    }

    public function test_slug_must_be_unique_but_ignores_current_record(): void
    {
        $link = AffiliateLink::factory()->create(['slug' => 'my-link']);

        Livewire::test(EditAffiliateLink::class, ['record' => $link->getRouteKey()])
            ->fillForm(['slug' => 'my-link'])
            ->call('save')
            ->assertHasNoFormErrors();
    }

    // -------------------------------------------------------------------------
    // Delete
    // -------------------------------------------------------------------------

    public function test_can_delete_link_not_used_in_content(): void
    {
        $link = AffiliateLink::factory()->create();

        Livewire::test(EditAffiliateLink::class, ['record' => $link->getRouteKey()])
            ->callAction(DeleteAction::class);

        $this->assertModelMissing($link);
    }

    public function test_delete_is_blocked_when_link_is_used_in_content(): void
    {
        $link = AffiliateLink::factory()->create();

        Content::factory()->create([
            'content' => $this->makeContentWithAffiliateLink($link->id),
        ]);

        Livewire::test(EditAffiliateLink::class, ['record' => $link->getRouteKey()])
            ->callAction(DeleteAction::class);

        $this->assertModelExists($link);
    }

    // -------------------------------------------------------------------------
    // AffiliateLinkUsageWidget
    // -------------------------------------------------------------------------

    public function test_usage_widget_is_visible_on_edit_page(): void
    {
        $link = AffiliateLink::factory()->create();

        Livewire::test(EditAffiliateLink::class, ['record' => $link->getRouteKey()])
            ->assertSeeLivewire(AffiliateLinkUsageWidget::class);
    }

    public function test_usage_widget_shows_articles_that_use_the_link(): void
    {
        $link    = AffiliateLink::factory()->create();
        $using   = Content::factory()->create(['content' => $this->makeContentWithAffiliateLink($link->id)]);
        $other   = Content::factory()->create();

        Livewire::test(AffiliateLinkUsageWidget::class, ['record' => $link])
            ->assertCanSeeTableRecords([$using])
            ->assertCanNotSeeTableRecords([$other]);
    }

    public function test_usage_widget_does_not_show_articles_from_other_links(): void
    {
        $linkA = AffiliateLink::factory()->create();
        $linkB = AffiliateLink::factory()->create();

        $articleA = Content::factory()->create(['content' => $this->makeContentWithAffiliateLink($linkA->id)]);
        $articleB = Content::factory()->create(['content' => $this->makeContentWithAffiliateLink($linkB->id)]);

        Livewire::test(AffiliateLinkUsageWidget::class, ['record' => $linkA])
            ->assertCanSeeTableRecords([$articleA])
            ->assertCanNotSeeTableRecords([$articleB]);
    }

    public function test_usage_widget_is_empty_when_link_is_unused(): void
    {
        $link = AffiliateLink::factory()->create();

        Livewire::test(AffiliateLinkUsageWidget::class, ['record' => $link])
            ->assertCountTableRecords(0);
    }

    public function test_usage_widget_shows_pivot_click_count_per_article(): void
    {
        $link    = AffiliateLink::factory()->create();
        $content = Content::factory()->create(['content' => $this->makeContentWithAffiliateLink($link->id)]);

        AffiliateLinkClick::create([
            'affiliate_link_id' => $link->id,
            'content_id'        => $content->id,
            'clicks'            => 7,
            'last_clicked_at'   => now(),
        ]);

        Livewire::test(AffiliateLinkUsageWidget::class, ['record' => $link])
            ->assertCanSeeTableRecords([$content])
            ->assertTableColumnStateSet('link_clicks', 7, $content);
    }

    public function test_usage_widget_shows_last_clicked_at_per_article(): void
    {
        $link      = AffiliateLink::factory()->create();
        $content   = Content::factory()->create(['content' => $this->makeContentWithAffiliateLink($link->id)]);
        $clickedAt = now()->subDay()->startOfSecond();

        AffiliateLinkClick::create([
            'affiliate_link_id' => $link->id,
            'content_id'        => $content->id,
            'clicks'            => 1,
            'last_clicked_at'   => $clickedAt,
        ]);

        Livewire::test(AffiliateLinkUsageWidget::class, ['record' => $link])
            ->assertTableColumnStateSet('link_last_clicked_at', $clickedAt->toDateTimeString(), $content);
    }

    public function test_usage_widget_shows_zero_clicks_for_articles_without_pivot_row(): void
    {
        $link    = AffiliateLink::factory()->create();
        $content = Content::factory()->create(['content' => $this->makeContentWithAffiliateLink($link->id)]);

        Livewire::test(AffiliateLinkUsageWidget::class, ['record' => $link])
            ->assertCanSeeTableRecords([$content])
            ->assertTableColumnStateSet('link_clicks', null, $content);
    }

    public function test_usage_widget_description_reflects_unassigned_click_count(): void
    {
        $link    = AffiliateLink::factory()->create(['clicks' => 10]);
        $content = Content::factory()->create(['content' => $this->makeContentWithAffiliateLink($link->id)]);

        AffiliateLinkClick::create([
            'affiliate_link_id' => $link->id,
            'content_id'        => $content->id,
            'clicks'            => 7,
            'last_clicked_at'   => now(),
        ]);

        $expected = __('resource.affiliate_link_resource.usage_widget.unassigned', ['count' => 3]);

        Livewire::test(AffiliateLinkUsageWidget::class, ['record' => $link])
            ->assertSee($expected);
    }

    public function test_usage_widget_unassigned_is_never_negative(): void
    {
        $link    = AffiliateLink::factory()->create(['clicks' => 2]);
        $content = Content::factory()->create(['content' => $this->makeContentWithAffiliateLink($link->id)]);

        AffiliateLinkClick::create([
            'affiliate_link_id' => $link->id,
            'content_id'        => $content->id,
            'clicks'            => 5,
            'last_clicked_at'   => now(),
        ]);

        $expected = __('resource.affiliate_link_resource.usage_widget.unassigned', ['count' => 0]);

        Livewire::test(AffiliateLinkUsageWidget::class, ['record' => $link])
            ->assertSee($expected);
    }

    private function makeContentWithAffiliateLink(int $affiliateLinkId): array
    {
        return [
            'type'    => 'doc',
            'content' => [[
                'type'    => 'paragraph',
                'attrs'   => ['textAlign' => 'start'],
                'content' => [[
                    'type'  => 'text',
                    'text'  => 'Test',
                    'marks' => [['type' => 'affiliateLink', 'attrs' => ['affiliateLinkId' => $affiliateLinkId]]],
                ]],
            ]],
        ];
    }
}
