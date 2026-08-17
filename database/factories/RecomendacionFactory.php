<?php

namespace Database\Factories;

use App\Enums\RecomendacionEstatus;
use App\Models\Recomendacion;
use App\Models\Respuesta;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Recomendacion>
 */
class RecomendacionFactory extends Factory
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
            'numero' => fake()->unique()->numberBetween(1, 1000),
            'descripcion' => fake()->sentence(),
            'estatus' => RecomendacionEstatus::Pendiente,
        ];
    }

    /**
     * Indicate the solicitante already proposed how they'll attend this recomendación.
     */
    public function propuesta(): static
    {
        return $this->state(fn (array $attributes) => [
            'estatus' => RecomendacionEstatus::Propuesta,
            'atencion_descripcion' => fake()->paragraph(),
        ]);
    }

    /**
     * Indicate the responsable accepted the atención.
     */
    public function aceptada(): static
    {
        return $this->state(fn (array $attributes) => [
            'estatus' => RecomendacionEstatus::Aceptada,
            'atencion_descripcion' => fake()->paragraph(),
        ]);
    }

    /**
     * Indicate the responsable requested an adjustment.
     */
    public function ajusteSolicitado(): static
    {
        return $this->state(fn (array $attributes) => [
            'estatus' => RecomendacionEstatus::AjusteSolicitado,
            'atencion_descripcion' => fake()->paragraph(),
            'comentario_responsable' => fake()->sentence(),
        ]);
    }
}
