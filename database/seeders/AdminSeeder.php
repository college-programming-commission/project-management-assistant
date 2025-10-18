<?php

namespace Database\Seeders;

use Alison\ProjectManagementAssistant\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Створення головного адміністратора системи.
     * 
     * ВАЖЛИВО: Цей seeder повинен запускатися завжди при першому розгортанні!
     * Пароль береться з .env змінної ADMIN_PASSWORD
     */
    public function run(): void
    {
        $adminEmail = env('ADMIN_EMAIL', 'it_commission_college@uzhnu.edu.ua');
        $adminPassword = env('ADMIN_PASSWORD');

        // Перевірка чи вказано пароль
        if (empty($adminPassword)) {
            $this->command->error('❌ ПОМИЛКА: Не вказано ADMIN_PASSWORD в .env файлі!');
            $this->command->error('Додайте в .env: ADMIN_PASSWORD=ваш_надійний_пароль');
            throw new \Exception('Admin password is not set in .env file');
        }

        // Перевірка чи існує адміністратор
        $existingAdmin = User::query()->where('email', $adminEmail)->first();

        if ($existingAdmin) {
            $this->command->info("ℹ️  Адміністратор з email {$adminEmail} вже існує.");
            
            // Перевірка чи має роль admin
            if (!$existingAdmin->hasRole('admin')) {
                $existingAdmin->assignRole('admin');
                $this->command->info("✅ Роль 'admin' призначена користувачу {$adminEmail}");
            }
            
            return;
        }

        // Створення нового адміністратора
        $admin = User::query()->create([
            'email' => $adminEmail,
            'email_verified_at' => now(),
            'password' => Hash::make($adminPassword),
            'first_name' => env('ADMIN_FIRST_NAME', 'IT'),
            'last_name' => env('ADMIN_LAST_NAME', 'Комісія'),
            'middle_name' => env('ADMIN_MIDDLE_NAME', null),
            'description' => 'Головний адміністратор системи управління проектами',
            'avatar' => null,
            'course_number' => null,
        ]);

        // Перевірка чи існує роль admin
        if (!\Spatie\Permission\Models\Role::where('name', 'admin')->exists()) {
            $this->command->error('❌ Роль "admin" не знайдена! Запустіть спочатку RolesAndPermissionsSeeder');
            throw new \Exception('Role "admin" not found');
        }

        // Призначення ролі адміністратора
        $admin->assignRole('admin');

        $this->command->info("✅ Адміністратор створений успішно!");
        $this->command->info("📧 Email: {$adminEmail}");
        $this->command->warn("⚠️  ВАЖЛИВО: Змініть пароль після першого входу!");
    }
}
