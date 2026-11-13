<?php

namespace Tests\Feature\RichEditor;

use App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\ImageBlock;
use Tests\FilamentTestCase;

class ImageBlockTest extends FilamentTestCase
{
    // -------------------------------------------------------------------------
    // Metadata
    // -------------------------------------------------------------------------

    public function test_has_correct_id(): void
    {
        $this->assertSame('image', ImageBlock::getId());
    }

    // -------------------------------------------------------------------------
    // Preview label
    // -------------------------------------------------------------------------

    public function test_preview_label_returns_block_label(): void
    {
        $label = ImageBlock::getPreviewLabel([]);

        $this->assertNotEmpty($label);
    }

    // -------------------------------------------------------------------------
    // toHtml
    // -------------------------------------------------------------------------

    public function test_to_html_renders_image_when_present(): void
    {
        $media = $this->createMedia(['path' => 'test/photo.jpg']);

        $html = ImageBlock::toHtml(['image_id' => $media->id], []);

        $this->assertStringContainsString('<img', $html);
        $this->assertStringContainsString('photo.jpg', $html);
    }

    public function test_to_html_renders_nothing_when_image_id_missing(): void
    {
        $html = ImageBlock::toHtml([], []);

        $this->assertStringNotContainsString('<img', $html);
    }

    public function test_to_html_renders_nothing_for_unknown_image_id(): void
    {
        $html = ImageBlock::toHtml(['image_id' => 99999], []);

        $this->assertStringNotContainsString('<img', $html);
    }

    public function test_to_html_wraps_image_in_figure_element(): void
    {
        $media = $this->createMedia();

        $html = ImageBlock::toHtml(['image_id' => $media->id], []);

        $this->assertStringContainsString('<figure', $html);
    }

    public function test_to_html_renders_caption_when_present(): void
    {
        $media = $this->createMedia(['caption' => 'A sunlit forest path.']);

        $html = ImageBlock::toHtml(['image_id' => $media->id], []);

        $this->assertStringContainsString('A sunlit forest path.', $html);
        $this->assertStringContainsString('<figcaption', $html);
    }

    public function test_to_html_omits_figcaption_when_caption_absent(): void
    {
        $media = $this->createMedia(['caption' => null]);

        $html = ImageBlock::toHtml(['image_id' => $media->id], []);

        $this->assertStringNotContainsString('<figcaption', $html);
    }

    public function test_to_html_sets_alt_attribute_from_media(): void
    {
        $media = $this->createMedia(['alt' => 'Forest path at sunset']);

        $html = ImageBlock::toHtml(['image_id' => $media->id], []);

        $this->assertStringContainsString('Forest path at sunset', $html);
    }

    // -------------------------------------------------------------------------
    // toPreviewHtml
    // -------------------------------------------------------------------------

    public function test_preview_html_renders_image_when_present(): void
    {
        $media = $this->createMedia();

        $html = ImageBlock::toPreviewHtml(['image_id' => $media->id]);

        $this->assertStringContainsString('<img', $html);
    }

    public function test_preview_html_shows_empty_state_when_no_image(): void
    {
        $html = ImageBlock::toPreviewHtml([]);

        $this->assertStringContainsString('No image', $html);
    }
}
