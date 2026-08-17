<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * RecomendacionEstatus dropped Propuesta/Aceptada/AjusteSolicitado in favor
     * of a plain Pendiente/Atendida/NoAtendida evaluation — the responsable no
     * longer leaves a comentario_responsable when a recomendación isn't met.
     */
    public function up(): void
    {
        DB::table('recomendaciones')->where('estatus', 'propuesta')->update(['estatus' => 'pendiente']);
        DB::table('recomendaciones')->where('estatus', 'aceptada')->update(['estatus' => 'atendida']);
        DB::table('recomendaciones')->where('estatus', 'ajuste_solicitado')->update(['estatus' => 'no_atendida']);

        Schema::table('recomendaciones', function (Blueprint $table) {
            $table->dropColumn('comentario_responsable');
        });
    }

    /**
     * Reverse the migrations.
     *
     * The estatus backfill isn't reversible (Propuesta vs. Pendiente can't be
     * told apart anymore); the column is restored empty.
     */
    public function down(): void
    {
        Schema::table('recomendaciones', function (Blueprint $table) {
            $table->text('comentario_responsable')->nullable()->after('atencion_descripcion');
        });
    }
};
