<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSecuritysToSecuritysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('securitys', function (Blueprint $table) {
            $table->string('password')->after('email');
            $table->string('active_token')->after('address');
            $table->boolean('status')->default(false)->after('active_token');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('securitys', function (Blueprint $table) {
            $table->dropColumn('password');
            $table->dropColumn('active_token');
            $table->dropColumn('status');
        });
    }
}
