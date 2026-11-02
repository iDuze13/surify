<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Destino;
use App\Models\Provincia;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DestinoController extends Controller
{
    public function __construct(protected CloudinaryService $cloudinary) {}

    public function index(Request $request)
    {
        $query = Destino::with('provincia')->latest();

        if ($request->filled('buscar')) {
            $query->where('nombre', 'ilike', '%' . $request->buscar . '%');
        }

        if ($request->filled('provincia_id')) {
            $query->where('provincia_id', $request->provincia_id);
        }

        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        if ($request->filled('precio')) {
            $query->where('rango_precio', $request->precio);
        }

        if ($request->filled('estado')) {
            $query->where('activo', $request->estado === 'activo');
        }

        $destinos = $query->paginate(12)->withQueryString();
        $provincias = Provincia::orderBy('nombre')->get();
        $categorias = Destino::select('categoria')->distinct()->whereNotNull('categoria')->pluck('categoria');

        return view('admin.destinos.index', compact('destinos', 'provincias', 'categorias'));
    }

    public function create()
    {
        $provincias = Provincia::orderBy('nombre')->get();
        return view('admin.destinos.create', compact('provincias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'       => 'required|string|max:255',
            'provincia_id' => 'required|exists:provincias,id',
            'descripcion'  => 'nullable|string',
            'categoria'    => 'nullable|string|max:255',
            'rango_precio' => 'required|string|max:255',
            'imagen'       => 'nullable|image|max:5120',
            'latitud'      => 'nullable|numeric|between:-90,90',
            'longitud'     => 'nullable|numeric|between:-180,180',
        ]);

        $imagenUrl = null;
        if ($request->hasFile('imagen')) {
            $imagenUrl = $this->cloudinary->subirImagen(
                $request->file('imagen')->getRealPath(),
                'surify/admin/destinos'
            );
        }

        $destino = Destino::create([
            'nombre'       => $request->nombre,
            'provincia_id' => $request->provincia_id,
            'descripcion'  => $request->descripcion,
            'categoria'    => $request->categoria,
            'rango_precio' => $request->rango_precio,
            'imagen_url'   => $imagenUrl,
            'activo'       => $request->has('activo'),
        ]);

        // Guardar ubicación PostGIS si se ingresaron coordenadas
        if ($request->filled('latitud') && $request->filled('longitud')) {
            DB::statement(
                'UPDATE destinos SET ubicacion = ST_SetSRID(ST_MakePoint(?, ?), 4326) WHERE id = ?',
                [$request->longitud, $request->latitud, $destino->id]
            );
        }

        return redirect()->route('admin.destinos.index')
            ->with('success', 'Destino creado correctamente.');
    }

    public function update(Request $request, Destino $destino)
    {
        $request->validate([
            'nombre'       => 'required|string|max:255',
            'provincia_id' => 'required|exists:provincias,id',
            'descripcion'  => 'nullable|string',
            'categoria'    => 'nullable|string|max:255',
            'rango_precio' => 'required|string|max:255',
            'imagen'       => 'nullable|image|max:5120',
            'latitud'      => 'nullable|numeric|between:-90,90',
            'longitud'     => 'nullable|numeric|between:-180,180',
        ]);

        $imagenUrl = $destino->imagen_url;
        if ($request->hasFile('imagen')) {
            $imagenAnterior = $destino->imagen_url;

            $imagenUrl = $this->cloudinary->subirImagen(
                $request->file('imagen')->getRealPath(),
                'surify/admin/destinos'
            );

            if ($imagenAnterior) {
                $publicId = $this->extraerPublicId($imagenAnterior);
                if ($publicId) {
                    $this->cloudinary->eliminarImagen($publicId);
                }
            }
        }

        $destino->update([
            'nombre'       => $request->nombre,
            'provincia_id' => $request->provincia_id,
            'descripcion'  => $request->descripcion,
            'categoria'    => $request->categoria,
            'rango_precio' => $request->rango_precio,
            'imagen_url'   => $imagenUrl,
            'activo'       => $request->has('activo'),
        ]);

        if ($request->filled('latitud') && $request->filled('longitud')) {
            DB::statement(
                'UPDATE destinos SET ubicacion = ST_SetSRID(ST_MakePoint(?, ?), 4326) WHERE id = ?',
                [$request->longitud, $request->latitud, $destino->id]
            );
        }

        return redirect()->route('admin.destinos.index')
            ->with('success', 'Destino actualizado correctamente.');
    }

    public function edit(Destino $destino)
    {
        $provincias = Provincia::orderBy('nombre')->get();
        return view('admin.destinos.edit', compact('destino', 'provincias'));
    }

    public function destroy(Destino $destino)
    {
        $destino->delete();
        return redirect()->route('admin.destinos.index')
            ->with('success', 'Destino eliminado correctamente.');
    }

    private function extraerPublicId(string $url): ?string
    {
        if (preg_match('#/upload/(?:v\d+/)?(.+)\.\w+$#', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }
}