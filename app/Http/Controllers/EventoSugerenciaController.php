<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Notifications\NuevaSugerenciaEvento;
use App\Notifications\SugerenciaEventoRecibida;
use App\Services\NotificacionService;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EventoSugerenciaController extends Controller
{
    public function __construct(protected CloudinaryService $cloudinary) {}

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre'       => 'required|string|max:255',
            'tipo'         => 'required|string|max:100',
            'provincia_id' => 'required|exists:provincias,id',
            'ubicacion'    => 'nullable|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'nullable|date|after_or_equal:fecha_inicio',
            'rango_precio' => 'nullable|string|max:100',
            'descripcion'  => 'required|string|max:2000',
            'imagen'       => 'nullable|image|max:5120',
        ]);

        // Si hay errores, volvemos siempre a /eventos (con el modal abierto y los datos cargados)
        if ($validator->fails()) {
            return redirect()->route('eventos.index')
                ->withErrors($validator, 'sugerencia')
                ->withInput();
        }

        $imagenUrl = null;
        if ($request->hasFile('imagen')) {
            $imagenUrl = $this->cloudinary->subirImagen(
                $request->file('imagen')->getRealPath(),
                'surify/sugerencias/eventos'
            );
        }

        $evento = Evento::create([
            'nombre'       => $request->nombre,
            'tipo'         => $request->tipo,
            'provincia_id' => $request->provincia_id,
            'ubicacion'    => $request->ubicacion,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin'    => $request->fecha_fin ?: null,
            'rango_precio' => $request->rango_precio,
            'descripcion'  => $request->descripcion,
            'imagen_url'   => $imagenUrl,
            'activo'       => false,           // queda pendiente hasta que un admin lo apruebe
            'sugerido_por' => auth()->id(),
        ]);

        // Aviso en la campanita: "¡Gracias por tu sugerencia!"
        NotificacionService::enviar(auth()->user(), new SugerenciaEventoRecibida($evento));

        // Aviso a los administradores: hay una sugerencia para revisar
        NotificacionService::enviar(
            NotificacionService::moderadoresDeEventos()->where('id', '!=', auth()->id()),
            new NuevaSugerenciaEvento($evento, auth()->user()->name)
        );

        return redirect()->route('eventos.index')->with('sugerencia_enviada', true);
    }
}