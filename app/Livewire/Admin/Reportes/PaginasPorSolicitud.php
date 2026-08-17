<?php

namespace App\Livewire\Admin\Reportes;

use App\Concerns\ExportsCsv;
use App\Enums\TipoArchivoSolicitud;
use App\Models\Solicitud;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Title('Páginas por solicitud')]
class PaginasPorSolicitud extends Component
{
    use ExportsCsv;

    /**
     * @return Collection<int, array{folio: string, solicitante: string, institucion: string, ejercicio_fiscal: int, paginas: int}>
     */
    #[Computed]
    public function filas(): Collection
    {
        $tiposContables = array_map(
            fn (TipoArchivoSolicitud $tipo) => $tipo->value,
            TipoArchivoSolicitud::tiposContablesEnReportePaginas(),
        );

        return Solicitud::query()
            ->with(['solicitante', 'institucion', 'ejercicioFiscal'])
            ->withSum(['archivos as paginas_pdf' => fn (Builder $query) => $query->whereIn('tipo', $tiposContables)], 'paginas')
            ->orderByDesc('paginas_pdf')
            ->get()
            ->map(fn (Solicitud $solicitud) => [
                'folio' => $solicitud->folio,
                'solicitante' => $solicitud->solicitante->name,
                'institucion' => $solicitud->institucion->nombre,
                'ejercicio_fiscal' => $solicitud->ejercicioFiscal->anio,
                'paginas' => (int) ($solicitud->getAttribute('paginas_pdf') ?? 0),
            ]);
    }

    /**
     * Export the report as a CSV file.
     */
    public function exportar(): StreamedResponse
    {
        return $this->streamCsv(
            'paginas-por-solicitud.csv',
            ['Folio', 'Solicitante', 'Institución', 'Ejercicio fiscal', 'Páginas'],
            $this->filas()->map(fn (array $fila) => [
                $fila['folio'],
                $fila['solicitante'],
                $fila['institucion'],
                $fila['ejercicio_fiscal'],
                $fila['paginas'],
            ]),
        );
    }
}
