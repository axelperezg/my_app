<?php

namespace App\Mail;

use App\Models\Configuracion;
use App\Models\Solicitud;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Spatie\LaravelPdf\Facades\Pdf;

class SolicitudRecibida extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public Solicitud $solicitud)
    {
        $this->solicitud->loadMissing(['institucion', 'ejercicioFiscal', 'solicitante', 'archivos']);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Acuse de recepción — Folio :folio', ['folio' => $this->solicitud->folio]),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.solicitudes.recibida',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $configuracion = Configuracion::actual();

        return [
            Pdf::view('pdf.solicitudes.acuse', [
                'solicitud' => $this->solicitud,
                'logoIzquierdo' => $configuracion->logoPdfIzquierdoBase64(),
                'logoDerecho' => $configuracion->logoPdfDerechoBase64(),
            ])
                ->name("acuse-{$this->solicitud->folio}.pdf")
                ->toMailAttachment(),
        ];
    }
}
