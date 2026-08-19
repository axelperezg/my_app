---
paths:
  - 'app/Models/Respuesta.php,app/Models/Atencion.php,app/Actions/Solicitudes/SubmitRespuesta.php,app/Actions/Solicitudes/RegistrarAtencion.php,app/Http/Controllers/SolicitudRespuestaController.php,app/Http/Controllers/SolicitudAtencionController.php'
---

# Controllers

## PDF de Respuesta y Atención son opcionales, no null-check disco/ruta
Desde la migración `2026_08_19_034308_make_respuesta_pdf_columns_nullable` / `..._atencion_...`, `disco`/`ruta`/`nombre_original` en `respuestas` y `atenciones` son nullable: el PDF firmado (pdfRespuesta) y el PDF de atención (pdfAtencion) son opcionales en sus formularios Livewire (`nullable`, no `required`). `SubmitRespuesta::handle()` y `RegistrarAtencion::handle()` aceptan `?UploadedFile $pdf` y solo guardan en R2 si viene el archivo — la `Respuesta`/`Atencion` se crea igual (para llevar recomendaciones/estatus) aunque no haya PDF. Cualquier vista que muestre el link de descarga debe verificar `$respuesta->ruta` (y `$atencion?->ruta`) antes de mostrarlo, no solo la existencia del registro. Los controllers de descarga hacen `abort_unless($modelo->ruta !== null, 404)` como defensa ante links viejos/manipulados.
