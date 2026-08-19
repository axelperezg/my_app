<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\RespuestaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $solicitud_id
 * @property int $responsable_id
 * @property string|null $disco
 * @property string|null $ruta
 * @property string|null $nombre_original
 * @property CarbonImmutable $fecha_respuesta
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['disco', 'ruta', 'nombre_original', 'fecha_respuesta'])]
class Respuesta extends Model
{
    /** @use HasFactory<RespuestaFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fecha_respuesta' => 'datetime',
        ];
    }

    /**
     * The solicitud this respuesta answers.
     *
     * @return BelongsTo<Solicitud, $this>
     */
    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(Solicitud::class);
    }

    /**
     * The responsable who issued the respuesta.
     *
     * @return BelongsTo<User, $this>
     */
    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    /**
     * The recomendaciones issued with this respuesta.
     *
     * @return HasMany<Recomendacion, $this>
     */
    public function recomendaciones(): HasMany
    {
        return $this->hasMany(Recomendacion::class);
    }

    /**
     * The solicitante's atención submission for this respuesta, if any.
     *
     * @return HasOne<Atencion, $this>
     */
    public function atencion(): HasOne
    {
        return $this->hasOne(Atencion::class);
    }
}
