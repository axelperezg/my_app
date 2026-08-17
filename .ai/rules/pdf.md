---
paths:
  - 'app/Models/Configuracion.php,app/Livewire/Admin/Logos.php,resources/views/components/app-logo.blade.php,resources/views/pdf/**'
---

# Pdf

## Logos configurables (app + membrete del PDF)
Fila única en `configuracion` (`App\Models\Configuracion::actual()` — `firstOrCreate([])`, no crear más filas). Se guardan 3 logos en el disco `public`: `logo_app_path` (usado por `x-app-logo` vía `logoAppUrl()`, cubre sidebar/header/auth) y `logo_pdf_izquierdo_path`/`logo_pdf_derecho_path` (para el membrete del acuse en `pdf.solicitudes.acuse`, ver [[solicitudes]]). Los logos del PDF **no** se pasan como URL — dompdf no puede resolver `storage/...` de forma confiable — se embeben como `data:` URI en base64 vía `logoPdfIzquierdoBase64()`/`logoPdfDerechoBase64()`, que leen el archivo del disco y lo codifican. Al subir un logo nuevo siempre se borra el archivo anterior (`Logos::guardar()`), nunca queda huérfano. Solo se aceptan png/jpg/jpeg/webp — sin SVG (riesgo de contenido embebido + soporte inconsistente en dompdf).
