---
paths:
  - 'app/Livewire/Admin/Reportes/**,app/Actions/Solicitudes/CountPdfPages.php,app/Concerns/ExportsCsv.php'
---

# Concerns

## Reportes de Admin: páginas de PDF y exportación CSV
`SolicitudArchivo.paginas` se calcula UNA vez al subir el archivo (`CreateSolicitud` inyecta `App\Actions\Solicitudes\CountPdfPages`, que usa `smalot/pdfparser` sobre el contenido en bytes — nunca sobre la ruta del disco, para no depender del driver de storage). Solo cuenta para tipos donde `TipoArchivoSolicitud::cuentaParaReportePaginas()` es true (los 4 PDF, no el Excel ni Muestra de Materiales); el reporte "Páginas por solicitud" sólo suma esos 4, ver [[solicitudes]]. `CountPdfPages::handle()` nunca lanza excepción — un PDF corrupto devuelve `null`, no debe romper la subida.

Los reportes de Admin agregan datos en PHP (Collection::groupBy) en vez de SQL con funciones de fecha específicas del motor, porque los tests corren en sqlite y la app en postgres — evita duplicar lógica por dialecto. La exportación a CSV usa el trait `App\Concerns\ExportsCsv` (`streamCsv()`), no repetir el patrón `fopen('php://output')` a mano en cada reporte nuevo.
