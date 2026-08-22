@extends('layouts.app')

@section('title', 'Surify - ' . $destino->nombre)

@section('content')

{{-- Hero --}}
<section class="relative h-[450px] w-full overflow-hidden -mx-4 sm:-mx-6 lg:-mx-8 -mt-8 mb-8">
    <img src="{{ $destino->imagen_url ?? 'https://images.unsplash.com/photo-1501854140801-50d01698950b?w=1200' }}"
        class="w-full h-full object-cover" alt="{{ $destino->nombre }}">
    <div class="absolute inset-0" style="background: linear-gradient(to bottom, rgba(0,0,0,0.1), rgba(0,0,0,0.6));"></div>
    <div class="absolute bottom-0 left-0 p-8 max-w-3xl">
        <div class="flex items-center gap-2 mb-3">
            <a href="{{ route('provincia.show', $destino->provincia->nombre) }}"
                class="text-white/80 text-xs font-bold uppercase tracking-widest hover:text-white transition-colors text-decoration-none flex items-center gap-1">
                <span class="material-symbols-outlined text-[14px]">location_on</span>
                {{ $destino->provincia->nombre ?? 'Argentina' }}
            </a>
            <span class="text-white/40">•</span>
            <span class="text-white/80 text-xs font-bold uppercase tracking-widest">{{ $destino->categoria }}</span>
        </div>
        <h1 class="text-5xl font-black text-white tracking-tight leading-none mb-3" style="font-family: 'Inter', sans-serif;">
            {{ $destino->nombre }}
        </h1>
        <div class="flex items-center gap-3">
            <span class="px-3 py-1.5 bg-white/20 backdrop-blur-sm text-white text-xs font-bold rounded-full border border-white/30">
                {{ $destino->rango_precio ?? '$' }}
            </span>
            @if($destino->resenas->count() > 0)
            <div class="flex items-center gap-1">
                <span class="material-symbols-outlined text-amber-400 text-[16px]" style="font-variation-settings: 'FILL' 1;">star</span>
                <span class="text-white text-sm font-bold">{{ number_format($destino->resenas->avg('calificacion'), 1) }}</span>
                <span class="text-white/60 text-xs">({{ $destino->resenas->count() }} reseñas)</span>
            </div>
            @endif
        </div>
    </div>
</section>

{{-- Contenido principal --}}
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

    {{-- Columna izquierda --}}
    <div class="lg:col-span-8 flex flex-col gap-8">

        {{-- Descripción --}}
        <section class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h2 class="text-xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-[#28628f]">info</span>
                Sobre este destino
            </h2>
            <p class="text-slate-600 leading-relaxed">{{ $destino->descripcion }}</p>
        </section>

        {{-- Eventos --}}
        @if($destino->eventos->count() > 0)
        <section class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h2 class="text-xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-[#28628f]">celebration</span>
                Eventos en este destino
            </h2>
            <div class="flex flex-col gap-3">
                @foreach($destino->eventos as $evento)
                <a href="{{ route('eventos.show', $evento->id) }}"
                    class="flex gap-4 p-4 bg-slate-50 rounded-xl border border-slate-200 hover:border-[#28628f] hover:shadow-sm transition-all group text-decoration-none">
                    <div class="flex flex-col items-center justify-center bg-[#28628f] text-white w-14 h-14 rounded-xl font-bold shrink-0">
                        <span class="text-[10px] uppercase font-semibold leading-none mb-0.5">{{ \Carbon\Carbon::parse($evento->fecha_inicio)->format('M') }}</span>
                        <span class="text-lg font-black leading-none">{{ \Carbon\Carbon::parse($evento->fecha_inicio)->format('d') }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-slate-800 group-hover:text-[#28628f] transition-colors">{{ $evento->nombre }}</h4>
                        <p class="text-slate-400 text-xs mt-0.5 line-clamp-2">{{ $evento->descripcion }}</p>
                    </div>
                    <span class="material-symbols-outlined text-slate-300 group-hover:text-[#28628f] self-center transition-colors shrink-0">arrow_forward</span>
                </a>
                @endforeach
            </div>
        </section>
        @endif

        {{-- Clima --}}
        @if($clima)
        <section class="bg-white border-y border-slate-200 py-6 my-4 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 md:px-8">
                <h2 class="text-lg font-black text-slate-800 mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#28628f]">partly_cloudy_day</span>
                    Clima en {{ $destino->nombre }} ahora
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gradient-to-br from-[#28628f] to-[#1a4669] rounded-2xl p-6 text-white flex items-center gap-6">
                        <div class="text-center">
                            <img src="https://openweathermap.org/img/wn/{{ $clima['weather'][0]['icon'] }}@2x.png"
                                alt="{{ $clima['weather'][0]['description'] }}" class="w-20 h-20">
                        </div>
                        <div>
                            <p class="text-6xl font-black leading-none">{{ round($clima['main']['temp']) }}°</p>
                            <p class="text-white/80 capitalize text-sm mt-1">{{ $clima['weather'][0]['description'] }}</p>
                            <p class="text-white/60 text-xs mt-2">Sensación térmica: {{ round($clima['main']['feels_like']) }}°C</p>
                        </div>
                        <div class="ml-auto flex flex-col gap-2 text-sm">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[16px] text-white/70">water_drop</span>
                                <span>{{ $clima['main']['humidity'] }}% humedad</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[16px] text-white/70">air</span>
                                <span>{{ round($clima['wind']['speed'] * 3.6) }} km/h viento</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[16px] text-white/70">thermostat</span>
                                <span>{{ round($clima['main']['temp_min']) }}° / {{ round($clima['main']['temp_max']) }}°</span>
                            </div>
                        </div>
                    </div>
                    @if($pronostico)
                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Próximas horas</p>
                        <div class="grid grid-cols-4 gap-2">
                            @foreach(array_slice($pronostico['list'], 0, 4) as $item)
                            <div class="text-center bg-white rounded-xl p-2 border border-slate-100">
                                <p class="text-xs text-slate-400 font-semibold">{{ \Carbon\Carbon::parse($item['dt_txt'])->format('H:i') }}</p>
                                <img src="https://openweathermap.org/img/wn/{{ $item['weather'][0]['icon'] }}.png" class="w-10 h-10 mx-auto" alt="">
                                <p class="text-sm font-black text-slate-700">{{ round($item['main']['temp']) }}°</p>
                                <p class="text-[10px] text-slate-400 capitalize leading-tight">{{ $item['weather'][0]['description'] }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </section>
        @endif

        {{-- Reseñas --}}
        <section class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-[#28628f]">rate_review</span>
                Reseñas de la Comunidad
                @if($destino->resenas->count() > 0)
                <span class="text-sm font-normal text-slate-400">({{ $destino->resenas->count() }})</span>
                @endif
            </h2>

            @if($destino->resenas->where('aprobada', true)->count() > 0)
            <div class="flex flex-col gap-4">
                @foreach($destino->resenas->where('aprobada', true) as $resena)
                <div class="p-5 bg-slate-50 rounded-xl border border-slate-200">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-full bg-[#28628f] text-white flex items-center justify-center font-bold text-sm shrink-0 overflow-hidden">
                            @if($resena->anonima)
                            <span class="material-symbols-outlined text-[18px]">person</span>
                            @elseif($resena->user?->avatar)
                            <img src="{{ $resena->user->avatar }}" class="w-full h-full object-cover" alt="">
                            @else
                            {{ strtoupper(substr($resena->user?->name ?? '?', 0, 1)) }}
                            @endif
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-bold text-slate-800 text-sm">
                                    {{ $resena->anonima ? 'Viajero Anónimo' : ($resena->user?->name ?? 'Usuario') }}
                                </span>
                                <div class="flex items-center gap-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="material-symbols-outlined text-[14px] {{ $i <= $resena->calificacion ? 'text-amber-400' : 'text-slate-200' }}"
                                        style="font-variation-settings: 'FILL' 1;">star</span>
                                        @endfor
                                </div>
                            </div>
                            @if($resena->titulo)
                            <p class="font-semibold text-slate-700 text-sm mb-1">{{ $resena->titulo }}</p>
                            @endif
                            <p class="text-slate-500 text-sm leading-relaxed">{{ $resena->comentario }}</p>

                            @if($resena->imagenes && $resena->imagenes->count() > 0)
                            <div class="flex gap-2 mt-3 flex-wrap">
                                @foreach($resena->imagenes as $img)
                                <a href="{{ asset($img->url) }}" target="_blank" class="w-20 h-20 rounded-xl overflow-hidden border border-slate-200 hover:border-[#28628f] transition-all hover:scale-105 inline-block">
                                    <img src="{{ asset($img->url) }}" class="w-full h-full object-cover" alt="Foto de reseña">
                                </a>
                                @endforeach
                            </div>
                            @endif

                            <p class="text-xs text-slate-300 mt-2">{{ $resena->created_at->diffForHumans() }}</p>

                            {{-- Botones editar/eliminar solo para el autor --}}
                            @auth
                            @if(auth()->id() === $resena->user_id)
                            <div class="flex gap-2 mt-3">
                                <button onclick="abrirEditarResena({{ $resena->id }}, {{ $resena->calificacion }}, '{{ addslashes($resena->comentario) }}')"
                                    class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-xl bg-blue-50 border border-[#28628f] text-[#28628f] text-xs font-bold hover:bg-blue-100 transition-all">
                                    <span class="material-symbols-outlined text-[16px]">edit</span>
                                    Editar
                                </button>
                                <form action="{{ route('resenas.destroy', $resena->id) }}" method="POST"
                                    class="form-eliminar flex-1" data-title="¿Eliminar reseña?" data-text="Esta acción no se puede revertir.">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl bg-rose-50 border border-rose-400 text-rose-500 text-xs font-bold hover:bg-rose-100 transition-all">
                                        <span class="material-symbols-outlined text-[16px]">delete</span>
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                            @endif
                            @endauth
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8 border-2 border-dashed border-slate-200 rounded-xl">
                <span class="material-symbols-outlined text-4xl text-slate-300 block mb-2">chat_bubble_outline</span>
                <p class="text-slate-400 font-medium">Todavía no hay reseñas para este destino.</p>
                <p class="text-slate-300 text-sm mt-1">¡Sé el primero en comentar!</p>
            </div>
            @endif
        </section>

    </div>

    {{-- Columna derecha --}}
    <aside class="lg:col-span-4 flex flex-col gap-6">

        {{-- Info rápida --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-4">Información</h3>
            <div class="flex flex-col gap-3">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-[#28628f] text-[20px]">location_on</span>
                    <div>
                        <p class="text-xs text-slate-400">Provincia</p>
                        <p class="text-sm font-bold text-slate-700">{{ $destino->provincia->nombre ?? '—' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-[#28628f] text-[20px]">category</span>
                    <div>
                        <p class="text-xs text-slate-400">Categoría</p>
                        <p class="text-sm font-bold text-slate-700">{{ $destino->categoria ?? '—' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-[#28628f] text-[20px]">payments</span>
                    <div>
                        <p class="text-xs text-slate-400">Rango de precio</p>
                        <p class="text-sm font-bold text-slate-700">{{ $destino->rango_precio ?? '—' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Botones favorito y visitado --}}
        @auth
        @php
        $esFavorito = auth()->user()->favoritos->where('destino_id', $destino->id)->count() > 0;
        $esVisitado = auth()->user()->destinosVisitados->where('destino_id', $destino->id)->count() > 0;
        @endphp
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col gap-3">
            <button id="btn-favorito" onclick="toggleFavorito({{ $destino->id }})"
                class="{{ $esFavorito ? 'bg-pink-400 text-white border-pink-400' : 'bg-white text-slate-600 border-slate-200 hover:border-pink-300 hover:text-pink-400' }} w-full px-4 py-2.5 rounded-xl font-bold transition-all flex items-center gap-2 justify-center border text-sm">
                <span id="icono-favorito" class="material-symbols-outlined text-[18px]" style="font-variation-settings: 'FILL' {{ $esFavorito ? 1 : 0 }};">favorite</span>
                <span id="texto-favorito">{{ $esFavorito ? 'En favoritos ♥' : 'Agregar a favoritos' }}</span>
            </button>
            <button id="btn-visitado" onclick="toggleVisitado({{ $destino->id }})"
                class="{{ $esVisitado ? 'bg-emerald-500 text-white border-emerald-500' : 'bg-white text-slate-600 border-slate-200 hover:border-[#28628f] hover:text-[#28628f]' }} w-full px-4 py-2.5 rounded-xl font-bold transition-all flex items-center gap-2 justify-center border text-sm">
                <span id="icono-visitado" class="material-symbols-outlined text-[18px]" style="font-variation-settings: 'FILL' {{ $esVisitado ? 1 : 0 }};">verified</span>
                <span id="texto-visitado">{{ $esVisitado ? 'Visitado ✓' : 'Marcar como visitado' }}</span>
            </button>
        </div>
        @endauth

        {{-- Compartir --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
            <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-3">Compartir destino</h3>
            <div class="grid grid-cols-2 gap-2">
                <a href="https://wa.me/?text={{ urlencode('¡Mirá ' . $destino->nombre . ' en Surify! ' . url()->current()) }}"
                    target="_blank"
                    class="flex items-center justify-center gap-2 py-2.5 rounded-xl bg-green-50 border border-green-200 text-green-600 text-xs font-bold hover:bg-green-100 transition-all text-decoration-none">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z" />
                        <path d="M12 0C5.373 0 0 5.373 0 12c0 2.107.547 4.068 1.504 5.774L.057 23.428a.75.75 0 00.921.921l5.656-1.447A11.93 11.93 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.907 0-3.695-.504-5.243-1.387l-.375-.217-3.888.995.995-3.888-.217-.375A9.96 9.96 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z" />
                    </svg>
                    WhatsApp
                </a>
                <a href="https://twitter.com/intent/tweet?text={{ urlencode('¡Descubrí ' . $destino->nombre . ' en Surify! 🇦🇷') }}&url={{ urlencode(url()->current()) }}"
                    target="_blank"
                    class="flex items-center justify-center gap-2 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold hover:bg-slate-100 transition-all text-decoration-none">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.742l7.737-8.835L1.254 2.25H8.08l4.259 5.629 5.905-5.629zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                    </svg>
                    X / Twitter
                </a>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                    target="_blank"
                    class="flex items-center justify-center gap-2 py-2.5 rounded-xl bg-blue-50 border border-blue-200 text-blue-600 text-xs font-bold hover:bg-blue-100 transition-all text-decoration-none">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                    </svg>
                    Facebook
                </a>
                <button onclick="copiarLink()" id="btn-copiar"
                    class="flex items-center justify-center gap-2 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold hover:bg-slate-100 transition-all">
                    <span class="material-symbols-outlined text-[16px]">link</span>
                    <span id="texto-copiar">Copiar link</span>
                </button>
            </div>
        </div>

        {{-- Escribir reseña --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 text-center">
            <span class="material-symbols-outlined text-4xl text-[#28628f] block mb-3">rate_review</span>
            <h3 class="text-base font-bold text-slate-800 mb-2">¿Visitaste este lugar?</h3>
            <p class="text-slate-500 text-sm mb-5">Compartí tu experiencia con otros viajeros.</p>
            @auth
            <button onclick="document.getElementById('modal-resena-destino').classList.remove('hidden')"
                class="bg-[#28628f] text-white px-6 py-2.5 rounded-xl font-bold hover:bg-[#1a4669] transition-all shadow-sm inline-flex items-center gap-2 w-full justify-center">
                <span class="material-symbols-outlined text-[18px]">edit</span>
                Escribir reseña
            </button>
            @else
            <a href="{{ route('login') }}"
                class="bg-[#28628f] text-white px-6 py-2.5 rounded-xl font-bold hover:bg-[#1a4669] transition-all shadow-sm inline-flex items-center gap-2 w-full justify-center text-decoration-none">
                <span class="material-symbols-outlined text-[18px]">login</span>
                Iniciá sesión para comentar
            </a>
            @endauth
        </div>

        {{-- Volver --}}
        <a href="{{ route('provincia.show', $destino->provincia->nombre) }}"
            class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-6 py-3 rounded-xl font-bold transition-all flex items-center gap-2 justify-center text-decoration-none">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            Volver a {{ $destino->provincia->nombre ?? 'la provincia' }}
        </a>

    </aside>
</div>

{{-- Modal nueva reseña --}}
<div id="modal-resena-destino" class="hidden fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50 rounded-t-2xl">
            <div class="flex items-center justify-between mb-1">
                <nav class="flex items-center gap-1 text-xs font-bold uppercase tracking-wider">
                    <span class="text-slate-500">Destinos</span>
                    <span class="material-symbols-outlined text-slate-400 text-[14px]">chevron_right</span>
                    <span class="text-[#28628f]">{{ $destino->provincia->nombre ?? '' }}</span>
                </nav>
                <button onclick="document.getElementById('modal-resena-destino').classList.add('hidden')"
                    class="text-slate-400 hover:text-slate-600 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <h2 class="text-xl font-bold text-slate-800">Compartí tu experiencia: {{ $destino->nombre }}</h2>
            <p class="text-sm text-slate-500 mt-1">Ayudá a otros a descubrir lo mejor de este destino.</p>
        </div>
        <form action="{{ route('resenas.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            <input type="hidden" name="destino_id" value="{{ $destino->id }}">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Calificación General</label>
                    <div class="flex gap-1" id="estrellas-destino">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" onclick="setCalificacionDestino({{ $i }})"
                            class="estrella-destino material-symbols-outlined text-[36px] text-amber-400 cursor-pointer transition-all"
                            style="font-variation-settings: 'FILL' 1;" data-valor="{{ $i }}">star</button>
                            @endfor
                    </div>
                    <input type="hidden" name="calificacion" id="calificacion-destino" value="5">
                    <p class="text-xs text-slate-400">Hacé clic en una estrella para calificar</p>
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Título de la reseña</label>
                    <input type="text" name="titulo" placeholder="Resumí tu visita en pocas palabras"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#28628f] transition-colors">
                </div>
            </div>
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Categorías</label>
                <div class="flex flex-wrap gap-2">
                    @foreach(['Ideal para familias', 'Aventura', 'Naturaleza', 'Gastronomía', 'Fotografía', 'Cultura', 'Relax'] as $cat)
                    <button type="button" onclick="toggleCategoriaDestino(this)"
                        class="cat-btn-destino px-4 py-1.5 rounded-full border border-slate-200 text-slate-600 font-semibold text-xs hover:border-[#28628f] hover:text-[#28628f] transition-all">
                        {{ $cat }}
                    </button>
                    @endforeach
                </div>
            </div>
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Tu Comentario</label>
                <textarea name="comentario" rows="5" required
                    placeholder="¿Cuál fue lo mejor de tu visita? ¿Tenés consejos para senderos, horarios o actividades?"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#28628f] transition-colors resize-none"></textarea>
            </div>
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Fotos</label>
                <div onclick="document.getElementById('fotos-input').click()"
                    class="border-2 border-dashed border-slate-200 rounded-xl p-8 flex flex-col items-center justify-center bg-slate-50 hover:bg-slate-100 transition-colors cursor-pointer group">
                    <div class="w-12 h-12 bg-[#28628f]/10 rounded-full flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-[#28628f] text-[28px]">upload_file</span>
                    </div>
                    <p class="text-sm font-semibold text-slate-700">Arrastrá y soltá tus fotos acá</p>
                    <p class="text-xs text-slate-400 mt-1">O hacé clic para explorar (hasta 5 fotos)</p>
                </div>
                <input type="file" id="fotos-input" name="imagenes[]" multiple accept="image/*" class="hidden" onchange="previewFotos(this)">
                <div id="fotos-preview" class="flex gap-3 overflow-x-auto pb-2 mt-2 hidden"></div>
            </div>
            <div class="flex items-start gap-3 py-3 border-t border-slate-100">
                <input type="checkbox" name="anonima" value="1" id="anonima-destino"
                    class="mt-1 w-5 h-5 rounded border-slate-300 text-[#28628f] focus:ring-[#28628f]">
                <label for="anonima-destino" class="text-sm text-slate-600 cursor-pointer">
                    Publicar de forma anónima
                    <span class="block text-xs text-slate-400 mt-0.5 italic">Tu nombre y foto no serán visibles para otros viajeros.</span>
                </label>
            </div>
            <div class="flex flex-col md:flex-row-reverse gap-3 pt-2">
                <button type="submit" class="w-full md:w-auto px-8 py-3 bg-[#28628f] text-white font-bold rounded-full hover:bg-[#1a4669] transition-all shadow-md">
                    Publicar Reseña
                </button>
                <button type="button" onclick="document.getElementById('modal-resena-destino').classList.add('hidden')"
                    class="w-full md:w-auto px-8 py-3 border border-[#28628f] text-[#28628f] font-bold rounded-full hover:bg-blue-50 transition-all">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal editar reseña --}}
@auth
<div id="modal-editar-resena" class="hidden fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-lg font-bold text-slate-800">Editar reseña</h2>
            <button onclick="document.getElementById('modal-editar-resena').classList.add('hidden')"
                class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form id="form-editar-resena" action="" method="POST" class="p-6 space-y-4">
            @csrf @method('PUT')
            <div class="space-y-1">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Calificación</label>
                <div class="flex gap-2" id="estrellas-editar">
                    @for($i = 1; $i <= 5; $i++)
                        <span class="material-symbols-outlined text-[28px] text-slate-200 cursor-pointer estrella-editar"
                        style="font-variation-settings: 'FILL' 1;" data-value="{{ $i }}">star</span>
                        @endfor
                </div>
                <input type="hidden" name="calificacion" id="editar-resena-calificacion" value="5">
            </div>
            <div class="space-y-1">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Comentario</label>
                <textarea name="comentario" id="editar-resena-comentario" rows="4" required
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#28628f] resize-none"></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-[#28628f] text-white font-bold py-3 rounded-xl hover:bg-[#1a4669] transition-all">
                    Guardar cambios
                </button>
                <button type="button" onclick="document.getElementById('modal-editar-resena').classList.add('hidden')"
                    class="flex-1 border border-slate-200 text-slate-600 font-bold py-3 rounded-xl hover:bg-slate-50 transition-all">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>
@endauth

<script>
    function setCalificacionDestino(valor) {
        document.getElementById('calificacion-destino').value = valor;
        document.querySelectorAll('.estrella-destino').forEach(function(e) {
            const v = parseInt(e.dataset.valor);
            e.style.fontVariationSettings = v <= valor ? "'FILL' 1" : "'FILL' 0";
            e.classList.toggle('text-amber-400', v <= valor);
            e.classList.toggle('text-slate-300', v > valor);
        });
    }

    function toggleCategoriaDestino(btn) {
        btn.classList.toggle('border-[#28628f]');
        btn.classList.toggle('text-[#28628f]');
        btn.classList.toggle('bg-[#28628f]/5');
        btn.classList.toggle('border-slate-200');
        btn.classList.toggle('text-slate-600');
    }

    function previewFotos(input) {
        const preview = document.getElementById('fotos-preview');
        preview.innerHTML = '';
        preview.classList.remove('hidden');
        Array.from(input.files).slice(0, 5).forEach(function(file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative w-24 h-24 flex-shrink-0 rounded-xl overflow-hidden border border-slate-200';
                div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                preview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    }

    function abrirEditarResena(id, calificacion, comentario) {
        document.getElementById('editar-resena-comentario').value = comentario;
        document.getElementById('editar-resena-calificacion').value = calificacion;
        document.getElementById('form-editar-resena').action = `/resenas/${id}`;

        document.querySelectorAll('.estrella-editar').forEach(function(s) {
            const v = parseInt(s.dataset.value);
            s.style.color = v <= calificacion ? '#fbbf24' : '#e2e8f0';
            s.onclick = function() {
                document.getElementById('editar-resena-calificacion').value = v;
                document.querySelectorAll('.estrella-editar').forEach(function(s2) {
                    s2.style.color = parseInt(s2.dataset.value) <= v ? '#fbbf24' : '#e2e8f0';
                });
            };
        });

        document.getElementById('modal-editar-resena').classList.remove('hidden');
    }

    function toggleFavorito(destinoId) {
        fetch('/favoritos/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                destino_id: destinoId
            })
        }).then(r => r.json()).then(data => {
            const btn = document.getElementById('btn-favorito');
            const icono = document.getElementById('icono-favorito');
            const texto = document.getElementById('texto-favorito');
            if (data.favorito) {
                btn.className = 'bg-pink-400 text-white border-pink-400 w-full px-4 py-2.5 rounded-xl font-bold transition-all flex items-center gap-2 justify-center border text-sm';
                icono.style.fontVariationSettings = "'FILL' 1";
                texto.textContent = 'En favoritos ♥';
            } else {
                btn.className = 'bg-white text-slate-600 border-slate-200 hover:border-pink-300 hover:text-pink-400 w-full px-4 py-2.5 rounded-xl font-bold transition-all flex items-center gap-2 justify-center border text-sm';
                icono.style.fontVariationSettings = "'FILL' 0";
                texto.textContent = 'Agregar a favoritos';
            }
        });
    }

    function toggleVisitado(destinoId) {
        fetch('/visitados/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                destino_id: destinoId
            })
        }).then(r => r.json()).then(data => {
            const btn = document.getElementById('btn-visitado');
            const icono = document.getElementById('icono-visitado');
            const texto = document.getElementById('texto-visitado');
            if (data.visitado) {
                btn.className = 'bg-emerald-500 text-white border-emerald-500 w-full px-4 py-2.5 rounded-xl font-bold transition-all flex items-center gap-2 justify-center border text-sm';
                icono.style.fontVariationSettings = "'FILL' 1";
                texto.textContent = 'Visitado ✓';
            } else {
                btn.className = 'bg-white text-slate-600 border-slate-200 hover:border-[#28628f] hover:text-[#28628f] w-full px-4 py-2.5 rounded-xl font-bold transition-all flex items-center gap-2 justify-center border text-sm';
                icono.style.fontVariationSettings = "'FILL' 0";
                texto.textContent = 'Marcar como visitado';
            }
        });
    }

    function copiarLink() {
        navigator.clipboard.writeText(window.location.href).then(function() {
            document.getElementById('texto-copiar').textContent = '¡Copiado!';
            document.getElementById('btn-copiar').classList.add('border-emerald-300', 'bg-emerald-50', 'text-emerald-600');
            setTimeout(function() {
                document.getElementById('texto-copiar').textContent = 'Copiar link';
                document.getElementById('btn-copiar').classList.remove('border-emerald-300', 'bg-emerald-50', 'text-emerald-600');
            }, 2000);
        });
    }
</script>

@endsection