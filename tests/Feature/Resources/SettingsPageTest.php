<?php

namespace Tests\Feature\Pages;

use App\Enums\MetaDataRobots;
use App\Filament\Pages\Settings;
use App\Models\Menu;
use App\Models\Setting;
use Livewire\Livewire;
use Tests\FilamentTestCase;

class SettingsPageTest extends FilamentTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAsSuperAdmin();
    }

    // -------------------------------------------------------------------------
    // Page Load
    // -------------------------------------------------------------------------

    public function test_can_load_settings_page(): void
    {
        Livewire::test(Settings::class)
            ->assertOk();
    }

    public function test_settings_page_loads_existing_values(): void
    {
        Setting::factory()->siteName()->create(['value' => 'My Site']);

        Livewire::test(Settings::class)
            ->assertSchemaStateSet([
                'site_name' => 'My Site',
            ]);
    }

    // -------------------------------------------------------------------------
    // General Tab
    // -------------------------------------------------------------------------

    public function test_can_save_site_name(): void
    {
        Livewire::test(Settings::class)
            ->fillForm(['site_name' => 'New Site Name'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('settings', [
            'group' => 'site',
            'key' => 'name',
            'value' => 'New Site Name',
        ]);
    }

    public function test_site_name_is_required(): void
    {
        Livewire::test(Settings::class)
            ->fillForm(['site_name' => null])
            ->call('save')
            ->assertHasFormErrors(['site_name' => 'required']);
    }

    // -------------------------------------------------------------------------
    // Menu Tab
    // -------------------------------------------------------------------------

    public function test_can_save_menu_header(): void
    {
        $menu = Menu::factory()->create();

        Livewire::test(Settings::class)
            ->fillForm([
                'site_name' => 'Test',
                'menu_header' => $menu->id,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('settings', [
            'group' => 'menu',
            'key' => 'header',
            'value' => $menu->id,
        ]);
    }

    public function test_can_save_menu_footer(): void
    {
        $menu = Menu::factory()->create();

        Livewire::test(Settings::class)
            ->fillForm([
                'site_name' => 'Test',
                'menu_footer' => $menu->id,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('settings', [
            'group' => 'menu',
            'key' => 'footer',
            'value' => $menu->id,
        ]);
    }

    public function test_can_save_menu_max_depth(): void
    {
        Livewire::test(Settings::class)
            ->fillForm([
                'site_name' => 'Test',
                'menu_max_depth' => 2,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('settings', [
            'group' => 'menu',
            'key' => 'max_depth',
            'value' => 2,
        ]);
    }

    public function test_can_save_footer_slogan(): void
    {
        Livewire::test(Settings::class)
            ->fillForm([
                'site_name' => 'Test',
                'footer_slogan' => 'Making the web better.',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('settings', [
            'group' => 'footer',
            'key' => 'slogan',
            'value' => 'Making the web better.',
        ]);
    }

    public function test_footer_slogan_loads_existing_value(): void
    {
        Setting::factory()->footerSlogan('Open source rocks.')->create();

        Livewire::test(Settings::class)
            ->assertSchemaStateSet([
                'footer_slogan' => 'Open source rocks.',
            ]);
    }

    // -------------------------------------------------------------------------
    // SEO Tab
    // -------------------------------------------------------------------------

    public function test_can_save_meta_data(): void
    {
        Livewire::test(Settings::class)
            ->fillForm([
                'site_name' => 'Test',
                'metaData.meta_title' => 'My Meta Title',
                'metaData.robots' => MetaDataRobots::INDEX_FOLLOW->value,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $setting = Setting::firstOrCreate(['group' => 'seo', 'key' => 'meta']);

        $this->assertDatabaseHas('meta_data', [
            'model_type' => Setting::class,
            'model_id' => $setting->id,
            'meta_title' => 'My Meta Title',
        ]);
    }

    // -------------------------------------------------------------------------
    // Theme Tab
    // -------------------------------------------------------------------------

    public function test_can_save_light_primary_color(): void
    {
        Livewire::test(Settings::class)
            ->fillForm(['site_name' => 'Test', 'theme_light_primary' => '#7C3AED'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('settings', [
            'group' => 'theme',
            'key' => 'light_primary',
            'value' => '#7C3AED',
        ]);
    }

    public function test_can_save_dark_primary_color(): void
    {
        Livewire::test(Settings::class)
            ->fillForm(['site_name' => 'Test', 'theme_dark_primary' => '#A78BFA'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('settings', [
            'group' => 'theme',
            'key' => 'dark_primary',
            'value' => '#A78BFA',
        ]);
    }

    public function test_theme_settings_load_existing_values(): void
    {
        Setting::factory()->themeLightPrimary('#FF0000')->create();
        Setting::factory()->themeDarkPrimary('#00FF00')->create();

        Livewire::test(Settings::class)
            ->assertSchemaStateSet([
                'theme_light_primary' => '#FF0000',
                'theme_dark_primary' => '#00FF00',
            ]);
    }

    // -------------------------------------------------------------------------
    // Content Tab
    // -------------------------------------------------------------------------

    public function test_can_save_category_page_size(): void
    {
        Livewire::test(Settings::class)
            ->fillForm(['site_name' => 'Test', 'category_per_page' => 6])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('settings', [
            'group' => 'content',
            'key' => 'category_page_size',
            'value' => 6,
        ]);
    }

    public function test_category_page_size_is_required(): void
    {
        Livewire::test(Settings::class)
            ->fillForm(['site_name' => 'Test', 'category_per_page' => null])
            ->call('save')
            ->assertHasFormErrors(['category_per_page' => 'required']);
    }

    public function test_category_page_size_loads_existing_value(): void
    {
        Setting::factory()->categoryPageSize(9)->create();

        Livewire::test(Settings::class)
            ->assertSchemaStateSet([
                'category_per_page' => 9,
            ]);
    }

    // -------------------------------------------------------------------------
    // RSS Tab
    // -------------------------------------------------------------------------

    public function test_can_save_rss_title(): void
    {
        Livewire::test(Settings::class)
            ->fillForm(['site_name' => 'Test', 'rss_title' => 'My RSS Feed'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('settings', [
            'group' => 'rss',
            'key' => 'title',
            'value' => 'My RSS Feed',
        ]);
    }

    public function test_can_save_rss_description(): void
    {
        Livewire::test(Settings::class)
            ->fillForm(['site_name' => 'Test', 'rss_description' => 'A blog about web development.'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('settings', [
            'group' => 'rss',
            'key' => 'description',
            'value' => 'A blog about web development.',
        ]);
    }

    public function test_rss_title_max_length_is_enforced(): void
    {
        Livewire::test(Settings::class)
            ->fillForm(['site_name' => 'Test', 'rss_title' => str_repeat('a', 256)])
            ->call('save')
            ->assertHasFormErrors(['rss_title' => 'max']);
    }

    public function test_rss_settings_load_existing_values(): void
    {
        Setting::factory()->rssTitle('My Feed')->create();
        Setting::factory()->rssDescription('My description.')->create();

        Livewire::test(Settings::class)
            ->assertSchemaStateSet([
                'rss_title' => 'My Feed',
                'rss_description' => 'My description.',
            ]);
    }

    // -------------------------------------------------------------------------
    // Cache
    // -------------------------------------------------------------------------

    public function test_cache_is_cleared_after_save(): void
    {
        Setting::factory()->siteName()->create(['value' => 'Old Name']);

        Setting::get('site.name');

        Livewire::test(Settings::class)
            ->fillForm(['site_name' => 'New Name'])
            ->call('save');

        $this->assertEquals('New Name', Setting::get('site.name'));
    }

    // -------------------------------------------------------------------------
    // Notification
    // -------------------------------------------------------------------------

    public function test_success_notification_is_sent_after_save(): void
    {
        Livewire::test(Settings::class)
            ->fillForm(['site_name' => 'Test'])
            ->call('save')
            ->assertNotified();
    }
}
