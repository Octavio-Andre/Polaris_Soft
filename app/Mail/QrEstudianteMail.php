<?php

namespace App\Mail;

use App\Models\Estudiante;
use App\Models\Examen;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** Correo al estudiante habilitado con el enlace a su código QR para ingresar al examen. */
class QrEstudianteMail extends Mailable
{
    public function __construct(
        public Estudiante $estudiante,
        public Examen $examen,
        public string $enlace,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'SCIEM · Tu código QR para el examen');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.qr-estudiante');
    }
}
