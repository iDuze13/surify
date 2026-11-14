<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Le avisa a quien sugirió un evento si el equipo lo aprobó o lo rechazó.
 * Recibe el nombre (y no el modelo) porque el evento rechazado no tiene página pública.
 */
class SugerenciaEventoRespondida extends Notification
{
    use Queueable;

    public function __construct(
        protected string $nombreEvento,
        protected bool $aprobada,
        protected ?int $eventoId = null,
        protected ?string $motivo = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        if ($this->aprobada) {
            return [
                'titulo'  => '¡Tu sugerencia fue aprobada!',
                'mensaje' => '"' . $this->nombreEvento . '" ya está publicado en el calendario de Surify. ¡Gracias por colaborar!',
                'url'     => $this->eventoId ? route('eventos.show', $this->eventoId) : route('eventos.index'),
                'icono'   => 'check_circle',
            ];
        }

        return [
            'titulo'  => 'Tu sugerencia no fue aprobada',
            'mensaje' => 'Revisamos "' . $this->nombreEvento . '" y esta vez no lo vamos a publicar.'
                . ($this->motivo ? ' Motivo: ' . $this->motivo : ' ¡Gracias igual por sumar!'),
            'url'     => route('eventos.index'),
            'icono'   => 'cancel',
        ];
    }
}