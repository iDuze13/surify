<?php

namespace App\Http\Controllers;

use App\Models\Provincia;
use App\Models\Destino;
use App\Models\Evento;
use Illuminate\Http\Request;

class BusquedaController extends Controller
{
    public function buscar(Request $request)
    {
        $q = trim($request->get('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json(['resultados' => []]);
        }

        $resultados = collect();

        // Provincias
        Provincia::query()
            ->where('nombre', 'ILIKE', "%{$q}%")
            ->limit(4)
            ->get()
            ->each(function (Provincia $p) use (&$resultados) {
                $resultados->push([
                    'tipo'    => 'Provincia',
                    'nombre'  => $p->nombre,
                    'subtitulo' => $p->region ?? 'Argentina',
                    'icono'   => 'map',
                    'url'     => route('provincia.show', $p->nombre),
                ]);
            });

        // Destinos
        Destino::query()
            ->with('provincia')
            ->where('nombre', 'ILIKE', "%{$q}%")
            ->where('activo', true)
            ->limit(5)
            ->get()
            ->each(function (Destino $d) use (&$resultados) {
                $resultados->push([
                    'tipo'      => 'Destino',
                    'nombre'    => $d->nombre,
                    'subtitulo' => $d->provincia->nombre ?? '',
                    'icono'     => 'landscape',
                    'url'       => route('destinos.show', $d->id),
                ]);
            });

        // Eventos / festivales
        Evento::query()
            ->where('activo', true)
            ->with('provincia')
            ->where('nombre', 'ILIKE', "%{$q}%")
            ->limit(5)
            ->get()
            ->each(function (Evento $e) use (&$resultados) {
                $resultados->push([
                    'tipo'      => 'Evento',
                    'nombre'    => $e->nombre,
                    'subtitulo' => $e->provincia->nombre ?? '',
                    'icono'     => 'celebration',
                    'url'       => route('eventos.show', $e->id),
                ]);
            });

        return response()->json(['resultados' => $resultados->values()]);
    }
}
