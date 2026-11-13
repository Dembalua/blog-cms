<?php

namespace Tests\Feature\Middleware;

use App\Models\Content;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Tests\FilamentTestCase;

class MaintenanceModeTest extends FilamentTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_site_is_accessible_when_maintenance_mode_disabled(): void
    {
        Setting::factory()->maintenanceEnabled(false)->create();

        $content = Content::factory()->page()->published()->create();

        $this->get('/' . $content->path->url)->assertOk();
    }

    public function test_site_returns_503_when_maintenance_mode_enabled(): void
    {
        Setting::factory()->maintenanceEnabled()->create();

        $content = Content::factory()->page()->published()->create();

        $this->get('/' . $content->path->url)->assertStatus(503);
    }

    public function test_maintenance_page_is_shown_when_maintenance_mode_enabled(): void
    {
        Setting::factory()->maintenanceEnabled()->create();

        $content = Content::factory()->page()->published()->create();

        $this->get('/' . $content->path->url)->assertViewIs('maintenance');
    }

    public function test_allowed_ip_can_access_site_during_maintenance(): void
    {
        Setting::factory()->maintenanceEnabled()->create();
        Setting::factory()->maintenanceAllowedIps('123.123.123.123')->create();

        $content = Content::factory()->page()->published()->create();

        $this->withServerVariables(['REMOTE_ADDR' => '123.123.123.123'])
            ->get('/' . $content->path->url)
            ->assertOk();
    }

    public function test_non_allowed_ip_cannot_access_site_during_maintenance(): void
    {
        Setting::factory()->maintenanceEnabled()->create();
        Setting::factory()->maintenanceAllowedIps('123.123.123.123')->create();

        $content = Content::factory()->page()->published()->create();

        $this->withServerVariables(['REMOTE_ADDR' => '999.999.999.999'])
            ->get('/' . $content->path->url)
            ->assertStatus(503);
    }

    public function test_allowed_page_is_accessible_during_maintenance(): void
    {
        $content = Content::factory()->page()->published()->create();
        Setting::factory()->maintenanceEnabled()->create();
        Setting::factory()->maintenancePages([$content->id])->create();

        $this->get('/' . $content->path->url)->assertOk();
    }

    public function test_allowed_page_has_maintenance_mode_active_flag(): void
    {
        $content = Content::factory()->page()->published()->create();
        Setting::factory()->maintenanceEnabled()->create();
        Setting::factory()->maintenancePages([$content->id])->create();

        $this->get('/' . $content->path->url)
            ->assertOk()
            ->assertViewHas('maintenanceModeActive', true);
    }

    public function test_non_allowed_page_is_blocked_during_maintenance(): void
    {
        $allowed = Content::factory()->page()->published()->create();
        $blocked = Content::factory()->page()->published()->create();
        Setting::factory()->maintenanceEnabled()->create();
        Setting::factory()->maintenancePages([$allowed->id])->create();

        $this->get('/' . $blocked->path->url)->assertStatus(503);
    }

    public function test_admin_is_accessible_during_maintenance(): void
    {
        Setting::factory()->maintenanceEnabled()->create();

        $this->get('/admin/login')->assertOk();
    }
}
