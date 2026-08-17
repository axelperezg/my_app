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
     * Indicate the solicitante already proposed how they'll attend this recomendación,
     * still awaiting the responsable's evaluation.
     */
    public function propuesta(): static
    {
        return $this->state(fn (array $attributes) => [
            'estatus' => RecomendacionEstatus::Pendiente,
            'atencion_descripcion' => fake()->paragraph(),
        ]);
    }

    /**
     * Indicate the responsable marked the atención as atendida (cumple).
     */
    public function atendida(): static
    {
        return $this->state(fn (array $attributes) => [
            'estatus' => RecomendacionEstatus::Atendida,
            'atencion_descripcion' => fake()->paragraph(),
        ]);
    }

    /**
     * Indicate the responsable marked the atención as no atendida (no cumple).
     */
    public function noAtendida(): static
    {
        return $this->state(fn (array $attributes) => [
            'estatus' => RecomendacionEstatus::NoAtendida,
            'atencion_descripcion' => fake()->paragraph(),
        ]);
    }
}
