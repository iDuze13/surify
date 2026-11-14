<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Destino;
use App\Models\Evento;
use App\Models\Provincia;
use App\Models\User;
use App\Notifications\NuevoEventoFavorito;
use App\Notifications\SugerenciaEventoRespondida;
use App\Services\NotificacionService;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;

class EventoController extends Controller
{
    public function __construct(protected CloudinaryService $cloudinary) {}

    public function index(Request $request)
    {
        $filtro = $request->get('filtro', 'todos');

        // Las sugerencias rechazadas no se listan acá: quedan en Sugerencias → Rechazadas
        $query = Evento::with(['provincia', 'destino'])->whereNull('rechazado_at')->orderBy('fecha_inicio', 'asc');

        if ($request->filled('buscar')) {
            $query->where('nombre', 'ilike', '%' . $request->buscar . '%');
        }

        if ($request->filled('provincia_id')) {
            $query->where('provincia_id', $request->provincia_id);
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        $eventos = $query->get();

        if ($filtro === 'proximos') {
            $eventos = $eventos->filter(fn($e) => !$e->pasado);
        } elseif ($filtro === 'pasados') {
            $eventos = $eventos->filter(fn($e) => $e->pasado);
        }

        $todos         = Evento::whereNull('rechazado_at')->get();
        $countTodos    = $todos->count();
        $countProximos = $todos->filter(fn($e) => !$e->pasado)->count();
        $countPasados  = $todos->filter(fn($e) => $e->pasado)->count();
        $provincias    = Provincia::orderBy('nombre')->get();

        return view('admin.eventos.index', compact(
            'eventos', 'filtro', 'countTodos', 'countProximos', 'countPasados', 'provincias'
        ));
    }

    public function create()
    {
        $destinos  = Destino::orderBy('nombre')->get();
        $provincias = Provincia::orderBy('nombre')->get();
        return view('admin.eventos.create', compact('destinos', 'provincias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'       => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'nullable|date|after_or_equal:fecha_inicio',
            'provincia_id' => 'nullable|exists:provincias,id',
            'destino_id'   => 'nullable|exists:destinos,id',
            'tipo'         => 'nullable|string|max:255',
            'rango_precio' => 'nullable|string|max:255',
            'descripcion'  => 'nullable|string',
            'imagen'       => 'nullable|image|max:10240',
        ], [
            'fecha_fin.after_or_equal' => 'La fecha de fin no puede ser anterior a la fecha de inicio.',
        ]);

        // Si seleccionó destino, tomamos su provincia automáticamente
        $provinciaId = $request->provincia_id;
        if ($request->destino_id) {
            $destino = Destino::find($request->destino_id);
            $provinciaId = $destino?->provincia_id ?? $provinciaId;
        }

        $imagenUrl = null;
        if ($request->hasFile('imagen')) {
            $imagenUrl = $this->cloudinary->subirImagen(
                $request->file('imagen')->getRealPath(),
                'surify/admin/eventos'
            );
        }

        $evento = Evento::create([
            'nombre'       => $request->nombre,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin'    => $request->fecha_fin ?: null,
            'provincia_id' => $provinciaId,
            'destino_id'   => $request->destino_id ?: null,
            'tipo'         => $request->tipo,
            'rango_precio' => $request->rango_precio,
            'imagen_url'   => $imagenUrl,
            'descripcion'  => $request->descripcion,
            'activo'       => $request->has('activo'),
        ]);

        // Si queda publicado, avisamos a quienes tienen el destino en favoritos
        if ($evento->activo) {
            $this->avisarFavoritos($evento);
        }

        return redirect()->route('admin.eventos.index')
            ->with('success', 'Evento creado correctamente.');
    }

    public function edit(Evento $evento)
    {
        $destinos   = Destino::orderBy('nombre')->get();
        $provincias = Provincia::orderBy('nombre')->get();
        return view('admin.eventos.edit', compact('evento', 'destinos', 'provincias'));
    }

    public function update(Request $request, Evento $evento)
    {
        $estabaActivo = (bool) $evento->activo;

        $request->validate([
            'nombre'       => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'nullable|date|after_or_equal:fecha_inicio',
            'provincia_id' => 'nullable|exists:provincias,id',
            'destino_id'   => 'nullable|exists:destinos,id',
            'tipo'         => 'nullable|string|max:255',
            'rango_precio' => 'nullable|string|max:255',
            'descripcion'  => 'nullable|string',
            'imagen'       => 'nullable|image|max:10240',
        ], [
            'fecha_fin.after_or_equal' => 'La fecha de fin no puede ser anterior a la fecha de inicio.',
        ]);

        $provinciaId = $request->provincia_id;
        if ($request->destino_id) {
            $destino = Destino::find($request->destino_id);
            $provinciaId = $destino?->provincia_id ?? $provinciaId;
        }

        $imagenUrl = $evento->imagen_url;
        if ($request->hasFile('imagen')) {
            $imagenAnterior = $evento->imagen_url;

            $imagenUrl = $this->cloudinary->subirImagen(
                $request->file('imagen')->getRealPath(),
                'surify/admin/eventos'
            );

            // Solo intentamos borrar de Cloudinary; si la imagen anterior era
            // una ruta vieja del storage local, no hay nada que borrar ahí.
            if ($imagenAnterior && str_starts_with($imagenAnterior, 'http')) {
                $publicId = $this->extraerPublicId($imagenAnterior);
                if ($publicId) {
                    $this->cloudinary->eliminarImagen($publicId);
                }
            }
        }

        $evento->update([
            'nombre'       => $request->nombre,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin'    => $request->fecha_fin ?: null,
            'provincia_id' => $provinciaId,
            'destino_id'   => $request->destino_id ?: null,
            'tipo'         => $request->tipo,
            'rango_precio' => $request->rango_precio,
            'imagen_url'   => $imagenUrl,
            'descripcion'  => $request->descripcion,
            'activo'       => $request->has('activo'),
        ]);

        // Si se publicó recién ahora (por ejemplo, una sugerencia aprobada desde Editar)
        if (!$estabaActivo && $evento->activo) {
            $this->marcarRevisado($evento);
            $this->alPublicar($evento);
        }

        return redirect()->route('admin.eventos.index')
            ->with('success', 'Evento actualizado correctamente.');
    }

    /**
     * Botón ✓ del listado: publica un evento pendiente sin tocar el resto de sus datos.
     */
    public function aprobar(Evento $evento)
    {
        if (!$evento->activo) {
            $evento->update(['activo' => true]);
            $this->marcarRevisado($evento);
            $this->alPublicar($evento);
        }

        // Vuelve a la pantalla desde donde se aprobó (listado de eventos o Sugerencias)
        return back()->with('success', 'Evento aprobado y publicado.');
    }

    /**
     * Sección "Sugerencias de festivales": lo que mandan los usuarios, por estado.
     */
    public function sugerencias(Request $request)
    {
        $estado = in_array($request->get('estado'), ['pendiente', 'aprobada', 'rechazada'])
            ? $request->get('estado')
            : 'pendiente';

        $sugerencias = Evento::sugerencias($estado)
            ->with(['provincia', 'destino', 'autorSugerencia', 'revisor'])
            ->orderByDesc($estado === 'pendiente' ? 'created_at' : 'revisado_at')
            ->paginate(10)
            ->withQueryString();

        $countPendientes = Evento::sugerencias('pendiente')->count();
        $countAprobadas  = Evento::sugerencias('aprobada')->count();
        $countRechazadas = Evento::sugerencias('rechazada')->count();

        return view('admin.eventos.sugerencias', compact(
            'sugerencias', 'estado', 'countPendientes', 'countAprobadas', 'countRechazadas'
        ));
    }

    /**
     * Rechaza una sugerencia: no se publica, queda guardada en "Rechazadas"
     * y se le avisa al usuario (con el motivo, si se escribió uno).
     */
    public function rechazar(Request $request, Evento $evento)
    {
        $request->validate([
            'motivo' => 'nullable|string|max:500',
        ]);

        if (!$evento->sugerido_por || $evento->activo || $evento->rechazado_at) {
            return back()->with('error', 'Esta sugerencia ya fue revisada.');
        }

        $evento->update([
            'rechazado_at'   => now(),
            'motivo_rechazo' => $request->motivo ?: null,
        ]);
        $this->marcarRevisado($evento);

        NotificacionService::enviar(
            User::find($evento->sugerido_por),
            new SugerenciaEventoRespondida($evento->nombre, false, null, $evento->motivo_rechazo)
        );

        return back()->with('success', 'Sugerencia rechazada. Le avisamos al usuario.');
    }

    /**
     * Vuelve a poner como pendiente una sugerencia rechazada por error.
     */
    public function restaurar(Evento $evento)
    {
        if ($evento->sugerido_por && $evento->rechazado_at) {
            $evento->update([
                'rechazado_at'   => null,
                'motivo_rechazo' => null,
                'revisado_por'   => null,
                'revisado_at'    => null,
            ]);
        }

        return back()->with('success', 'La sugerencia volvió a Pendientes.');
    }

    private function marcarRevisado(Evento $evento): void
    {
        if ($evento->sugerido_por) {
            $evento->update([
                'revisado_por'   => auth()->id(),
                'revisado_at'    => now(),
                'rechazado_at'   => null,
                'motivo_rechazo' => null,
            ]);
        }
    }

    /**
     * Avisos cuando un evento pasa de pendiente a publicado:
     * al usuario que lo sugirió y a quienes tienen el destino en favoritos.
     */
    private function alPublicar(Evento $evento): void
    {
        if ($evento->sugerido_por) {
            NotificacionService::enviar(
                User::find($evento->sugerido_por),
                new SugerenciaEventoRespondida($evento->nombre, true, $evento->id)
            );
        }

        $this->avisarFavoritos($evento);
    }

    private function avisarFavoritos(Evento $evento): void
    {
        if (!$evento->destino_id) {
            return;
        }

        $usuarios = User::whereHas('favoritos', fn ($q) => $q->where('destino_id', $evento->destino_id))->get();

        NotificacionService::enviar($usuarios, new NuevoEventoFavorito($evento->load('destino')));
    }

    public function destroy(Evento $evento)
    {
        if ($evento->imagen_url && str_starts_with($evento->imagen_url, 'http')) {
            $publicId = $this->extraerPublicId($evento->imagen_url);
            if ($publicId) {
                $this->cloudinary->eliminarImagen($publicId);
            }
        }

        // Si era una sugerencia que todavía no se había publicado, se toma como rechazada
        if (!$evento->activo && $evento->sugerido_por) {
            NotificacionService::enviar(
                User::find($evento->sugerido_por),
                new SugerenciaEventoRespondida($evento->nombre, false)
            );
        }

        $evento->delete();
        return redirect()->route('admin.eventos.index')
            ->with('success', 'Evento eliminado correctamente.');
    }

    private function extraerPublicId(string $url): ?string
    {
        if (preg_match('#/upload/(?:v\d+/)?(.+)\.\w+$#', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }
}