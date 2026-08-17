<?php

namespace App\Models;

use App\Enums\SolicitudEstatus;
use App\Policies\SolicitudPolicy;
use Carbon\CarbonImmutable;
use Database\Factories\SolicitudFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $folio
 * @property int $solicitante_id
 * @property int|null $responsable_id
 * @property int $institucion_id
 * @property int $ejercicio_fiscal_id
 * @property string $correo_electronico
 * @property CarbonImmutable $fecha_recepcion
 * @property SolicitudEstatus $estatus
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Table('solicitudes')]
#[UsePolicy(SolicitudPolicy::class)]
#[Fillable(['correo_electronico', 'institucion_id', 'ejercicio_fiscal_id'])]
class Solicitud extends Model
{
    /** @use HasFactory<SolicitudFactory> */
    use HasFactory;

    protected $attributes = [
        'estatus' => 'recibida',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fecha_recepcion' => 'datetime',
            'estatus' => SolicitudEstatus::class,
        ];
    }

    /**
     * The user who submitted the solicitud.
     *
     * @return BelongsTo<User, $this>
     */
    public function solicitante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'solicitante_id');
    }

    /**
     * The responsable assigned to attend the solicitud.
     *
     * @return BelongsTo<User, $this>
     */
    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    /**
     * The institución the solicitud was submitted for.
     *
     * @return BelongsTo<Institucion, $this>
     */
    public function institucion(): BelongsTo
    {
        return $this->belongsTo(Institucion::class);
    }

    /**
     * The ejercicio fiscal the solicitud was submitted for.
     *
     * @return BelongsTo<EjercicioFiscal, $this>
     */
    public function ejercicioFiscal(): BelongsTo
    {
        return $this->belongsTo(EjercicioFiscal::class);
    }

    /**
     * The files attached to the solicitud.
     *
     * @return HasMany<SolicitudArchivo, $this>
     */
    public function archivos(): HasMany
    {
        return $this->hasMany(SolicitudArchivo::class);
    }

    /**
     * The respuesta issued for this solicitud, if any.
     *
     * @return HasOne<Respuesta, $this>
     */
    public function respuesta(): HasOne
    {
        return $this->hasOne(Respuesta::class);
    }
}
