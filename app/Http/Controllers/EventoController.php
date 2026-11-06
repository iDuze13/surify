<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventoController extends Controller
{
    /**
     * Muestra la lista de todos los eventos.
     */
    public function index(Request $request)
    {
        $query = Evento::with(['destino', 'provincia'])->orderBy('fecha_inicio', 'asc');

        if ($request->filled('provincia_id')) {
            $query->where('provincia_id', $request->provincia_id);
        }

        $año = $request->get('año', now()->year);
        $mes = $request->get('mes', now()->month);

        $eventos = $query->whereYear('fecha_inicio', $año)
            ->whereMonth('fecha_inicio', $mes)
            ->get();

        $provincias = \App\Models\Provincia::orderBy('nombre')->get();

        return view('eventos.index', compact('eventos', 'provincias', 'año', 'mes'));
    }

    /**
     * Muestra la ficha detallada de un evento específico.
     */
    public function show(int $id)
    {
        $evento = Evento::with(['provincia', 'destino', 'resenas.user'])->findOrFail($id);

        $eventosRelacionados = Evento::with('provincia')
            ->where('id', '!=', $id)
            ->where('provincia_id', $evento->provincia_id)
            ->limit(3)
            ->get();

        $esVisitado = false;
        $esFavorito = false;

        if (Auth::check()) {
            $esVisitado = \App\Models\EventoVisitado::where('user_id', Auth::id())
                ->where('evento_id', $evento->id)
                ->exists();

            $esFavorito = \App\Models\Favorito::where('user_id', Auth::id())
                ->where('evento_id', $evento->id)
                ->exists();
        }

        return view('eventos.show', compact('evento', 'eventosRelacionados', 'esVisitado', 'esFavorito'));
    }
}
