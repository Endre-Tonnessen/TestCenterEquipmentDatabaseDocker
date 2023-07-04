<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DummyEquipmentSeeder extends Seeder
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
            ['equipmentID' => 'Test1', 'Placement'=>'Shelf 98','Category_id'=>1, 'created_at'=>Carbon::now()],
            ['equipmentID' => 'Test2', 'Placement'=>'Shelf 98','Category_id'=>1, 'created_at'=>Carbon::now()],
            ['equipmentID' => 'Test3', 'Placement'=>'Shelf 98','Category_id'=>1, 'created_at'=>Carbon::now()],
        ];

        //DB::table('equipment')->insert($categoryData);
    }
}
