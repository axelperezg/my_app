<?php

namespace App\Actions\Users;

use App\Enums\UserRole;
use App\Models\Institucion;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;

class CreateUserAccount
{
    /**
     * Create a user account with the credentials the Admin assigned.
     *
     * The Admin controls every account's password directly; nothing is emailed.
     */
    public function handle(
        string $name,
        string $email,
        string $password,
        UserRole $role,
        ?Institucion $institucion = null,
        ?string $numeroCelular = null,
        ?string $direccion = null,
        ?string $telefonoOficina = null,
    ): User {
        if ($role === UserRole::Solicitante && $institucion === null) {
            throw new InvalidArgumentException('Un solicitante debe tener una institución asignada.');
        }

        $user = new User([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'numero_celular' => $numeroCelular,
            'direccion' => $direccion,
            'telefono_oficina' => $telefonoOficina,
        ]);
        $user->role = $role;
        $user->institucion_id = $role === UserRole::Solicitante ? $institucion->id : null;
        $user->email_verified_at = CarbonImmutable::now();
        $user->save();

        return $user;
    }
}
