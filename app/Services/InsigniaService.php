<?php

namespace App\Services;

use App\Models\Evento;
use App\Models\EventoVisitado;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Calcula las insignias de un usuario a partir de su actividad,
 * incluyendo la fecha en que se obtuvo cada una y el progreso de las bloqueadas.
 */
class InsigniaService
{
    // Provincias por región (sin tildes y en minúsculas; ver normalizar())
    private const REGIONES = [
        'norte'     => ['jujuy', 'salta', 'tucuman', 'catamarca', 'santiago del estero', 'formosa', 'chaco'],
        'litoral'   => ['misiones', 'corrientes', 'entre rios', 'santa fe'],
        'cuyo'      => ['mendoza', 'san juan', 'san luis', 'la rioja'],
        'centro'    => ['cordoba', 'buenos aires', 'ciudad autonoma de buenos aires', 'caba', 'la pampa'],
        'patagonia' => ['neuquen', 'rio negro', 'chubut', 'santa cruz', 'tierra del fuego'],
    ];

    // Metas (cambiar acá si quieren ajustar la dificultad)
    private const META_RESENAS          = 3;
    private const META_RESENAS_EXPERTO  = 10;
    private const META_PROVINCIAS       = 3;
    private const META_PROVINCIAS_FED   = 10;
    private const META_DESTINOS         = 10;
    private const META_FAVORITOS        = 10;
    private const META_EVENTOS          = 5;

    public function paraUsuario(User $user): array
    {
        // Todo ordenado del más viejo al más nuevo: el registro que cumple la meta
        // es el que desbloqueó la insignia, y su fecha es la fecha de obtención.
        $visitados = $user->destinosVisitados()
            ->with('destino.provincia')
            ->orderBy('created_at')
            ->get();

        $resenas = $user->resenas()
            ->where('aprobada', true)
            ->orderBy('created_at')
            ->get(['id', 'created_at']);

        $favoritos = $user->favoritos()
            ->orderBy('created_at')
            ->get(['id', 'created_at']);

        $eventosVisitados = EventoVisitado::with('evento')
            ->where('user_id', $user->id)
            ->orderBy('created_at')
            ->get();

        // La fecha de aprobación es la última actualización (cuando pasó a activo)
        $sugerenciaAprobada = Evento::where('sugerido_por', $user->id)
            ->where('activo', true)
            ->orderBy('updated_at')
            ->first();

        $fechasProvincias = $this->fechasNuevasProvincias($visitados);
        $cantProvincias   = count($fechasProvincias);

        $norte     = $this->primeroEnRegion($visitados, 'norte');
        $litoral   = $this->primeroEnRegion($visitados, 'litoral');
        $cuyo      = $this->primeroEnRegion($visitados, 'cuyo');
        $centro    = $this->primeroEnRegion($visitados, 'centro');
        $patagonia = $this->primeroEnRegion($visitados, 'patagonia');

        $montana = $visitados->first(fn ($v) => $this->normalizar($v->destino?->categoria) === 'montana');
        $gastro  = $visitados->first(fn ($v) => $this->normalizar($v->destino?->categoria) === 'gastronomia');

        $primerEvento = $eventosVisitados->first();

        return [
            // ---------- Bienvenida ----------
            $this->insignia(
                clave: 'aventurero', nombre: 'Aventurero Inicial', emoji: '🐣',
                como: 'Creá tu cuenta en Surify. ¡Es la primera de muchas!',
                ganada: true, fecha: $user->created_at,
                detalle: 'Te uniste a la comunidad de Surify.',
            ),

            // ---------- Regiones ----------
            $this->insigniaRegion('norte', 'Viajero del Norte', 'north', $norte,
                'Jujuy, Salta, Tucumán, Catamarca, Santiago del Estero, Formosa o Chaco'),
            $this->insigniaRegion('litoral', 'Viajero del Litoral', 'water', $litoral,
                'Misiones, Corrientes, Entre Ríos o Santa Fe'),
            $this->insigniaRegion('cuyo', 'Viajero de Cuyo', 'wine_bar', $cuyo,
                'Mendoza, San Juan, San Luis o La Rioja'),
            $this->insigniaRegion('centro', 'Viajero del Centro', 'location_city', $centro,
                'Córdoba, Buenos Aires o La Pampa'),
            $this->insigniaRegion('patagonia', 'Explorador Patagónico', 'landscape', $patagonia,
                'Neuquén, Río Negro, Chubut, Santa Cruz o Tierra del Fuego'),

            // ---------- Tipos de destino ----------
            $this->insignia(
                clave: 'montanista', nombre: 'Montañista Pro', icono: 'hiking',
                como: 'Marcá como visitado un destino de la categoría Montaña.',
                ganada: $montana !== null, fecha: $montana?->created_at,
                detalle: $montana ? 'Desbloqueada al visitar ' . $montana->destino?->nombre . '.' : null,
            ),
            $this->insignia(
                clave: 'asados', nombre: 'Catador de Asados', icono: 'restaurant',
                como: 'Marcá como visitado un destino de la categoría Gastronomía.',
                ganada: $gastro !== null, fecha: $gastro?->created_at,
                detalle: $gastro ? 'Desbloqueada al visitar ' . $gastro->destino?->nombre . '.' : null,
            ),

            // ---------- Niveles de viaje ----------
            $this->insignia(
                clave: 'nacional', nombre: 'Explorador Nacional', icono: 'map',
                como: 'Visitá destinos en ' . self::META_PROVINCIAS . ' provincias distintas.',
                ganada: $cantProvincias >= self::META_PROVINCIAS,
                fecha: $fechasProvincias[self::META_PROVINCIAS - 1] ?? null,
                progreso: min($cantProvincias, self::META_PROVINCIAS), meta: self::META_PROVINCIAS,
                detalle: 'Llevás ' . $cantProvincias . ' provincias recorridas.',
            ),
            $this->insignia(
                clave: 'federal', nombre: 'Explorador Federal', icono: 'flag',
                como: 'Visitá destinos en ' . self::META_PROVINCIAS_FED . ' provincias distintas.',
                ganada: $cantProvincias >= self::META_PROVINCIAS_FED,
                fecha: $fechasProvincias[self::META_PROVINCIAS_FED - 1] ?? null,
                progreso: min($cantProvincias, self::META_PROVINCIAS_FED), meta: self::META_PROVINCIAS_FED,
                detalle: 'Llevás ' . $cantProvincias . ' provincias recorridas.',
            ),
            $this->insigniaPorCantidad(
                'trotamundos', 'Trotamundos', 'travel_explore', $visitados, self::META_DESTINOS,
                'Marcá ' . self::META_DESTINOS . ' destinos como visitados.',
                'destinos visitados',
            ),

            // ---------- Comunidad ----------
            $this->insigniaPorCantidad(
                'comentador', 'Comentador Activo', 'forum', $resenas, self::META_RESENAS,
                'Publicá ' . self::META_RESENAS . ' reseñas que sean aprobadas por el equipo de Surify.',
                'reseñas publicadas',
            ),
            $this->insigniaPorCantidad(
                'critico', 'Crítico Experto', 'rate_review', $resenas, self::META_RESENAS_EXPERTO,
                'Publicá ' . self::META_RESENAS_EXPERTO . ' reseñas que sean aprobadas por el equipo de Surify.',
                'reseñas publicadas',
            ),
            $this->insigniaPorCantidad(
                'coleccionista', 'Coleccionista', 'favorite', $favoritos, self::META_FAVORITOS,
                'Guardá ' . self::META_FAVORITOS . ' destinos o eventos en tus favoritos.',
                'favoritos guardados',
            ),

            // ---------- Eventos ----------
            $this->insignia(
                clave: 'fiestero', nombre: 'Fiestero', icono: 'celebration',
                como: 'Marcá como visitado tu primer evento o festival.',
                ganada: $primerEvento !== null, fecha: $primerEvento?->created_at,
                detalle: $primerEvento?->evento ? 'Tu primer evento fue ' . $primerEvento->evento->nombre . '.' : null,
            ),
            $this->insigniaPorCantidad(
                'festival', 'Alma de Festival', 'festival', $eventosVisitados, self::META_EVENTOS,
                'Marcá ' . self::META_EVENTOS . ' eventos o festivales como visitados.',
                'eventos visitados',
            ),

            // ---------- Colaboración ----------
            $this->insignia(
                clave: 'colaborador', nombre: 'Colaborador de Surify', icono: 'volunteer_activism',
                como: 'Sugerí un festival o evento desde el calendario y que el equipo lo apruebe.',
                ganada: $sugerenciaAprobada !== null, fecha: $sugerenciaAprobada?->updated_at,
                detalle: $sugerenciaAprobada ? 'Tu sugerencia "' . $sugerenciaAprobada->nombre . '" fue publicada.' : null,
            ),
        ];
    }

    // ================= Constructores de insignias =================

    private function insignia(
        string $clave,
        string $nombre,
        string $como,
        bool $ganada,
        ?\DateTimeInterface $fecha = null,
        ?string $icono = null,
        ?string $emoji = null,
        ?int $progreso = null,
        int $meta = 1,
        ?string $detalle = null,
    ): array {
        return [
            'clave'    => $clave,
            'nombre'   => $nombre,
            'icono'    => $icono,
            'emoji'    => $emoji,
            'como'     => $como,
            'ganada'   => $ganada,
            'fecha'    => $ganada ? $fecha : null,
            'progreso' => $progreso ?? ($ganada ? 1 : 0),
            'meta'     => $meta,
            'detalle'  => $ganada ? $detalle : null,
        ];
    }

    /** Insignia por visitar un destino de una región. */
    private function insigniaRegion(string $clave, string $nombre, string $icono, $visitado, string $provincias): array
    {
        return $this->insignia(
            clave: $clave, nombre: $nombre, icono: $icono,
            como: 'Marcá como visitado un destino de ' . $provincias . '.',
            ganada: $visitado !== null, fecha: $visitado?->created_at,
            detalle: $visitado ? 'Desbloqueada al visitar ' . $visitado->destino?->nombre . '.' : null,
        );
    }

    /** Insignia por alcanzar una cantidad (la fecha es la del registro N° meta). */
    private function insigniaPorCantidad(
        string $clave, string $nombre, string $icono,
        Collection $registros, int $meta, string $como, string $unidad,
    ): array {
        $cantidad = $registros->count();

        return $this->insignia(
            clave: $clave, nombre: $nombre, icono: $icono, como: $como,
            ganada: $cantidad >= $meta,
            fecha: $registros->get($meta - 1)?->created_at,
            progreso: min($cantidad, $meta), meta: $meta,
            detalle: 'Llegaste a ' . $meta . ' ' . $unidad . '.',
        );
    }

    // ================= Helpers =================

    /** Primer destino visitado (en orden de fecha) que pertenece a la región. */
    private function primeroEnRegion(Collection $visitados, string $region)
    {
        return $visitados->first(fn ($v) => $this->enRegion($v->destino?->provincia?->nombre, self::REGIONES[$region]));
    }

    private function enRegion(?string $provincia, array $lista): bool
    {
        $nombre = $this->normalizar($provincia);
        if ($nombre === '') {
            return false;
        }
        foreach ($lista as $item) {
            // starts_with para cubrir nombres largos ("Tierra del Fuego, Antártida e Islas...")
            if (str_starts_with($nombre, $item)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Fecha en que se sumó cada provincia nueva, en orden.
     * [0] = primera provincia, [2] = cuando llegó a 3, [9] = cuando llegó a 10...
     */
    private function fechasNuevasProvincias(Collection $visitados): array
    {
        $vistas = [];
        $fechas = [];

        foreach ($visitados as $v) {
            $provincia = $this->normalizar($v->destino?->provincia?->nombre);
            if ($provincia === '' || isset($vistas[$provincia])) {
                continue;
            }
            $vistas[$provincia] = true;
            $fechas[] = $v->created_at;
        }

        return $fechas;
    }

    /** "Neuquén" → "neuquen", "Montaña" → "montana": compara sin tildes ni mayúsculas. */
    private function normalizar(?string $texto): string
    {
        return Str::lower(Str::ascii(trim((string) $texto)));
    }
}