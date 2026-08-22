<?php

namespace App\Http\Controllers;

use App\Models\Resena;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ResenaController extends Controller
{
    /**
     * Guarda una nueva reseña en la base de datos.
     */
    public function store(Request $request)
    {
        // Validación de datos recibidos
        $validated = $request->validate([
            'destino_id' => 'nullable|exists:destinos,id',
            'evento_id' => 'nullable|exists:eventos,id',
            'calificacion' => 'required|integer|min:1|max:5',
            'comentario' => 'required|string|min:5|max:1000',
            'imagenes' => 'nullable|array|max:5',
            'imagenes.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Aseguramos que el usuario esté autenticado
        if (!Auth::check()) {
            return redirect()->back()->with('error', 'Debes iniciar sesión para dejar una reseña.');
        }

        $user = Auth::user();

        // Creamos la reseña
        $resena = new Resena();
        $resena->user_id = $user->id;
        $resena->destino_id = $validated['destino_id'] ?? null;
        $resena->evento_id = $validated['evento_id'] ?? null;
        $resena->calificacion = $validated['calificacion'];
        $resena->comentario = $validated['comentario'];

        $resena->save();

        // Si se subieron imágenes, las procesamos y guardamos en la tabla relacional
        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $file) {
                $path = $file->store('resenas', 'public');
                $resena->imagenes()->create([
                    'url' => Storage::url($path)
                ]);
            }
        }

        return redirect()->back()->with('success', '¡Gracias por compartir tu experiencia!');
    }
    public function update(Request $request, Resena $resena)
    {
        // Solo el autor puede editar su reseña
        if ($resena->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'No tenés permiso para editar esta reseña.');
        }

        $validated = $request->validate([
            'calificacion' => 'required|integer|min:1|max:5',
            'comentario'   => 'required|string|min:5|max:1000',
        ]);

        $resena->update($validated);

        return redirect()->back()->with('success', 'Reseña actualizada correctamente.');
    }

    public function destroy(Resena $resena)
    {
        // Solo el autor puede eliminar su reseña
        if ($resena->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'No tenés permiso para eliminar esta reseña.');
        }

        // Eliminar imágenes asociadas
        foreach ($resena->imagenes as $imagen) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $imagen->url));
            $imagen->delete();
        }

        $resena->delete();

        return redirect()->back()->with('success', 'Reseña eliminada correctamente.');
    }
}
