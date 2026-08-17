<?php

namespace App\Actions\Users;

use App\Enums\UserRole;
use App\Models\Institucion;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;

class UpdateUserAccount
{
    /**
     * Update an existing account's data. The Admin controls the password directly;
     * pass null to leave the current password unchanged.
     */
    public function handle(
        User $user,
        string $name,
        string $email,
        UserRole $role,
        ?Institucion $institucion,
        ?string $password,
        ?string $numeroCelular = null,
        ?string $direccion = null,
        ?string $telefonoOficina = null,
    ): User {
        if ($role === UserRole::Solicitante && $institucion === null) {
            throw new InvalidArgumentException('Un solicitante debe tener una institución asignada.');
        }

        $user->name = $name;
        $user->email = $email;
        $user->role = $role;
        $user->institucion_id = $role === UserRole::Solicitante ? $institucion->id : null;
        $user->numero_celular = $numeroCelular;
        $user->direccion = $direccion;
        $user->telefono_oficina = $telefonoOficina;

        if ($password !== null) {
            $user->password = Hash::make($password);
        }

        $user->save();

        return $user;
    }
}
