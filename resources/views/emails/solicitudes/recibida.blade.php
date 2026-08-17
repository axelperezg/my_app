<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: sans-serif; color: #1f2933;">
    <p>{{ __('Estimado(a) :nombre,', ['nombre' => $solicitud->solicitante->name]) }}</p>

    <p>
        {{ __('Confirmamos la recepción de tu solicitud con el siguiente folio:') }}
    </p>

    <p style="font-size: 1.25rem; font-weight: bold;">{{ $solicitud->folio }}</p>

    <table style="border-collapse: collapse;">
        <tr>
            <td style="padding: 4px 12px 4px 0;">{{ __('Institución') }}</td>
            <td style="padding: 4px 0;">{{ $solicitud->institucion->nombre }}</td>
        </tr>
        <tr>
            <td style="padding: 4px 12px 4px 0;">{{ __('Ejercicio fiscal') }}</td>
            <td style="padding: 4px 0;">{{ $solicitud->ejercicioFiscal->anio }}</td>
        </tr>
        <tr>
            <td style="padding: 4px 12px 4px 0;">{{ __('Fecha de recepción') }}</td>
            <td style="padding: 4px 0;">{{ $solicitud->fecha_recepcion->translatedFormat('d \d\e F \d\e Y, H:i') }}</td>
        </tr>
    </table>

    <p>
        {{ __('Adjuntamos el acuse de recibo en PDF con el detalle de tu solicitud. Puedes dar seguimiento a tu solicitud dentro del sistema con tu cuenta.') }}
    </p>
</body>
</html>
