<?php

namespace App\Notifications;

use App\Models\Evento;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Les avisa a los administradores que llegó una sugerencia de evento para revisar.
 */
class NuevaSugerenciaEvento extends Notification
{
    use Queueable;

    public function __construct(protected Evento $evento, protected string $autor) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'titulo'  => 'Nueva sugerencia de evento',
            'mensaje' => $this->autor . ' sugirió "' . $this->evento->nombre . '". Está pendiente de revisión.',
            'url'     => route('admin.eventos.sugerencias'),
            'icono'   => 'pending_actions',
        ];
    }
}