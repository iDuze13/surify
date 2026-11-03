<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\AnuncioAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

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

        if ($usuarios->isNotEmpty()) {
            Notification::send($usuarios, new AnuncioAdmin(
                $request->titulo,
                $request->mensaje,
                $request->url ?: null
            ));
        }

        return back()->with('success', 'Anuncio enviado a ' . $usuarios->count() . ' usuarios.');
    }
}