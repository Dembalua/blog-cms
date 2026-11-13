<?php

namespace Tests\Feature\Observers;

use App\Enums\RedirectStatus;
use App\Models\Category;
use App\Models\PathRedirect;
use Tests\FilamentTestCase;

class PathObserverTest extends FilamentTestCase
{
    public function test_path_change_creates_301_redirect(): void
    {
        $category = Category::factory()->create();
        $oldUrl = $category->path->url;

        $category->path->update(['url' => 'new-url']);

        $this->assertDatabaseHas('path_redirects', [
            'old_path' => $oldUrl,
            'new_path' => 'new-url',
            'status_code' => RedirectStatus::MOVED_PERMANENTLY->value,
        ]);
    }

    public function test_redirect_chain_is_resolved(): void
    {
        $category = Category::factory()->create();
        $firstUrl = $category->path->url;

        $category->path->update(['url' => 'second-url']);
        $category->path->update(['url' => 'third-url']);

        $this->assertDatabaseHas('path_redirects', [
            'old_path' => $firstUrl,
            'new_path' => 'third-url',
            'status_code' => RedirectStatus::MOVED_PERMANENTLY->value,
        ]);
    }

    public function test_new_path_cleans_up_existing_redirect(): void
    {
        PathRedirect::create([
            'old_path' => 'old-url',
            'new_path' => 'some-url',
            'status_code' => RedirectStatus::MOVED_PERMANENTLY,
        ]);

        $category = Category::factory()->create();
        $category->path->update(['url' => 'old-url']);

        $this->assertDatabaseMissing('path_redirects', [
            'old_path' => 'old-url',
        ]);
    }

    public function test_created_path_cleans_up_existing_redirect(): void
    {
        PathRedirect::create([
            'old_path' => 'existing-url',
            'new_path' => null,
            'status_code' => RedirectStatus::GONE,
        ]);

        $category = Category::factory()->create();
        $category->path->update(['url' => 'existing-url']);

        $this->assertDatabaseMissing('path_redirects', [
            'old_path' => 'existing-url',
        ]);
    }

    public function test_first_url_assignment_removes_conflicting_redirect(): void
    {
        PathRedirect::create([
            'old_path' => 'webdevelopment/devops',
            'new_path' => 'devops',
            'status_code' => RedirectStatus::MOVED_PERMANENTLY,
        ]);

        $category = Category::factory()->create();
        $category->path->update(['url' => 'webdevelopment/devops']);

        $this->assertDatabaseMissing('path_redirects', [
            'old_path' => 'webdevelopment/devops',
        ]);
    }

    public function test_url_set_to_null_creates_gone_redirect(): void
    {
        $category = Category::factory()->create();
        $oldUrl = $category->path->url;

        $category->path->update(['url' => null]);

        $this->assertDatabaseHas('path_redirects', [
            'old_path' => $oldUrl,
            'new_path' => null,
            'status_code' => RedirectStatus::GONE->value,
        ]);
    }
}
