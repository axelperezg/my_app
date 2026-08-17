<?php

namespace Database\Factories;

use App\Models\EjercicioFiscal;
use App\Models\Institucion;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Solicitud>
 */
class SolicitudFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'folio' => fake()->unique()->numerify(now()->year.'-######'),
            'solicitante_id' => User::factory()->solicitante(),
            'institucion_id' => Institucion::factory(),
            'ejercicio_fiscal_id' => EjercicioFiscal::factory(),
            'correo_electronico' => fake()->unique()->safeEmail(),
            'fecha_recepcion' => now(),
        ];
    }
}
