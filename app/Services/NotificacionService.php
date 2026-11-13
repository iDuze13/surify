<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

/**
 * Envía notificaciones de a un usuario por vez.
 *
 * Si falla el envío a alguien (por ejemplo, el mail), se registra en el log
 * y se sigue con el resto: así un error no deja sin aviso a todos los demás
 * ni corta la acción que disparó la notificación (crear un evento, aprobar
 * una reseña, etc.).
 */
class NotificacionService
{
    /**
     * @param  iterable<User>|User|null  $destinatarios
     * @return int  cantidad de usuarios a los que se les envió sin errores
     */
    public static function enviar(iterable|User|null $destinatarios, Notification $notificacion): int
    {
        if ($destinatarios === null) {
            return 0;
        }

        if ($destinatarios instanceof User) {
            $destinatarios = [$destinatarios];
        }

        $enviadas = 0;

        foreach ($destinatarios as $usuario) {
            try {
                $usuario->notify($notificacion);
                $enviadas++;
            } catch (\Throwable $e) {
                Log::warning('No se pudo enviar ' . class_basename($notificacion) . ' al usuario ' . $usuario->id . ': ' . $e->getMessage());
            }
        }

        return $enviadas;
    }

    /**
     * Usuarios que moderan eventos: administradores y quienes tienen el
     * permiso de administrar eventos sugeridos.
     */
    public static function moderadoresDeEventos()
    {
        return User::whereHas('roles', function ($q) {
            $q->where(function ($w) {
                $w->whereIn('nombre', ['admin', 'Admin'])
                    ->orWhereHas('permisos', fn ($p) => $p->where('nombre', 'administrar_eventos_sugeridos'));
            });
        })->get();
    }
}