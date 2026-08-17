<?php

namespace App\Concerns;

use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

trait ExportsCsv
{
    /**
     * Stream an in-memory dataset to the browser as a downloadable UTF-8 CSV.
     *
     * @param  array<int, string>  $encabezados
     * @param  iterable<array<int, string|int>>  $filas
     */
    protected function streamCsv(string $nombreArchivo, array $encabezados, iterable $filas): StreamedResponse
    {
        return response()->streamDownload(function () use ($encabezados, $filas) {
            $handle = fopen('php://output', 'w');

            if ($handle === false) {
                throw new RuntimeException('No se pudo generar el archivo CSV.');
            }

            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, $encabezados);

            foreach ($filas as $fila) {
                fputcsv($handle, $fila);
            }

            fclose($handle);
        }, $nombreArchivo);
    }
}
