<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeLocationPointNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('absences', function (Blueprint $table) {
            // Mengubah kolom location_point_id menjadi nullable
            $table->foreignId('location_point_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('absences', function (Blueprint $table) {
            // Kembalikan kolom menjadi tidak nullable
            $table->foreignId('location_point_id')->nullable(false)->change();
        });
    }
}
