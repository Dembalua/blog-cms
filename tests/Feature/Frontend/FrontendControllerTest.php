<?php

namespace Tests\Feature\Frontend;

use App\Enums\RedirectStatus;
use App\Models\Category;
use App\Models\Content;
use App\Models\PathRedirect;
use App\Models\Setting;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\FilamentTestCase;

class FrontendControllerTest extends FilamentTestCase
{
    public function test_can_access_published_post(): void
    {
        $content = Content::factory()->post()->published()->create();

        $this->get('/'.$content->path->url)
            ->assertOk()
            ->assertViewIs('pages.post');
    }

    public function test_can_access_published_page(): void
    {
        $content = Content::factory()->page()->published()->create();

        $this->get('/'.$content->path->url)
            ->assertOk()
            ->assertViewIs('pages.page');
    }

    public function test_can_access_category(): void
    {
        $category = Category::factory()->active()->create();

        $this->get('/'.$category->path->url)
            ->assertOk()
            ->assertViewIs('pages.category');
    }

    public function test_unpublished_content_returns_404(): void
    {
        $content = Content::factory()->draft()->create();

        $this->get('/'.$content->path->url)
            ->assertNotFound();
    }

    public function test_inactive_category_returns_404(): void
    {
        $category = Category::factory()->create(['active' => false]);

        $this->get('/'.$category->path->url)
            ->assertNotFound();
    }

    public function test_unknown_path_returns_404(): void
    {
        $this->get('/this-does-not-exist')
            ->assertNotFound()
            ->assertSee('404');
    }

    public function test_301_redirect_is_followed(): void
    {
        PathRedirect::create([
            'old_path' => 'old-url',
            'new_path' => 'new-url',
            'status_code' => RedirectStatus::MOVED_PERMANENTLY,
        ]);

        $this->get('/old-url')
            ->assertRedirect('/new-url')
            ->assertStatus(301);
    }

    public function test_410_returns_gone(): void
    {
        PathRedirect::create([
            'old_path' => 'deleted-url',
            'new_path' => null,
            'status_code' => RedirectStatus::GONE,
        ]);

        $this->get('/deleted-url')
            ->assertStatus(410)
            ->assertSee('410');
    }

    public function test_archived_content_returns_404(): void
    {
        $content = Content::factory()->archived()->create();

        $this->get('/'.$content->path->url)
            ->assertNotFound();
    }

    // -------------------------------------------------------------------------
    // Category pagination
    // -------------------------------------------------------------------------

    public function test_category_contents_are_paginated(): void
    {
        $category = Category::factory()->active()->create();
        Content::factory()->post()->published()->count(3)->create(['category_id' => $category->id]);

        $response = $this->get('/'.$category->path->url);

        $response->assertOk();
        $this->assertInstanceOf(LengthAwarePaginator::class, $response->original->getData()['contents']);
    }

    public function test_category_pagination_respects_page_size_setting(): void
    {
        Setting::factory()->categoryPageSize(2)->create();

        $category = Category::factory()->active()->create();
        Content::factory()->post()->published()->count(3)->create(['category_id' => $category->id]);

        $response = $this->get('/'.$category->path->url);

        $response->assertOk();
        $this->assertCount(2, $response->original->getData()['contents']);
    }

    public function test_category_page_2_returns_remaining_contents(): void
    {
        Setting::factory()->categoryPageSize(2)->create();

        $category = Category::factory()->active()->create();
        Content::factory()->post()->published()->count(3)->create(['category_id' => $category->id]);

        $response = $this->get('/'.$category->path->url.'?page=2');

        $response->assertOk();
        $this->assertCount(1, $response->original->getData()['contents']);
    }

    public function test_category_uses_fallback_page_size_when_setting_missing(): void
    {
        $category = Category::factory()->active()->create();
        Content::factory()->post()->published()->count(3)->create(['category_id' => $category->id]);

        $response = $this->get('/'.$category->path->url);

        $response->assertOk();
        $paginator = $response->original->getData()['contents'];
        $this->assertEquals(12, $paginator->perPage());
    }
}
