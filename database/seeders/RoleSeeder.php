<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Artisan::call('shield:generate', [
            '--all' => true,
            '--panel' => 'admin',
            '--option' => 'policies_and_permissions',
            '--no-interaction' => true,
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $superAdmin = Role::query()->firstOrCreate(['name' => 'super_admin']);
        $superAdmin->syncPermissions(Permission::all());

        $user = Role::query()->firstOrCreate(['name' => 'user']);
        $userPermissions = Permission::all()
            ->filter(fn($p) =>
                !str_ends_with($p->name, ':Role') &&
                !str_ends_with($p->name, ':User')
            );
        $user->syncPermissions($userPermissions);
    }
}
