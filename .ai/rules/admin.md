---
paths:
  - 'app/Models/**,app/Livewire/Admin/**,routes/admin.php'
---

# Admin

## Roles de usuario y panel Admin (sistema de solicitudes)
Roles fijos en `App\Enums\UserRole` (Solicitante/Responsable/Admin), columna `users.role` (no está en el `#[Fillable]` del modelo — se asigna explícitamente, nunca por mass-assignment). Rutas admin-only usan el middleware `role:admin` (alias en bootstrap/app.php → `EnsureUserHasRole`), no policies. `Gate::before` en AppServiceProvider da bypass total a Admin. El registro público de Fortify está deshabilitado, ver [[auth]].

## Cuentas: el Admin controla contraseña e institución, no hay autoservicio
El Admin crea y edita cuentas de Solicitante/Responsable desde `App\Livewire\Admin\Users` (`app_form`, un solo modal para alta y edición). El Admin escribe la contraseña directamente en el formulario (`App\Actions\Users\CreateUserAccount` / `UpdateUserAccount`) — no se manda ningún correo, no hay link de "establecer contraseña". `Features::resetPasswords()` está deshabilitado en config/fortify.php por la misma razón (ver [[auth]]); el cambio de contraseña estando autenticado (Settings → Security) sigue funcionando igual.

Solo el rol Solicitante está ligado a una institución (`users.institucion_id`, FK nullable — null para Responsable/Admin). `CreateUserAccount`/`UpdateUserAccount` lanzan `InvalidArgumentException` si `role === Solicitante` y no se pasó institución; validar esto también en el formulario Livewire antes de llegar a la acción. `App\Livewire\Solicitudes\Create` ya no pide institución: la toma de `Auth::user()->institucion_id` y bloquea el formulario (callout, no 403 duro) si el usuario no tiene institución activa asignada.
