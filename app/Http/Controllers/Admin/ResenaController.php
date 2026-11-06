<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resena;
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
        $resena->update(['aprobada' => true]);
        return redirect()->back()->with('success', 'Reseña aprobada correctamente.');
    }

    public function rechazar(Resena $resena)
    {
        $resena->update(['aprobada' => false]);
        return redirect()->back()->with('success', 'Reseña rechazada correctamente.');
    }

    public function destroy(Resena $resena)
    {
        $resena->delete();
        return redirect()->back()->with('success', 'Reseña eliminada correctamente.');
    }
}