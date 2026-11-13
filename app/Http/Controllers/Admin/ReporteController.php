<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Destino;
use App\Models\DestinoVisitado;
use App\Models\Evento;
use App\Models\EventoVisitado;
use App\Models\Favorito;
use App\Models\Provincia;
use App\Models\Resena;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Reportes del panel de administración (descarga en PDF).
 *
 * Secciones: resumen general, usuarios, destinos, reseñas y eventos/sugerencias,
 * filtradas por un rango de fechas.
 */
class ReporteController extends Controller
{
    /** Zona horaria en la que se muestran y se eligen las fechas */
    private const ZONA = 'America/Argentina/Buenos_Aires';

    public const SECCIONES = [
        'usuarios' => 'Usuarios',
        'destinos' => 'Destinos',
        'resenas'  => 'Reseñas',
        'eventos'  => 'Eventos y sugerencias',
    ];

    public function index()
    {
        return view('admin.reportes.index', [
            'secciones' => self::SECCIONES,
            'desde'     => now(self::ZONA)->subDays(29)->toDateString(),
            'hasta'     => now(self::ZONA)->toDateString(),
        ]);
    }

    public function generar(Request $request)
    {
        $request->validate([
            'desde'       => 'required|date',
            'hasta'       => 'required|date|after_or_equal:desde',
            'secciones'   => 'nullable|array',
            'secciones.*' => 'in:' . implode(',', array_keys(self::SECCIONES)),
        ], [
            'hasta.after_or_equal' => 'La fecha "hasta" no puede ser anterior a "desde".',
        ]);

        // El rango se elige en hora argentina y se pasa a UTC para consultar la base
        $desde = Carbon::parse($request->desde, self::ZONA)->startOfDay();
        $hasta = Carbon::parse($request->hasta, self::ZONA)->endOfDay();
        $rango = [$desde->copy()->utc(), $hasta->copy()->utc()];

        $secciones = $request->input('secciones', array_keys(self::SECCIONES));

        $datos = [
            'desde'      => $desde,
            'hasta'      => $hasta,
            'generado'   => now(self::ZONA),
            'autor'      => $request->user()->name,
            'secciones'  => $secciones,
            'titulos'    => self::SECCIONES,
            'resumen'    => $this->resumen($rango),
        ];

        if (in_array('usuarios', $secciones)) $datos['usuarios'] = $this->usuarios($rango);
        if (in_array('destinos', $secciones)) $datos['destinos'] = $this->destinos($rango);
        if (in_array('resenas', $secciones))  $datos['resenas']  = $this->resenas($rango);
        if (in_array('eventos', $secciones))  $datos['eventos']  = $this->eventos($rango, $desde, $hasta);

        $archivo = 'reporte-surify-' . $desde->format('Y-m-d') . '-a-' . $hasta->format('Y-m-d') . '.pdf';

        return Pdf::loadView('admin.reportes.pdf', $datos)
            ->setPaper('a4')
            ->stream($archivo);
    }

    // ==================== SECCIONES ====================

    private function resumen(array $rango): array
    {
        $resenas = Resena::whereBetween('created_at', $rango);

        return [
            'usuarios_nuevos'  => User::whereBetween('created_at', $rango)->count(),
            'usuarios_total'   => User::count(),
            'resenas'          => (clone $resenas)->count(),
            'promedio'         => round((float) (clone $resenas)->where('aprobada', true)->avg('calificacion'), 1),
            'visitas'          => DestinoVisitado::whereBetween('created_at', $rango)->count()
                                + EventoVisitado::whereBetween('created_at', $rango)->count(),
            'favoritos'        => Favorito::whereBetween('created_at', $rango)->count(),
            'sugerencias'      => Evento::whereNotNull('sugerido_por')->whereBetween('created_at', $rango)->count(),
            'destinos_total'   => Destino::count(),
            'eventos_activos'  => Evento::where('activo', true)->count(),
        ];
    }

    private function usuarios(array $rango): array
    {
        $nuevos = User::whereBetween('created_at', $rango)->orderBy('created_at')->get();

        return [
            'total'         => User::count(),
            'nuevos'        => $nuevos->count(),
            'por_mes'       => $this->porMes($nuevos, 'created_at'),
            'con_google'    => User::whereNotNull('google_id')->count(),
            'con_2fa'       => User::whereNotNull('two_factor_confirmed_at')->count(),
            'publicos'      => User::where('perfil_publico', true)->count(),
            'privados'      => User::where('perfil_publico', false)->count(),
            'ultimos'       => $nuevos->sortByDesc('created_at')->take(15)->values(),
            'mas_activos'   => User::withCount([
                                    'resenas'           => fn ($q) => $q->whereBetween('created_at', $rango),
                                    'destinosVisitados' => fn ($q) => $q->whereBetween('created_at', $rango),
                                ])
                                ->get()
                                ->map(fn ($u) => [
                                    'nombre'   => $u->name,
                                    'resenas'  => $u->resenas_count,
                                    'visitas'  => $u->destinos_visitados_count,
                                    'total'    => $u->resenas_count + $u->destinos_visitados_count,
                                ])
                                ->where('total', '>', 0)
                                ->sortByDesc('total')
                                ->take(10)
                                ->values(),
        ];
    }

    private function destinos(array $rango): array
    {
        $nombres = Destino::with('provincia')->get()->keyBy('id');

        $ranking = function (Collection $conteos) use ($nombres) {
            return $conteos->map(function ($cantidad, $id) use ($nombres) {
                $d = $nombres->get($id);
                return [
                    'nombre'    => $d->nombre ?? 'Destino eliminado',
                    'provincia' => $d->provincia->nombre ?? '—',
                    'valor'     => $cantidad,
                ];
            })->sortByDesc('valor')->take(10)->values();
        };

        $visitas = DestinoVisitado::whereBetween('created_at', $rango)->get()->countBy('destino_id');
        $favs    = Favorito::whereNotNull('destino_id')->whereBetween('created_at', $rango)->get()->countBy('destino_id');

        $calificados = Resena::where('aprobada', true)
            ->whereNotNull('destino_id')
            ->whereBetween('created_at', $rango)
            ->get()
            ->groupBy('destino_id')
            ->map(fn ($grupo, $id) => [
                'nombre'    => $nombres->get($id)->nombre ?? 'Destino eliminado',
                'provincia' => $nombres->get($id)->provincia->nombre ?? '—',
                'promedio'  => round($grupo->avg('calificacion'), 1),
                'cantidad'  => $grupo->count(),
            ])
            ->sortBy([['promedio', 'desc'], ['cantidad', 'desc']])
            ->take(10)
            ->values();

        return [
            'total'          => $nombres->count(),
            'mas_visitados'  => $ranking($visitas),
            'mas_favoritos'  => $ranking($favs),
            'mejor_calif'    => $calificados,
            'por_provincia'  => Provincia::withCount('destinos')
                                    ->orderByDesc('destinos_count')
                                    ->orderBy('nombre')
                                    ->get()
                                    ->map(fn ($p) => ['nombre' => $p->nombre, 'valor' => $p->destinos_count]),
            'por_categoria'  => $nombres->countBy(fn ($d) => $d->categoria ?: 'Sin categoría')->sortDesc(),
        ];
    }

    private function resenas(array $rango): array
    {
        $resenas = Resena::with(['user', 'destino', 'evento'])
            ->whereBetween('created_at', $rango)
            ->latest()
            ->get();

        $aprobadas = $resenas->where('aprobada', true);

        $estrellas = [];
        for ($i = 5; $i >= 1; $i--) {
            $estrellas[$i] = $aprobadas->where('calificacion', $i)->count();
        }

        return [
            'total'      => $resenas->count(),
            'aprobadas'  => $aprobadas->count(),
            'pendientes' => $resenas->whereNull('aprobada')->count(),
            'rechazadas' => $resenas->where('aprobada', false)->count(),
            'anonimas'   => $resenas->where('anonima', true)->count(),
            'promedio'   => round((float) $aprobadas->avg('calificacion'), 1),
            'estrellas'  => $estrellas,
            'por_mes'    => $this->porMes($resenas, 'created_at'),
            'ultimas'    => $resenas->take(15),
        ];
    }

    private function eventos(array $rango, Carbon $desde, Carbon $hasta): array
    {
        // Eventos que ocurren dentro del período (por su fecha, no por cuándo se cargaron)
        $enPeriodo = Evento::with(['provincia', 'destino'])
            ->where('activo', true)
            ->whereDate('fecha_inicio', '<=', $hasta->toDateString())
            ->where(function ($q) use ($desde) {
                $q->whereDate('fecha_fin', '>=', $desde->toDateString())
                    ->orWhere(fn ($w) => $w->whereNull('fecha_fin')->whereDate('fecha_inicio', '>=', $desde->toDateString()));
            })
            ->orderBy('fecha_inicio')
            ->get();

        $sugerencias = Evento::whereNotNull('sugerido_por')->whereBetween('created_at', $rango)->get();

        $nombresEventos = Evento::pluck('nombre', 'id');
        $masVisitados = EventoVisitado::whereBetween('created_at', $rango)->get()
            ->countBy('evento_id')
            ->map(fn ($cantidad, $id) => ['nombre' => $nombresEventos[$id] ?? 'Evento eliminado', 'valor' => $cantidad])
            ->sortByDesc('valor')->take(10)->values();

        return [
            'en_periodo'     => $enPeriodo,
            'por_provincia'  => $enPeriodo->countBy(fn ($e) => $e->provincia->nombre ?? 'Sin provincia')->sortDesc(),
            'por_tipo'       => $enPeriodo->countBy(fn ($e) => $e->tipo ?: 'Sin tipo')->sortDesc(),
            'sugerencias'    => $sugerencias->count(),
            'sug_aprobadas'  => $sugerencias->where('activo', true)->count(),
            'sug_pendientes' => $sugerencias->where('activo', false)->count(),
            'mas_visitados'  => $masVisitados,
            'activos_total'  => Evento::where('activo', true)->count(),
        ];
    }

    // ==================== AYUDAS ====================

    /** Cuenta registros por mes (en hora argentina), ej: ['Nov 2026' => 12] */
    private function porMes(Collection $items, string $campo): Collection
    {
        return $items
            ->groupBy(fn ($i) => Carbon::parse($i->{$campo})->timezone(self::ZONA)->format('Y-m'))
            ->sortKeys()
            ->mapWithKeys(fn ($grupo, $mes) => [
                ucfirst(Carbon::createFromFormat('Y-m-d', $mes . '-01')->locale('es')->isoFormat('MMMM YYYY')) => $grupo->count(),
            ]);
    }
}