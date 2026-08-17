---
paths:
  - 'app/Models/Solicitud.php,app/Models/Respuesta.php,app/Models/Recomendacion.php,app/Actions/Solicitudes/**,app/Livewire/Admin/Solicitudes.php'
---

# Livewire Admin

## Foreign keys y estatus en Solicitud/Respuesta se asignan por propiedad, no por update()/create()
`Solicitud` (#[Fillable] = correo_electronico, numero_celular, institucion_id, ejercicio_fiscal_id) y `Respuesta` (#[Fillable] = disco, ruta, nombre_original, fecha_respuesta) excluyen a propósito `estatus`, `responsable_id`, `solicitante_id`: cualquier `->update([...])` o `->create([...])` con esas llaves las descarta en silencio (mass-assignment protection), sin error visible — solo se nota al leer el registro después. Asignar siempre por propiedad + `->save()`, ej. `$solicitud->estatus = SolicitudEstatus::Respondida; $solicitud->save();`. Ya pasó 3 veces en la Fase 3 (asignación de Admin, `SubmitRespuesta`). Ver [[solicitudes]].
