<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Notifications\NuevaSugerenciaEvento;
use App\Notifications\SugerenciaEventoRecibida;
use App\Services\NotificacionService;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class EventoSugerenciaController extends Controller
{
    public function __construct(protected CloudinaryService $cloudinary) {}

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre'       => 'required|string|max:255',
            'tipo'         => 'required|string|max:100',
            'provincia_id' => 'required|exists:provincias,id',
            // Lugar: se elige de los destinos ya cargados (y tiene que ser de la provincia elegida)
            'destino_id'   => [
                'nullable',
                Rule::exists('destinos', 'id')->where('provincia_id', $request->provincia_id),
            ],
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'nullable|date|after_or_equal:fecha_inicio',
            'rango_precio' => 'nullable|string|max:100',
            'descripcion'  => 'required|string|max:2000',
            'imagen'       => 'nullable|image|max:5120',
        ], [
            'fecha_fin.after_or_equal' => 'La fecha de fin no puede ser anterior a la fecha de inicio.',
        ]);

        // Si hay errores, vuelve a la página desde donde se sugirió (calendario o destino)
        // con el modal abierto y los datos cargados
        if ($validator->fails()) {
            return back()
                ->withErrors($validator, 'sugerencia')
                ->withInput();
        }

        $imagenUrl = null;
        if ($request->hasFile('imagen')) {
            try {
                $imagenUrl = $this->cloudinary->subirImagen(
                    $request->file('imagen')->getRealPath(),
                    'surify/sugerencias/eventos'
                );
            } catch (\Throwable $e) {
                // Si Cloudinary falla, no se pierde todo lo que escribió el usuario
                Log::warning('No se pudo subir la imagen de una sugerencia de evento: ' . $e->getMessage());

                return back()
                    ->withErrors(['imagen' => 'No pudimos subir la imagen. Probá con otra o enviá la sugerencia sin imagen.'], 'sugerencia')
                    ->withInput();
            }
        }

        $evento = Evento::create([
            'nombre'       => $request->nombre,
            'tipo'         => $request->tipo,
            'provincia_id' => $request->provincia_id,
            'destino_id'   => $request->destino_id ?: null,
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

        return back()->with('sugerencia_enviada', true);
    }
}