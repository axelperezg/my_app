<?php

namespace App\Enums;

enum TipoArchivoSolicitud: string
{
    case OficioEntrada = 'oficio_entrada';
    case FormatoResultadosPdf = 'formato_resultados_pdf';
    case FormatoResultadosExcel = 'formato_resultados_excel';
    case CarpetaResultados = 'carpeta_resultados';
    case InstrumentoEvaluacion = 'instrumento_evaluacion';
    case Video = 'video';
    case Audio = 'audio';
    case Imagenes = 'imagenes';

    /**
     * Get the human-readable label for the tipo.
     */
    public function label(): string
    {
        return match ($this) {
            self::OficioEntrada => __('Oficio de Entrada'),
            self::FormatoResultadosPdf => __('Formato de Resultados (PDF)'),
            self::FormatoResultadosExcel => __('Formato de Resultados (Excel)'),
            self::CarpetaResultados => __('Carpeta de Resultados'),
            self::InstrumentoEvaluacion => __('Instrumento de Evaluación'),
            self::Video => __('Video'),
            self::Audio => __('Audio'),
            self::Imagenes => __('Imágenes'),
        };
    }

    /**
     * Determine whether this tipo accepts multiple files.
     */
    public function multiple(): bool
    {
        return in_array($this, [self::Video, self::Audio, self::Imagenes], strict: true);
    }

    /**
     * Get the validation rule fragment for accepted file extensions.
     *
     * @return array<int, string>
     */
    public function mimes(): array
    {
        return match ($this) {
            self::FormatoResultadosExcel => ['xlsx', 'xls'],
            self::Video => ['mp4', 'mov', 'avi'],
            self::Audio => ['mp3', 'wav', 'm4a'],
            self::Imagenes => ['jpg', 'jpeg', 'png', 'webp'],
            default => ['pdf'],
        };
    }

    /**
     * Get the maximum file size in kilobytes.
     */
    public function maxKilobytes(): int
    {
        return match ($this) {
            self::Video, self::Audio, self::Imagenes => 51200,
            default => 20480,
        };
    }

    /**
     * Determine whether this tipo's page count is included in the
     * "páginas por solicitud" report (i.e. it is always a PDF).
     */
    public function cuentaParaReportePaginas(): bool
    {
        return match ($this) {
            self::FormatoResultadosExcel, self::Video, self::Audio, self::Imagenes => false,
            default => true,
        };
    }

    /**
     * @return array<int, self>
     */
    public static function tiposContablesEnReportePaginas(): array
    {
        return array_values(array_filter(
            self::cases(),
            fn (self $tipo) => $tipo->cuentaParaReportePaginas(),
        ));
    }
}
