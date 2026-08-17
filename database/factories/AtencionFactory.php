<?php

namespace Database\Factories;

use App\Models\Atencion;
use App\Models\Respuesta;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Atencion>
 */
class AtencionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'respuesta_id' => Respuesta::factory(),
            'disco' => 's3',
            'ruta' => 'atenciones/'.fake()->uuid().'.pdf',
            'nombre_original' => 'atencion.pdf',
            'fecha_atencion' => now(),
        ];
    }
}
