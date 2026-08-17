<?php

namespace App\Models;

use App\Enums\RecomendacionEstatus;
use Database\Factories\RecomendacionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $respuesta_id
 * @property int $numero
 * @property string $descripcion
 * @property RecomendacionEstatus $estatus
 * @property string|null $atencion_descripcion
 * @property string|null $comentario_responsable
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Table('recomendaciones')]
#[Fillable(['numero', 'descripcion'])]
class Recomendacion extends Model
{
    /** @use HasFactory<RecomendacionFactory> */
    use HasFactory;

    protected $attributes = [
        'estatus' => 'pendiente',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'estatus' => RecomendacionEstatus::class,
        ];
    }

    /**
     * The respuesta this recomendación belongs to.
     *
     * @return BelongsTo<Respuesta, $this>
     */
    public function respuesta(): BelongsTo
    {
        return $this->belongsTo(Respuesta::class);
    }
}
