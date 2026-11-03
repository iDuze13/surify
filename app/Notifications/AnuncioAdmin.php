<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AnuncioAdmin extends Notification
{
    use Queueable;

    public function __construct(
        protected string $titulo,
        protected string $mensaje,
        protected ?string $url = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->titulo)
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line($this->mensaje);

        if ($this->url) {
            $mail->action('Ver más', $this->url);
        }

                return $mail->line('Gracias por usar Surify.')->salutation('Saludos, el equipo de Surify');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'titulo'  => $this->titulo,
            'mensaje' => $this->mensaje,
            'url'     => $this->url ?? '#',
            'icono'   => 'campaign',
        ];
    }
}