<?php

namespace App\Actions\Solicitudes;

use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser;
use Throwable;

class CountPdfPages
{
    /**
     * Count the pages in a PDF's raw contents.
     *
     * Returns null (instead of throwing) when the file can't be parsed —
     * a malformed or unusual PDF should never block an upload.
     */
    public function handle(string $contents): ?int
    {
        try {
            return \count((new Parser)->parseContent($contents)->getPages());
        } catch (Throwable $exception) {
            Log::warning('No se pudo contar las páginas de un PDF.', [
                'exception' => $exception->getMessage(),
            ]);

            return null;
        }
    }
}
