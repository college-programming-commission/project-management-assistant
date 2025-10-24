<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view users', 'create users', 'edit users', 'delete users',
            'view projects', 'create projects', 'edit projects', 'delete projects', 'assign projects',
            'view categories', 'create categories', 'edit categories', 'delete categories',
            'view subjects', 'create subjects', 'edit subjects', 'delete subjects',
            'view technologies', 'create technologies', 'edit technologies', 'delete technologies',
            'view events', 'create events', 'edit events', 'delete events',
            'view offers', 'create offers', 'edit offers', 'delete offers',
            'view supervisors', 'create supervisors', 'edit supervisors', 'delete supervisors',
            'access admin panel',
        ];

        foreach ($permissions as $permission) {
            Permission::query()->firstOrCreate(['name' => $permission]);
        }

        $admin = Role::query()->firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        $student = Role::query()->firstOrCreate(['name' => 'student']);
        $student->syncPermissions([
            'view projects', 'view categories', 'view subjects', 'view technologies',
            'view events', 'view offers', 'create offers', 'edit offers', 'delete offers',
            'view supervisors',
        ]);

        $teacher = Role::query()->firstOrCreate(['name' => 'teacher']);
        $teacher->syncPermissions([
            'view projects', 'create projects', 'edit projects', 'delete projects',
            'view categories', 'create categories', 'edit categories', 'delete categories',
            'view subjects', 'create subjects', 'edit subjects', 'delete subjects',
            'view technologies', 'create technologies', 'edit technologies', 'delete technologies',
            'view events', 'create events', 'edit events', 'delete events',
            'view offers', 'view supervisors', 'create supervisors', 'edit supervisors', 'delete supervisors',
        ]);
    }
}
