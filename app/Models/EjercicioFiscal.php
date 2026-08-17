<?php

namespace App\Models;

use Database\Factories\EjercicioFiscalFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $anio
 * @property bool $activo
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Table('ejercicios_fiscales')]
#[Fillable(['anio', 'activo'])]
class EjercicioFiscal extends Model
{
    /** @use HasFactory<EjercicioFiscalFactory> */
    use HasFactory;

    protected $attributes = [
        'activo' => true,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'anio' => 'integer',
            'activo' => 'boolean',
        ];
    }

    /**
     * Scope the query to only active ejercicios fiscales.
     *
     * @param  Builder<EjercicioFiscal>  $query
     * @return Builder<EjercicioFiscal>
     */
    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('activo', true);
    }
}
