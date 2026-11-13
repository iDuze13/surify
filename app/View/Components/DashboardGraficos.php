<?php

namespace App\View\Components;

use App\Models\DestinoVisitado;
use App\Models\EventoVisitado;
use App\Models\Resena;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\Component;

/**
 * Gráficos del dashboard de administración.
 * Se usa en cualquier vista con: <x-dashboard-graficos />
 *
 * "Más visitadas" = cuántas veces las personas marcaron un destino o evento
 * como "visitado" en su perfil (el sitio no mide visitas a páginas).
 */
class DashboardGraficos extends Component
{
    /** Reseñas por estado: aprobadas, rechazadas y a revisión manual (pendientes). */
    public array $resenas;

    /** Quién decidió: IA, manual o todavía nadie (null si la migración de moderación no corrió). */
    public ?array $moderacion = null;

    /** @var array{etiquetas: array<int, string>, valores: array<int, int>} */
    public array $provincias;

    /** @var array{etiquetas: array<int, string>, valores: array<int, int>} */
    public array $eventos;

    public function __construct(int $limite = 8)
    {
        $this->resenas = [
            'aprobadas' => Resena::where('aprobada', true)->count(),
            'rechazadas' => Resena::where('aprobada', false)->count(),
            'revision' => Resena::whereNull('aprobada')->count(),
        ];

        if (Schema::hasColumn('resenas', 'moderada_por')) {
            $this->moderacion = [
                'ia' => Resena::where('moderada_por', 'ia')->count(),
                'manual' => Resena::where('moderada_por', 'manual')->count(),
                'sin' => Resena::whereNull('moderada_por')->count(),
            ];
        }

        // Provincias más visitadas: destinos marcados como visitados, agrupados por provincia
        $porProvincia = DestinoVisitado::with('destino.provincia')->get()
            ->filter(fn($visita) => $visita->destino && $visita->destino->provincia)
            ->groupBy(fn($visita) => $visita->destino->provincia->nombre)
            ->map(fn($grupo) => $grupo->count())
            ->sortDesc()
            ->take($limite);

        $this->provincias = [
            'etiquetas' => $porProvincia->keys()->values()->all(),
            'valores' => $porProvincia->values()->all(),
        ];

        // Eventos más visitados: eventos marcados como visitados
        $porEvento = EventoVisitado::with('evento')->get()
            ->filter(fn($visita) => $visita->evento)
            ->groupBy(fn($visita) => $visita->evento->id)
            ->map(fn($grupo) => [
                'nombre' => Str::limit($grupo->first()->evento->nombre, 30),
                'total' => $grupo->count(),
            ])
            ->sortByDesc('total')
            ->take($limite)
            ->values();

        $this->eventos = [
            'etiquetas' => $porEvento->pluck('nombre')->all(),
            'valores' => $porEvento->pluck('total')->all(),
        ];
    }

    public function render(): View
    {
        return view('components.dashboard-graficos');
    }
}