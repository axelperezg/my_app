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
        Schema::table('atenciones', function (Blueprint $table) {
            $table->string('disco')->nullable()->change();
            $table->string('ruta')->nullable()->change();
            $table->string('nombre_original')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('atenciones', function (Blueprint $table) {
            $table->string('disco')->nullable(false)->change();
            $table->string('ruta')->nullable(false)->change();
            $table->string('nombre_original')->nullable(false)->change();
        });
    }
};
