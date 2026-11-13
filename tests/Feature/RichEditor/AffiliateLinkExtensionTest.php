<?php

namespace Tests\Feature\RichEditor;

use App\Filament\Forms\Components\RichEditor\TipTapExtensions\AffiliateLinkExtension;
use App\Models\AffiliateLink;
use Illuminate\Support\Facades\DB;
use Tests\FilamentTestCase;

class AffiliateLinkExtensionTest extends FilamentTestCase
{
    private function makeMark(mixed $id): object
    {
        $attrs                   = new \stdClass();
        $attrs->affiliateLinkId  = $id;

        $mark        = new \stdClass();
        $mark->attrs = $attrs;

        return $mark;
    }

    // -------------------------------------------------------------------------
    // Active link rendering
    // -------------------------------------------------------------------------

    public function test_renders_anchor_tag_for_active_link(): void
    {
        $link   = AffiliateLink::factory()->create(['slug' => 'phpstorm-ide']);
        $result = (new AffiliateLinkExtension())->renderHTML($this->makeMark($link->id), []);

        $this->assertSame('a', $result[0]);
    }

    public function test_renders_go_url(): void
    {
        $link   = AffiliateLink::factory()->create(['slug' => 'git-pro-book']);
        $result = (new AffiliateLinkExtension())->renderHTML($this->makeMark($link->id), []);

        $this->assertStringContainsString('go/git-pro-book', $result[1]['href']);
    }

    public function test_renders_rel_nofollow_sponsored_noopener(): void
    {
        $link   = AffiliateLink::factory()->create();
        $result = (new AffiliateLinkExtension())->renderHTML($this->makeMark($link->id), []);

        $this->assertSame('nofollow sponsored noopener', $result[1]['rel']);
    }

    public function test_renders_target_blank(): void
    {
        $link   = AffiliateLink::factory()->create();
        $result = (new AffiliateLinkExtension())->renderHTML($this->makeMark($link->id), []);

        $this->assertSame('_blank', $result[1]['target']);
    }

    public function test_renders_affiliate_link_class(): void
    {
        $link   = AffiliateLink::factory()->create();
        $result = (new AffiliateLinkExtension())->renderHTML($this->makeMark($link->id), []);

        $this->assertSame('affiliate-link', $result[1]['class']);
    }

    public function test_renders_content_slot(): void
    {
        $link   = AffiliateLink::factory()->create();
        $result = (new AffiliateLinkExtension())->renderHTML($this->makeMark($link->id), []);

        $this->assertSame(0, $result[2]);
    }

    // -------------------------------------------------------------------------
    // Inactive / missing link → plaintext
    // -------------------------------------------------------------------------

    public function test_inactive_link_returns_null(): void
    {
        $link   = AffiliateLink::factory()->inactive()->create();
        $result = (new AffiliateLinkExtension())->renderHTML($this->makeMark($link->id), []);

        $this->assertNull($result);
    }

    public function test_unknown_id_returns_null(): void
    {
        $result = (new AffiliateLinkExtension())->renderHTML($this->makeMark(99999), []);

        $this->assertNull($result);
    }

    public function test_null_id_returns_null(): void
    {
        $result = (new AffiliateLinkExtension())->renderHTML($this->makeMark(null), []);

        $this->assertNull($result);
    }

    // -------------------------------------------------------------------------
    // URL caching
    // -------------------------------------------------------------------------

    public function test_same_link_is_queried_only_once(): void
    {
        $link      = AffiliateLink::factory()->create();
        $extension = new AffiliateLinkExtension();
        $mark      = $this->makeMark($link->id);

        $queryCount = 0;
        DB::listen(function ($query) use (&$queryCount) {
            if (str_contains($query->sql, 'affiliate_links')) {
                $queryCount++;
            }
        });

        $extension->renderHTML($mark, []);
        $extension->renderHTML($mark, []);

        $this->assertSame(1, $queryCount);
    }
}
