<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OptionToReportContactDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('participants', function ($table) {
            $table->boolean('report_details')->default(true);
        });
        Schema::table('tenants', function ($table) {
            $table->boolean('option_to_report_contact_details')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('report_contact_details', function (Blueprint $table) {
            //
        });
    }
}
