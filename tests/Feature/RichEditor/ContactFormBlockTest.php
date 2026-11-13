<?php

namespace Tests\Feature\RichEditor;

use App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\ContactFormBlock;
use Tests\FilamentTestCase;

class ContactFormBlockTest extends FilamentTestCase
{
    // -------------------------------------------------------------------------
    // Metadata
    // -------------------------------------------------------------------------

    public function test_has_correct_id(): void
    {
        $this->assertSame('contact-form', ContactFormBlock::getId());
    }

    // -------------------------------------------------------------------------
    // Preview label
    // -------------------------------------------------------------------------

    public function test_preview_label_with_button_text_appends_button_text(): void
    {
        $label = ContactFormBlock::getPreviewLabel(['button_text' => 'Schreib uns']);

        $this->assertStringContainsString('Schreib uns', $label);
        $this->assertStringContainsString(':', $label);
    }

    public function test_preview_label_without_button_text_has_no_colon(): void
    {
        $label = ContactFormBlock::getPreviewLabel([]);

        $this->assertStringNotContainsString(':', $label);
    }

    // -------------------------------------------------------------------------
    // toHtml — outputs a safe placeholder; modal is injected via app.js
    // -------------------------------------------------------------------------

    public function test_to_html_renders_configured_button_text(): void
    {
        $html = ContactFormBlock::toHtml(['button_text' => 'Jetzt anfragen'], []);

        $this->assertStringContainsString('Jetzt anfragen', $html);
    }

    public function test_to_html_contains_data_type_marker(): void
    {
        $html = ContactFormBlock::toHtml(['button_text' => 'Contact'], []);

        $this->assertStringContainsString('data-type="contact-form"', $html);
    }

    // -------------------------------------------------------------------------
    // toPreviewHtml
    // -------------------------------------------------------------------------

    public function test_preview_html_renders_button_text(): void
    {
        $html = ContactFormBlock::toPreviewHtml(['button_text' => 'Schreib uns']);

        $this->assertStringContainsString('Schreib uns', $html);
    }
}
