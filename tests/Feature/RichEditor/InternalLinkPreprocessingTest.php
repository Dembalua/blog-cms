<?php

namespace Tests\Feature\RichEditor;

use App\Models\Content;
use Tests\FilamentTestCase;

class InternalLinkPreprocessingTest extends FilamentTestCase
{
    /**
     * @param  array<int, array<string, mixed>>  $paragraphs
     * @return array<string, mixed>
     */
    private function docWith(array $paragraphs): array
    {
        return [
            'type' => 'doc',
            'content' => $paragraphs,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function paragraphWith(array $children): array
    {
        return [
            'type' => 'paragraph',
            'content' => $children,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function internalLinkText(string $text, int $postId, bool $withArrow = false): array
    {
        return [
            'type' => 'text',
            'text' => $text,
            'marks' => [[
                'type' => 'internalLink',
                'attrs' => ['postId' => $postId, 'withArrow' => $withArrow],
            ]],
        ];
    }

    private function makeHostWithContent(array $contentJson): Content
    {
        return Content::factory()->published()->create(['content' => $contentJson]);
    }

    // -------------------------------------------------------------------------
    // Title refresh
    // -------------------------------------------------------------------------

    public function test_renders_current_post_title_instead_of_stored_text(): void
    {
        $target = Content::factory()->published()->create(['title' => 'Current Target Title']);
        $host = $this->makeHostWithContent(
            $this->docWith([
                $this->paragraphWith([
                    $this->internalLinkText('Stale Link Text', $target->id),
                ]),
            ])
        );

        $html = $host->renderSafeRichContent('content');

        $this->assertStringContainsString('Current Target Title', $html);
        $this->assertStringNotContainsString('Stale Link Text', $html);
    }

    public function test_renders_updated_title_after_target_rename(): void
    {
        $target = Content::factory()->published()->create(['title' => 'Original Title']);
        $host = $this->makeHostWithContent(
            $this->docWith([
                $this->paragraphWith([
                    $this->internalLinkText('Original Title', $target->id),
                ]),
            ])
        );

        $target->update(['title' => 'Renamed Title']);

        $html = $host->renderSafeRichContent('content');

        $this->assertStringContainsString('Renamed Title', $html);
        $this->assertStringNotContainsString('Original Title', $html);
    }

    public function test_href_follows_target_slug(): void
    {
        $target = Content::factory()->published()->create(['title' => 'Pointed To']);
        $host = $this->makeHostWithContent(
            $this->docWith([
                $this->paragraphWith([
                    $this->internalLinkText('Pointed To', $target->id),
                ]),
            ])
        );

        $html = $host->renderSafeRichContent('content');

        $this->assertStringContainsString($target->path->url, $html);
    }

    // -------------------------------------------------------------------------
    // Dead link removal
    // -------------------------------------------------------------------------

    public function test_dead_link_leaves_no_anchor_and_no_text(): void
    {
        $host = $this->makeHostWithContent(
            $this->docWith([
                $this->paragraphWith([
                    $this->internalLinkText('Ghost Link', 99999),
                ]),
            ])
        );

        $html = $host->renderSafeRichContent('content');

        $this->assertStringNotContainsString('Ghost Link', $html);
        $this->assertStringNotContainsString('internal-link', $html);
    }

    public function test_dead_link_in_otherwise_empty_paragraph_removes_paragraph(): void
    {
        $host = $this->makeHostWithContent(
            $this->docWith([
                $this->paragraphWith([
                    $this->internalLinkText('Ghost Link', 99999),
                ]),
            ])
        );

        $html = $host->renderSafeRichContent('content');

        $this->assertStringNotContainsString('<p', $html);
    }

    public function test_dead_link_null_post_id_is_also_removed(): void
    {
        $host = $this->makeHostWithContent(
            $this->docWith([
                $this->paragraphWith([
                    $this->internalLinkText('Broken Link', 0),
                ]),
            ])
        );

        $html = $host->renderSafeRichContent('content');

        $this->assertStringNotContainsString('Broken Link', $html);
    }

    public function test_paragraph_with_only_whitespace_after_removal_is_dropped(): void
    {
        $host = $this->makeHostWithContent(
            $this->docWith([
                $this->paragraphWith([
                    ['type' => 'text', 'text' => '   '],
                    $this->internalLinkText('Ghost', 99999),
                ]),
            ])
        );

        $html = $host->renderSafeRichContent('content');

        $this->assertStringNotContainsString('<p', $html);
    }

    // -------------------------------------------------------------------------
    // Preservation of surrounding content
    // -------------------------------------------------------------------------

    public function test_other_paragraphs_are_preserved_when_one_link_dies(): void
    {
        $host = $this->makeHostWithContent(
            $this->docWith([
                $this->paragraphWith([['type' => 'text', 'text' => 'First paragraph.']]),
                $this->paragraphWith([$this->internalLinkText('Ghost', 99999)]),
                $this->paragraphWith([['type' => 'text', 'text' => 'Third paragraph.']]),
            ])
        );

        $html = $host->renderSafeRichContent('content');

        $this->assertStringContainsString('First paragraph.', $html);
        $this->assertStringContainsString('Third paragraph.', $html);
        $this->assertSame(2, substr_count($html, '<p'));
    }

    public function test_paragraph_with_other_text_keeps_paragraph_after_link_removal(): void
    {
        $host = $this->makeHostWithContent(
            $this->docWith([
                $this->paragraphWith([
                    ['type' => 'text', 'text' => 'Keep me. '],
                    $this->internalLinkText('Ghost', 99999),
                ]),
            ])
        );

        $html = $host->renderSafeRichContent('content');

        $this->assertStringContainsString('Keep me.', $html);
        $this->assertStringContainsString('<p', $html);
        $this->assertStringNotContainsString('Ghost', $html);
    }

    public function test_multiple_links_in_document_are_processed_independently(): void
    {
        $alive = Content::factory()->published()->create(['title' => 'Alive Target']);
        $host = $this->makeHostWithContent(
            $this->docWith([
                $this->paragraphWith([$this->internalLinkText('old', $alive->id)]),
                $this->paragraphWith([$this->internalLinkText('ghost', 99999)]),
            ])
        );

        $html = $host->renderSafeRichContent('content');

        $this->assertStringContainsString('Alive Target', $html);
        $this->assertStringNotContainsString('ghost', $html);
        $this->assertSame(1, substr_count($html, '<p'));
    }

    // -------------------------------------------------------------------------
    // Non-persistence
    // -------------------------------------------------------------------------

    public function test_original_content_attribute_is_restored_after_rendering(): void
    {
        $target = Content::factory()->published()->create(['title' => 'New Title']);
        $originalText = 'Pre-rename Text';
        $host = $this->makeHostWithContent(
            $this->docWith([
                $this->paragraphWith([
                    $this->internalLinkText($originalText, $target->id),
                ]),
            ])
        );

        $host->renderSafeRichContent('content');

        $stored = Content::find($host->id)->content;
        $this->assertSame($originalText, $stored['content'][0]['content'][0]['text']);
    }

    // -------------------------------------------------------------------------
    // Unrelated content is untouched
    // -------------------------------------------------------------------------

    public function test_plain_text_without_internal_links_is_unchanged(): void
    {
        $host = $this->makeHostWithContent(
            $this->docWith([
                $this->paragraphWith([['type' => 'text', 'text' => 'Plain paragraph.']]),
            ])
        );

        $html = $host->renderSafeRichContent('content');

        $this->assertStringContainsString('Plain paragraph.', $html);
    }

    public function test_external_links_are_not_affected(): void
    {
        $host = $this->makeHostWithContent(
            $this->docWith([
                $this->paragraphWith([[
                    'type' => 'text',
                    'text' => 'External site',
                    'marks' => [[
                        'type' => 'link',
                        'attrs' => ['href' => 'https://example.com'],
                    ]],
                ]]),
            ])
        );

        $html = $host->renderSafeRichContent('content');

        $this->assertStringContainsString('External site', $html);
        $this->assertStringContainsString('example.com', $html);
    }
}
