<?php

namespace Tests\Feature\Frontend;

use App\Enums\RedirectStatus;
use App\Models\AffiliateLink;
use App\Models\AffiliateLinkClick;
use App\Models\Content;
use App\Models\PathRedirect;
use Tests\FilamentTestCase;

class AffiliateLinkRedirectTest extends FilamentTestCase
{
    public function test_active_link_redirects_to_target_url(): void
    {
        $link = AffiliateLink::factory()->create([
            'slug'      => 'tailwind-css-book',
            'url'       => 'https://example.com/tailwind',
            'is_active' => true,
        ]);

        $this->get('/go/tailwind-css-book')
            ->assertRedirect('https://example.com/tailwind')
            ->assertStatus(302);
    }

    public function test_active_link_increments_click_counter(): void
    {
        $link = AffiliateLink::factory()->create([
            'slug'   => 'webpack-guide',
            'clicks' => 0,
        ]);

        $this->get('/go/webpack-guide');

        $this->assertDatabaseHas('affiliate_links', [
            'id'     => $link->id,
            'clicks' => 1,
        ]);
    }

    public function test_inactive_link_redirects_to_home(): void
    {
        AffiliateLink::factory()->inactive()->create(['slug' => 'deprecated-tool']);

        $this->get('/go/deprecated-tool')
            ->assertRedirect('/')
            ->assertStatus(302);
    }

    public function test_unknown_slug_returns_404(): void
    {
        $this->get('/go/does-not-exist')
            ->assertNotFound();
    }

    public function test_old_slug_follows_301_redirect(): void
    {
        PathRedirect::create([
            'old_path'    => 'go/old-devops-book',
            'new_path'    => 'go/new-devops-book',
            'status_code' => RedirectStatus::MOVED_PERMANENTLY,
        ]);

        $this->get('/go/old-devops-book')
            ->assertRedirect('/go/new-devops-book')
            ->assertStatus(301);
    }

    public function test_deleted_slug_returns_410(): void
    {
        PathRedirect::create([
            'old_path'    => 'go/removed-course',
            'new_path'    => null,
            'status_code' => RedirectStatus::GONE,
        ]);

        $this->get('/go/removed-course')
            ->assertStatus(410);
    }

    // -------------------------------------------------------------------------
    // Per-article click tracking (?c= parameter)
    // -------------------------------------------------------------------------

    public function test_valid_c_parameter_creates_pivot_row(): void
    {
        $link    = AffiliateLink::factory()->create(['slug' => 'per-article-link']);
        $content = Content::factory()->create();

        $this->get('/go/per-article-link?c=' . $content->id);

        $this->assertDatabaseHas('affiliate_link_clicks', [
            'affiliate_link_id' => $link->id,
            'content_id'        => $content->id,
            'clicks'            => 1,
        ]);
    }

    public function test_subsequent_click_increments_pivot_and_updates_last_clicked_at(): void
    {
        $link    = AffiliateLink::factory()->create(['slug' => 'repeat-link']);
        $content = Content::factory()->create();

        $this->get('/go/repeat-link?c=' . $content->id);

        $firstClickedAt = AffiliateLinkClick::query()
            ->where('affiliate_link_id', $link->id)
            ->where('content_id', $content->id)
            ->value('last_clicked_at');

        AffiliateLinkClick::query()
            ->where('affiliate_link_id', $link->id)
            ->where('content_id', $content->id)
            ->update(['last_clicked_at' => now()->subHour()]);

        $this->get('/go/repeat-link?c=' . $content->id);

        $pivot = AffiliateLinkClick::query()
            ->where('affiliate_link_id', $link->id)
            ->where('content_id', $content->id)
            ->first();

        $this->assertSame(2, (int) $pivot->clicks);
        $this->assertTrue($pivot->last_clicked_at->greaterThan(now()->subMinute()));
        $this->assertSame(
            1,
            AffiliateLinkClick::where('affiliate_link_id', $link->id)
                ->where('content_id', $content->id)
                ->count(),
            'should still be a single pivot row after the second click',
        );
    }

    public function test_pivot_tracks_total_separately_from_global_counter(): void
    {
        $link    = AffiliateLink::factory()->create(['slug' => 'dual-counter']);
        $content = Content::factory()->create();

        $this->get('/go/dual-counter?c=' . $content->id);
        $this->get('/go/dual-counter?c=' . $content->id);
        $this->get('/go/dual-counter');

        $this->assertDatabaseHas('affiliate_links', [
            'id'     => $link->id,
            'clicks' => 3,
        ]);
        $this->assertDatabaseHas('affiliate_link_clicks', [
            'affiliate_link_id' => $link->id,
            'content_id'        => $content->id,
            'clicks'            => 2,
        ]);
    }

    public function test_missing_c_parameter_does_not_create_pivot_row(): void
    {
        $link = AffiliateLink::factory()->create(['slug' => 'no-c']);

        $this->get('/go/no-c');

        $this->assertDatabaseMissing('affiliate_link_clicks', [
            'affiliate_link_id' => $link->id,
        ]);
    }

    public function test_non_numeric_c_parameter_is_ignored(): void
    {
        $link = AffiliateLink::factory()->create(['slug' => 'bad-c']);

        $this->get('/go/bad-c?c=not-a-number');

        $this->assertDatabaseMissing('affiliate_link_clicks', [
            'affiliate_link_id' => $link->id,
        ]);
    }

    public function test_c_pointing_to_nonexistent_content_is_ignored(): void
    {
        $link = AffiliateLink::factory()->create(['slug' => 'ghost-c']);

        $this->get('/go/ghost-c?c=99999');

        $this->assertDatabaseMissing('affiliate_link_clicks', [
            'affiliate_link_id' => $link->id,
        ]);
    }

    public function test_zero_or_negative_c_is_ignored(): void
    {
        $link = AffiliateLink::factory()->create(['slug' => 'zero-c']);

        $this->get('/go/zero-c?c=0');
        $this->get('/go/zero-c?c=-1');

        $this->assertDatabaseMissing('affiliate_link_clicks', [
            'affiliate_link_id' => $link->id,
        ]);
    }

    public function test_inactive_link_does_not_record_pivot_click(): void
    {
        $link    = AffiliateLink::factory()->inactive()->create(['slug' => 'inactive-c']);
        $content = Content::factory()->create();

        $this->get('/go/inactive-c?c=' . $content->id);

        $this->assertDatabaseMissing('affiliate_link_clicks', [
            'affiliate_link_id' => $link->id,
        ]);
    }
}
