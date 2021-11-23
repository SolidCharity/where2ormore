<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TextsForReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tenants', function ($table) {
            $table->string('text_for_report_welcome_person')->default('Eingangskontrolle durch');
            $table->string('text_for_report_destroy_list')->default('Diese Liste wird nach 4 Wochen vernichtet.');
            $table->string('text_for_report_church_details')->default('Beispiel Gemeinde XY, Wiesenweg 3, 12345 Berlin');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
