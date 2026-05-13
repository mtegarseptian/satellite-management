<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('satellites', function (Blueprint $table) {
            // Menambahkan kolom norad_id (boleh kosong/nullable) diletakkan setelah kolom name
            $table->string('norad_id')->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('satellites', function (Blueprint $table) {
            $table->dropColumn('norad_id');
        });
    }
};