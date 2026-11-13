<?php

namespace Tests\Feature\RichEditor;

use App\Filament\Forms\Components\RichEditor\TipTapExtensions\InternalLinkExtension;
use App\Models\Content;
use Illuminate\Support\Facades\DB;
use Tests\FilamentTestCase;

class InternalLinkExtensionTest extends FilamentTestCase
{
    private function makeMark(mixed $postId): object
    {
        $attrs = new \stdClass;
        $attrs->postId = $postId;

        $mark = new \stdClass;
        $mark->attrs = $attrs;

        return $mark;
    }

    // -------------------------------------------------------------------------
    // Tag structure
    // -------------------------------------------------------------------------

    public function test_renders_anchor_tag(): void
    {
        $post = Content::factory()->published()->create();
        $result = (new InternalLinkExtension)->renderHTML($this->makeMark($post->id), []);

        $this->assertSame('a', $result[0]);
    }

    public function test_renders_with_content_slot(): void
    {
        $post = Content::factory()->published()->create();
        $result = (new InternalLinkExtension)->renderHTML($this->makeMark($post->id), []);

        $this->assertSame(0, $result[2]);
    }

    // -------------------------------------------------------------------------
    // URL resolution
    // -------------------------------------------------------------------------

    public function test_resolves_post_id_to_url(): void
    {
        $post = Content::factory()->published()->create();
        $result = (new InternalLinkExtension)->renderHTML($this->makeMark($post->id), []);

        $this->assertStringContainsString($post->path->url, $result[1]['href']);
    }

    public function test_falls_back_to_hash_when_post_not_found(): void
    {
        $result = (new InternalLinkExtension)->renderHTML($this->makeMark(99999), []);

        $this->assertSame('#', $result[1]['href']);
    }

    public function test_falls_back_to_hash_when_post_id_is_null(): void
    {
        $result = (new InternalLinkExtension)->renderHTML($this->makeMark(null), []);

        $this->assertSame('#', $result[1]['href']);
    }

    // -------------------------------------------------------------------------
    // HTML attributes
    // -------------------------------------------------------------------------

    public function test_merges_extra_html_attributes(): void
    {
        $post = Content::factory()->published()->create();
        $result = (new InternalLinkExtension)->renderHTML(
            $this->makeMark($post->id),
            ['class' => 'my-link', 'data-test' => 'value'],
        );

        $this->assertSame('internal-link', $result[1]['class']);
        $this->assertSame('value', $result[1]['data-test']);
        $this->assertArrayHasKey('href', $result[1]);
    }

    // -------------------------------------------------------------------------
    // URL caching
    // -------------------------------------------------------------------------

    public function test_same_post_is_queried_only_once(): void
    {
        $post = Content::factory()->published()->create();
        $extension = new InternalLinkExtension;
        $mark = $this->makeMark($post->id);

        $queryCount = 0;
        DB::listen(function ($query) use (&$queryCount) {
            if (str_contains($query->sql, 'contents')) {
                $queryCount++;
            }
        });

        $extension->renderHTML($mark, []);
        $extension->renderHTML($mark, []);

        $this->assertSame(1, $queryCount);
    }
}
