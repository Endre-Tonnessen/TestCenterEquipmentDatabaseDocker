<?php

use App\Models\ReasonForChange;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCalibrationFrequenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calibration_frequencies', function (Blueprint $table) {
            $table->id();

            $table->string('equipmentID');
            $table->integer('Cal_Interval_Year')->nullable();
            $table->integer('Cal_Interval_Month')->nullable();
            $table->date('Last_Calibration_Date')->nullable();
            $table->string('Calibration_Provider')->nullable();
            $table->string('Calibration_location')->nullable();
            $table->string('Document_Reference')->nullable();
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
        Schema::dropIfExists('calibration_frequencies');
    }
}
