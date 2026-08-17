<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('solicitud_archivos', function (Blueprint $table) {
            $table->string('estatus')->default('vacio')->after('paginas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitud_archivos', function (Blueprint $table) {
            $table->dropColumn('estatus');
        });
    }
};
