<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * Correo que recibe un usuario cuando el administrador crea su cuenta o le cambia la contraseña.
 * Lleva la contraseña en texto plano (la única vez que existe así), por eso conviene cambiarla al ingresar.
 */
class CredencialesUsuario extends Mailable
{
    public function __construct(
        public User $usuario,
        public string $contrasena,
        public bool $esCambio = false,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->esCambio
                ? 'SCIEM · Se actualizó tu contraseña'
                : 'SCIEM · Tu cuenta fue creada',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.credenciales');
    }
}
