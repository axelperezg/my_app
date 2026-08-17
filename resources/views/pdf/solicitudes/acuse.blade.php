<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; color: #1f2933; font-size: 12px; }
        h1 { font-size: 16px; margin-bottom: 0; }
        .folio { font-size: 20px; font-weight: bold; margin: 12px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        td { padding: 4px 0; vertical-align: top; }
        td.label { width: 180px; color: #52606d; }
        .archivos li { margin-bottom: 4px; }
        .membrete { width: 100%; margin-bottom: 12px; }
        .membrete td { padding: 0; vertical-align: middle; }
        .membrete td.logo { width: 90px; }
        .membrete td.logo img { max-height: 60px; max-width: 90px; }
        .membrete td.logo.derecho { text-align: right; }
        .membrete td.titulo { text-align: center; }
    </style>
</head>
<body>
    <table class="membrete">
        <tr>
            <td class="logo">
                @if ($logoIzquierdo)
                    <img src="{{ $logoIzquierdo }}" alt="">
                @endif
            </td>
            <td class="titulo">
                <h1>{{ config('app.name') }}</h1>
                <p>{{ __('Acuse de recepción de solicitud') }}</p>
            </td>
            <td class="logo derecho">
                @if ($logoDerecho)
                    <img src="{{ $logoDerecho }}" alt="">
                @endif
            </td>
        </tr>
    </table>

    <p class="folio">{{ __('Folio') }}: {{ $solicitud->folio }}</p>

    <table>
        <tr>
            <td class="label">{{ __('Solicitante') }}</td>
            <td>{{ $solicitud->solicitante->name }}</td>
        </tr>
        <tr>
            <td class="label">{{ __('Institución') }}</td>
            <td>{{ $solicitud->institucion->nombre }}</td>
        </tr>
        <tr>
            <td class="label">{{ __('Ejercicio fiscal') }}</td>
            <td>{{ $solicitud->ejercicioFiscal->anio }}</td>
        </tr>
        <tr>
            <td class="label">{{ __('Correo electrónico') }}</td>
            <td>{{ $solicitud->correo_electronico }}</td>
        </tr>
        @if ($solicitud->solicitante->numero_celular)
            <tr>
                <td class="label">{{ __('Número de celular') }}</td>
                <td>{{ $solicitud->solicitante->numero_celular }}</td>
            </tr>
        @endif
        <tr>
            <td class="label">{{ __('Fecha de recepción') }}</td>
            <td>{{ $solicitud->fecha_recepcion->translatedFormat('d \d\e F \d\e Y, H:i') }}</td>
        </tr>
    </table>

    <p style="margin-top: 20px;">{{ __('Documentos recibidos') }}:</p>
    <ul class="archivos">
        @foreach ($solicitud->archivos as $archivo)
            <li>{{ $archivo->tipo->label() }} — {{ $archivo->nombre_original }}</li>
        @endforeach
    </ul>
</body>
</html>
