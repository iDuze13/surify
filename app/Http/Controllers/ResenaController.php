<?php

namespace App\Http\Controllers;

use App\Jobs\ModerarResenaJob;
use App\Models\Resena;
use App\Services\CloudinaryService;
use App\Services\ModeracionResenaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResenaController extends Controller
{
    public function __construct(protected CloudinaryService $cloudinary) {}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'destino_id' => 'nullable|exists:destinos,id',
            'evento_id' => 'nullable|exists:eventos,id',
            'calificacion' => 'required|integer|min:1|max:5',
            'titulo' => 'nullable|string|max:150',
            'comentario' => 'required|string|min:5|max:1000',
            'anonima' => 'nullable|boolean',
            'imagenes' => 'nullable|array|max:5',
            'imagenes.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if (!Auth::check()) {
            return redirect()->back()->with('error', 'Debes iniciar sesión para dejar una reseña.');
        }

        $user = Auth::user();

        $resena = new Resena();
        $resena->user_id = $user->id;
        $resena->destino_id = $validated['destino_id'] ?? null;
        $resena->evento_id = $validated['evento_id'] ?? null;
        $resena->calificacion = $validated['calificacion'];
        $resena->titulo = $validated['titulo'] ?? null;
        $resena->comentario = $validated['comentario'];
        // El formulario manda anonima=1 solo cuando la casilla está tildada
        $resena->anonima = $request->boolean('anonima');
        $resena->save(); // aprobada queda en null = pendiente

        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $file) {
                $url = $this->cloudinary->subirImagen($file->getRealPath(), 'surify/resenas');
                $resena->imagenes()->create([
                    'url' => $url,
                ]);
            }
        }

        // Moderación automática (texto + fotos). Corre después de responderle a la persona.
        if (ModeracionResenaService::habilitada()) {
            ModerarResenaJob::dispatchAfterResponse($resena->id);
        }

        return redirect()->back()->with('success', '¡Gracias por compartir tu experiencia! Tu reseña se publicará en cuanto sea revisada.');
    }

    public function update(Request $request, Resena $resena)
    {
        if ($resena->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'No tenés permiso para editar esta reseña.');
        }

        $validated = $request->validate([
            'calificacion' => 'required|integer|min:1|max:5',
            'comentario'   => 'required|string|min:5|max:1000',
        ]);

        // Al editar, la reseña vuelve a moderarse: si no, alguien podría publicar
        // algo correcto, esperar la aprobación y después cambiarlo por spam.
        $resena->update($validated + [
            'aprobada' => null,
            'moderacion_motivo' => null,
            'moderada_por' => null,
            'moderada_at' => null,
        ]);

        if (ModeracionResenaService::habilitada()) {
            ModerarResenaJob::dispatchAfterResponse($resena->id);
        }

        return redirect()->back()->with('success', 'Reseña actualizada. Volverá a publicarse cuando sea revisada.');
    }

    public function destroy(Resena $resena)
    {
        if ($resena->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'No tenés permiso para eliminar esta reseña.');
        }

        foreach ($resena->imagenes as $imagen) {
            $publicId = $this->extraerPublicId($imagen->url);
            if ($publicId) {
                $this->cloudinary->eliminarImagen($publicId);
            }
            $imagen->delete();
        }

        $resena->delete();

        return redirect()->back()->with('success', 'Reseña eliminada correctamente.');
    }

    /**
     * Extrae el public_id de Cloudinary a partir de la URL guardada,
     * necesario para poder borrar la imagen del storage remoto.
     */
    private function extraerPublicId(string $url): ?string
    {
        // Ej: https://res.cloudinary.com/xxx/image/upload/v123456/surify/resenas/abc123.jpg
        // public_id = surify/resenas/abc123
        if (preg_match('#/upload/(?:v\d+/)?(.+)\.\w+$#', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }
}