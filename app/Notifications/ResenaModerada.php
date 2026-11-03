<?php

namespace App\Notifications;

use App\Models\Resena;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Avisa al autor de una reseña que un administrador la aprobó o la rechazó.
 */
class ResenaModerada extends Notification
{
    use Queueable;

    protected bool $aprobada;
    protected string $sitio;
    protected string $url;

    public function __construct(Resena $resena, bool $aprobada)
    {
        $this->aprobada = $aprobada;

        $this->sitio = $resena->destino?->nombre
            ?? $resena->evento?->nombre
            ?? 'Surify';

        if ($resena->destino_id) {
            $this->url = route('destinos.show', ['id' => $resena->destino_id]);
        } elseif ($resena->evento_id) {
            $this->url = route('eventos.show', $resena->evento_id);
        } else {
            $this->url = url('/');
        }
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->titulo())
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line($this->mensaje());

        if ($this->aprobada) {
            $mail->action('Ver reseña', $this->url);
        }

        return $mail
            ->line('Gracias por usar Surify.')
            ->salutation('Saludos, el equipo de Surify');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'titulo'  => $this->titulo(),
            'mensaje' => $this->mensaje(),
            'url'     => $this->url,
            'icono'   => $this->aprobada ? 'check_circle' : 'cancel',
        ];
    }

    protected function titulo(): string
    {
        return $this->aprobada
            ? 'Tu reseña fue aprobada'
            : 'Tu reseña no fue aprobada';
    }

    protected function mensaje(): string
    {
        return $this->aprobada
            ? 'Tu reseña sobre ' . $this->sitio . ' ya es visible para todos los viajeros.'
            : 'Tu reseña sobre ' . $this->sitio . ' no cumplió con las normas de la comunidad y no se publicará.';
    }
}