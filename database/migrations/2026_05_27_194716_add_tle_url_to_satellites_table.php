<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('satellites', function (Blueprint $table) {
            $table->string('tle_url')->nullable()->after('norad_id');
        });
    }

    public function down()
    {
        Schema::table('satellites', function (Blueprint $table) {
            $table->dropColumn('tle_url');
        });
    }
};
