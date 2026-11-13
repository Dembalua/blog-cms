<?php

namespace Tests\Feature\RichEditor;

use App\Enums\ContentStatus;
use App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\SectionsBlock;
use App\Models\Cluster;
use App\Models\Content;
use Tests\FilamentTestCase;

class SectionsBlockTest extends FilamentTestCase
{
    // -------------------------------------------------------------------------
    // Metadata
    // -------------------------------------------------------------------------

    public function test_has_correct_id(): void
    {
        $this->assertSame('sections', SectionsBlock::getId());
    }

    // -------------------------------------------------------------------------
    // Preview label
    // -------------------------------------------------------------------------

    public function test_preview_label_without_heading_has_no_colon(): void
    {
        $label = SectionsBlock::getPreviewLabel(['items' => []]);

        $this->assertStringNotContainsString(':', $label);
    }

    public function test_preview_label_with_heading_appends_heading(): void
    {
        $label = SectionsBlock::getPreviewLabel(['heading' => 'My Sections', 'items' => []]);

        $this->assertStringContainsString('My Sections', $label);
        $this->assertStringContainsString(':', $label);
    }

    public function test_preview_label_includes_item_count(): void
    {
        $items = [
            ['heading' => 'A', 'body' => 'a'],
            ['heading' => 'B', 'body' => 'b'],
            ['heading' => 'C', 'body' => 'c'],
        ];

        $label = SectionsBlock::getPreviewLabel(['items' => $items]);

        $this->assertStringContainsString('(3)', $label);
    }

    public function test_preview_label_with_zero_items_has_no_count(): void
    {
        $label = SectionsBlock::getPreviewLabel(['items' => []]);

        $this->assertStringNotContainsString('(', $label);
    }

    // -------------------------------------------------------------------------
    // toHtml – structure
    // -------------------------------------------------------------------------

    public function test_to_html_renders_item_heading(): void
    {
        $html = SectionsBlock::toHtml(
            ['items' => [['heading' => 'Section A', 'body' => 'Body A']]],
            []
        );

        $this->assertStringContainsString('Section A', $html);
    }

    public function test_to_html_renders_item_body(): void
    {
        $html = SectionsBlock::toHtml(
            ['items' => [['heading' => 'Section A', 'body' => 'Body text A']]],
            []
        );

        $this->assertStringContainsString('Body text A', $html);
    }

    public function test_to_html_renders_section_heading_when_present(): void
    {
        $html = SectionsBlock::toHtml(
            ['heading' => 'Main Heading', 'items' => [['heading' => 'A', 'body' => 'a']]],
            []
        );

        $this->assertStringContainsString('Main Heading', $html);
        $this->assertStringContainsString('<h2', $html);
    }

    public function test_to_html_omits_h2_when_heading_absent(): void
    {
        $html = SectionsBlock::toHtml(
            ['items' => [['heading' => 'A', 'body' => 'a']]],
            []
        );

        $this->assertStringNotContainsString('<h2', $html);
    }

    public function test_to_html_renders_intro_when_present(): void
    {
        $html = SectionsBlock::toHtml(
            ['intro' => 'Intro paragraph.', 'items' => [['heading' => 'A', 'body' => 'a']]],
            []
        );

        $this->assertStringContainsString('Intro paragraph.', $html);
    }

    public function test_to_html_renders_multiple_items(): void
    {
        $html = SectionsBlock::toHtml(
            [
                'items' => [
                    ['heading' => 'First', 'body' => 'body one'],
                    ['heading' => 'Second', 'body' => 'body two'],
                ],
            ],
            []
        );

        $this->assertStringContainsString('First', $html);
        $this->assertStringContainsString('Second', $html);
    }

    // -------------------------------------------------------------------------
    // toHtml – internal link
    // -------------------------------------------------------------------------

    public function test_to_html_renders_internal_link_when_post_selected(): void
    {
        $cluster = Cluster::factory()->create();
        $post    = Content::factory()->published()->create([
            'cluster_id' => $cluster->id,
            'title'      => 'Linked Article',
        ]);

        $html = SectionsBlock::toHtml(
            ['items' => [[
                'heading'      => 'A',
                'body'         => 'a',
                'link_post_id' => $post->id,
            ]]],
            []
        );

        $this->assertStringContainsString('Linked Article', $html);
        $this->assertStringContainsString('internal-link', $html);
    }

    public function test_to_html_omits_link_when_post_id_missing(): void
    {
        $html = SectionsBlock::toHtml(
            ['items' => [['heading' => 'A', 'body' => 'a']]],
            []
        );

        $this->assertStringNotContainsString('internal-link', $html);
    }

    public function test_to_html_omits_link_for_unknown_post(): void
    {
        $html = SectionsBlock::toHtml(
            ['items' => [['heading' => 'A', 'body' => 'a', 'link_post_id' => 99999]]],
            []
        );

        $this->assertStringNotContainsString('internal-link', $html);
    }

    public function test_to_html_link_with_arrow_class_when_enabled(): void
    {
        $cluster = Cluster::factory()->create();
        $post    = Content::factory()->published()->create(['cluster_id' => $cluster->id]);

        $html = SectionsBlock::toHtml(
            ['items' => [[
                'heading'         => 'A',
                'body'            => 'a',
                'link_post_id'    => $post->id,
                'link_with_arrow' => true,
            ]]],
            []
        );

        $this->assertStringContainsString('internal-link--with-arrow', $html);
    }

    public function test_to_html_link_without_arrow_class_when_disabled(): void
    {
        $cluster = Cluster::factory()->create();
        $post    = Content::factory()->published()->create(['cluster_id' => $cluster->id]);

        $html = SectionsBlock::toHtml(
            ['items' => [[
                'heading'         => 'A',
                'body'            => 'a',
                'link_post_id'    => $post->id,
                'link_with_arrow' => false,
            ]]],
            []
        );

        $this->assertStringNotContainsString('internal-link--with-arrow', $html);
    }

    // -------------------------------------------------------------------------
    // toPreviewHtml
    // -------------------------------------------------------------------------

    public function test_preview_html_renders_item_heading(): void
    {
        $html = SectionsBlock::toPreviewHtml(
            ['items' => [['heading' => 'Preview Section', 'body' => 'body']]]
        );

        $this->assertStringContainsString('Preview Section', $html);
    }

    public function test_preview_html_renders_section_heading_when_present(): void
    {
        $html = SectionsBlock::toPreviewHtml(
            ['heading' => 'Preview Heading', 'items' => []]
        );

        $this->assertStringContainsString('Preview Heading', $html);
    }

    public function test_preview_html_shows_empty_state_when_no_items(): void
    {
        $html = SectionsBlock::toPreviewHtml(['items' => []]);

        $this->assertStringContainsString('No sections', $html);
    }
}
