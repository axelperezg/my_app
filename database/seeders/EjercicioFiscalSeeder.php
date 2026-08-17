<?php

namespace Database\Seeders;

use App\Models\EjercicioFiscal;
use Illuminate\Database\Seeder;

class EjercicioFiscalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect([now()->year - 1, now()->year, now()->year + 1])
            ->each(fn (int $anio) => EjercicioFiscal::query()->firstOrCreate(['anio' => $anio]));
    }
}
