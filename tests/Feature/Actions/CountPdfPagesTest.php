<?php

use App\Actions\Solicitudes\CountPdfPages;
use Spatie\LaravelPdf\Facades\Pdf;

test('it counts the pages of a single-page pdf', function () {
    $pdf = base64_decode(Pdf::html('<h1>Una sola página</h1>')->base64());

    expect((new CountPdfPages)->handle($pdf))->toBe(1);
});

test('it counts the pages of a multi-page pdf', function () {
    $html = '<div>Página 1</div><div style="page-break-before: always;">Página 2</div><div style="page-break-before: always;">Página 3</div>';
    $pdf = base64_decode(Pdf::html($html)->base64());

    expect((new CountPdfPages)->handle($pdf))->toBe(3);
});

test('it returns null instead of throwing for content that is not a valid pdf', function () {
    expect((new CountPdfPages)->handle('esto no es un pdf'))->toBeNull();
});
