<?php

namespace App\Models;

use App\Enums\EstatusArchivoSolicitud;
use App\Enums\TipoArchivoSolicitud;
use Database\Factories\SolicitudArchivoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $solicitud_id
 * @property TipoArchivoSolicitud $tipo
 * @property string $disco
 * @property string $ruta
 * @property string $nombre_original
 * @property string $mime
 * @property int $tamano
 * @property int|null $paginas
 * @property EstatusArchivoSolicitud $estatus
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['tipo', 'disco', 'ruta', 'nombre_original', 'mime', 'tamano', 'paginas'])]
class SolicitudArchivo extends Model
{
    /** @use HasFactory<SolicitudArchivoFactory> */
    use HasFactory;

    protected $attributes = [
        'estatus' => 'sin_evaluar',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tipo' => TipoArchivoSolicitud::class,
            'tamano' => 'integer',
            'paginas' => 'integer',
            'estatus' => EstatusArchivoSolicitud::class,
        ];
    }

    /**
     * The solicitud this file belongs to.
     *
     * @return BelongsTo<Solicitud, $this>
     */
    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(Solicitud::class);
    }
}
