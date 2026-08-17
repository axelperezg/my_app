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
        Schema::table('recomendaciones', function (Blueprint $table) {
            $table->string('estatus')->default('pendiente')->index()->after('descripcion');
            $table->text('atencion_descripcion')->nullable()->after('estatus');
            $table->text('comentario_responsable')->nullable()->after('atencion_descripcion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recomendaciones', function (Blueprint $table) {
            $table->dropColumn(['estatus', 'atencion_descripcion', 'comentario_responsable']);
        });
    }
};
