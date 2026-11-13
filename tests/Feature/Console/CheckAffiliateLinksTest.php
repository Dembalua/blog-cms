<?php

namespace Tests\Feature\Console;

use App\Mail\BrokenAffiliateLinks;
use App\Models\AffiliateLink;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\FilamentTestCase;

class CheckAffiliateLinksTest extends FilamentTestCase
{
    public function test_stores_status_code_and_checked_at_for_active_links(): void
    {
        $link = AffiliateLink::factory()->create(['url' => 'https://example.com/tool']);

        Http::fake(['https://example.com/tool' => Http::response('', 200)]);

        $this->artisan('affiliate-links:check');

        $this->assertDatabaseHas('affiliate_links', [
            'id'               => $link->id,
            'last_status_code' => 200,
        ]);

        $this->assertNotNull($link->fresh()->last_checked_at);
    }

    public function test_skips_inactive_links(): void
    {
        $link = AffiliateLink::factory()->inactive()->create(['url' => 'https://example.com/tool']);

        Http::fake();

        $this->artisan('affiliate-links:check');

        $this->assertNull($link->fresh()->last_status_code);
        $this->assertNull($link->fresh()->last_checked_at);

        Http::assertNothingSent();
    }

    public function test_sends_email_when_broken_links_found(): void
    {
        Mail::fake();

        AffiliateLink::factory()->create(['url' => 'https://example.com/broken']);

        Http::fake(['https://example.com/broken' => Http::response('', 404)]);

        $this->artisan('affiliate-links:check');

        Mail::assertSent(BrokenAffiliateLinks::class);
    }

    public function test_does_not_send_email_when_all_links_are_ok(): void
    {
        Mail::fake();

        AffiliateLink::factory()->create(['url' => 'https://example.com/ok']);

        Http::fake(['https://example.com/ok' => Http::response('', 200)]);

        $this->artisan('affiliate-links:check');

        Mail::assertNotSent(BrokenAffiliateLinks::class);
    }

    public function test_falls_back_to_get_when_head_returns_405(): void
    {
        $link = AffiliateLink::factory()->create(['url' => 'https://example.com/no-head']);

        Http::fake([
            'https://example.com/no-head' => Http::sequence()
                ->push('', 405)
                ->push('', 200),
        ]);

        $this->artisan('affiliate-links:check');

        $this->assertDatabaseHas('affiliate_links', [
            'id'               => $link->id,
            'last_status_code' => 200,
        ]);
    }

    public function test_stores_zero_on_connection_error(): void
    {
        $link = AffiliateLink::factory()->create(['url' => 'https://example.com/unreachable']);

        Http::fake(['https://example.com/unreachable' => fn() => throw new \Exception('Connection failed')]);

        $this->artisan('affiliate-links:check');

        $this->assertDatabaseHas('affiliate_links', [
            'id'               => $link->id,
            'last_status_code' => 0,
        ]);
    }
}
