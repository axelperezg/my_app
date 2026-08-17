<?php

namespace App\Models;

use Database\Factories\ConfiguracionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * Singleton-style settings row for the application (currently just the
 * three configurable logos). Use Configuracion::actual() to fetch it.
 *
 * @property int $id
 * @property string|null $logo_app_path
 * @property string|null $logo_pdf_izquierdo_path
 * @property string|null $logo_pdf_derecho_path
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Table('configuracion')]
#[Fillable(['logo_app_path', 'logo_pdf_izquierdo_path', 'logo_pdf_derecho_path'])]
class Configuracion extends Model
{
    /** @use HasFactory<ConfiguracionFactory> */
    use HasFactory;

    /**
     * Get the single configuracion row, creating it if it doesn't exist yet.
     */
    public static function actual(): self
    {
        return static::query()->firstOrCreate([]);
    }

    /**
     * Public URL for the application logo, if one is configured.
     */
    public function logoAppUrl(): ?string
    {
        return $this->logo_app_path ? Storage::disk('public')->url($this->logo_app_path) : null;
    }

    /**
     * Base64 data URI for the left PDF logo, for embedding in generated PDFs.
     */
    public function logoPdfIzquierdoBase64(): ?string
    {
        return $this->base64Para($this->logo_pdf_izquierdo_path);
    }

    /**
     * Base64 data URI for the right PDF logo, for embedding in generated PDFs.
     */
    public function logoPdfDerechoBase64(): ?string
    {
        return $this->base64Para($this->logo_pdf_derecho_path);
    }

    private function base64Para(?string $path): ?string
    {
        if ($path === null || ! Storage::disk('public')->exists($path)) {
            return null;
        }

        $contenido = Storage::disk('public')->get($path);

        if ($contenido === null) {
            return null;
        }

        $mime = Storage::disk('public')->mimeType($path) ?: 'image/png';

        return 'data:'.$mime.';base64,'.base64_encode($contenido);
    }
}
