<?php

namespace Database\Seeders;

use App\Models\Institucion;
use Illuminate\Database\Seeder;

class InstitucionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect([
            'Secretaría de Educación Pública',
            'Secretaría de Salud',
            'Secretaría de Hacienda y Crédito Público',
        ])->each(fn (string $nombre) => Institucion::query()->firstOrCreate(['nombre' => $nombre]));
    }
}
