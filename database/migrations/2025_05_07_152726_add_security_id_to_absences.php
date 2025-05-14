<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSecurityIdToAbsences extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('absences', function (Blueprint $table) {
            // Menambahkan kolom security_id sebagai foreign key
            $table->foreignId('security_id')->constrained('securitys')->onDelete('cascade');
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
            // Menghapus kolom security_id dan foreign key
            $table->dropForeign(['security_id']);
            $table->dropColumn('security_id');
        });
    }
}
