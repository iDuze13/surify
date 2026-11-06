<?php

namespace App\Notifications;

use App\Models\Evento;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SugerenciaEventoRecibida extends Notification
{
    use Queueable;

    public function __construct(protected Evento $evento) {}

    /**
     * Solo campanita: si el mail falla, no debe afectar el envío de la sugerencia.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'titulo'  => '¡Gracias por tu sugerencia!',
            'mensaje' => '"' . $this->evento->nombre . '" está siendo verificada por el equipo de Surify. Te avisaremos cuando tengamos una respuesta.',
            'url'     => route('eventos.index'),
            'icono'   => 'volunteer_activism',
        ];
    }
}