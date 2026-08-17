<?php

namespace Database\Factories;

use App\Models\Respuesta;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Respuesta>
 */
class RespuestaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'solicitud_id' => Solicitud::factory(),
            'responsable_id' => User::factory()->responsable(),
            'disco' => 'local',
            'ruta' => 'respuestas/'.fake()->uuid().'.pdf',
            'nombre_original' => 'respuesta.pdf',
            'fecha_respuesta' => now(),
        ];
    }
}
