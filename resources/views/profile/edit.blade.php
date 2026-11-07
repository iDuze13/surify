@extends('layouts.app')

@section('title', 'Mi Perfil - Surify')

@section('content')

<style>
    .hero-gradient {
        background: linear-gradient(to bottom, rgba(247, 249, 252, 0.2), rgba(247, 249, 252, 1));
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(12px);
    }

    .scroll-hide::-webkit-scrollbar {
        display: none;
    }

    html {
        scroll-behavior: smooth;
    }
</style>

{{-- Hero Section --}}
<section class="relative h-[400px] w-full overflow-hidden -mx-4 sm:-mx-6 lg:-mx-8 -mt-8">
    <img id="hero-bg" src="" class="w-full h-full object-cover" alt="Hero">
    <button onclick="document.getElementById('modal-portada').classList.remove('hidden')"
        class="absolute top-4 right-4 z-20 bg-black/50 hover:bg-black/70 text-white text-xs font-semibold px-3 py-2 rounded-full flex items-center gap-1 transition-all">
        <span class="material-symbols-outlined text-[16px]">wallpaper</span>
        Cambiar portada
    </button>
    <button onclick="abrirConfig()"
        class="absolute top-4 left-4 z-20 bg-black/50 hover:bg-black/70 text-white p-2 rounded-full transition-all group">
        <span id="settings-btn-icon" class="material-symbols-outlined text-[20px] transition-transform duration-500">settings</span>
    </button>
    <div class="absolute inset-0 hero-gradient"></div>
    <div class="absolute inset-0 flex flex-col items-center justify-center mt-8">
        <div class="relative group cursor-pointer" onclick="document.getElementById('avatar-input').click()">
            @if(auth()->user()->avatar)
            <img src="{{ auth()->user()->avatar }}" class="w-32 h-32 md:w-40 md:h-40 rounded-full border-4 border-white object-cover shadow-2xl" alt="Avatar">
            @else
            <div class="w-32 h-32 md:w-40 md:h-40 rounded-full bg-[#28628f] text-white flex items-center justify-center text-4xl font-black border-4 border-white shadow-2xl">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            @endif
            <div class="absolute inset-0 rounded-full bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                <span class="material-symbols-outlined text-white text-3xl">photo_camera</span>
            </div>
            <div class="absolute bottom-2 right-2 bg-[#28628f] text-white rounded-full p-1 border-4 border-white">
                <span class="material-symbols-outlined text-[14px]" style="font-variation-settings: 'FILL' 1;">verified</span>
            </div>
        </div>
        <form method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data" id="avatar-form">
            @csrf
            <input type="file" id="avatar-input" name="avatar" accept="image/*" class="hidden"
                onchange="document.getElementById('avatar-form').submit()">
        </form>
        <h1 class="text-3xl font-black text-slate-800 mt-3" style="font-family: 'Inter', sans-serif;">{{ auth()->user()->name }}</h1>
        <p class="text-sm text-slate-500">{{ auth()->user()->email }}</p>
        <p class="text-xs text-[#28628f] font-bold uppercase tracking-widest mt-1">
            Miembro desde {{ auth()->user()->created_at->translatedFormat('F Y') }}
        </p>
    </div>
</section>

{{-- Stats --}}
@php
$countVisitados = auth()->user()->destinosVisitados()->count();
$countResenas = auth()->user()->resenas()->count();
@endphp
<div class="max-w-2xl mx-auto -mt-6 relative z-10 mb-8 px-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xl p-6 grid grid-cols-3 gap-4 text-center">
        <div>
            <div class="text-2xl font-bold text-[#28628f]">{{ $countVisitados }}</div>
            <div class="text-xs text-slate-500 mt-1">Lugares visitados</div>
        </div>
        <div class="border-x border-slate-200">
            <div class="text-2xl font-bold text-[#28628f]">{{ $countResenas }}</div>
            <div class="text-xs text-slate-500 mt-1">Reseñas publicadas</div>
        </div>
        <div>
            <div class="text-2xl font-bold text-[#28628f]">0</div>
            <div class="text-xs text-slate-500 mt-1">Fotos subidas</div>
        </div>
    </div>
</div>

{{-- Contenido principal --}}
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    {{-- Columna izquierda --}}
    <div class="lg:col-span-8 space-y-6">

        {{-- Insignias --}}
        <section class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2 mb-4">
                <span class="material-symbols-outlined text-yellow-500" style="font-variation-settings: 'FILL' 1;">military_tech</span>
                Mis Insignias
            </h2>
            @php
            $visitadosInsignias = auth()->user()->destinosVisitados()->with('destino.provincia')->get();
            $resenasCount = auth()->user()->resenas()->count();
            $provinciasVisitadas = $visitadosInsignias->pluck('destino.provincia.nombre')->unique()->filter()->count();
            $provinciasNorte = ['Jujuy', 'Salta', 'Formosa', 'Chaco', 'Santiago del Estero'];
            $provinciasPatagonia = ['Santa Cruz', 'Chubut', 'Tierra del Fuego', 'Neuquén', 'Río Negro'];
            $insignias = [
            ['emoji' => '🐣', 'nombre' => 'Aventurero Inicial', 'descripcion' => 'Te uniste a Surify', 'ganada' => true],
            ['icono' => 'north', 'nombre' => 'Viajero del Norte', 'descripcion' => 'Visitaste el norte argentino', 'ganada' => $visitadosInsignias->contains(fn($v) => in_array($v->destino?->provincia?->nombre, $provinciasNorte))],
            ['icono' => 'landscape', 'nombre' => 'Explorador Patagónico', 'descripcion' => 'Visitaste la Patagonia', 'ganada' => $visitadosInsignias->contains(fn($v) => in_array($v->destino?->provincia?->nombre, $provinciasPatagonia))],
            ['icono' => 'forum', 'nombre' => 'Comentador Activo', 'descripcion' => 'Publicaste 3 o más reseñas', 'ganada' => $resenasCount >= 3],
            ['icono' => 'hiking', 'nombre' => 'Montañista Pro', 'descripcion' => 'Visitaste un destino de montaña', 'ganada' => $visitadosInsignias->contains(fn($v) => $v->destino?->categoria === 'montaña')],
            ['icono' => 'restaurant', 'nombre' => 'Catador de Asados', 'descripcion' => 'Visitaste un destino gastronómico', 'ganada' => $visitadosInsignias->contains(fn($v) => $v->destino?->categoria === 'gastronomia')],
            ['icono' => 'map', 'nombre' => 'Explorador Nacional', 'descripcion' => 'Visitaste 3 o más provincias', 'ganada' => $provinciasVisitadas >= 3],
            ];
            @endphp
            <div class="flex gap-4 overflow-x-auto pb-2 scroll-hide">
                @foreach($insignias as $insignia)
                <div class="flex-shrink-0 flex flex-col items-center gap-2 {{ $insignia['ganada'] ? '' : 'opacity-40 grayscale' }}" title="{{ $insignia['descripcion'] }}">
                    <div class="w-20 h-20 rounded-full flex items-center justify-center border-2 shadow-sm
                        {{ $insignia['ganada'] ? 'bg-gradient-to-br from-[#28628f] to-[#1a4669] border-[#28628f]/30' : 'bg-slate-200 border-dashed border-slate-400' }}">
                        @if(isset($insignia['emoji']))
                        <span class="text-3xl">{{ $insignia['emoji'] }}</span>
                        @else
                        <span class="material-symbols-outlined text-3xl {{ $insignia['ganada'] ? 'text-white' : 'text-slate-500' }}"
                            style="font-variation-settings: 'FILL' {{ $insignia['ganada'] ? '1' : '0' }};">{{ $insignia['icono'] }}</span>
                        @endif
                    </div>
                    <span class="text-xs font-semibold text-center max-w-[80px] {{ $insignia['ganada'] ? 'text-slate-700' : 'text-slate-400' }}">{{ $insignia['nombre'] }}</span>
                    @if($insignia['ganada'])
                    <span class="text-[9px] font-bold text-emerald-500 uppercase tracking-wider">✓ Ganada</span>
                    @else
                    <span class="text-[9px] text-slate-400">Bloqueada</span>
                    @endif
                </div>
                @endforeach
            </div>
        </section>

        {{-- Lugares visitados --}}
        <section class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-slate-800">Mis lugares visitados</h2>
                <div class="flex gap-2">
                    <button onclick="prevVisitados()" class="p-2 rounded-full border border-slate-200 hover:bg-slate-100 transition-colors">
                        <span class="material-symbols-outlined text-sm">chevron_left</span>
                    </button>
                    <button onclick="nextVisitados()" class="p-2 rounded-full border border-slate-200 hover:bg-slate-100 transition-colors">
                        <span class="material-symbols-outlined text-sm">chevron_right</span>
                    </button>
                </div>
            </div>
            @php
            $visitados = auth()->user()->destinosVisitados()->with('destino.provincia')->get()->filter(fn($v) => $v->destino)->values();
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
                Aún no marcaste ningún destino como visitado
            </div>
            @endif
        </section>

        {{-- Festividades visitadas --}}
        <section class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                    <span class="material-symbols-outlined text-emerald-500" style="font-variation-settings: 'FILL' 1;">celebration</span>
                    Festividades visitadas
                </h2>
                <div class="flex gap-2">
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
            $festVisitadas = auth()->user()->eventosVisitados()->with('evento.provincia')->get()->filter(fn($v) => $v->evento)->values();
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
                Aún no marcaste ninguna festividad como visitada
            </div>
            @endif
        </section>

        {{-- Festividades favoritas --}}
        <section class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                    <span class="material-symbols-outlined text-rose-500" style="font-variation-settings: 'FILL' 1;">favorite</span>
                    Festividades favoritas
                </h2>
                <div class="flex gap-2">
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
            $festividades = auth()->user()->favoritos()->with('evento.provincia')->get()->filter(function ($f) {
            return $f->evento;
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
                Aún no guardaste ninguna festividad como favorita
            </div>
            @endif
        </section>

        {{-- Favoritos destinos --}}
        <section class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-slate-800">Mis favoritos</h2>
                <div class="flex gap-2">
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
            $favoritos = auth()->user()->favoritos()->with('destino.provincia')->get()->filter(fn($f) => $f->destino)->values();
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
                Aún no guardaste ningún favorito
            </div>
            @endif
        </section>

    </div>

    {{-- Columna derecha - Actividad --}}
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
</div>

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
                <h4 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-4 px-1">Conexiones Sociales</h4>
                <div class="bg-slate-50 rounded-xl border border-slate-200 overflow-hidden divide-y divide-slate-200">
                    @foreach([['photo_camera','Instagram'],['terminal','X / Twitter'],['public','Facebook']] as [$ico,$red])
                    <div class="flex items-center justify-between p-4 hover:bg-slate-100 transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-slate-500">{{ $ico }}</span>
                            <span class="text-sm text-slate-700">{{ $red }}</span>
                        </div>
                        <button class="text-[#28628f] text-xs font-semibold hover:underline">Vincular</button>
                    </div>
                    @endforeach
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
                    <button class="w-full flex items-center justify-between p-4 hover:bg-slate-100 transition-colors text-left">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-slate-500">visibility</span>
                            <div>
                                <p class="text-sm text-slate-700">Visibilidad del Perfil</p>
                                <p class="text-xs text-slate-400">Público</p>
                            </div>
                        </div>
                        <span class="material-symbols-outlined text-slate-400">chevron_right</span>
                    </button>
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
                            <button class="py-2 px-3 rounded-lg border border-slate-300 text-slate-500 text-xs hover:border-[#28628f] transition-all theme-btn">Oscuro</button>
                            <button class="py-2 px-3 rounded-lg border border-[#28628f] bg-[#28628f]/10 text-[#28628f] text-xs font-bold transition-all theme-btn">Claro</button>
                            <button class="py-2 px-3 rounded-lg border border-slate-300 text-slate-500 text-xs hover:border-[#28628f] transition-all theme-btn">Sistema</button>
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
                    @foreach([['help_center','Centro de Ayuda','open_in_new'],['description','Términos de Servicio','chevron_right'],['policy','Política de Privacidad','chevron_right']] as [$ico,$label,$end])
                    <button class="w-full flex items-center justify-between p-4 hover:bg-slate-100 transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-slate-500">{{ $ico }}</span>
                            <span class="text-sm text-slate-700">{{ $label }}</span>
                        </div>
                        <span class="material-symbols-outlined text-slate-400">{{ $end }}</span>
                    </button>
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

<script>
    var PORTADA_DEFAULT = 'https://señalcalafate.com/download/multimedia.normal.b4444966a274ae56.Z2xhY2lhcl9ub3JtYWwud2VicA==.webp';
    var portadaGuardada = localStorage.getItem('surify-portada-{{ auth()->id() }}');
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