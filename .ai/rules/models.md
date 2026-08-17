---
paths:
  - 'app/Models/*.php'
---

# Models

## Todo modelo con nombre en español necesita #[Table] explícito
Eloquent adivina el plural en inglés del nombre de la clase, y falla con casi cualquier sustantivo en español: Institucion→institucions, EjercicioFiscal→ejercicio_fiscals, Solicitud→solicituds, Recomendacion→recomendacions, Atencion→atencions. Ya pasó 4 veces (solo `SolicitudArchivo` y `Respuesta` coincidieron por accidente). Al crear un modelo nuevo, agregar siempre `#[Table('plural_correcto_en_espanol')]` y verificarlo corriendo las migraciones antes de dar por bueno el modelo — no asumir que el nombre por defecto es correcto.
