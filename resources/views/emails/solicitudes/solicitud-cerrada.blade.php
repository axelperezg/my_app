<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: sans-serif; color: #1f2933;">
    <p>{{ __('Estimado(a) :nombre,', ['nombre' => $solicitud->solicitante->name]) }}</p>

    <p>
        {{ __('Tu solicitud con folio :folio ha sido cerrada: todas las recomendaciones fueron aceptadas.', [
            'folio' => $solicitud->folio,
        ]) }}
    </p>

    <p>
        <a href="{{ route('solicitudes.index') }}">{{ route('solicitudes.index') }}</a>
    </p>
</body>
</html>
