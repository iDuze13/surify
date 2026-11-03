<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificacionController extends Controller
{
    /**
     * Devuelve las últimas notificaciones del usuario autenticado (para la campanita).
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $notificaciones = $user->notifications()
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($n) {
                return [
                    'id'      => $n->id,
                    'titulo'  => $n->data['titulo'] ?? '',
                    'mensaje' => $n->data['mensaje'] ?? '',
                    'url'     => $n->data['url'] ?? '#',
                    'icono'   => $n->data['icono'] ?? 'notifications',
                    'leida'   => !is_null($n->read_at),
                    'fecha'   => $n->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'notificaciones' => $notificaciones,
            'no_leidas'      => $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * Marca una notificación puntual como leída.
     */
    public function marcarLeida(Request $request, string $id)
    {
        $notificacion = Auth::user()->notifications()->findOrFail($id);
        $notificacion->markAsRead();

        return response()->json(['ok' => true]);
    }

    /**
     * Marca todas las notificaciones no leídas del usuario como leídas.
     */
    public function marcarTodasLeidas(Request $request)
    {
        Auth::user()->unreadNotifications->markAsRead();

        return response()->json(['ok' => true]);
    }
}