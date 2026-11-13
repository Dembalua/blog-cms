<?php

namespace Tests\Feature\RichEditor;

use App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\FaqBlock;
use Tests\FilamentTestCase;

class FaqBlockTest extends FilamentTestCase
{
    // -------------------------------------------------------------------------
    // Metadata
    // -------------------------------------------------------------------------

    public function test_has_correct_id(): void
    {
        $this->assertSame('faq', FaqBlock::getId());
    }

    // -------------------------------------------------------------------------
    // Preview label
    // -------------------------------------------------------------------------

    public function test_preview_label_without_heading_does_not_contain_colon(): void
    {
        $label = FaqBlock::getPreviewLabel(['items' => []]);

        $this->assertStringNotContainsString(':', $label);
    }

    public function test_preview_label_with_heading_appends_heading(): void
    {
        $label = FaqBlock::getPreviewLabel(['heading' => 'Common Questions', 'items' => []]);

        $this->assertStringContainsString('Common Questions', $label);
        $this->assertStringContainsString(':', $label);
    }

    public function test_preview_label_includes_item_count(): void
    {
        $items = [
            ['question' => 'Q1', 'answer' => 'A1'],
            ['question' => 'Q2', 'answer' => 'A2'],
        ];

        $label = FaqBlock::getPreviewLabel(['items' => $items]);

        $this->assertStringContainsString('(2)', $label);
    }

    public function test_preview_label_with_zero_items_has_no_count(): void
    {
        $label = FaqBlock::getPreviewLabel(['items' => []]);

        $this->assertStringNotContainsString('(', $label);
    }

    // -------------------------------------------------------------------------
    // toHtml – structure
    // -------------------------------------------------------------------------

    public function test_to_html_renders_question_text(): void
    {
        $html = FaqBlock::toHtml(
            ['items' => [['question' => 'What is Laravel?', 'answer' => 'A PHP web framework.']]],
            []
        );

        $this->assertStringContainsString('What is Laravel?', $html);
    }

    public function test_to_html_renders_answer_text(): void
    {
        $html = FaqBlock::toHtml(
            ['items' => [['question' => 'What is Laravel?', 'answer' => 'A PHP web framework.']]],
            []
        );

        $this->assertStringContainsString('A PHP web framework.', $html);
    }

    public function test_to_html_renders_heading_when_present(): void
    {
        $html = FaqBlock::toHtml(
            ['heading' => 'Frequently Asked', 'items' => [['question' => 'What is Laravel?', 'answer' => 'A PHP framework.']]],
            []
        );

        $this->assertStringContainsString('Frequently Asked', $html);
    }

    public function test_to_html_omits_heading_element_when_absent(): void
    {
        $html = FaqBlock::toHtml(
            ['items' => [['question' => 'What is Laravel?', 'answer' => 'A PHP framework.']]],
            []
        );

        $this->assertStringNotContainsString('<h2', $html);
    }

    public function test_to_html_renders_multiple_items(): void
    {
        $html = FaqBlock::toHtml(
            [
                'items' => [
                    ['question' => 'What is Composer?', 'answer' => 'A PHP dependency manager.'],
                    ['question' => 'What is Artisan?', 'answer' => 'The Laravel CLI tool.'],
                ],
            ],
            []
        );

        $this->assertStringContainsString('What is Composer?', $html);
        $this->assertStringContainsString('What is Artisan?', $html);
    }

    public function test_to_html_uses_details_summary_for_accordion(): void
    {
        $html = FaqBlock::toHtml(
            ['items' => [['question' => 'What is Tailwind?', 'answer' => 'A utility-first CSS framework.']]],
            []
        );

        $this->assertStringContainsString('<details', $html);
        $this->assertStringContainsString('<summary', $html);
    }

    // -------------------------------------------------------------------------
    // toPreviewHtml
    // -------------------------------------------------------------------------

    public function test_preview_html_renders_question_text(): void
    {
        $html = FaqBlock::toPreviewHtml(
            ['items' => [['question' => 'What is Filament?', 'answer' => 'An admin panel for Laravel.']]]
        );

        $this->assertStringContainsString('What is Filament?', $html);
    }

    public function test_preview_html_renders_heading_when_present(): void
    {
        $html = FaqBlock::toPreviewHtml(
            ['heading' => 'Preview Heading', 'items' => [['question' => 'What is Filament?', 'answer' => 'An admin panel.']]]
        );

        $this->assertStringContainsString('Preview Heading', $html);
    }

    public function test_preview_html_shows_empty_state_when_no_items(): void
    {
        $html = FaqBlock::toPreviewHtml(['items' => []]);

        $this->assertStringContainsString('No entries', $html);
    }

    // -------------------------------------------------------------------------
    // extractItems
    // -------------------------------------------------------------------------

    public function test_extract_items_returns_empty_for_null_content(): void
    {
        $this->assertSame([], FaqBlock::extractItems(null));
    }

    public function test_extract_items_returns_empty_for_empty_content(): void
    {
        $this->assertSame([], FaqBlock::extractItems([]));
    }

    public function test_extract_items_returns_empty_when_no_faq_blocks(): void
    {
        $content = [
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Hello world']]],
            ],
        ];

        $this->assertSame([], FaqBlock::extractItems($content));
    }

    public function test_extract_items_returns_items_from_faq_block(): void
    {
        $content = [
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'customBlock',
                    'attrs' => [
                        'id' => 'faq',
                        'config' => [
                            'items' => [
                                ['question' => 'What is PHP?', 'answer' => 'A server-side scripting language.'],
                                ['question' => 'What is MySQL?', 'answer' => 'A relational database.'],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $items = FaqBlock::extractItems($content);

        $this->assertCount(2, $items);
        $this->assertSame('What is PHP?', $items[0]['question']);
        $this->assertSame('A relational database.', $items[1]['answer']);
    }

    public function test_extract_items_ignores_other_custom_blocks(): void
    {
        $content = [
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'customBlock',
                    'attrs' => [
                        'id' => 'image-text',
                        'config' => ['heading' => 'Some heading', 'body' => 'Some text'],
                    ],
                ],
            ],
        ];

        $this->assertSame([], FaqBlock::extractItems($content));
    }

    public function test_extract_items_skips_incomplete_items(): void
    {
        $content = [
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'customBlock',
                    'attrs' => [
                        'id' => 'faq',
                        'config' => [
                            'items' => [
                                ['question' => 'What is Git?', 'answer' => 'A version control system.'],
                                ['question' => 'Only question'],
                                ['answer' => 'Only answer'],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $items = FaqBlock::extractItems($content);

        $this->assertCount(1, $items);
        $this->assertSame('What is Git?', $items[0]['question']);
    }

    public function test_extract_items_merges_items_from_multiple_faq_blocks(): void
    {
        $content = [
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'customBlock',
                    'attrs' => [
                        'id' => 'faq',
                        'config' => ['items' => [['question' => 'What is Docker?', 'answer' => 'A container platform.']]],
                    ],
                ],
                [
                    'type' => 'customBlock',
                    'attrs' => [
                        'id' => 'faq',
                        'config' => ['items' => [['question' => 'What is Kubernetes?', 'answer' => 'A container orchestrator.']]],
                    ],
                ],
            ],
        ];

        $items = FaqBlock::extractItems($content);

        $this->assertCount(2, $items);
    }
}
