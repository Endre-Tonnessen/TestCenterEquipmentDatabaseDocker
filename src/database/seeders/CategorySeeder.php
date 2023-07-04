<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Class CategorySeeder
 * Populates the Categories Table in the database with default values.
 * @package Database\Seeders
 */
class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Default categories.
        $categoryData = [
            ['category_name'=>'Dimension'],
            ['category_name'=>'Pressure, Volume & Flow'],
            ['category_name'=>'Temperature & Humidity'],
            ['category_name'=>'Electrical'],
            ['category_name'=>'Misc Test Instruments'],
            ['category_name'=>'Other Products']
        ];

        DB::table('categories')->insert($categoryData);
    }
}
