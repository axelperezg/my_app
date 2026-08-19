<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\AtencionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $respuesta_id
 * @property string|null $disco
 * @property string|null $ruta
 * @property string|null $nombre_original
 * @property CarbonImmutable $fecha_atencion
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Table('atenciones')]
#[Fillable(['disco', 'ruta', 'nombre_original', 'fecha_atencion'])]
class Atencion extends Model
{
    /** @use HasFactory<AtencionFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fecha_atencion' => 'datetime',
        ];
    }

    /**
     * The respuesta this atención answers.
     *
     * @return BelongsTo<Respuesta, $this>
     */
    public function respuesta(): BelongsTo
    {
        return $this->belongsTo(Respuesta::class);
    }
}
