<?php

namespace App\Notifications;

use App\Models\Evento;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

class NuevoEventoFavorito extends Notification
{
    use Queueable;

    public function __construct(protected Evento $evento) {}

    /**
     * Canales por los que se envía esta notificación: in-app (campanita) y mail.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $nombreDestino = $this->evento->destino->nombre ?? 'un destino que seguís';

        return (new MailMessage)
            ->subject('Nuevo evento en ' . $nombreDestino)
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('Se agregó un nuevo evento en ' . $nombreDestino . ', uno de tus destinos favoritos.')
            ->line('"' . $this->evento->nombre . '"')
            ->when($this->evento->fecha_inicio, fn ($mail) => $mail->line(
                'Fecha: ' . Carbon::parse($this->evento->fecha_inicio)->format('d/m/Y')
            ))
            ->action('Ver evento', route('eventos.show', $this->evento->id))
            ->line('Gracias por usar Surify.')
            ->salutation('Saludos, el equipo de Surify');
    }

    public function toArray(object $notifiable): array
    {
        $nombreDestino = $this->evento->destino->nombre ?? null;

        return [
            'titulo'   => 'Nuevo evento' . ($nombreDestino ? ' en ' . $nombreDestino : ''),
            'mensaje'  => $this->evento->nombre,
            'url'      => route('eventos.show', $this->evento->id),
            'icono'    => 'celebration',
        ];
    }
}