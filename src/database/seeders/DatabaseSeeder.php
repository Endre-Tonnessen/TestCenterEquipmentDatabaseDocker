<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //Run all Seeders.
        $this->call([
            CategorySeeder::class,
            DummyEquipmentSeeder::class,
            AdminUserSeeder::class
        ]);
    }
}
