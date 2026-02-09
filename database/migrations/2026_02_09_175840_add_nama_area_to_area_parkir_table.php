<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('area_parkir', function (Blueprint $table) {
            $table->string('nama_area', 100)
                  ->after('kode_area')
                  ->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('area_parkir', function (Blueprint $table) {
            $table->dropColumn('nama_area');
        });
    }
};
