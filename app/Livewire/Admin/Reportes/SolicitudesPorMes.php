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

#[Title('Solicitudes por mes')]
class SolicitudesPorMes extends Component
{
    use ExportsCsv;

    /**
     * @return Collection<int, array{mes: string, cantidad: int<0, max>, paginas: int}>
     */
    #[Computed]
    public function porMes(): Collection
    {
        $tiposContables = array_map(
            fn (TipoArchivoSolicitud $tipo) => $tipo->value,
            TipoArchivoSolicitud::tiposContablesEnReportePaginas(),
        );

        return Solicitud::query()
            ->withSum(['archivos as paginas_pdf' => fn (Builder $query) => $query->whereIn('tipo', $tiposContables)], 'paginas')
            ->get()
            ->groupBy(fn (Solicitud $solicitud) => $solicitud->fecha_recepcion->format('Y-m'))
            ->map(fn (Collection $grupo, string $mes) => [
                'mes' => $mes,
                'cantidad' => $grupo->count(),
                'paginas' => (int) $grupo->sum(fn (Solicitud $solicitud) => $solicitud->getAttribute('paginas_pdf') ?? 0),
            ])
            ->sortKeysDesc()
            ->values();
    }

    /**
     * Export the report as a CSV file.
     */
    public function exportar(): StreamedResponse
    {
        return $this->streamCsv(
            'solicitudes-por-mes.csv',
            ['Mes', 'Solicitudes', 'Páginas'],
            $this->porMes()->map(fn (array $fila) => [$fila['mes'], $fila['cantidad'], $fila['paginas']]),
        );
    }
}
