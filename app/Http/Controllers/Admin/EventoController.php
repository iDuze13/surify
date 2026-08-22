<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use App\Models\Provincia;
use App\Models\Destino;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventoController extends Controller
{
    public function index(Request $request)
    {
        $filtro = $request->get('filtro', 'todos');

        $query = Evento::with(['provincia', 'destino'])->orderBy('fecha_inicio', 'asc');

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

        $countTodos    = Evento::count();
        $countProximos = Evento::get()->filter(fn($e) => !$e->pasado)->count();
        $countPasados  = Evento::get()->filter(fn($e) => $e->pasado)->count();
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
            'imagen_file'  => 'nullable|image|max:10240',
            'imagen_url'   => 'nullable|string|max:2048',
        ]);

        // Si seleccionó destino, tomamos su provincia automáticamente
        $provinciaId = $request->provincia_id;
        if ($request->destino_id) {
            $destino = Destino::find($request->destino_id);
            $provinciaId = $destino?->provincia_id ?? $provinciaId;
        }

        // Imagen: prioridad archivo subido, luego URL ingresada
        $rutaImagen = null;
        if ($request->hasFile('imagen_file')) {
            $rutaImagen = $request->file('imagen_file')->store('eventos', 'public');
        } elseif ($request->filled('imagen_url')) {
            $rutaImagen = $request->imagen_url;
        }

        Evento::create([
            'nombre'       => $request->nombre,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin'    => $request->fecha_fin ?: null,
            'provincia_id' => $provinciaId,
            'destino_id'   => $request->destino_id ?: null,
            'tipo'         => $request->tipo,
            'rango_precio' => $request->rango_precio,
            'imagen_url'   => $rutaImagen,
            'descripcion'  => $request->descripcion,
            'activo'       => $request->has('activo'),
        ]);

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
        ]);

        $provinciaId = $request->provincia_id;
        if ($request->destino_id) {
            $destino = Destino::find($request->destino_id);
            $provinciaId = $destino?->provincia_id ?? $provinciaId;
        }

        $rutaImagen = $evento->imagen_url;
        if ($request->hasFile('imagen')) {
            if ($evento->imagen_url && !str_starts_with($evento->imagen_url, 'http') && Storage::disk('public')->exists($evento->imagen_url)) {
                Storage::disk('public')->delete($evento->imagen_url);
            }
            $rutaImagen = $request->file('imagen')->store('eventos', 'public');
        }

        $evento->update([
            'nombre'       => $request->nombre,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin'    => $request->fecha_fin ?: null,
            'provincia_id' => $provinciaId,
            'destino_id'   => $request->destino_id ?: null,
            'tipo'         => $request->tipo,
            'rango_precio' => $request->rango_precio,
            'imagen_url'   => $rutaImagen,
            'descripcion'  => $request->descripcion,
            'activo'       => $request->has('activo'),
        ]);

        return redirect()->route('admin.eventos.index')
            ->with('success', 'Evento actualizado correctamente.');
    }

    public function destroy(Evento $evento)
    {
        if ($evento->imagen_url && !str_starts_with($evento->imagen_url, 'http') && Storage::disk('public')->exists($evento->imagen_url)) {
            Storage::disk('public')->delete($evento->imagen_url);
        }

        $evento->delete();
        return redirect()->route('admin.eventos.index')
            ->with('success', 'Evento eliminado correctamente.');
    }
}