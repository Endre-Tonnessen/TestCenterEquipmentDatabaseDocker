<?php

use App\Models\ReasonForChange;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEquipmentNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equipment_notes', function (Blueprint $table) {
            $table->id();
            $table->string('equipmentID');
            $table->text('notes')->nullable()->default(NULL);
            $table->dateTime('Date_Archived')->nullable()->default(NULL); //TODO: Might not need this.

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
        Schema::dropIfExists('equipment_notes');
    }
}
