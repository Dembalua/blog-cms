<?php

namespace Tests\Feature\RichEditor;

use App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\ImageTextBlock;
use Tests\FilamentTestCase;

class ImageTextBlockTest extends FilamentTestCase
{
    // -------------------------------------------------------------------------
    // Metadata
    // -------------------------------------------------------------------------

    public function test_has_correct_id(): void
    {
        $this->assertSame('image-text', ImageTextBlock::getId());
    }

    // -------------------------------------------------------------------------
    // Preview label
    // -------------------------------------------------------------------------

    public function test_preview_label_without_heading_returns_block_label(): void
    {
        $label = ImageTextBlock::getPreviewLabel(['heading' => null, 'body' => 'Some text.']);

        $this->assertStringNotContainsString(':', $label);
    }

    public function test_preview_label_with_heading_appends_heading(): void
    {
        $label = ImageTextBlock::getPreviewLabel(['heading' => 'My Heading', 'body' => 'Some text.']);

        $this->assertStringContainsString('My Heading', $label);
        $this->assertStringContainsString(':', $label);
    }

    // -------------------------------------------------------------------------
    // toHtml – text and structure
    // -------------------------------------------------------------------------

    public function test_to_html_renders_body_text(): void
    {
        $html = ImageTextBlock::toHtml(
            ['body' => 'Body content here.', 'image_position' => 'right'],
            []
        );

        $this->assertStringContainsString('Body content here.', $html);
    }

    public function test_to_html_renders_heading_when_present(): void
    {
        $html = ImageTextBlock::toHtml(
            ['heading' => 'Section Title', 'body' => 'Body text.', 'image_position' => 'right'],
            []
        );

        $this->assertStringContainsString('Section Title', $html);
    }

    public function test_to_html_omits_heading_element_when_absent(): void
    {
        $html = ImageTextBlock::toHtml(
            ['body' => 'Body text.', 'image_position' => 'right'],
            []
        );

        $this->assertStringNotContainsString('<h3', $html);
    }

    // -------------------------------------------------------------------------
    // toHtml – image position
    // -------------------------------------------------------------------------

    public function test_to_html_image_left_applies_order_class(): void
    {
        $html = ImageTextBlock::toHtml(
            ['body' => 'Body text.', 'image_position' => 'left'],
            []
        );

        $this->assertStringContainsString('md:order-first', $html);
    }

    public function test_to_html_image_right_does_not_apply_order_first_class(): void
    {
        $html = ImageTextBlock::toHtml(
            ['body' => 'Body text.', 'image_position' => 'right'],
            []
        );

        $this->assertStringNotContainsString('md:order-first', $html);
    }

    // -------------------------------------------------------------------------
    // toHtml – image
    // -------------------------------------------------------------------------

    public function test_to_html_renders_image_when_present(): void
    {
        $media = $this->createMedia(['path' => 'test/photo.jpg']);

        $html = ImageTextBlock::toHtml(
            ['image_id' => $media->id, 'body' => 'Body text.', 'image_position' => 'right'],
            []
        );

        $this->assertStringContainsString('<img', $html);
    }

    public function test_to_html_renders_no_image_tag_when_image_id_missing(): void
    {
        $html = ImageTextBlock::toHtml(
            ['body' => 'Body text.', 'image_position' => 'right'],
            []
        );

        $this->assertStringNotContainsString('<img', $html);
    }

    // -------------------------------------------------------------------------
    // toPreviewHtml
    // -------------------------------------------------------------------------

    public function test_preview_html_renders_body_text(): void
    {
        $html = ImageTextBlock::toPreviewHtml(
            ['body' => 'Preview body text.', 'image_position' => 'right'],
            []
        );

        $this->assertStringContainsString('Preview body text.', $html);
    }

    public function test_preview_html_renders_heading_when_present(): void
    {
        $html = ImageTextBlock::toPreviewHtml(
            ['heading' => 'Preview Heading', 'body' => 'Body.', 'image_position' => 'right'],
            []
        );

        $this->assertStringContainsString('Preview Heading', $html);
    }
}
