<?php

namespace App\Models;

use Database\Factories\InstitucionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $nombre
 * @property bool $activo
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Table('instituciones')]
#[Fillable(['nombre', 'activo'])]
class Institucion extends Model
{
    /** @use HasFactory<InstitucionFactory> */
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
            'activo' => 'boolean',
        ];
    }

    /**
     * Scope the query to only active instituciones.
     *
     * @param  Builder<Institucion>  $query
     * @return Builder<Institucion>
     */
    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('activo', true);
    }
}
