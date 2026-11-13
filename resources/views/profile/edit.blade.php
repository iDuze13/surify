@extends('layouts.app')

@php
// Esta vista sirve para el perfil propio (/profile) y para el de otros usuarios (/usuarios/{id})
$usuario     = $usuario ?? auth()->user();
$esPropio    = $esPropio ?? true;
$privado     = $privado ?? false;
$vistaPrevia = $vistaPrevia ?? false;
$avisoAdmin  = $avisoAdmin ?? false;
@endphp

@section('title', ($esPropio ? 'Mi Perfil' : $usuario->name) . ' - Surify')

@section('content')

<style>
    /* La portada se funde con el fondo de la página (claro u oscuro según el modo) */
    .hero-gradient {
        background: linear-gradient(to bottom,
                rgba(0, 0, 0, 0.15) 0%,
                transparent 30%,
                color-mix(in srgb, var(--bg-body) 55%, transparent) 70%,
                var(--bg-body) 100%);
    }

    /* Placa detrás del nombre: se lee bien con cualquier portada y en los dos modos */
    .perfil-identidad {
        background: color-mix(in srgb, var(--bg-body) 72%, transparent);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid color-mix(in srgb, var(--text-body) 10%, transparent);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    }

    .perfil-nombre {
        color: var(--text-body);
    }

    .perfil-miembro {
        color: #28628f;
    }

    .dark .perfil-miembro,
    .dark .perfil-stat {
        color: #97ccfe;
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(12px);
    }

    .dark .glass-card {
        background: rgba(30, 41, 59, 0.75);
    }

    .scroll-hide::-webkit-scrollbar {
        display: none;
    }

    html {
        scroll-behavior: smooth;
    }
</style>

{{-- Hero Section --}}
<section class="relative isolate h-[360px] sm:h-[400px] overflow-hidden -mx-4 sm:-mx-6 lg:-mx-8 -mt-8">
    <img id="hero-bg" src="" class="w-full h-full object-cover" alt="Hero">
    @if($esPropio)
    <button onclick="document.getElementById('modal-portada').classList.remove('hidden')"
        title="Cambiar portada"
        class="absolute top-4 right-4 z-20 bg-black/50 hover:bg-black/70 text-white text-xs font-semibold p-2 sm:px-3 sm:py-2 rounded-full flex items-center gap-1 transition-all">
        <span class="material-symbols-outlined text-[20px] sm:text-[16px]">wallpaper</span>
        <span class="hidden sm:inline">Cambiar portada</span>
    </button>
    <button onclick="abrirConfig()"
        class="absolute top-4 left-4 z-20 bg-black/50 hover:bg-black/70 text-white p-2 rounded-full transition-all group">
        <span id="settings-btn-icon" class="material-symbols-outlined text-[20px] transition-transform duration-500">settings</span>
    </button>
    @endif
    <div class="absolute inset-0 hero-gradient"></div>
    <div class="absolute inset-0 flex flex-col items-center justify-center mt-6 sm:mt-8 px-4">
        <div class="relative {{ $esPropio ? 'group cursor-pointer' : '' }}" @if($esPropio) onclick="document.getElementById('avatar-input').click()" @endif>
            @if($usuario->avatar)
            <img src="{{ $usuario->avatar }}" class="w-28 h-28 sm:w-32 sm:h-32 md:w-40 md:h-40 rounded-full border-4 border-white object-cover shadow-2xl" alt="Avatar">
            @else
            <div class="w-28 h-28 sm:w-32 sm:h-32 md:w-40 md:h-40 rounded-full bg-[#28628f] text-white flex items-center justify-center text-4xl font-black border-4 border-white shadow-2xl">
                {{ strtoupper(substr($usuario->name, 0, 1)) }}
            </div>
            @endif
            @if($esPropio)
            <div class="absolute inset-0 rounded-full bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                <span class="material-symbols-outlined text-white text-3xl">photo_camera</span>
            </div>
            @endif
            <div class="absolute bottom-2 right-2 bg-[#28628f] text-white rounded-full p-1 border-4 border-white">
                <span class="material-symbols-outlined text-[14px]" style="font-variation-settings: 'FILL' 1;">verified</span>
            </div>
        </div>
        @if($esPropio)
        <form method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data" id="avatar-form">
            @csrf
            <input type="file" id="avatar-input" name="avatar" accept="image/*" class="hidden"
                onchange="document.getElementById('avatar-form').submit()">
        </form>
        @endif
        <div class="perfil-identidad mt-3 max-w-full rounded-2xl px-5 py-2.5 text-center">
            <h1 class="perfil-nombre text-2xl sm:text-3xl font-black leading-tight break-words" style="font-family: 'Inter', sans-serif;">{{ $usuario->name }}</h1>
            @if($esPropio)
            {{-- El email solo lo ve el dueño --}}
            <p class="text-sm text-slate-500 break-all">{{ $usuario->email }}</p>
            @endif
            <p class="perfil-miembro text-[11px] sm:text-xs font-bold uppercase tracking-widest mt-1">
                Miembro desde {{ $usuario->created_at->translatedFormat('F Y') }}
            </p>
        </div>
    </div>
</section>

{{-- Avisos de vista --}}
@if($vistaPrevia)
<div class="max-w-2xl mx-auto mt-4 mb-2 sm:px-4">
    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 rounded-xl border border-[#28628f]/20 bg-[#28628f]/5 px-4 py-3 text-sm text-[#28628f]">
        <span class="material-symbols-outlined">visibility</span>
        Así ven tu perfil los demás.
        <a href="{{ route('profile.edit') }}" class="ml-auto font-bold hover:underline text-decoration-none">Volver a mi perfil</a>
    </div>
</div>
@endif
@if($avisoAdmin)
<div class="max-w-2xl mx-auto mt-4 mb-2 sm:px-4">
    <div class="flex items-center gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
        <span class="material-symbols-outlined">lock</span>
        Este perfil es privado. Lo ves completo porque sos administrador.
    </div>
</div>
@endif

@if($privado)

{{-- Perfil privado: solo nombre y foto --}}
<div class="max-w-2xl mx-auto mt-8 mb-8 sm:px-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-12 text-center">
        <span class="material-symbols-outlined text-5xl text-slate-300 block mb-3">lock</span>
        <h2 class="text-xl font-bold text-slate-700">Este perfil es privado</h2>
        <p class="text-sm text-slate-400 mt-1">{{ $usuario->name }} eligió no mostrar su actividad.</p>
    </div>
</div>

@else

{{-- Stats --}}
@php
$countVisitados = $usuario->destinosVisitados()->count();
// Solo reseñas aprobadas; a los demás tampoco se les cuentan las anónimas
$countResenas = $usuario->resenas()
    ->where('aprobada', true)
    ->when(!$esPropio, fn ($q) => $q->where(fn ($w) => $w->where('anonima', false)->orWhereNull('anonima')))
    ->count();
@endphp
<div class="max-w-2xl mx-auto -mt-6 relative z-10 mb-8 sm:px-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xl p-4 sm:p-6 grid grid-cols-3 gap-2 sm:gap-4 text-center">
        <div>
            <div class="perfil-stat text-2xl font-bold text-[#28628f]">{{ $countVisitados }}</div>
            <div class="text-xs text-slate-500 mt-1">Lugares visitados</div>
        </div>
        <div class="border-x border-slate-200">
            <div class="perfil-stat text-2xl font-bold text-[#28628f]">{{ $countResenas }}</div>
            <div class="text-xs text-slate-500 mt-1">Reseñas publicadas</div>
        </div>
        <div>
            <div class="perfil-stat text-2xl font-bold text-[#28628f]">0</div>
            <div class="text-xs text-slate-500 mt-1">Fotos subidas</div>
        </div>
    </div>
</div>

{{-- Contenido principal --}}
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    {{-- Columna izquierda --}}
    <div class="{{ $esPropio ? 'lg:col-span-8' : 'lg:col-span-12' }} space-y-6">

        {{-- Insignias --}}
        @php
        $insignias = app(\App\Services\InsigniaService::class)->paraUsuario($usuario);
        $insigniasGanadas = collect($insignias)->where('ganada', true)->count();
        @endphp
        <section class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-6">
            <div class="flex items-center justify-between gap-4 mb-1">
                <h2 class="text-lg sm:text-xl font-bold text-slate-800 flex flex-wrap items-center gap-x-2">
                    <span class="material-symbols-outlined text-yellow-500" style="font-variation-settings: 'FILL' 1;">military_tech</span>
                    {{ $esPropio ? 'Mis Insignias' : 'Insignias' }}
                    <span class="text-sm font-semibold text-slate-400">{{ $insigniasGanadas }}/{{ count($insignias) }}</span>
                </h2>
                <div class="flex gap-2 shrink-0">
                    <button type="button" title="Anteriores"
                        onclick="const c = document.getElementById('carrusel-insignias'); c.scrollBy({ left: -c.clientWidth, behavior: 'smooth' });"
                        class="p-2 rounded-full border border-slate-200 hover:bg-slate-100 transition-colors">
                        <span class="material-symbols-outlined text-sm">chevron_left</span>
                    </button>
                    <button type="button" title="Siguientes"
                        onclick="const c = document.getElementById('carrusel-insignias'); c.scrollBy({ left: c.clientWidth, behavior: 'smooth' });"
                        class="p-2 rounded-full border border-slate-200 hover:bg-slate-100 transition-colors">
                        <span class="material-symbols-outlined text-sm">chevron_right</span>
                    </button>
                </div>
            </div>
            <p class="text-xs text-slate-400 mb-4">Tocá una insignia para ver cómo conseguirla.</p>

            <div id="carrusel-insignias" class="flex gap-4 overflow-x-auto pb-2 scroll-hide snap-x snap-mandatory">
                @foreach($insignias as $ins)
                <button type="button"
                    onclick="document.getElementById('modal-insignia-{{ $ins['clave'] }}').classList.remove('hidden')"
                    class="snap-start group flex-shrink-0 flex flex-col items-center gap-2 rounded-xl p-1 transition-transform duration-200 hover:-translate-y-1 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#28628f] {{ $ins['ganada'] ? '' : 'opacity-50 grayscale' }}">
                    <div class="w-20 h-20 rounded-full flex items-center justify-center border-2 shadow-sm transition-shadow duration-200 group-hover:shadow-lg
                        {{ $ins['ganada'] ? 'bg-gradient-to-br from-[#28628f] to-[#1a4669] border-[#28628f]/30' : 'bg-slate-200 border-dashed border-slate-400' }}">
                        @if($ins['emoji'])
                        <span class="text-3xl">{{ $ins['emoji'] }}</span>
                        @else
                        <span class="material-symbols-outlined text-3xl {{ $ins['ganada'] ? 'text-white' : 'text-slate-500' }}"
                            style="font-variation-settings: 'FILL' {{ $ins['ganada'] ? '1' : '0' }};">{{ $ins['icono'] }}</span>
                        @endif
                    </div>
                    <span class="text-xs font-semibold text-center max-w-[80px] {{ $ins['ganada'] ? 'text-slate-700' : 'text-slate-400' }}">{{ $ins['nombre'] }}</span>
                    @if($ins['ganada'])
                    <span class="text-[9px] font-bold text-emerald-500 uppercase tracking-wider">
                        ✓ {{ $ins['fecha'] ? $ins['fecha']->format('d/m/Y') : 'Ganada' }}
                    </span>
                    @elseif($ins['meta'] > 1)
                    <span class="text-[9px] font-semibold text-slate-400">{{ $ins['progreso'] }}/{{ $ins['meta'] }}</span>
                    @else
                    <span class="text-[9px] text-slate-400">Bloqueada</span>
                    @endif
                </button>
                @endforeach
            </div>

            {{-- Un modal por insignia --}}
            @foreach($insignias as $ins)
            <div id="modal-insignia-{{ $ins['clave'] }}" class="modal-insignia hidden fixed inset-0 z-50">
                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"
                    onclick="this.parentElement.classList.add('hidden')"></div>

                <div class="pointer-events-none relative flex min-h-full items-center justify-center p-4">
                    <div class="pointer-events-auto relative w-full max-w-sm rounded-2xl bg-white p-6 text-center shadow-2xl">
                        <button type="button"
                            onclick="this.closest('.modal-insignia').classList.add('hidden')"
                            class="absolute right-3 top-3 rounded-lg p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                            <span class="material-symbols-outlined">close</span>
                        </button>

                        {{-- Ícono grande --}}
                        <div class="mx-auto mb-4 flex h-24 w-24 items-center justify-center rounded-full border-2 shadow-md
                            {{ $ins['ganada'] ? 'bg-gradient-to-br from-[#28628f] to-[#1a4669] border-[#28628f]/30' : 'bg-slate-200 border-dashed border-slate-400 grayscale' }}">
                            @if($ins['emoji'])
                            <span class="text-5xl">{{ $ins['emoji'] }}</span>
                            @else
                            <span class="material-symbols-outlined text-5xl {{ $ins['ganada'] ? 'text-white' : 'text-slate-500' }}"
                                style="font-variation-settings: 'FILL' {{ $ins['ganada'] ? '1' : '0' }};">{{ $ins['icono'] }}</span>
                            @endif
                        </div>

                        <h3 class="text-xl font-bold text-slate-800">{{ $ins['nombre'] }}</h3>
                        @if($ins['ganada'])
                        <span class="mt-2 inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-600">
                            <span class="material-symbols-outlined text-[14px]">verified</span> Ganada
                        </span>
                        @else
                        <span class="mt-2 inline-flex items-center gap-1 rounded-full border border-slate-200 bg-slate-100 px-3 py-1 text-xs font-bold text-slate-500">
                            <span class="material-symbols-outlined text-[14px]">lock</span> Bloqueada
                        </span>
                        @endif

                        {{-- Cómo conseguirla --}}
                        <div class="mt-5 rounded-xl bg-slate-50 p-4 text-left">
                            <p class="mb-1 text-[11px] font-bold uppercase tracking-wider text-slate-400">Cómo conseguirla</p>
                            <p class="text-sm text-slate-700">{{ $ins['como'] }}</p>
                        </div>

                        @if($ins['ganada'])
                        {{-- Fecha de obtención --}}
                        <div class="mt-3 rounded-xl border border-emerald-100 bg-emerald-50 p-4 text-left">
                            <p class="mb-1 text-[11px] font-bold uppercase tracking-wider text-emerald-500">Obtenida</p>
                            <p class="text-sm font-semibold text-emerald-700">
                                {{ $ins['fecha'] ? $ins['fecha']->locale('es')->isoFormat('D [de] MMMM [de] YYYY') : 'Fecha no disponible' }}
                            </p>
                            @if($ins['detalle'])
                            <p class="mt-1 text-xs text-emerald-600">{{ $ins['detalle'] }}</p>
                            @endif
                        </div>
                        @else
                        {{-- Progreso --}}
                        @php $pct = $ins['meta'] > 0 ? round($ins['progreso'] / $ins['meta'] * 100) : 0; @endphp
                        <div class="mt-3 rounded-xl bg-slate-50 p-4 text-left">
                            <div class="mb-2 flex items-center justify-between">
                                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ $esPropio ? 'Tu progreso' : 'Progreso' }}</p>
                                <p class="text-xs font-bold text-[#28628f]">{{ $ins['progreso'] }} de {{ $ins['meta'] }}</p>
                            </div>
                            <div class="h-2 w-full overflow-hidden rounded-full bg-slate-200">
                                <div class="h-full rounded-full bg-[#28628f] transition-all" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach

            <script>
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') document.querySelectorAll('.modal-insignia').forEach(m => m.classList.add('hidden'));
                });
            </script>
        </section>

        {{-- Lugares visitados --}}
        <section class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-6">
            <div class="flex items-center justify-between gap-3 mb-4">
                <h2 class="text-lg sm:text-xl font-bold text-slate-800 min-w-0">{{ $esPropio ? 'Mis lugares visitados' : 'Lugares visitados' }}</h2>
                <div class="flex gap-2 shrink-0">
                    <button onclick="prevVisitados()" class="p-2 rounded-full border border-slate-200 hover:bg-slate-100 transition-colors">
                        <span class="material-symbols-outlined text-sm">chevron_left</span>
                    </button>
                    <button onclick="nextVisitados()" class="p-2 rounded-full border border-slate-200 hover:bg-slate-100 transition-colors">
                        <span class="material-symbols-outlined text-sm">chevron_right</span>
                    </button>
                </div>
            </div>
            @php
            $visitados = $usuario->destinosVisitados()->with('destino.provincia')->get()->filter(fn($v) => $v->destino)->values();
            @endphp
            @if($visitados->count() > 0)
            <div id="visitados-container" class="grid grid-cols-2 gap-3">
                @foreach($visitados as $visitado)
                <a href="{{ route('destinos.show', $visitado->destino->id) }}"
                    class="visitado-item relative bg-slate-100 rounded-xl overflow-hidden group text-decoration-none {{ $loop->index >= 4 ? 'hidden' : '' }}"
                    data-index="{{ $loop->index }}">
                    <div class="h-24 overflow-hidden relative">
                        <img src="{{ $visitado->destino->imagen_url ?? 'https://images.unsplash.com/photo-1501854140801-50d01698950b?w=400' }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            alt="{{ $visitado->destino->nombre }}">
                        <div class="absolute top-2 right-2 bg-emerald-500 text-white rounded-full p-0.5">
                            <span class="material-symbols-outlined text-[12px]" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                        </div>
                    </div>
                    <div class="p-2">
                        <h4 class="text-xs font-bold text-slate-800 truncate">{{ $visitado->destino->nombre }}</h4>
                        <p class="text-[10px] text-slate-400">{{ $visitado->destino->provincia->nombre ?? '' }}</p>
                        @if($visitado->visitado_en)
                        <p class="text-[10px] text-emerald-500 font-semibold">{{ \Carbon\Carbon::parse($visitado->visitado_en)->format('d/m/Y') }}</p>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
            <p class="text-xs text-slate-400 text-center mt-3" id="visitados-counter">
                Mostrando 1-{{ min(4, $visitados->count()) }} de {{ $visitados->count() }}
            </p>
            @else
            <div class="text-center py-8 text-slate-400 text-sm border-2 border-dashed border-slate-200 rounded-xl">
                <span class="material-symbols-outlined text-3xl mb-2 block">explore</span>
                {{ $esPropio ? 'Aún no marcaste ningún destino como visitado' : 'Todavía no marcó ningún destino como visitado' }}
            </div>
            @endif
        </section>

        {{-- Festividades visitadas --}}
        <section class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                    <span class="material-symbols-outlined text-emerald-500" style="font-variation-settings: 'FILL' 1;">celebration</span>
                    Festividades visitadas
                </h2>
                <div class="flex gap-2 shrink-0">
                    <button onclick="prevFestVisitadas()" class="p-2 rounded-full border border-slate-200 hover:bg-slate-100 transition-colors">
                        <span class="material-symbols-outlined text-sm">chevron_left</span>
                    </button>
                    <button onclick="nextFestVisitadas()" class="p-2 rounded-full border border-slate-200 hover:bg-slate-100 transition-colors">
                        <span class="material-symbols-outlined text-sm">chevron_right</span>
                    </button>
                </div>
            </div>
            @php
            // ✅ .values() para reindexar desde 0
            $festVisitadas = $usuario->eventosVisitados()->with('evento.provincia')->get()->filter(fn($v) => $v->evento && ($esPropio || $v->evento->activo))->values();
            @endphp
            @if($festVisitadas->count() > 0)
            <div id="fest-visitadas-container" class="grid grid-cols-2 gap-3">
                @foreach($festVisitadas as $vis)
                <a href="{{ route('eventos.show', $vis->evento->id) }}"
                    class="fest-visitada-item relative bg-slate-100 rounded-xl overflow-hidden group text-decoration-none {{ $loop->index >= 4 ? 'hidden' : '' }}"
                    data-index="{{ $loop->index }}">
                    <div class="h-24 overflow-hidden relative">
                        <img src="{{ $vis->evento->imagen_url
                            ? (str_starts_with($vis->evento->imagen_url, 'http') ? $vis->evento->imagen_url : asset('storage/' . $vis->evento->imagen_url))
                            : 'https://images.unsplash.com/photo-1501854140801-50d01698950b?w=400' }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            alt="{{ $vis->evento->nombre }}">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                        <div class="absolute top-2 left-2 bg-amber-500 text-white text-[9px] font-bold px-2 py-0.5 rounded-full">
                            {{ $vis->evento->tipo ?? 'Festival' }}
                        </div>
                        <div class="absolute top-2 right-2 bg-emerald-500 text-white rounded-full p-0.5">
                            <span class="material-symbols-outlined text-[12px]" style="font-variation-settings: 'FILL' 1;">verified</span>
                        </div>
                    </div>
                    <div class="p-2">
                        <h4 class="text-xs font-bold text-slate-800 truncate">{{ $vis->evento->nombre }}</h4>
                        <p class="text-[10px] text-slate-400">{{ $vis->evento->provincia->nombre ?? '' }}</p>
                        @if($vis->visitado_en)
                        <p class="text-[10px] text-emerald-500 font-semibold">{{ \Carbon\Carbon::parse($vis->visitado_en)->format('d/m/Y') }}</p>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
            <p class="text-xs text-slate-400 text-center mt-3" id="fest-visitadas-counter">
                Mostrando 1-{{ min(4, $festVisitadas->count()) }} de {{ $festVisitadas->count() }}
            </p>
            @else
            <div class="text-center py-8 text-slate-400 text-sm border-2 border-dashed border-slate-200 rounded-xl">
                <span class="material-symbols-outlined text-3xl mb-2 block">celebration</span>
                {{ $esPropio ? 'Aún no marcaste ninguna festividad como visitada' : 'Todavía no marcó ninguna festividad como visitada' }}
            </div>
            @endif
        </section>

        {{-- Festividades favoritas --}}
        <section class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                    <span class="material-symbols-outlined text-rose-500" style="font-variation-settings: 'FILL' 1;">favorite</span>
                    Festividades favoritas
                </h2>
                <div class="flex gap-2 shrink-0">
                    <button onclick="prevFestFavoritas()" class="p-2 rounded-full border border-slate-200 hover:bg-slate-100 transition-colors">
                        <span class="material-symbols-outlined text-sm">chevron_left</span>
                    </button>
                    <button onclick="nextFestFavoritas()" class="p-2 rounded-full border border-slate-200 hover:bg-slate-100 transition-colors">
                        <span class="material-symbols-outlined text-sm">chevron_right</span>
                    </button>
                </div>
            </div>
            {{-- ✅ .values() para reindexar desde 0 --}}
            @php
            $festividades = $usuario->favoritos()->with('evento.provincia')->get()->filter(function ($f) use ($esPropio) {
            return $f->evento && ($esPropio || $f->evento->activo);
            })->values();
            @endphp
            @if($festividades->count() > 0)
            <div id="festividades-container" class="grid grid-cols-2 gap-3">
                @foreach($festividades as $fav)
                <a href="{{ route('eventos.show', $fav->evento->id) }}"
                    class="festividad-item relative bg-slate-100 rounded-xl overflow-hidden group text-decoration-none {{ $loop->index >= 4 ? 'hidden' : '' }}"
                    data-index="{{ $loop->index }}">
                    <div class="h-24 overflow-hidden relative">
                        <img src="{{ $fav->evento->imagen_url
                            ? (str_starts_with($fav->evento->imagen_url, 'http') ? $fav->evento->imagen_url : asset('storage/' . $fav->evento->imagen_url))
                            : 'https://images.unsplash.com/photo-1501854140801-50d01698950b?w=400' }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            alt="{{ $fav->evento->nombre }}">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                        <div class="absolute top-2 left-2 bg-amber-500 text-white text-[9px] font-bold px-2 py-0.5 rounded-full">
                            {{ $fav->evento->tipo ?? 'Festival' }}
                        </div>
                        <div class="absolute top-2 right-2 bg-rose-500 text-white rounded-full p-0.5">
                            <span class="material-symbols-outlined text-[12px]" style="font-variation-settings: 'FILL' 1;">favorite</span>
                        </div>
                    </div>
                    <div class="p-2">
                        <h4 class="text-xs font-bold text-slate-800 truncate">{{ $fav->evento->nombre }}</h4>
                        <p class="text-[10px] text-slate-400">{{ $fav->evento->provincia->nombre ?? '' }}</p>
                        @if($fav->evento->fecha_inicio)
                        <p class="text-[10px] text-rose-500 font-semibold">{{ \Carbon\Carbon::parse($fav->evento->fecha_inicio)->format('d/m/Y') }}</p>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
            <p class="text-xs text-slate-400 text-center mt-3" id="festividades-counter">
                Mostrando 1-{{ min(4, $festividades->count()) }} de {{ $festividades->count() }}
            </p>
            @else
            <div class="text-center py-8 text-slate-400 text-sm border-2 border-dashed border-slate-200 rounded-xl">
                <span class="material-symbols-outlined text-3xl mb-2 block">favorite</span>
                {{ $esPropio ? 'Aún no guardaste ninguna festividad como favorita' : 'Todavía no guardó festividades favoritas' }}
            </div>
            @endif
        </section>

        {{-- Favoritos destinos --}}
        <section class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-6">
            <div class="flex items-center justify-between gap-3 mb-4">
                <h2 class="text-lg sm:text-xl font-bold text-slate-800 min-w-0">{{ $esPropio ? 'Mis favoritos' : 'Favoritos' }}</h2>
                <div class="flex gap-2 shrink-0">
                    <button onclick="prevFavoritos()" class="p-2 rounded-full border border-slate-200 hover:bg-slate-100 transition-colors">
                        <span class="material-symbols-outlined text-sm">chevron_left</span>
                    </button>
                    <button onclick="nextFavoritos()" class="p-2 rounded-full border border-slate-200 hover:bg-slate-100 transition-colors">
                        <span class="material-symbols-outlined text-sm">chevron_right</span>
                    </button>
                </div>
            </div>
            @php
            // ✅ .values() para reindexar desde 0
            $favoritos = $usuario->favoritos()->with('destino.provincia')->get()->filter(fn($f) => $f->destino)->values();
            @endphp
            @if($favoritos->count() > 0)
            <div id="favoritos-container" class="grid grid-cols-2 gap-3">
                @foreach($favoritos as $favorito)
                <a href="{{ route('destinos.show', $favorito->destino->id) }}"
                    class="favorito-item relative bg-slate-100 rounded-xl overflow-hidden group text-decoration-none {{ $loop->index >= 4 ? 'hidden' : '' }}"
                    data-index="{{ $loop->index }}">
                    <div class="h-24 overflow-hidden">
                        <img src="{{ $favorito->destino->imagen_url ?? 'https://images.unsplash.com/photo-1501854140801-50d01698950b?w=400' }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            alt="{{ $favorito->destino->nombre }}">
                    </div>
                    <div class="p-2 flex justify-between items-center">
                        <div>
                            <h4 class="text-xs font-bold text-slate-800 truncate">{{ $favorito->destino->nombre }}</h4>
                            <p class="text-[10px] text-slate-400">{{ $favorito->destino->provincia->nombre ?? '' }}</p>
                        </div>
                        <span class="material-symbols-outlined text-rose-500 text-[16px]" style="font-variation-settings: 'FILL' 1;">favorite</span>
                    </div>
                </a>
                @endforeach
            </div>
            <p class="text-xs text-slate-400 text-center mt-3" id="favoritos-counter">
                Mostrando 1-{{ min(4, $favoritos->count()) }} de {{ $favoritos->count() }}
            </p>
            @else
            <div class="text-center py-8 text-slate-400 text-sm border-2 border-dashed border-slate-200 rounded-xl">
                <span class="material-symbols-outlined text-3xl mb-2 block">bookmark</span>
                {{ $esPropio ? 'Aún no guardaste ningún favorito' : 'Todavía no guardó favoritos' }}
            </div>
            @endif
        </section>

    </div>

    {{-- Columna derecha - Actividad (solo la ve el dueño del perfil) --}}
    @if($esPropio)
    <aside class="lg:col-span-4">
        <div class="glass-card rounded-2xl border border-slate-200 shadow-md p-6 sticky top-24">
            <h2 class="text-lg font-bold text-slate-800 mb-6">Mi actividad reciente</h2>

            @php
            $actividad = collect();

            auth()->user()->favoritos()->with('destino', 'evento')->get()->each(function($fav) use (&$actividad) {
            $nombre = $fav->destino ? $fav->destino->nombre : ($fav->evento ? $fav->evento->nombre : null);
            if ($nombre) {
            $actividad->push(['tipo' => 'favorito', 'texto' => 'Guardaste como favorito', 'nombre' => $nombre, 'fecha' => $fav->created_at, 'icono' => 'favorite', 'color' => 'rose']);
            }
            });

            auth()->user()->destinosVisitados()->with('destino')->get()->each(function($vis) use (&$actividad) {
            if ($vis->destino) {
            $actividad->push(['tipo' => 'visitado', 'texto' => 'Marcaste como visitado', 'nombre' => $vis->destino->nombre, 'fecha' => $vis->created_at, 'icono' => 'check_circle', 'color' => 'emerald']);
            }
            });

            auth()->user()->eventosVisitados()->with('evento')->get()->each(function($vis) use (&$actividad) {
            if ($vis->evento) {
            $actividad->push(['tipo' => 'evento_visitado', 'texto' => 'Marcaste como visitado el evento', 'nombre' => $vis->evento->nombre, 'fecha' => $vis->created_at, 'icono' => 'verified', 'color' => 'emerald']);
            }
            });

            auth()->user()->resenas()->with('destino', 'evento')->get()->each(function($r) use (&$actividad) {
            $nombre = $r->destino ? $r->destino->nombre : ($r->evento ? $r->evento->nombre : null);
            if ($nombre) {
            $actividad->push(['tipo' => 'resena', 'texto' => 'Escribiste una reseña en', 'nombre' => $nombre, 'fecha' => $r->created_at, 'icono' => 'rate_review', 'color' => 'amber']);
            }
            });

            $actividad = $actividad->sortByDesc('fecha')->take(8)->values();
            @endphp

            <div class="relative space-y-6 before:content-[''] before:absolute before:left-3 before:top-2 before:bottom-2 before:w-[2px] before:bg-slate-200">
                @foreach($actividad as $item)
                <div class="relative pl-10">
                    <div class="absolute left-0 top-1 w-6 h-6 rounded-full bg-{{ $item['color'] }}-100 flex items-center justify-center z-10">
                        <span class="material-symbols-outlined text-{{ $item['color'] }}-500 text-[14px]" style="font-variation-settings: 'FILL' 1;">{{ $item['icono'] }}</span>
                    </div>
                    <p class="text-sm text-slate-700">
                        {{ $item['texto'] }} <span class="font-semibold text-slate-800">{{ $item['nombre'] }}</span>
                    </p>
                    <p class="text-xs text-slate-400 mt-1">{{ $item['fecha']->diffForHumans() }}</p>
                </div>
                @endforeach

                <div class="relative pl-10">
                    <div class="absolute left-0 top-1 w-6 h-6 rounded-full bg-[#28628f]/20 flex items-center justify-center z-10">
                        <span class="material-symbols-outlined text-[#28628f] text-[14px]" style="font-variation-settings: 'FILL' 1;">person_add</span>
                    </div>
                    <p class="text-sm text-slate-700">Cuenta creada en Surify</p>
                    <p class="text-xs text-slate-400 mt-1">{{ auth()->user()->created_at->diffForHumans() }}</p>
                </div>
            </div>

            @if($actividad->isEmpty())
            <p class="text-xs text-slate-400 text-center mt-6">La actividad se irá registrando a medida que uses Surify</p>
            @endif
        </div>
    </aside>
    @endif
</div>

@endif {{-- fin perfil privado --}}

@if($esPropio)

{{-- Modal configuración --}}
<div id="modal-configuracion" class="hidden fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between p-6 border-b border-slate-100 sticky top-0 bg-white rounded-t-2xl z-10">
            <h3 class="text-lg font-bold text-[#28628f] flex items-center gap-2">
                <span class="material-symbols-outlined">settings</span>
                Configuración
            </h3>
            <button onclick="cerrarConfig()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="p-6 space-y-8">
            <section class="bg-slate-50 rounded-xl p-4 border border-slate-200 flex items-center gap-4">
                @if(auth()->user()->avatar)
                <img src="{{ auth()->user()->avatar }}" class="w-14 h-14 rounded-full object-cover border-2 border-[#28628f]/30" alt="Avatar">
                @else
                <div class="w-14 h-14 rounded-full bg-[#28628f] text-white flex items-center justify-center text-xl font-bold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                @endif
                <div>
                    <p class="font-bold text-slate-800">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-slate-500">{{ auth()->user()->email }}</p>
                </div>
            </section>

            <section>
                <h4 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-4 px-1">Información personal</h4>
                <div class="bg-slate-50 rounded-xl border border-slate-200 p-4">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </section>

            <section>
                <h4 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-4 px-1">Cambiar contraseña</h4>
                <div class="bg-slate-50 rounded-xl border border-slate-200 p-4">
                    @include('profile.partials.update-password-form')
                </div>
            </section>

            <section>
                <h4 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-4 px-1">Números de Emergencia Argentina</h4>
                <div class="bg-slate-50 rounded-xl border border-slate-200 overflow-hidden divide-y divide-slate-200">
                    @foreach([
                    ['911','Policía / Emergencias','local_police','text-blue-600','bg-blue-50'],
                    ['100','Bomberos','local_fire_department','text-orange-500','bg-orange-50'],
                    ['107','SAME / Ambulancia','emergency','text-red-500','bg-red-50'],
                    ['144','Violencia de Género','support_agent','text-purple-500','bg-purple-50'],
                    ['102','Niñez y Adolescencia','child_care','text-emerald-500','bg-emerald-50'],
                    ['103','Defensa Civil','shield','text-slate-600','bg-slate-100'],
                    ['134','Gas / Fugas','gas_meter','text-yellow-600','bg-yellow-50'],
                    ['135','Centro de Asistencia al Suicida','psychology','text-pink-500','bg-pink-50'],
                    ] as [$numero,$descripcion,$icono,$color,$bgColor])
                    <div class="flex items-center justify-between p-4 hover:bg-slate-100 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg {{ $bgColor }} flex items-center justify-center">
                                <span class="material-symbols-outlined {{ $color }} text-[18px]">{{ $icono }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-700">{{ $descripcion }}</p>
                                <p class="text-xs text-slate-400">Llamada gratuita las 24hs</p>
                            </div>
                        </div>
                        <a href="tel:{{ $numero }}" class="text-lg font-black text-[#28628f] hover:underline">{{ $numero }}</a>
                    </div>
                    @endforeach
                </div>
            </section>

            <section>
                <h4 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-4 px-1">Privacidad & Seguridad</h4>
                <div class="bg-slate-50 rounded-xl border border-slate-200 overflow-hidden divide-y divide-slate-200">
                    {{-- Visibilidad del perfil: se guarda al tocar el interruptor --}}
                    <form method="POST" action="{{ route('profile.visibilidad') }}"
                        class="flex items-center justify-between gap-3 p-4 hover:bg-slate-100 transition-colors">
                        @csrf
                        @method('PATCH')
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-slate-500">{{ auth()->user()->perfil_publico ? 'visibility' : 'visibility_off' }}</span>
                            <div>
                                <p class="text-sm text-slate-700">Perfil público</p>
                                <p class="text-xs text-slate-400">
                                    {{ auth()->user()->perfil_publico
                                        ? 'Los demás ven tus insignias, lugares, reseñas y favoritos.'
                                        : 'Los demás solo ven tu nombre y tu foto.' }}
                                </p>
                                <a href="{{ route('usuarios.show', ['user' => auth()->id(), 'vista' => 'publica']) }}" target="_blank"
                                    class="text-xs font-semibold text-[#28628f] hover:underline">Ver mi perfil público</a>
                            </div>
                        </div>
                        <input type="hidden" name="perfil_publico" value="0">
                        <label class="relative inline-flex items-center cursor-pointer shrink-0">
                            <input type="checkbox" name="perfil_publico" value="1" class="sr-only peer"
                                @checked(auth()->user()->perfil_publico) onchange="this.form.submit()">
                            <div class="w-11 h-6 bg-slate-300 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#28628f]"></div>
                        </label>
                    </form>
                    <a href="{{ route('two-factor.setup') }}" class="w-full flex items-center justify-between p-4 hover:bg-slate-100 transition-colors text-left text-decoration-none">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-slate-500">verified_user</span>
                            <p class="text-sm text-slate-700">Autenticación de Dos Factores</p>
                        </div>
                        @if(auth()->user()->hasTwoFactorEnabled())
                        <span class="text-emerald-500 text-xs font-semibold">Activado</span>
                        @else
                        <span class="text-red-400 text-xs font-semibold">Desactivado</span>
                        @endif
                    </a>
                </div>
            </section>

            <section>
                <h4 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-4 px-1">Preferencias</h4>
                <div class="bg-slate-50 rounded-xl border border-slate-200 overflow-hidden divide-y divide-slate-200">
                    <div class="p-4">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="material-symbols-outlined text-slate-500">light_mode</span>
                            <span class="text-sm text-slate-700">Tema</span>
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <button type="button" data-theme="dark" class="py-2 px-3 rounded-lg border border-slate-300 text-slate-500 text-xs hover:border-[#28628f] transition-all theme-btn">Oscuro</button>
                            <button type="button" data-theme="light" class="py-2 px-3 rounded-lg border border-slate-300 text-slate-500 text-xs hover:border-[#28628f] transition-all theme-btn">Claro</button>
                            <button type="button" data-theme="system" class="py-2 px-3 rounded-lg border border-slate-300 text-slate-500 text-xs hover:border-[#28628f] transition-all theme-btn">Sistema</button>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-4 hover:bg-slate-100 transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-slate-500">notifications</span>
                            <span class="text-sm text-slate-700">Notificaciones de festividades</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-300 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#28628f]"></div>
                        </label>
                    </div>
                </div>
            </section>

            <section>
                <h4 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-4 px-1">Soporte</h4>
                <div class="bg-slate-50 rounded-xl border border-slate-200 overflow-hidden divide-y divide-slate-200">
                    @foreach([
                    ['help_center', 'Centro de Ayuda', 'open_in_new', null],
                    ['description', 'Términos de Servicio', 'open_in_new', route('terminos')],
                    ['policy', 'Política de Privacidad', 'chevron_right', null],
                    ] as [$ico, $label, $end, $url])
                    @if($url)
                    <a href="{{ $url }}" target="_blank" rel="noopener" class="w-full flex items-center justify-between p-4 hover:bg-slate-100 transition-colors text-decoration-none">
                        @else
                        <button type="button" class="w-full flex items-center justify-between p-4 hover:bg-slate-100 transition-colors">
                            @endif
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-slate-500">{{ $ico }}</span>
                                <span class="text-sm text-slate-700">{{ $label }}</span>
                            </div>
                            <span class="material-symbols-outlined text-slate-400">{{ $end }}</span>
                            @if($url)
                    </a>
                    @else
                    </button>
                    @endif
                    @endforeach
                </div>
            </section>

            <section>
                <h4 class="text-sm font-bold text-red-400 uppercase tracking-wider mb-4 px-1">Zona de peligro</h4>
                <div class="bg-red-50 rounded-xl border border-red-100 p-4">
                    @include('profile.partials.delete-user-form')
                </div>
            </section>
        </div>
    </div>
</div>

{{-- Modal portadas --}}
<div id="modal-portada" class="hidden fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-slate-800">Elegí tu foto de portada</h3>
            <button onclick="document.getElementById('modal-portada').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            @foreach([
            ['https://señalcalafate.com/download/multimedia.normal.b4444966a274ae56.Z2xhY2lhcl9ub3JtYWwud2VicA==.webp','Glaciar Perito Moreno'],
            ['https://www.amerian.com/wp-content/uploads/2021/11/vista-panoramica-cataratas-pirayu-hotel-resort-puerto-iguazu-misiones-argentina.jpeg','Cataratas del Iguazú'],
            ['https://turismo-en-argentina.com/wp-content/uploads/2020/07/14074819238_6c00f7f002_o-1024x575.jpg','Cerro de los 7 Colores'],
            ['https://todopatagonia.net/wp-content/uploads/2016/10/Patagonia-Argentina.jpg.webp','Montañas Patagónicas'],
            ['https://cilsa.org/wp-content/uploads/2024/06/Fortin-soledad-atardecer-8-747x498.jpg','Bañado La Estrella'],
            ['https://turismo-en-argentina.com/wp-content/uploads/2020/07/Valle-de-la-luna-1-2-1024x576.jpg','Valle de la Luna'],
            ] as [$url,$nombre])
            <div class="relative cursor-pointer rounded-xl overflow-hidden border-2 border-transparent hover:border-[#28628f] transition-all"
                onclick="elegirPortada('{{ $url }}')">
                <img src="{{ $url }}" class="w-full h-24 object-cover" alt="{{ $nombre }}">
                <div class="absolute bottom-0 left-0 right-0 bg-black/50 text-white text-xs text-center py-1 font-semibold">{{ $nombre }}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif {{-- fin modales del dueño --}}

<script>
    var PORTADA_DEFAULT = 'https://señalcalafate.com/download/multimedia.normal.b4444966a274ae56.Z2xhY2lhcl9ub3JtYWwud2VicA==.webp';
    var portadaGuardada = localStorage.getItem('surify-portada-{{ $usuario->id }}');
    document.getElementById('hero-bg').src = portadaGuardada || PORTADA_DEFAULT;

    function abrirConfig() {
        document.getElementById('settings-btn-icon').style.transform = 'rotate(180deg)';
        document.getElementById('modal-configuracion').classList.remove('hidden');
    }

    function cerrarConfig() {
        document.getElementById('settings-btn-icon').style.transform = 'rotate(0deg)';
        document.getElementById('modal-configuracion').classList.add('hidden');
    }

    function elegirPortada(url) {
        document.getElementById('hero-bg').src = url;
        localStorage.setItem('surify-portada-{{ auth()->id() }}', url);
        document.getElementById('modal-portada').classList.add('hidden');
    }

    // ── Visitados destinos ──
    var visitadosActual = 0,
        visitadosPorPagina = 4;
    var totalVisitados = document.querySelectorAll('.visitado-item').length;

    function mostrarVisitados() {
        document.querySelectorAll('.visitado-item').forEach(item => {
            var i = parseInt(item.dataset.index);
            item.classList.toggle('hidden', i < visitadosActual || i >= visitadosActual + visitadosPorPagina);
        });
        var c = document.getElementById('visitados-counter');
        if (c) c.textContent = 'Mostrando ' + (visitadosActual + 1) + '-' + Math.min(visitadosActual + visitadosPorPagina, totalVisitados) + ' de ' + totalVisitados;
    }

    function nextVisitados() {
        if (visitadosActual + visitadosPorPagina < totalVisitados) {
            visitadosActual += visitadosPorPagina;
            mostrarVisitados();
        }
    }

    function prevVisitados() {
        if (visitadosActual > 0) {
            visitadosActual -= visitadosPorPagina;
            mostrarVisitados();
        }
    }

    // ── Favoritos destinos ──
    var favoritosActual = 0,
        favoritosPorPagina = 4;
    var totalFavoritos = document.querySelectorAll('.favorito-item').length;

    function mostrarFavoritos() {
        document.querySelectorAll('.favorito-item').forEach(item => {
            var i = parseInt(item.dataset.index);
            item.classList.toggle('hidden', i < favoritosActual || i >= favoritosActual + favoritosPorPagina);
        });
        var c = document.getElementById('favoritos-counter');
        if (c) c.textContent = 'Mostrando ' + (favoritosActual + 1) + '-' + Math.min(favoritosActual + favoritosPorPagina, totalFavoritos) + ' de ' + totalFavoritos;
    }

    function nextFavoritos() {
        if (favoritosActual + favoritosPorPagina < totalFavoritos) {
            favoritosActual += favoritosPorPagina;
            mostrarFavoritos();
        }
    }

    function prevFavoritos() {
        if (favoritosActual > 0) {
            favoritosActual -= favoritosPorPagina;
            mostrarFavoritos();
        }
    }

    // ── Festividades visitadas ──
    var festVisitadasActual = 0,
        festVisitadasPorPagina = 4;
    var totalFestVisitadas = document.querySelectorAll('.fest-visitada-item').length;

    function mostrarFestVisitadas() {
        document.querySelectorAll('.fest-visitada-item').forEach(item => {
            var i = parseInt(item.dataset.index);
            item.classList.toggle('hidden', i < festVisitadasActual || i >= festVisitadasActual + festVisitadasPorPagina);
        });
        var c = document.getElementById('fest-visitadas-counter');
        if (c) c.textContent = 'Mostrando ' + (festVisitadasActual + 1) + '-' + Math.min(festVisitadasActual + festVisitadasPorPagina, totalFestVisitadas) + ' de ' + totalFestVisitadas;
    }

    function nextFestVisitadas() {
        if (festVisitadasActual + festVisitadasPorPagina < totalFestVisitadas) {
            festVisitadasActual += festVisitadasPorPagina;
            mostrarFestVisitadas();
        }
    }

    function prevFestVisitadas() {
        if (festVisitadasActual > 0) {
            festVisitadasActual -= festVisitadasPorPagina;
            mostrarFestVisitadas();
        }
    }

    // ── Festividades favoritas ──
    var festFavoritasActual = 0,
        festFavoritasPorPagina = 4;
    var totalFestFavoritas = document.querySelectorAll('.festividad-item').length;

    function mostrarFestFavoritas() {
        document.querySelectorAll('.festividad-item').forEach(item => {
            var i = parseInt(item.dataset.index);
            item.classList.toggle('hidden', i < festFavoritasActual || i >= festFavoritasActual + festFavoritasPorPagina);
        });
        var c = document.getElementById('festividades-counter');
        if (c) c.textContent = 'Mostrando ' + (festFavoritasActual + 1) + '-' + Math.min(festFavoritasActual + festFavoritasPorPagina, totalFestFavoritas) + ' de ' + totalFestFavoritas;
    }

    function nextFestFavoritas() {
        if (festFavoritasActual + festFavoritasPorPagina < totalFestFavoritas) {
            festFavoritasActual += festFavoritasPorPagina;
            mostrarFestFavoritas();
        }
    }

    function prevFestFavoritas() {
        if (festFavoritasActual > 0) {
            festFavoritasActual -= festFavoritasPorPagina;
            mostrarFestFavoritas();
        }
    }

    function aplicarTema(themeName) {
        if (themeName === 'dark') {
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        } else if (themeName === 'light') {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        } else {
            localStorage.removeItem('theme');
            if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
        actualizarBotonesTema(themeName);
    }

    function actualizarBotonesTema(themeName) {
        document.querySelectorAll('.theme-btn').forEach(btn => {
            if (btn.dataset.theme === themeName) {
                btn.classList.remove('border-slate-300', 'dark:border-slate-700', 'text-slate-500', 'dark:text-slate-400');
                btn.classList.add('border-[#28628f]', 'bg-[#28628f]/10', 'text-[#28628f]', 'font-bold');
            } else {
                btn.classList.remove('border-[#28628f]', 'bg-[#28628f]/10', 'text-[#28628f]', 'font-bold');
                btn.classList.add('border-slate-300', 'dark:border-slate-700', 'text-slate-500', 'dark:text-slate-400');
            }
        });
    }

    // Inicializar en carga de página
    var temaGuardado = localStorage.getItem('theme') || 'system';
    actualizarBotonesTema(temaGuardado);

    document.querySelectorAll('.theme-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            aplicarTema(this.dataset.theme);
        });
    });
</script>

@endsection