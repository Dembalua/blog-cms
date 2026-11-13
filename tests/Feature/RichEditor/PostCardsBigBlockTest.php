<?php

namespace Tests\Feature\RichEditor;

use App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\PostCardsBigBlock;
use App\Models\Category;
use App\Models\Cluster;
use App\Models\Content;
use Tests\FilamentTestCase;

class PostCardsBigBlockTest extends FilamentTestCase
{
    // -------------------------------------------------------------------------
    // Metadata
    // -------------------------------------------------------------------------

    public function test_has_correct_id(): void
    {
        $this->assertSame('post_cards_big', PostCardsBigBlock::getId());
    }

    // -------------------------------------------------------------------------
    // Preview label
    // -------------------------------------------------------------------------

    public function test_preview_label_without_heading_has_no_colon(): void
    {
        $label = PostCardsBigBlock::getPreviewLabel([]);

        $this->assertStringNotContainsString(':', $label);
    }

    public function test_preview_label_with_heading_appends_heading(): void
    {
        $label = PostCardsBigBlock::getPreviewLabel(['heading' => 'Latest News', 'count' => 3]);

        $this->assertStringContainsString('Latest News', $label);
        $this->assertStringContainsString(':', $label);
    }

    public function test_preview_label_includes_count(): void
    {
        $label = PostCardsBigBlock::getPreviewLabel(['count' => 6]);

        $this->assertStringContainsString('(6)', $label);
    }

    // -------------------------------------------------------------------------
    // toHtml – source latest
    // -------------------------------------------------------------------------

    public function test_to_html_renders_latest_published_posts(): void
    {
        Content::factory()->published()->create(['title' => 'Published Post A']);
        Content::factory()->published()->create(['title' => 'Published Post B']);

        $html = PostCardsBigBlock::toHtml(
            ['heading' => 'Latest', 'count' => 3, 'source' => 'latest'],
            []
        );

        $this->assertStringContainsString('Published Post A', $html);
        $this->assertStringContainsString('Published Post B', $html);
    }

    public function test_to_html_excludes_draft_posts(): void
    {
        Content::factory()->draft()->create(['title' => 'Draft Post']);

        $html = PostCardsBigBlock::toHtml(
            ['heading' => 'Latest', 'count' => 3, 'source' => 'latest'],
            []
        );

        $this->assertStringNotContainsString('Draft Post', $html);
    }

    public function test_to_html_excludes_non_post_types(): void
    {
        Content::factory()->published()->page()->create(['title' => 'A Page']);

        $html = PostCardsBigBlock::toHtml(
            ['heading' => 'Latest', 'count' => 3, 'source' => 'latest'],
            []
        );

        $this->assertStringNotContainsString('A Page', $html);
    }

    public function test_to_html_respects_count_limit(): void
    {
        Content::factory()->count(5)->published()->create();

        $html = PostCardsBigBlock::toHtml(
            ['heading' => 'Latest', 'count' => 3, 'source' => 'latest'],
            []
        );

        $this->assertSame(3, substr_count($html, '<article'));
    }

    public function test_to_html_falls_back_to_default_count_for_invalid_value(): void
    {
        Content::factory()->count(5)->published()->create();

        $html = PostCardsBigBlock::toHtml(
            ['heading' => 'Latest', 'count' => 99, 'source' => 'latest'],
            []
        );

        $this->assertSame(3, substr_count($html, '<article'));
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

        $html = PostCardsBigBlock::toHtml(
            [
                'heading' => 'Category A',
                'count' => 3,
                'source' => 'category',
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

        $html = PostCardsBigBlock::toHtml(
            [
                'heading' => 'Cluster A',
                'count' => 3,
                'source' => 'cluster',
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
        $html = PostCardsBigBlock::toHtml(
            ['heading' => 'My Section Heading', 'count' => 3, 'source' => 'latest'],
            []
        );

        $this->assertStringContainsString('My Section Heading', $html);
    }

    public function test_to_html_renders_intro_when_present(): void
    {
        $html = PostCardsBigBlock::toHtml(
            ['heading' => 'H', 'intro' => 'Intro paragraph here.', 'count' => 3, 'source' => 'latest'],
            []
        );

        $this->assertStringContainsString('Intro paragraph here.', $html);
    }

    // -------------------------------------------------------------------------
    // toPreviewHtml
    // -------------------------------------------------------------------------

    public function test_preview_html_renders_heading(): void
    {
        $html = PostCardsBigBlock::toPreviewHtml(
            ['heading' => 'Preview Heading', 'count' => 3, 'source' => 'latest']
        );

        $this->assertStringContainsString('Preview Heading', $html);
    }

    public function test_preview_html_renders_intro_when_present(): void
    {
        $html = PostCardsBigBlock::toPreviewHtml(
            ['heading' => 'H', 'intro' => 'Preview intro.', 'count' => 3, 'source' => 'latest']
        );

        $this->assertStringContainsString('Preview intro.', $html);
    }

    public function test_preview_html_renders_count(): void
    {
        $html = PostCardsBigBlock::toPreviewHtml(
            ['heading' => 'H', 'count' => 9, 'source' => 'latest']
        );

        $this->assertStringContainsString('9', $html);
    }
}
