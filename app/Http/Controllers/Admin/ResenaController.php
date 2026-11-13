<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resena;
use App\Notifications\ResenaModerada;
use App\Services\NotificacionService;
use Illuminate\Http\Request;

class ResenaController extends Controller
{
    public function index(Request $request)
    {
        $estado = $request->get('estado', 'pendiente');

        $resenas = Resena::with(['user', 'destino', 'evento'])
            ->when($estado === 'pendiente', fn($q) => $q->whereNull('aprobada'))
            ->when($estado === 'aprobada', fn($q) => $q->where('aprobada', true))
            ->when($estado === 'rechazada', fn($q) => $q->where('aprobada', false))
            ->latest()
            ->paginate(10);

        $countPendientes = Resena::whereNull('aprobada')->count();
        $countAprobadas = Resena::where('aprobada', true)->count();
        $countRechazadas = Resena::where('aprobada', false)->count();

        return view('admin.resenas.index', compact(
            'resenas', 'estado',
            'countPendientes', 'countAprobadas', 'countRechazadas'
        ));
    }

    public function aprobar(Resena $resena)
    {
        $this->moderar($resena, true);
        return redirect()->back()->with('success', 'Reseña aprobada correctamente.');
    }

    public function rechazar(Resena $resena)
    {
        $this->moderar($resena, false);
        return redirect()->back()->with('success', 'Reseña rechazada correctamente.');
    }

    /**
     * Cambia el estado de la reseña, deja registrado que la decisión fue manual
     * (una decisión manual siempre tiene la última palabra sobre la de la IA)
     * y le avisa al autor, solo si el estado cambió (para no mandar avisos repetidos).
     */
    private function moderar(Resena $resena, bool $aprobada): void
    {
        $cambio = $resena->aprobada !== $aprobada;

        $resena->update([
            'aprobada' => $aprobada,
            'moderada_por' => 'manual',
            'moderada_at' => now(),
        ]);

        if ($cambio && $resena->user) {
            NotificacionService::enviar($resena->user, new ResenaModerada($resena->load(['destino', 'evento']), $aprobada));
        }
    }

    public function destroy(Resena $resena)
    {
        $resena->delete();
        return redirect()->back()->with('success', 'Reseña eliminada correctamente.');
    }
}