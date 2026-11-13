<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'lastname' => 'Starter',
                'password' => Hash::make('Password123!'),
                'email_verified_at' => now(),
            ]
        );

        $admin->assignRole('super_admin');
    }
}
