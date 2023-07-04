<?php

use App\Models\ReasonForChange;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeasuringRangeAndAccuraciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('measuring_range_and_accuracies', function (Blueprint $table) {
            $table->id();

            $table->string('equipmentID');
            $table->float('Range_Lower')->nullable();
            $table->float('Range_Upper')->nullable();
            $table->string('SI_Unit');
            $table->string('Accuracy')->nullable();
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
        Schema::dropIfExists('measuring_range_and_accuracies');
    }
}
