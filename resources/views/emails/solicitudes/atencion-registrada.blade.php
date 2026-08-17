<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: sans-serif; color: #1f2933;">
    <p>{{ __('Estimado(a) :nombre,', ['nombre' => $solicitud->respuesta->responsable->name]) }}</p>

    <p>
        {{ __('El solicitante de la solicitud con folio :folio registró cómo atenderá las recomendaciones.', [
            'folio' => $solicitud->folio,
        ]) }}
    </p>

    <p>
        {{ __('Ingresa al sistema con tu cuenta para revisar la propuesta y aceptarla o solicitar un ajuste.') }}
    </p>

    <p>
        <a href="{{ route('responsable.solicitudes.show', $solicitud) }}">{{ route('responsable.solicitudes.show', $solicitud) }}</a>
    </p>
</body>
</html>
