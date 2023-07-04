<?php

use App\Models\ReasonForChange;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEquipmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();

            $table->string('equipmentID');
            $table->string('location')->nullable()->default("Test center, LMAS Stavanger");
            $table->text('Description')->nullable();
            $table->integer('Category_id');
            $table->string('img_path')->nullable()->default(NULL);
            $table->tinyInteger('Deleted')->default(0);

            $table->string('Placement')->nullable();
            $table->string('Department')->nullable()->default("T & V Shared Services");
            $table->string('Serial_Number')->nullable();
            $table->string('Model_Number')->nullable();
            $table->string('Usage')->nullable();
            $table->string('Manufacturer')->nullable();
            $table->dateTime('Date_Archived')->nullable()->default(NULL);

            $table->foreignIdFor(ReasonForChange::class,'ReasonForChangeID');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('equipment');
    }
}
