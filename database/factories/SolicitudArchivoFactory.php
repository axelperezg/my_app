<?php

namespace Database\Factories;

use App\Enums\TipoArchivoSolicitud;
use App\Models\Solicitud;
use App\Models\SolicitudArchivo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SolicitudArchivo>
 */
class SolicitudArchivoFactory extends Factory
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
            'tipo' => TipoArchivoSolicitud::OficioEntrada,
            'disco' => 's3',
            'ruta' => 'solicitudes/'.fake()->uuid().'.pdf',
            'nombre_original' => fake()->word().'.pdf',
            'mime' => 'application/pdf',
            'tamano' => fake()->numberBetween(1024, 1024 * 1024),
        ];
    }
}
