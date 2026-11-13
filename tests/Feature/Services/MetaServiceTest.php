<?php

namespace Tests\Feature\Services;

use App\Enums\ContentType;
use App\Models\Category;
use App\Models\Content;
use App\Models\MetaData;
use App\Models\Setting;
use App\Services\MetaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\FilamentTestCase;

class MetaServiceTest extends FilamentTestCase
{
    private MetaService $metaService;

    protected function setUp(): void
    {
        parent::setUp();

        $property = new \ReflectionProperty(MetaService::class, 'globalMeta');
        $property->setValue(null, null);

        $this->metaService = new MetaService();
    }

    public function test_model_meta_title_overrides_global(): void
    {
        $setting = Setting::factory()->seoMeta()->create();
        $setting->metaData()->create(
            MetaData::factory()->make(['meta_title' => 'Global Title'])->toArray()
        );

        $content = Content::factory()->create();
        $content->metaData()->create(
            MetaData::factory()->make(['meta_title' => 'Post Title'])->toArray()
        );

        $result = $this->metaService->resolve($content);

        $this->assertSame('Post Title', $result['meta_title']);
    }

    public function test_falls_back_to_global_meta_title_when_model_has_none(): void
    {
        $setting = Setting::factory()->seoMeta()->create();
        $setting->metaData()->create(
            MetaData::factory()->make(['meta_title' => 'Global Title'])->toArray()
        );

        $content = Content::factory()->create();
        $content->metaData()->create(
            MetaData::factory()->make(['meta_title' => null])->toArray()
        );

        $result = $this->metaService->resolve($content);

        $this->assertSame('Global Title', $result['meta_title']);
    }

    public function test_og_type_is_article_for_posts(): void
    {
        $content = Content::factory()->create(['type' => ContentType::POST]);

        $result = $this->metaService->resolve($content);

        $this->assertSame('article', $result['og_type']);
    }

    public function test_og_type_is_website_for_pages(): void
    {
        $content = Content::factory()->create(['type' => ContentType::PAGE]);

        $result = $this->metaService->resolve($content);

        $this->assertSame('website', $result['og_type']);
    }

    public function test_og_image_falls_back_to_feature_image_for_content(): void
    {
        $media = $this->createMedia();
        $content = Content::factory()->create(['feature_image_id' => $media->id]);

        $result = $this->metaService->resolve($content);

        $this->assertSame($media->og_url, $result['og_image']);
    }

    public function test_og_image_falls_back_to_global_when_content_has_no_feature_image(): void
    {
        $globalImage = $this->createMedia();
        $setting = Setting::factory()->seoMeta()->create();
        $setting->metaData()->create(
            MetaData::factory()->make(['og_image_id' => $globalImage->id])->toArray()
        );

        $content = Content::factory()->create(['feature_image_id' => null]);

        $result = $this->metaService->resolve($content);

        $this->assertSame($globalImage->og_url, $result['og_image']);
    }

    public function test_og_image_is_null_when_content_and_global_have_no_image(): void
    {
        $content = Content::factory()->create(['feature_image_id' => null]);

        $result = $this->metaService->resolve($content);

        $this->assertNull($result['og_image']);
    }

    public function test_og_image_falls_back_to_category_image(): void
    {
        $media = $this->createMedia();
        $category = Category::factory()->create(['image_id' => $media->id]);

        $result = $this->metaService->resolve($category);

        $this->assertSame($media->og_url, $result['og_image']);
    }

    public function test_canonical_url_excludes_query_parameters(): void
    {
        $content = Content::factory()->create();

        $this->get('/web-development?page=2');

        $result = $this->metaService->resolve($content);

        $this->assertStringNotContainsString('page=', $result['canonical_url']);
        $this->assertStringEndsWith('/web-development', $result['canonical_url']);
    }
}
