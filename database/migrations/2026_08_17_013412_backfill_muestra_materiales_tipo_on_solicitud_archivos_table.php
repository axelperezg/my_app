<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * `TipoArchivoSolicitud::MuestraMateriales` was split into Video/Audio/Imagenes;
     * reclassify any pre-existing rows still holding the old value based on their
     * mime type so the enum cast doesn't blow up when reading them.
     */
    public function up(): void
    {
        DB::table('solicitud_archivos')
            ->where('tipo', 'muestra_materiales')
            ->where('mime', 'like', 'video/%')
            ->update(['tipo' => 'video']);

        DB::table('solicitud_archivos')
            ->where('tipo', 'muestra_materiales')
            ->where('mime', 'like', 'audio/%')
            ->update(['tipo' => 'audio']);

        // Anything left (images, or an unrecognized mime) becomes "imagenes".
        DB::table('solicitud_archivos')
            ->where('tipo', 'muestra_materiales')
            ->update(['tipo' => 'imagenes']);
    }

    /**
     * Reverse the migrations.
     *
     * Not reversible: once split, the original "muestra_materiales" grouping
     * can't be reconstructed.
     */
    public function down(): void
    {
        //
    }
};
