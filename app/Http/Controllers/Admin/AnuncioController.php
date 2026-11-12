<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\AnuncioAdmin;
use Illuminate\Http\Request;
use App\Services\NotificacionService;

class AnuncioController extends Controller
{
    public function create()
    {
        return view('admin.anuncios.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo'  => 'required|string|max:150',
            'mensaje' => 'required|string|max:1000',
            'url'     => 'nullable|string|max:255',
        ]);

        $usuarios = User::all();

        // De a uno: si falla el mail de alguien, el resto lo recibe igual
        $enviados = NotificacionService::enviar($usuarios, new AnuncioAdmin(
            $request->titulo,
            $request->mensaje,
            $request->url ?: null
        ));

        $mensaje = 'Anuncio enviado a ' . $enviados . ' de ' . $usuarios->count() . ' usuarios.';
        if ($enviados < $usuarios->count()) {
            $mensaje .= ' A algunos no les llegó el mail (revisá el log); la notificación en la campanita sí les quedó.';
        }

        return back()->with('success', $mensaje);
    }
}