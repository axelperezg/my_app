<?php

namespace App\Livewire\Solicitudes;

use App\Actions\Solicitudes\CreateSolicitud;
use App\Enums\TipoArchivoSolicitud;
use App\Models\EjercicioFiscal;
use App\Models\Solicitud;
use App\Models\User;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Nueva solicitud')]
class Create extends Component
{
    use WithFileUploads;

    public string $ejercicio_fiscal_id = '';

    public string $correo_electronico = '';

    public mixed $oficioEntrada = null;

    public mixed $formatoResultadosPdf = null;

    public mixed $formatoResultadosExcel = null;

    public mixed $carpetaResultados = null;

    public mixed $instrumentoEvaluacion = null;

    /** @var array<int, mixed> */
    public array $videos = [];

    /** @var array<int, mixed> */
    public array $audios = [];

    /** @var array<int, mixed> */
    public array $imagenes = [];

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        Gate::authorize('create', Solicitud::class);

        $this->correo_electronico = Auth::user()->email;
    }

    /**
     * Determine whether the solicitante's account has an active institución assigned.
     */
    #[Computed]
    public function institucionValida(): bool
    {
        /** @var User $user */
        $user = Auth::user();

        return $user->institucion !== null && $user->institucion->activo;
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'ejercicio_fiscal_id' => ['required', Rule::exists(EjercicioFiscal::class, 'id')->where('activo', true)],
            'correo_electronico' => ['required', 'email', 'max:255'],
            'oficioEntrada' => $this->fileRules(TipoArchivoSolicitud::OficioEntrada),
            'formatoResultadosPdf' => $this->fileRules(TipoArchivoSolicitud::FormatoResultadosPdf),
            'formatoResultadosExcel' => $this->fileRules(TipoArchivoSolicitud::FormatoResultadosExcel),
            'carpetaResultados' => $this->fileRules(TipoArchivoSolicitud::CarpetaResultados),
            'instrumentoEvaluacion' => $this->fileRules(TipoArchivoSolicitud::InstrumentoEvaluacion),
            'videos' => ['array'],
            'videos.*' => $this->fileRules(TipoArchivoSolicitud::Video),
            'audios' => ['array'],
            'audios.*' => $this->fileRules(TipoArchivoSolicitud::Audio),
            'imagenes' => ['array'],
            'imagenes.*' => $this->fileRules(TipoArchivoSolicitud::Imagenes),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function fileRules(TipoArchivoSolicitud $tipo): array
    {
        return [
            'required',
            'file',
            'mimes:'.implode(',', $tipo->mimes()),
            'max:'.$tipo->maxKilobytes(),
        ];
    }

    /**
     * Submit the solicitud.
     */
    public function submit(CreateSolicitud $createSolicitud): void
    {
        /** @var User $solicitante */
        $solicitante = Auth::user();

        $institucionId = $solicitante->institucion_id;

        if ($institucionId === null || ! $solicitante->institucion?->activo) {
            abort(403);
        }

        $validated = $this->validate();

        $createSolicitud->handle(
            solicitante: $solicitante,
            datos: [
                'institucion_id' => $institucionId,
                'ejercicio_fiscal_id' => (int) $validated['ejercicio_fiscal_id'],
                'correo_electronico' => $validated['correo_electronico'],
            ],
            archivos: [
                TipoArchivoSolicitud::OficioEntrada->value => $this->oficioEntrada,
                TipoArchivoSolicitud::FormatoResultadosPdf->value => $this->formatoResultadosPdf,
                TipoArchivoSolicitud::FormatoResultadosExcel->value => $this->formatoResultadosExcel,
                TipoArchivoSolicitud::CarpetaResultados->value => $this->carpetaResultados,
                TipoArchivoSolicitud::InstrumentoEvaluacion->value => $this->instrumentoEvaluacion,
                TipoArchivoSolicitud::Video->value => $this->videos,
                TipoArchivoSolicitud::Audio->value => $this->audios,
                TipoArchivoSolicitud::Imagenes->value => $this->imagenes,
            ],
        );

        Flux::toast(variant: 'success', text: __('Solicitud enviada. Te enviamos el acuse de recibo por correo.'));

        $this->redirectRoute('solicitudes.index', navigate: true);
    }

    /**
     * @return Collection<int, EjercicioFiscal>
     */
    #[Computed]
    public function ejerciciosFiscales(): Collection
    {
        return EjercicioFiscal::active()->orderByDesc('anio')->get();
    }
}
