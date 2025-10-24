<?php

namespace Database\Seeders;

use Alison\ProjectManagementAssistant\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminEmail = env('ADMIN_EMAIL', 'it_commission_college@uzhnu.edu.ua');
        $adminPassword = env('ADMIN_PASSWORD');

        if (empty($adminPassword)) {
            $this->command->error('❌ ADMIN_PASSWORD not set in .env file');
            throw new \Exception('Admin password is not set in .env file');
        }

        $admin = User::query()->firstOrCreate(
            ['email' => $adminEmail],
            [
                'email_verified_at' => now(),
                'password' => Hash::make($adminPassword),
                'first_name' => env('ADMIN_FIRST_NAME', 'IT'),
                'last_name' => env('ADMIN_LAST_NAME', 'Комісія'),
                'middle_name' => env('ADMIN_MIDDLE_NAME'),
                'description' => 'Головний адміністратор системи управління проектами',
                'avatar' => null,
                'course_number' => null,
            ]
        );

        if (! $admin->hasRole('admin')) {
            $admin->assignRole('admin');
            $this->command->info("✅ Admin role assigned to {$adminEmail}");
        }
    }
}
