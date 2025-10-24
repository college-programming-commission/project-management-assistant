<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            SubjectSeeder::class,
            TechnologySeeder::class,
            CategorySeeder::class,
            AdminSeeder::class,
        ]);
    }
}
