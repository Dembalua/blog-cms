<?php

namespace Tests\Feature\RichEditor;

use App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\PostListBlock;
use App\Models\Category;
use App\Models\Cluster;
use App\Models\Content;
use Tests\FilamentTestCase;

class PostListBlockTest extends FilamentTestCase
{
    // -------------------------------------------------------------------------
    // Metadata
    // -------------------------------------------------------------------------

    public function test_has_correct_id(): void
    {
        $this->assertSame('post_list', PostListBlock::getId());
    }

    // -------------------------------------------------------------------------
    // Preview label
    // -------------------------------------------------------------------------

    public function test_preview_label_without_heading_has_no_colon(): void
    {
        $label = PostListBlock::getPreviewLabel([]);

        $this->assertStringNotContainsString(':', $label);
    }

    public function test_preview_label_with_heading_appends_heading(): void
    {
        $label = PostListBlock::getPreviewLabel(['heading' => 'Curated', 'count' => 4]);

        $this->assertStringContainsString('Curated', $label);
        $this->assertStringContainsString(':', $label);
    }

    public function test_preview_label_includes_count(): void
    {
        $label = PostListBlock::getPreviewLabel(['count' => 6]);

        $this->assertStringContainsString('(6)', $label);
    }

    // -------------------------------------------------------------------------
    // toHtml – items
    // -------------------------------------------------------------------------

    public function test_to_html_returns_empty_string_when_no_items(): void
    {
        $html = PostListBlock::toHtml(
            ['heading' => 'Empty', 'count' => 4, 'items' => []],
            []
        );

        $this->assertSame('', $html);
    }

    public function test_to_html_returns_empty_string_when_items_have_no_post_ids(): void
    {
        $html = PostListBlock::toHtml(
            ['heading' => 'Empty', 'count' => 4, 'items' => [[], ['post_id' => null]]],
            []
        );

        $this->assertSame('', $html);
    }

    public function test_to_html_renders_selected_posts(): void
    {
        $postA = Content::factory()->create(['title' => 'Row Post A']);
        $postB = Content::factory()->create(['title' => 'Row Post B']);

        $html = PostListBlock::toHtml(
            [
                'heading' => 'Picked',
                'count' => 2,
                'items' => [
                    ['post_id' => $postA->id],
                    ['post_id' => $postB->id],
                ],
            ],
            []
        );

        $this->assertStringContainsString('Row Post A', $html);
        $this->assertStringContainsString('Row Post B', $html);
    }

    public function test_to_html_preserves_item_order(): void
    {
        $postA = Content::factory()->create(['title' => 'Alpha']);
        $postB = Content::factory()->create(['title' => 'Beta']);
        $postC = Content::factory()->create(['title' => 'Gamma']);

        $html = PostListBlock::toHtml(
            [
                'heading' => 'Ordered',
                'count' => 3,
                'items' => [
                    ['post_id' => $postC->id],
                    ['post_id' => $postA->id],
                    ['post_id' => $postB->id],
                ],
            ],
            []
        );

        $posGamma = strpos($html, 'Gamma');
        $posAlpha = strpos($html, 'Alpha');
        $posBeta = strpos($html, 'Beta');

        $this->assertNotFalse($posGamma);
        $this->assertNotFalse($posAlpha);
        $this->assertNotFalse($posBeta);
        $this->assertLessThan($posAlpha, $posGamma);
        $this->assertLessThan($posBeta, $posAlpha);
    }

    public function test_to_html_skips_deleted_posts(): void
    {
        $post = Content::factory()->create(['title' => 'Still Here']);

        $html = PostListBlock::toHtml(
            [
                'heading' => 'Skip Missing',
                'count' => 2,
                'items' => [
                    ['post_id' => $post->id],
                    ['post_id' => 99999],
                ],
            ],
            []
        );

        $this->assertStringContainsString('Still Here', $html);
        $this->assertSame(1, substr_count($html, '<li'));
    }

    public function test_to_html_renders_heading(): void
    {
        $post = Content::factory()->create();

        $html = PostListBlock::toHtml(
            [
                'heading' => 'My List Heading',
                'count' => 1,
                'items' => [['post_id' => $post->id]],
            ],
            []
        );

        $this->assertStringContainsString('My List Heading', $html);
    }

    public function test_to_html_renders_intro_when_present(): void
    {
        $post = Content::factory()->create();

        $html = PostListBlock::toHtml(
            [
                'heading' => 'H',
                'intro' => 'List intro text.',
                'count' => 1,
                'items' => [['post_id' => $post->id]],
            ],
            []
        );

        $this->assertStringContainsString('List intro text.', $html);
    }

    public function test_to_html_uses_cluster_title_as_overline_when_present(): void
    {
        $cluster = Cluster::factory()->create(['title' => 'Sleep Cluster']);
        $post = Content::factory()->create([
            'title' => 'Deep Sleep Tips',
            'cluster_id' => $cluster->id,
        ]);

        $html = PostListBlock::toHtml(
            [
                'heading' => 'H',
                'count' => 1,
                'items' => [['post_id' => $post->id]],
            ],
            []
        );

        $this->assertStringContainsString('Sleep Cluster', $html);
    }

    public function test_to_html_falls_back_to_category_as_overline(): void
    {
        $category = Category::factory()->create(['name' => 'Mindfulness Category']);
        $post = Content::factory()->create([
            'title' => 'Being Present',
            'category_id' => $category->id,
            'cluster_id' => null,
        ]);

        $html = PostListBlock::toHtml(
            [
                'heading' => 'H',
                'count' => 1,
                'items' => [['post_id' => $post->id]],
            ],
            []
        );

        $this->assertStringContainsString('Mindfulness Category', $html);
    }

    // -------------------------------------------------------------------------
    // toPreviewHtml
    // -------------------------------------------------------------------------

    public function test_preview_html_renders_heading(): void
    {
        $html = PostListBlock::toPreviewHtml(
            ['heading' => 'Preview Heading', 'count' => 4, 'items' => []]
        );

        $this->assertStringContainsString('Preview Heading', $html);
    }

    public function test_preview_html_renders_intro_when_present(): void
    {
        $html = PostListBlock::toPreviewHtml(
            ['heading' => 'H', 'intro' => 'Preview intro.', 'count' => 4, 'items' => []]
        );

        $this->assertStringContainsString('Preview intro.', $html);
    }

    public function test_preview_html_renders_items_count_against_total(): void
    {
        $html = PostListBlock::toPreviewHtml(
            [
                'heading' => 'H',
                'count' => 4,
                'items' => [
                    ['post_id' => 1],
                    ['post_id' => 2],
                ],
            ]
        );

        $this->assertStringContainsString('2 / 4', $html);
    }
}
