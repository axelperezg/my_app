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
     * EstatusArchivoSolicitud::Vacio became SinEvaluar ('sin_evaluar') and
     * SolicitudEstatus::Cerrada became Concluida ('concluida') so the enum
     * case names agree with their display labels.
     */
    public function up(): void
    {
        DB::table('solicitud_archivos')->where('estatus', 'vacio')->update(['estatus' => 'sin_evaluar']);
        DB::table('solicitudes')->where('estatus', 'cerrada')->update(['estatus' => 'concluida']);

        Schema::table('solicitud_archivos', function (Blueprint $table) {
            $table->string('estatus')->default('sin_evaluar')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('solicitud_archivos')->where('estatus', 'sin_evaluar')->update(['estatus' => 'vacio']);
        DB::table('solicitudes')->where('estatus', 'concluida')->update(['estatus' => 'cerrada']);

        Schema::table('solicitud_archivos', function (Blueprint $table) {
            $table->string('estatus')->default('vacio')->change();
        });
    }
};
