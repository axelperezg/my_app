<?php

namespace Database\Factories;

use App\Models\EjercicioFiscal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EjercicioFiscal>
 */
class EjercicioFiscalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'anio' => fake()->unique()->numberBetween(2020, 2035),
            'activo' => true,
        ];
    }

    /**
     * Indicate that the ejercicio fiscal is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'activo' => false,
        ]);
    }
}
