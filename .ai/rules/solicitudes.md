---
paths:
  - 'app/Models/Solicitud.php,app/Models/SolicitudArchivo.php,app/Actions/Solicitudes/**,app/Livewire/Solicitudes/**'
---

# Solicitudes

## Solicitudes: folio, archivos y acuse en PDF
Tabla `solicitudes` (no `solicituds` — Eloquent adivina mal el plural en español, se fuerza con `#[Table('solicitudes')]`). El folio (`{anio}-{consecutivo6}`) se genera en `App\Actions\Solicitudes\CreateSolicitud::generateFolio()` bloqueando la fila de `EjercicioFiscal` (`lockForUpdate`) dentro de una transacción — no usar un contador separado. Tipos de archivo y sus reglas de validación (mimes/tamaño) viven en `App\Enums\TipoArchivoSolicitud`, no hardcodeadas en el componente Livewire. El acuse en PDF usa `spatie/laravel-pdf` con driver **dompdf** (`LARAVEL_PDF_DRIVER=dompdf` en .env) — no Browsershot/Chromium, no está instalado en este entorno. Vista del PDF: `resources/views/pdf/solicitudes/acuse.blade.php` (sin logos aún — el módulo de logos de Admin es una fase pendiente). Límites de subida: `public/.user.ini` (60M/200M PHP) y `config/livewire.php` `temporary_file_upload.rules` (61440 KB) — si se cambia el tamaño máximo de "Muestra de Materiales" en `TipoArchivoSolicitud::maxKilobytes()`, hay que subir también estos dos límites. Nota de testing: `Mail::fake()` no ejecuta `Mailable::attachments()`, así que los tests con fake no cubren el renderizado real del PDF — se verificó manualmente que dompdf renderiza la vista sin errores.

## Muestra de Materiales se separó en Video/Audio/Imágenes; SolicitudArchivo tiene estatus de revisión
`TipoArchivoSolicitud::MuestraMateriales` ya no existe: se dividió en `Video`, `Audio` e `Imagenes` (cada uno acepta varios archivos, opcionales — sin mínimo requerido — en `App\Livewire\Solicitudes\Create`).

`solicitud_archivos.estatus` (enum `App\Enums\EstatusArchivoSolicitud`: Vacio/Incompleto/Completo, default Vacio) lo califica el Responsable por documento en `/responsable/solicitudes/{id}`. Sigue el mismo patrón que `Solicitud`/`Respuesta` ([[livewire-admin]]): `estatus` está fuera del `#[Fillable]` de `SolicitudArchivo` a propósito, se asigna por propiedad + `->save()` (ver `App\Livewire\Responsable\Solicitudes\Show::updatedArchivoEstatus()`), gateado por la policy `responder` (solo mientras la solicitud está `Asignada`).

## Folio de Solicitud: consecutivo de 3 dígitos, no 6
`CreateSolicitud::generateFolio()` genera `{anio}-{consecutivo3}` (ej. `2026-001`), no 6 dígitos — ver [[solicitudes]]. `SolicitudFactory` usa el mismo patrón (`now()->year.'-###'`). Si el consecutivo supera 999 en un año, `sprintf('%03d', ...)` simplemente amplía el ancho (ej. `2026-1000`), no trunca ni falla.

## RecomendacionEstatus: 3 estados, sin retroalimentación del Responsable
`RecomendacionEstatus` tiene solo 3 cases: `Pendiente` (default, aún sin evaluar), `Atendida` (el Responsable marcó "Cumple"), `NoAtendida` (marcó "No cumple"). Ya no existen `Propuesta`/`Aceptada`/`AjusteSolicitado` ni la columna `comentario_responsable` — el Responsable ya no deja retroalimentación de texto, solo evalúa binario. `RegistrarAtencion` deja cualquier recomendación no-Atendida en `Pendiente` (con `atencion_descripcion` llena) al recibir la propuesta del Solicitante; la vista del Responsable (`App\Livewire\Responsable\Solicitudes\Show::marcarAtendida()`/`marcarNoAtendida()`) solo muestra los botones "Cumple"/"No cumple" cuando `estatus === Pendiente && atencion_descripcion` está llena (i.e. el Solicitante ya respondió). Ver [[solicitudes]].

## Calificar Documentos recibidos usa su propia policy, no "responder"
El estatus de cada `SolicitudArchivo` (Vacío/Incompleto/Completo) lo gatea `SolicitudPolicy::calificarArchivos()`, no `responder()`. `responder()` solo es true mientras `estatus === Asignada` (antes de emitir la respuesta) — reusarla para calificar documentos dejaba el select deshabilitado en cuanto el Responsable emitía su respuesta, que era el bug real. `calificarArchivos()` es true mientras `responsable_id` coincide y la solicitud no está `Cerrada`, sin importar en qué otro estatus esté. Ver [[solicitudes]].

## Archivos de Solicitudes se guardan en R2 (disco 's3'), no local
`CreateSolicitud`, `SubmitRespuesta` y `RegistrarAtencion` guardan los archivos de una solicitud (`SolicitudArchivo`, `Respuesta`, `Atencion`) en el disco `s3` (Cloudflare R2, configurado en `config/filesystems.php` vía variables `AWS_*` — no `R2_*`, reutiliza el disco genérico `s3` de Laravel). El campo `disco` se guarda por registro (no asumir un valor fijo): solicitudes creadas antes de este cambio tienen `disco = 'local'`. Los controllers de descarga (`SolicitudArchivoController`, `SolicitudRespuestaController`, `SolicitudAtencionController`) ya son disco-agnósticos vía `Storage::disk($modelo->disco)`.

`DeleteSolicitud::handle()` recolecta los discos realmente usados por los archivos de la solicitud (`archivos`, `respuesta`, `respuesta.atencion`) y borra el folder `solicitudes/{folio}` de cada uno — no asumir un solo disco fijo, porque una solicitud vieja puede tener archivos en `local` mientras el resto de la app ya usa `s3`.

Importante: `LIVEWIRE_TEMPORARY_FILE_UPLOAD_DISK=local` está fijo en `.env` a propósito, independiente de `FILESYSTEM_DISK=s3` — si se quita, Livewire intenta subir los archivos temporales directo del navegador al bucket R2 vía URLs pre-firmadas, lo cual falla en silencio (el campo se ve lleno pero la validación sigue marcando "required") porque el bucket no tiene CORS configurado para eso.
