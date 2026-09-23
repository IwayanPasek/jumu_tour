<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminName = env('ADMIN_NAME', 'Administrator MVP');
        $adminEmail = env('ADMIN_EMAIL', 'admin@balitourservice.local');
        $adminPassword = env('ADMIN_PASSWORD', 'AdminJumu2026!');

        User::updateOrCreate(
            ['email' => $adminEmail],
            [
                'name' => $adminName,
                'password' => Hash::make($adminPassword),
                'is_admin' => true,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
