<?php

namespace Tests\Feature\Observers;

use App\Enums\RedirectStatus;
use App\Models\AffiliateLink;
use App\Models\PathRedirect;
use Tests\FilamentTestCase;

class AffiliateLinkObserverTest extends FilamentTestCase
{
    public function test_slug_change_creates_301_redirect(): void
    {
        $link = AffiliateLink::factory()->create(['slug' => 'laravel-course']);

        $link->update(['slug' => 'laravel-course-pro']);

        $this->assertDatabaseHas('path_redirects', [
            'old_path' => 'go/laravel-course',
            'new_path' => 'go/laravel-course-pro',
            'status_code' => RedirectStatus::MOVED_PERMANENTLY->value,
        ]);
    }

    public function test_redirect_chain_is_resolved(): void
    {
        $link = AffiliateLink::factory()->create(['slug' => 'vue-guide']);

        $link->update(['slug' => 'vue-guide-v2']);
        $link->update(['slug' => 'vue-guide-v3']);

        $this->assertDatabaseHas('path_redirects', [
            'old_path' => 'go/vue-guide',
            'new_path' => 'go/vue-guide-v3',
            'status_code' => RedirectStatus::MOVED_PERMANENTLY->value,
        ]);
    }

    public function test_new_slug_removes_conflicting_redirect(): void
    {
        PathRedirect::create([
            'old_path' => 'go/react-hooks',
            'new_path' => 'go/something-else',
            'status_code' => RedirectStatus::MOVED_PERMANENTLY,
        ]);

        $link = AffiliateLink::factory()->create(['slug' => 'typescript-handbook']);
        $link->update(['slug' => 'react-hooks']);

        $this->assertDatabaseMissing('path_redirects', [
            'old_path' => 'go/react-hooks',
        ]);
    }

    public function test_deleted_link_creates_410_redirect(): void
    {
        $link = AffiliateLink::factory()->create(['slug' => 'node-js-book']);

        $link->delete();

        $this->assertDatabaseHas('path_redirects', [
            'old_path' => 'go/node-js-book',
            'new_path' => null,
            'status_code' => RedirectStatus::GONE->value,
        ]);
    }

    public function test_created_link_removes_existing_redirect(): void
    {
        PathRedirect::create([
            'old_path' => 'go/docker-mastery',
            'new_path' => null,
            'status_code' => RedirectStatus::GONE,
        ]);

        AffiliateLink::factory()->create(['slug' => 'docker-mastery']);

        $this->assertDatabaseMissing('path_redirects', [
            'old_path' => 'go/docker-mastery',
        ]);
    }
}
