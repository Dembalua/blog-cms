<?php

namespace Tests\Feature\RichEditor;

use App\Models\AffiliateLink;
use App\Models\Content;
use Tests\FilamentTestCase;

class AffiliateLinkPreprocessingTest extends FilamentTestCase
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
    private function affiliateLinkText(string $text, ?int $linkId): array
    {
        return [
            'type' => 'text',
            'text' => $text,
            'marks' => [[
                'type' => 'affiliateLink',
                'attrs' => ['affiliateLinkId' => $linkId],
            ]],
        ];
    }

    private function makeHostWithContent(array $contentJson): Content
    {
        return Content::factory()->published()->create(['content' => $contentJson]);
    }

    // -------------------------------------------------------------------------
    // Name refresh
    // -------------------------------------------------------------------------

    public function test_renders_current_link_name_instead_of_stored_text(): void
    {
        $link = AffiliateLink::factory()->create(['name' => 'Current Link Name']);
        $host = $this->makeHostWithContent(
            $this->docWith([
                $this->paragraphWith([
                    $this->affiliateLinkText('Stale Link Text', $link->id),
                ]),
            ])
        );

        $html = $host->renderSafeRichContent('content');

        $this->assertStringContainsString('Current Link Name', $html);
        $this->assertStringNotContainsString('Stale Link Text', $html);
    }

    public function test_renders_updated_name_after_link_rename(): void
    {
        $link = AffiliateLink::factory()->create(['name' => 'Original Name']);
        $host = $this->makeHostWithContent(
            $this->docWith([
                $this->paragraphWith([
                    $this->affiliateLinkText('Original Name', $link->id),
                ]),
            ])
        );

        $link->update(['name' => 'Renamed Link']);

        $html = $host->renderSafeRichContent('content');

        $this->assertStringContainsString('Renamed Link', $html);
        $this->assertStringNotContainsString('Original Name', $html);
    }

    public function test_href_points_to_go_slug(): void
    {
        $link = AffiliateLink::factory()->create(['slug' => 'amazon-sleep-mask']);
        $host = $this->makeHostWithContent(
            $this->docWith([
                $this->paragraphWith([
                    $this->affiliateLinkText('Sleep Mask', $link->id),
                ]),
            ])
        );

        $html = $host->renderSafeRichContent('content');

        $this->assertStringContainsString('/go/amazon-sleep-mask', $html);
    }

    // -------------------------------------------------------------------------
    // ?c= parameter appended on render
    // -------------------------------------------------------------------------

    public function test_appends_c_query_parameter_with_host_content_id(): void
    {
        $link = AffiliateLink::factory()->create(['slug' => 'amazon-pillow']);
        $host = $this->makeHostWithContent(
            $this->docWith([
                $this->paragraphWith([
                    $this->affiliateLinkText('Pillow', $link->id),
                ]),
            ])
        );

        $html = $host->renderSafeRichContent('content');

        $this->assertStringContainsString('/go/amazon-pillow?c='.$host->id, $html);
    }

    // -------------------------------------------------------------------------
    // Dead / inactive link removal
    // -------------------------------------------------------------------------

    public function test_inactive_link_leaves_no_anchor_and_no_text(): void
    {
        $link = AffiliateLink::factory()->inactive()->create(['name' => 'Inactive Name']);
        $host = $this->makeHostWithContent(
            $this->docWith([
                $this->paragraphWith([
                    $this->affiliateLinkText('Was a link', $link->id),
                ]),
            ])
        );

        $html = $host->renderSafeRichContent('content');

        $this->assertStringNotContainsString('Was a link', $html);
        $this->assertStringNotContainsString('Inactive Name', $html);
        $this->assertStringNotContainsString('affiliate-link', $html);
    }

    public function test_inactive_link_in_otherwise_empty_paragraph_removes_paragraph(): void
    {
        $link = AffiliateLink::factory()->inactive()->create();
        $host = $this->makeHostWithContent(
            $this->docWith([
                $this->paragraphWith([
                    $this->affiliateLinkText('Gone', $link->id),
                ]),
            ])
        );

        $html = $host->renderSafeRichContent('content');

        $this->assertStringNotContainsString('<p', $html);
    }

    public function test_unknown_id_is_removed(): void
    {
        $host = $this->makeHostWithContent(
            $this->docWith([
                $this->paragraphWith([
                    $this->affiliateLinkText('Ghost Affiliate', 99999),
                ]),
            ])
        );

        $html = $host->renderSafeRichContent('content');

        $this->assertStringNotContainsString('Ghost Affiliate', $html);
    }

    public function test_null_affiliate_link_id_is_removed(): void
    {
        $host = $this->makeHostWithContent(
            $this->docWith([
                $this->paragraphWith([
                    $this->affiliateLinkText('Broken', null),
                ]),
            ])
        );

        $html = $host->renderSafeRichContent('content');

        $this->assertStringNotContainsString('Broken', $html);
    }

    // -------------------------------------------------------------------------
    // Preservation of surrounding content
    // -------------------------------------------------------------------------

    public function test_other_paragraphs_preserved_when_one_link_dies(): void
    {
        $host = $this->makeHostWithContent(
            $this->docWith([
                $this->paragraphWith([['type' => 'text', 'text' => 'First paragraph.']]),
                $this->paragraphWith([$this->affiliateLinkText('Ghost', 99999)]),
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
                    $this->affiliateLinkText('Ghost', 99999),
                ]),
            ])
        );

        $html = $host->renderSafeRichContent('content');

        $this->assertStringContainsString('Keep me.', $html);
        $this->assertStringContainsString('<p', $html);
        $this->assertStringNotContainsString('Ghost', $html);
    }

    public function test_multiple_affiliate_links_are_processed_independently(): void
    {
        $alive = AffiliateLink::factory()->create(['name' => 'Alive Affiliate']);
        $host = $this->makeHostWithContent(
            $this->docWith([
                $this->paragraphWith([$this->affiliateLinkText('old', $alive->id)]),
                $this->paragraphWith([$this->affiliateLinkText('ghost', 99999)]),
            ])
        );

        $html = $host->renderSafeRichContent('content');

        $this->assertStringContainsString('Alive Affiliate', $html);
        $this->assertStringNotContainsString('ghost', $html);
        $this->assertSame(1, substr_count($html, '<p'));
    }

    // -------------------------------------------------------------------------
    // Non-persistence
    // -------------------------------------------------------------------------

    public function test_original_content_attribute_is_restored_after_rendering(): void
    {
        $link = AffiliateLink::factory()->create(['name' => 'New Name']);
        $originalText = 'Pre-rename Text';
        $host = $this->makeHostWithContent(
            $this->docWith([
                $this->paragraphWith([
                    $this->affiliateLinkText($originalText, $link->id),
                ]),
            ])
        );

        $host->renderSafeRichContent('content');

        $stored = Content::find($host->id)->content;
        $this->assertSame($originalText, $stored['content'][0]['content'][0]['text']);
    }
}
