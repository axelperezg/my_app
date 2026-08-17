<?php

namespace App\Livewire\Admin;

use App\Actions\Solicitudes\DeleteSolicitud;
use App\Enums\SolicitudEstatus;
use App\Enums\UserRole;
use App\Models\Solicitud;
use App\Models\User;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Solicitudes')]
class Solicitudes extends Component
{
    /**
     * Assign, reassign, or unassign the responsable in charge of a solicitud.
     */
    public function asignar(Solicitud $solicitud, string $responsableId): void
    {
        if ($responsableId === '') {
            if ($solicitud->estatus === SolicitudEstatus::Asignada) {
                $solicitud->responsable_id = null;
                $solicitud->estatus = SolicitudEstatus::Recibida;
                $solicitud->save();

                Flux::toast(text: __('Solicitud sin asignar.'));

                unset($this->solicitudes);
            }

            return;
        }

        $responsable = User::query()->where('role', UserRole::Responsable)->findOrFail($responsableId);

        $solicitud->responsable_id = $responsable->id;

        if ($solicitud->estatus === SolicitudEstatus::Recibida) {
            $solicitud->estatus = SolicitudEstatus::Asignada;
        }

        $solicitud->save();

        Flux::toast(variant: 'success', text: __('Solicitud asignada.'));

        unset($this->solicitudes);
    }

    /**
     * Permanently delete a solicitud: its files and every related record.
     */
    public function eliminar(Solicitud $solicitud, DeleteSolicitud $deleteSolicitud): void
    {
        $folio = $solicitud->folio;
        $modalName = "eliminar-solicitud-{$solicitud->id}";

        $deleteSolicitud->handle($solicitud);

        Flux::modal($modalName)->close();
        Flux::toast(variant: 'success', text: __('Solicitud :folio eliminada.', ['folio' => $folio]));

        unset($this->solicitudes);
    }

    /**
     * @return Collection<int, Solicitud>
     */
    #[Computed]
    public function solicitudes(): Collection
    {
        return Solicitud::query()
            ->with(['solicitante', 'institucion', 'ejercicioFiscal', 'responsable'])
            ->latest()
            ->get();
    }

    /**
     * @return Collection<int, User>
     */
    #[Computed]
    public function responsables(): Collection
    {
        return User::query()->where('role', UserRole::Responsable)->orderBy('name')->get();
    }
}
