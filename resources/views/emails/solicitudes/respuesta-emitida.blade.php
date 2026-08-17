<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: sans-serif; color: #1f2933;">
    <p>{{ __('Estimado(a) :nombre,', ['nombre' => $solicitud->solicitante->name]) }}</p>

    <p>
        {{ __('Tu solicitud con folio :folio ya tiene respuesta, con :cantidad recomendación(es).', [
            'folio' => $solicitud->folio,
            'cantidad' => $solicitud->respuesta->recomendaciones->count(),
        ]) }}
    </p>

    <p>
        {{ __('Ingresa al sistema con tu cuenta para revisar el detalle y registrar cómo atenderás cada recomendación.') }}
    </p>

    <p>
        <a href="{{ route('solicitudes.index') }}">{{ route('solicitudes.index') }}</a>
    </p>
</body>
</html>
