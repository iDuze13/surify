<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gastronomia;
use App\Models\Provincia;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;

class GastronomiaController extends Controller
{
    public function __construct(protected CloudinaryService $cloudinary) {}

    public function index(Request $request)
    {
        $query = Provincia::with(['gastronomia' => function ($q) use ($request) {
            if ($request->filled('buscar')) {
                $q->where('nombre', 'ilike', '%' . $request->buscar . '%');
            }
            if ($request->filled('categoria')) {
                $q->where('categoria', $request->categoria);
            }
        }])->orderBy('nombre');

        if ($request->filled('provincia_id')) {
            $query->where('id', $request->provincia_id);
        }

        $provincias = $query->get();

        return view('admin.gastronomia.index', compact('provincias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'provincia_id' => 'required|exists:provincias,id',
            'nombre'       => 'required|string|max:255',
            'descripcion'  => 'required|string',
            'categoria'    => 'required|string|max:100',
            'imagen'       => 'nullable|image|max:5120',
        ]);

        $imagenUrl = null;
        if ($request->hasFile('imagen')) {
            $imagenUrl = $this->cloudinary->subirImagen(
                $request->file('imagen')->getRealPath(),
                'surify/admin/gastronomia'
            );
        }

        Gastronomia::create([
            'provincia_id' => $request->provincia_id,
            'nombre'       => $request->nombre,
            'descripcion'  => $request->descripcion,
            'categoria'    => $request->categoria,
            'imagen_url'   => $imagenUrl,
        ]);

        return back()->with('success', 'Plato agregado correctamente.');
    }

    public function update(Request $request, Gastronomia $gastronomia)
    {
        $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'required|string',
            'categoria'   => 'required|string|max:100',
            'imagen'      => 'nullable|image|max:5120',
        ]);

        $imagenUrl = $gastronomia->imagen_url;
        if ($request->hasFile('imagen')) {
            $imagenAnterior = $gastronomia->imagen_url;

            $imagenUrl = $this->cloudinary->subirImagen(
                $request->file('imagen')->getRealPath(),
                'surify/admin/gastronomia'
            );

            if ($imagenAnterior) {
                $publicId = $this->extraerPublicId($imagenAnterior);
                if ($publicId) {
                    $this->cloudinary->eliminarImagen($publicId);
                }
            }
        }

        $gastronomia->update([
            'nombre'      => $request->nombre,
            'descripcion' => $request->descripcion,
            'categoria'   => $request->categoria,
            'imagen_url'  => $imagenUrl,
        ]);

        return back()->with('success', 'Plato actualizado correctamente.');
    }

    public function destroy(Gastronomia $gastronomia)
    {
        $gastronomia->delete();

        return back()->with('success', 'Plato eliminado correctamente.');
    }

    private function extraerPublicId(string $url): ?string
    {
        if (preg_match('#/upload/(?:v\d+/)?(.+)\.\w+$#', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }
}