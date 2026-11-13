<?php

namespace Tests\Feature\Pages;

use App\Filament\Pages\Settings;
use Tests\FilamentTestCase;

class SettingsPageAccessTest extends FilamentTestCase
{
    public function test_guest_cannot_access_settings_page(): void
    {
        $this->get(Settings::getUrl())
            ->assertRedirect('/admin/login');
    }

    public function test_user_without_roles_cannot_access_settings_page(): void
    {
        $this->actingAsUserWithoutRoles();

        $this->get(Settings::getUrl())
            ->assertForbidden();
    }

    public function test_user_role_can_access_settings_page(): void
    {
        $this->actingAsUser();

        $this->get(Settings::getUrl())
            ->assertOk();
    }

    public function test_super_admin_can_access_settings_page(): void
    {
        $this->actingAsSuperAdmin();

        $this->get(Settings::getUrl())
            ->assertOk();
    }
}
