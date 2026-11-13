<?php

namespace Tests\Feature\RichEditor;

use App\Enums\ContentStatus;
use App\Enums\ContentType;
use App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\PostCardsSmallBlock;
use App\Models\Category;
use App\Models\Cluster;
use App\Models\Content;
use Tests\FilamentTestCase;

class PostCardsSmallBlockTest extends FilamentTestCase
{
    // -------------------------------------------------------------------------
    // Metadata
    // -------------------------------------------------------------------------

    public function test_has_correct_id(): void
    {
        $this->assertSame('post_cards_small', PostCardsSmallBlock::getId());
    }

    // -------------------------------------------------------------------------
    // Preview label
    // -------------------------------------------------------------------------

    public function test_preview_label_without_heading_has_no_colon(): void
    {
        $label = PostCardsSmallBlock::getPreviewLabel([]);

        $this->assertStringNotContainsString(':', $label);
    }

    public function test_preview_label_with_heading_appends_heading(): void
    {
        $label = PostCardsSmallBlock::getPreviewLabel(['heading' => 'More Reads', 'count' => 4]);

        $this->assertStringContainsString('More Reads', $label);
        $this->assertStringContainsString(':', $label);
    }

    public function test_preview_label_includes_count(): void
    {
        $label = PostCardsSmallBlock::getPreviewLabel(['count' => 8]);

        $this->assertStringContainsString('(8)', $label);
    }

    // -------------------------------------------------------------------------
    // toHtml – source latest
    // -------------------------------------------------------------------------

    public function test_to_html_renders_latest_published_posts(): void
    {
        Content::factory()->published()->create(['title' => 'Small Post A']);
        Content::factory()->published()->create(['title' => 'Small Post B']);

        $html = PostCardsSmallBlock::toHtml(
            ['heading' => 'Latest', 'count' => 4, 'source' => 'latest'],
            []
        );

        $this->assertStringContainsString('Small Post A', $html);
        $this->assertStringContainsString('Small Post B', $html);
    }

    public function test_to_html_excludes_draft_posts(): void
    {
        Content::factory()->draft()->create(['title' => 'Hidden Draft']);

        $html = PostCardsSmallBlock::toHtml(
            ['heading' => 'Latest', 'count' => 4, 'source' => 'latest'],
            []
        );

        $this->assertStringNotContainsString('Hidden Draft', $html);
    }

    public function test_to_html_excludes_non_post_types(): void
    {
        Content::factory()->published()->page()->create(['title' => 'A Page']);

        $html = PostCardsSmallBlock::toHtml(
            ['heading' => 'Latest', 'count' => 4, 'source' => 'latest'],
            []
        );

        $this->assertStringNotContainsString('A Page', $html);
    }

    public function test_to_html_respects_count_limit(): void
    {
        Content::factory()->count(6)->published()->create();

        $html = PostCardsSmallBlock::toHtml(
            ['heading' => 'Latest', 'count' => 4, 'source' => 'latest'],
            []
        );

        $this->assertSame(4, substr_count($html, '<article'));
    }

    public function test_to_html_falls_back_to_default_count_for_invalid_value(): void
    {
        Content::factory()->count(6)->published()->create();

        $html = PostCardsSmallBlock::toHtml(
            ['heading' => 'Latest', 'count' => 99, 'source' => 'latest'],
            []
        );

        $this->assertSame(4, substr_count($html, '<article'));
    }

    // -------------------------------------------------------------------------
    // toHtml – source category
    // -------------------------------------------------------------------------

    public function test_to_html_filters_by_category(): void
    {
        $categoryA = Category::factory()->create();
        $categoryB = Category::factory()->create();

        Content::factory()->published()->create(['title' => 'In A', 'category_id' => $categoryA->id]);
        Content::factory()->published()->create(['title' => 'In B', 'category_id' => $categoryB->id]);

        $html = PostCardsSmallBlock::toHtml(
            [
                'heading'     => 'Category A',
                'count'       => 4,
                'source'      => 'category',
                'category_id' => $categoryA->id,
            ],
            []
        );

        $this->assertStringContainsString('In A', $html);
        $this->assertStringNotContainsString('In B', $html);
    }

    // -------------------------------------------------------------------------
    // toHtml – source cluster
    // -------------------------------------------------------------------------

    public function test_to_html_filters_by_cluster(): void
    {
        $clusterA = Cluster::factory()->create();
        $clusterB = Cluster::factory()->create();

        Content::factory()->published()->create(['title' => 'In Cluster A', 'cluster_id' => $clusterA->id]);
        Content::factory()->published()->create(['title' => 'In Cluster B', 'cluster_id' => $clusterB->id]);

        $html = PostCardsSmallBlock::toHtml(
            [
                'heading'    => 'Cluster A',
                'count'      => 4,
                'source'     => 'cluster',
                'cluster_id' => $clusterA->id,
            ],
            []
        );

        $this->assertStringContainsString('In Cluster A', $html);
        $this->assertStringNotContainsString('In Cluster B', $html);
    }

    // -------------------------------------------------------------------------
    // toHtml – heading and intro
    // -------------------------------------------------------------------------

    public function test_to_html_renders_heading(): void
    {
        $html = PostCardsSmallBlock::toHtml(
            ['heading' => 'My Small Section', 'count' => 4, 'source' => 'latest'],
            []
        );

        $this->assertStringContainsString('My Small Section', $html);
    }

    public function test_to_html_renders_intro_when_present(): void
    {
        $html = PostCardsSmallBlock::toHtml(
            ['heading' => 'H', 'intro' => 'Short intro.', 'count' => 4, 'source' => 'latest'],
            []
        );

        $this->assertStringContainsString('Short intro.', $html);
    }

    // -------------------------------------------------------------------------
    // toPreviewHtml
    // -------------------------------------------------------------------------

    public function test_preview_html_renders_heading(): void
    {
        $html = PostCardsSmallBlock::toPreviewHtml(
            ['heading' => 'Preview Heading', 'count' => 4, 'source' => 'latest']
        );

        $this->assertStringContainsString('Preview Heading', $html);
    }

    public function test_preview_html_renders_intro_when_present(): void
    {
        $html = PostCardsSmallBlock::toPreviewHtml(
            ['heading' => 'H', 'intro' => 'Preview intro.', 'count' => 4, 'source' => 'latest']
        );

        $this->assertStringContainsString('Preview intro.', $html);
    }

    public function test_preview_html_renders_count(): void
    {
        $html = PostCardsSmallBlock::toPreviewHtml(
            ['heading' => 'H', 'count' => 12, 'source' => 'latest']
        );

        $this->assertStringContainsString('12', $html);
    }
}
