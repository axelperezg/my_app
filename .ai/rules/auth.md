---
paths:
  - 'config/fortify.php,resources/views/livewire/auth/**'
---

# Auth

## Registro público y reseteo de contraseña deshabilitados
`Features::registration()` y `Features::resetPasswords()` están comentados en config/fortify.php: las cuentas y sus contraseñas las controla el Admin por completo desde el panel (ver [[admin]] — `App\Actions\Users\CreateUserAccount` / `UpdateUserAccount`), no hay alta ni recuperación de contraseña por el propio usuario. El link "Sign up" se quitó de login.blade.php porque `route('register')` ya no existe; el link "Forgot your password?" ya estaba condicionado a `Route::has('password.request')` así que no hizo falta tocarlo. Los tests de RegistrationTest y PasswordResetTest se auto-omiten vía `skipUnlessFortifyHas`, no hace falta tocarlos si se reactiva alguno de los dos features.

El cambio de contraseña **estando autenticado** (Settings → Security, vía Fortify) no depende de `Features::resetPasswords()` y sigue funcionando normal — solo se quitó el flujo de "olvidé mi contraseña" para usuarios no autenticados.
