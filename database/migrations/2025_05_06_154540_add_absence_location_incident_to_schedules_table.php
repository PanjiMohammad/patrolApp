<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAbsenceLocationIncidentToSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->foreignId('absence_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('location_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('incident_id')->nullable()->constrained('incident_reports')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropForeign(['absence_id']);
            $table->dropForeign(['location_id']);
            $table->dropForeign(['incident_id']);

            $table->dropColumn(['absence_id', 'location_id', 'incident_id']);
        });
    }
}
