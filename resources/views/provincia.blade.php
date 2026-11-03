@extends('layouts.app')

@section('title', 'Surify - ' . ($provincia ? $provincia->nombre : $nombre))

@section('content')

<div class="antialiased min-h-screen pt-4">

    {{-- ========== HERO ========== --}}
    <section class="max-w-7xl mx-auto px-4 md:px-8 py-6">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">

            {{-- Texto izquierda --}}
            <div class="lg:col-span-5 flex flex-col gap-4">
                <div class="flex items-center gap-1">
                    <span class="material-symbols-outlined text-[#28628f]">location_on</span>
                    <span class="text-xs font-bold uppercase tracking-widest text-[#28628f]">
                        {{ $provincia ? ($provincia->region ?? 'Argentina') : 'Argentina' }}
                    </span>
                </div>

                <h1 class="text-5xl md:text-6xl font-black text-[#28628f] tracking-tighter leading-none" style="font-family: 'Inter', sans-serif;">
                    {{ $provincia ? $provincia->nombre : $nombre }}
                </h1>

                <p class="text-base text-slate-600 leading-relaxed">
                    {{ $provincia && $provincia->descripcion
                        ? $provincia->descripcion
                        : 'Descubrí los atractivos turísticos, paisajes imponentes, gastronomía regional y festivales tradicionales que esta provincia tiene para ofrecer.' }}
                </p>

                <div class="flex gap-3 pt-2">
                    <a href="#destinos-section"
                        class="bg-[#28628f] text-white px-6 py-3 rounded-xl font-bold hover:bg-[#1a4669] transition-all shadow-sm flex items-center gap-1 text-decoration-none">
                        <span>Explorar Destinos</span>
                        <span class="material-symbols-outlined text-sm">arrow_downward</span>
                    </a>
                    <a href="{{ route('mapa.nacional') }}"
                        class="border border-[#28628f] text-[#28628f] px-6 py-3 rounded-xl font-bold hover:bg-blue-50 transition-all flex items-center gap-1 text-decoration-none">
                        <span class="material-symbols-outlined text-sm">map</span>
                        <span>Ver Mapa</span>
                    </a>
                </div>
            </div>

            {{-- Galería derecha --}}
            <div class="lg:col-span-7 grid grid-cols-2 grid-rows-2 gap-3 h-[450px]">
                @php
                $imagenesProvincia = $provincia ? $provincia->imagenes->take(3)->values() : collect();

                $img1 = $imagenesProvincia->get(0)?->url
                ?? 'https://images.unsplash.com/photo-1589307775553-9f62f3a61dfc?auto=format&fit=crop&w=1200&q=80';

                $img2 = $imagenesProvincia->get(1)?->url
                ?? 'https://images.unsplash.com/photo-1513026705753-bc31df43b444?auto=format&fit=crop&w=600&q=80';

                $img3 = $imagenesProvincia->get(2)?->url
                ?? 'https://images.unsplash.com/photo-1560493676-04071c5f467b?auto=format&fit=crop&w=600&q=80';
                @endphp
                <div class="col-span-2 row-span-1 rounded-2xl overflow-hidden relative group shadow-sm">
                    <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                        src="{{ $img1 }}" alt="Banner {{ $nombre }}">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>
                </div>
                <div class="col-span-1 row-span-1 rounded-2xl overflow-hidden group shadow-sm bg-slate-100">
                    <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                        src="{{ $img2 }}" alt="Detalle 1">
                </div>
                <div class="col-span-1 row-span-1 rounded-2xl overflow-hidden group shadow-sm bg-slate-100">
                    <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                        src="{{ $img3 }}" alt="Detalle 2">
                </div>
            </div>
        </div>
    </section>

    {{-- ========== CHIPS DE CARACTERÍSTICAS ========== --}}
    <section class="bg-white border-y border-slate-200 py-6 my-4 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 md:px-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div class="max-w-xl">
                    <h2 class="text-xl font-black text-slate-800 tracking-tight mb-1">Cultura, Naturaleza e Historia</h2>
                    <p class="text-sm text-slate-500 leading-relaxed">Una experiencia integral para el explorador moderno, con datos en tiempo real.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <div class="flex items-center gap-1.5 px-4 py-2 bg-slate-50 border border-slate-200 rounded-full shadow-sm">
                        <span class="material-symbols-outlined text-[#28628f] text-[18px]">restaurant</span>
                        <span class="text-xs font-bold text-slate-700">Gastronomía Regional</span>
                    </div>
                    <div class="flex items-center gap-1.5 px-4 py-2 bg-slate-50 border border-slate-200 rounded-full shadow-sm">
                        <span class="material-symbols-outlined text-[#28628f] text-[18px]">partly_cloudy_day</span>
                        <span class="text-xs font-bold text-slate-700">Clima en Tiempo Real</span>
                    </div>
                    <div class="flex items-center gap-1.5 px-4 py-2 bg-slate-50 border border-slate-200 rounded-full shadow-sm">
                        <span class="material-symbols-outlined text-[#28628f] text-[18px]">celebration</span>
                        <span class="text-xs font-bold text-slate-700">Festivales Oficiales</span>
                    </div>
                    <div class="flex items-center gap-1.5 px-4 py-2 bg-slate-50 border border-slate-200 rounded-full shadow-sm">
                        <span class="material-symbols-outlined text-[#28628f] text-[18px]">kayaking</span>
                        <span class="text-xs font-bold text-slate-700">Aventura</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ========== DESTINOS ========== --}}
    <section id="destinos-section" class="py-12 max-w-7xl mx-auto px-4 md:px-8">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-black text-slate-800 tracking-tight" style="font-family: 'Inter', sans-serif;">Destinos Imperdibles</h2>
            <a class="text-[#28628f] font-bold text-sm hover:underline flex items-center gap-1 text-decoration-none" href="{{ route('mapa.nacional') }}">
                <span class="material-symbols-outlined text-sm">map</span>
                <span>Ver en Mapa</span>
            </a>
        </div>

        <!-- Filtros de Precio -->
        <div class="flex flex-wrap items-center gap-2 mb-8 bg-slate-50 border border-slate-200 p-3 rounded-2xl">
            <span class="text-xs font-extrabold text-slate-400 uppercase tracking-wider mr-2 flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px] text-[#28628f]">payments</span> Filtrar por Rango de Precio:
            </span>
            <button onclick="filtrarPorPrecio('todos')" id="btn-filtro-todos" class="px-4 py-1.5 rounded-full bg-[#28628f] text-white font-bold text-xs shadow-sm transition-all cursor-pointer">
                Todos
            </button>
            <button onclick="filtrarPorPrecio('Bajo')" id="btn-filtro-Bajo" class="px-4 py-1.5 rounded-full bg-white border border-slate-200 text-slate-600 font-bold text-xs hover:border-[#28628f] hover:text-[#28628f] transition-all cursor-pointer">
                💚 Bajo
            </button>
            <button onclick="filtrarPorPrecio('Medio')" id="btn-filtro-Medio" class="px-4 py-1.5 rounded-full bg-white border border-slate-200 text-slate-600 font-bold text-xs hover:border-[#28628f] hover:text-[#28628f] transition-all cursor-pointer">
                💛 Medio
            </button>
            <button onclick="filtrarPorPrecio('Alto')" id="btn-filtro-Alto" class="px-4 py-1.5 rounded-full bg-white border border-slate-200 text-slate-600 font-bold text-xs hover:border-[#28628f] hover:text-[#28628f] transition-all cursor-pointer">
                🔴 Alto
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @if(isset($destinos) && $destinos->count() > 0)
            @foreach($destinos as $destino)
            {{-- ✅ Tarjeta entera clickeable --}}
            <a href="{{ route('destinos.show', ['id' => $destino->id]) }}"
                class="destino-card bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md hover:scale-[1.01] transition-all border border-slate-200 flex flex-col group text-decoration-none"
                data-precio="{{ $destino->rango_precio }}">
                <div class="h-60 bg-slate-100 overflow-hidden relative">
                    <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                        src="{{ $destino->imagen_url ? (str_starts_with($destino->imagen_url, 'http') ? $destino->imagen_url : asset('storage/' . $destino->imagen_url)) : 'https://images.unsplash.com/photo-1544198365-f5d60b6d8190?auto=format&fit=crop&w=600&q=80' }}"
                        alt="{{ $destino->nombre }}">
                    <div class="absolute top-4 left-4 flex gap-1.5 flex-wrap">
                        <span class="bg-white/90 backdrop-blur-sm px-2.5 py-1 rounded-full font-bold text-[10px] text-slate-700 uppercase border border-slate-200 tracking-wider">
                            {{ $destino->categoria ?? 'Turismo' }}
                        </span>
                        <span class="bg-white/90 backdrop-blur-sm px-2.5 py-1 rounded-full font-bold text-[10px] text-slate-700 uppercase border border-slate-200 tracking-wider">
                            Precio: {{ $destino->rango_precio }}
                        </span>
                    </div>
                </div>
                <div class="p-6 flex flex-col flex-grow">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-lg font-bold text-slate-800 group-hover:text-[#28628f] transition-colors leading-tight">{{ $destino->nombre }}</h3>
                        <div class="flex items-center gap-0.5 text-amber-500 shrink-0 ml-2">
                            <span class="material-symbols-outlined text-[15px]" style="font-variation-settings: 'FILL' 1;">star</span>
                            <span class="text-sm font-bold text-slate-700">
                                {{ $destino->resenas->count() > 0 ? number_format($destino->resenas->avg('calificacion'), 1) : '4.5' }}
                            </span>
                        </div>
                    </div>
                    <p class="text-slate-500 text-sm mb-4 line-clamp-3 leading-relaxed">{{ $destino->descripcion }}</p>
                    <div class="mt-auto pt-3 border-t border-slate-100 flex justify-between items-center">
                        <span class="text-xs font-semibold text-slate-400 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">location_on</span>
                            {{ $destino->ubicacion ? 'Mapeado' : 'Ver ubicación' }}
                        </span>
                        <span class="text-[#28628f] font-bold text-xs inline-flex items-center gap-1 group-hover:translate-x-0.5 transition-transform">
                            <span>Ver Detalles</span>
                            <span class="material-symbols-outlined text-[15px]">arrow_forward</span>
                        </span>
                    </div>
                </div>
            </a>
            {{-- FIN tarjeta clickeable --}}
            @endforeach
            @else
            <div class="col-span-1 md:col-span-3 bg-white border border-slate-200 p-12 rounded-2xl text-center">
                <span class="material-symbols-outlined text-5xl text-slate-300 block mb-3">folder_open</span>
                <p class="text-slate-400 font-medium">Todavía no hay destinos cargados para esta provincia.</p>
                <p class="text-slate-300 text-sm mt-1">Pronto estarán disponibles.</p>
            </div>
            @endif
            <div id="sin-resultados-precio" class="hidden text-center py-16 border-2 border-dashed border-slate-200 rounded-2xl mt-6 bg-slate-50 col-span-1 md:col-span-3">
                <span class="material-symbols-outlined text-4xl text-slate-300 block mb-2">search_off</span>
                <p class="text-slate-400 font-bold text-sm">No se encontraron destinos con este rango de precio.</p>
            </div>
        </div>
    </section>

    {{-- ========== CLIMA ========== --}}
    @if($clima)
    <section class="bg-white border-y border-slate-200 py-6 my-4 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 md:px-8">
            <h2 class="text-lg font-black text-slate-800 mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-[#28628f]">partly_cloudy_day</span>
                Clima en {{ $nombre }} ahora
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
                            <p class="text-xs text-slate-400 font-semibold">
                                {{ \Carbon\Carbon::parse($item['dt_txt'])->format('H:i') }}
                            </p>
                            <img src="https://openweathermap.org/img/wn/{{ $item['weather'][0]['icon'] }}.png"
                                class="w-10 h-10 mx-auto" alt="">
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

    {{-- ========== EVENTOS + GASTRONOMÍA ========== --}}
    <section class="bg-white border-t border-slate-200 py-12">
        <div class="max-w-7xl mx-auto px-4 md:px-8 grid grid-cols-1 lg:grid-cols-12 gap-12">

            {{-- Eventos --}}
            <div class="lg:col-span-5 flex flex-col gap-5">
                <h2 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-2" style="font-family: 'Inter', sans-serif;">
                    <span class="material-symbols-outlined text-[#28628f]">celebration</span>
                    Próximos Eventos Regionales
                </h2>

                <div class="flex flex-col gap-3">
                    @if(isset($provincia) && $provincia->eventos && $provincia->eventos->count() > 0)
                    @foreach($provincia->eventos as $evento)
                    {{-- ✅ Evento clickeable --}}
                    <a href="{{ route('eventos.show', $evento->id) }}"
                        class="flex gap-4 p-4 bg-slate-50 rounded-xl border border-slate-200 hover:border-[#28628f] hover:shadow-sm transition-all group text-decoration-none">
                        <div class="flex flex-col items-center justify-center bg-[#28628f] text-white w-14 h-14 rounded-xl font-bold shrink-0 shadow-sm">
                            <span class="text-[10px] uppercase font-semibold leading-none mb-0.5">{{ \Carbon\Carbon::parse($evento->fecha_inicio)->format('M') }}</span>
                            <span class="text-lg font-black leading-none">{{ \Carbon\Carbon::parse($evento->fecha_inicio)->format('d') }}</span>
                        </div>
                        <div class="truncate">
                            <h4 class="font-bold text-base text-slate-800 group-hover:text-[#28628f] transition-colors truncate">{{ $evento->nombre }}</h4>
                            <p class="text-slate-400 text-xs mt-0.5 font-medium flex items-center gap-0.5">
                                <span class="material-symbols-outlined text-xs">location_on</span>
                                {{ $evento->ubicacion ?? 'Centro' }}
                            </p>
                        </div>
                        <span class="material-symbols-outlined text-slate-300 group-hover:text-[#28628f] ml-auto self-center transition-colors shrink-0">arrow_forward</span>
                    </a>
                    {{--  FIN evento clickeable --}}
                    @endforeach
                    @else
                    {{-- Placeholder eventos (no clickeables, son estáticos) --}}
                    @foreach([['SEP','07','Fiesta Nacional del Inmigrante','Oberá'],['NOV','14','Música Nacional del Litoral','Posadas'],['DIC','05','Fiesta de la Yerba Mate','Apóstoles']] as $ev)
                    <div class="flex gap-4 p-4 bg-slate-50 rounded-xl border border-slate-200">
                        <div class="flex flex-col items-center justify-center bg-[#28628f] text-white w-14 h-14 rounded-xl font-bold shrink-0 shadow-sm">
                            <span class="text-[10px] uppercase font-semibold leading-none mb-0.5">{{ $ev[0] }}</span>
                            <span class="text-lg font-black leading-none">{{ $ev[1] }}</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-base text-slate-800">{{ $ev[2] }}</h4>
                            <p class="text-slate-400 text-xs mt-0.5 font-medium flex items-center gap-0.5">
                                <span class="material-symbols-outlined text-xs">location_on</span>
                                {{ $ev[3] }}, {{ $nombre }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>

            {{-- Gastronomía --}}
            <div class="lg:col-span-7 flex flex-col gap-5">
                <h2 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-2" style="font-family: 'Inter', sans-serif;">
                    <span class="material-symbols-outlined text-[#28628f]">restaurant</span>
                    Sabores Locales
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($gastronomia->count() > 0)
                    @foreach($gastronomia as $plato)
                    <div onclick="abrirModalPlato('{{ addslashes($plato->nombre) }}', '{{ addslashes($plato->descripcion) }}', '{{ addslashes($plato->categoria) }}', '{{ $plato->imagen_url ?: '' }}')"
                        class="bg-slate-50 border border-slate-200 rounded-2xl overflow-hidden shadow-sm flex flex-col cursor-pointer hover:border-[#28628f] hover:shadow-md transition-all">
                        <div class="h-36 bg-slate-200 overflow-hidden">
                            <img src="{{ $plato->imagen_url ?: 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=400&q=80' }}"
                                class="w-full h-full object-cover" alt="{{ $plato->nombre }}">
                        </div>
                        <div class="p-4 flex-grow">
                            <span class="inline-block px-2 py-0.5 bg-[#28628f]/10 rounded-md font-bold text-[9px] text-[#28628f] uppercase tracking-wider mb-1">{{ $plato->categoria }}</span>
                            <h3 class="font-bold text-slate-800 text-base mb-1">{{ $plato->nombre }}</h3>
                            <p class="text-xs text-slate-500 leading-relaxed line-clamp-2">{{ $plato->descripcion }}</p>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5">
                        <div class="w-10 h-10 rounded-xl bg-[#28628f]/10 flex items-center justify-center mb-3">
                            <span class="material-symbols-outlined text-[#28628f]">restaurant</span>
                        </div>
                        <h3 class="font-bold text-slate-800 text-base mb-1">Gastronomía Autóctona</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">Descubrí los platos típicos e ingredientes locales que componen la identidad cultural y el patrimonio culinario de esta región.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

</div>

{{-- ========== RESEÑAS ========== --}}
<section class="py-12 max-w-7xl mx-auto px-4 md:px-8">
    <h2 class="text-3xl font-black text-slate-800 tracking-tight mb-8 flex items-center gap-2" style="font-family: 'Inter', sans-serif;">
        <span class="material-symbols-outlined text-[#28628f]">rate_review</span>
        Opiniones de Viajeros
    </h2>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <div class="lg:col-span-7 flex flex-col gap-4">
            @php
            $resenas = \App\Models\Resena::where('aprobada', true)->whereHas('destino', function($q) use ($provincia) {
            $q->where('provincia_id', $provincia?->id);
            })->where('aprobada', true)->with('user')->latest()->take(5)->get();
            @endphp

            @if($resenas->count() > 0)
            @foreach($resenas as $resena)
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-[#28628f] text-white flex items-center justify-center font-bold text-sm shrink-0">
                        @if($resena->anonima)
                        <span class="material-symbols-outlined text-[18px]">person</span>
                        @elseif($resena->user->avatar)
                        <img src="{{ $resena->user->avatar }}" class="w-10 h-10 rounded-full object-cover" alt="">
                        @else
                        {{ strtoupper(substr($resena->user->name, 0, 1)) }}
                        @endif
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-bold text-slate-800 text-sm">
                                {{ $resena->anonima ? 'Viajero Anónimo' : $resena->user->name }}
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
                    </div>
                </div>
            </div>
            @endforeach
            @else
            <div class="bg-white rounded-2xl border border-slate-200 p-10 text-center">
                <span class="material-symbols-outlined text-4xl text-slate-300 block mb-3">chat_bubble_outline</span>
                <p class="text-slate-400 font-medium">Todavía no hay reseñas para esta provincia.</p>
                <p class="text-slate-300 text-sm mt-1">¡Sé el primero en comentar!</p>
            </div>
            @endif
        </div>

        <div class="lg:col-span-5">
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm sticky top-24 text-center">
                <span class="material-symbols-outlined text-5xl text-[#28628f] block mb-3">rate_review</span>
                <h3 class="text-lg font-bold text-slate-800 mb-2">¿Ya visitaste esta provincia?</h3>
                <p class="text-slate-500 text-sm mb-6">Compartí tu experiencia y ayudá a otros viajeros a descubrir lo mejor de Argentina.</p>
                @auth
                <button onclick="document.getElementById('modal-resena').classList.remove('hidden')"
                    class="bg-[#28628f] text-white px-8 py-3 rounded-xl font-bold hover:bg-[#1a4669] transition-all shadow-sm inline-flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">edit</span>
                    Escribir reseña
                </button>
                @else
                <a href="{{ route('login') }}"
                    class="bg-[#28628f] text-white px-8 py-3 rounded-xl font-bold hover:bg-[#1a4669] transition-all shadow-sm inline-flex items-center gap-2 text-decoration-none">
                    <span class="material-symbols-outlined text-[18px]">login</span>
                    Iniciá sesión para comentar
                </a>
                @endauth
            </div>
        </div>
    </div>
</section>

{{-- Modal reseña --}}
<div id="modal-resena" class="hidden fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-5 border-b border-slate-100">
            <div class="flex items-center justify-between mb-1">
                <nav class="flex items-center gap-1 text-xs font-bold uppercase tracking-wider">
                    <span class="text-slate-500">Destinos</span>
                    <span class="material-symbols-outlined text-slate-400 text-[14px]">chevron_right</span>
                    <span class="text-[#28628f]">{{ $nombre }}</span>
                </nav>
                <button onclick="document.getElementById('modal-resena').classList.add('hidden')"
                    class="text-slate-400 hover:text-slate-600 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <h2 class="text-xl font-bold text-slate-800">Compartí tu experiencia en {{ $nombre }}</h2>
            <p class="text-sm text-slate-500 mt-1">Ayudá a otros viajeros a descubrir lo mejor de esta provincia.</p>
        </div>
        <form action="{{ route('resenas.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Destino turistico a calificar</label>
                <select name="destino_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#28628f] transition-colors font-semibold text-slate-700">
                    @foreach($destinos as $dest)
                    <option value="{{ $dest->id }}">{{ $dest->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Calificación General</label>
                    <div class="flex gap-1" id="estrellas-modal">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" onclick="setCalificacionModal({{ $i }})"
                            class="estrella-modal material-symbols-outlined text-[32px] text-amber-400 cursor-pointer transition-colors"
                            style="font-variation-settings: 'FILL' 1;" data-valor="{{ $i }}">star</button>
                            @endfor
                    </div>
                    <input type="hidden" name="calificacion" id="calificacion-modal" value="5">
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
                    <button type="button" onclick="toggleCategoria(this)"
                        class="categoria-btn px-4 py-1.5 rounded-full border border-slate-200 text-slate-600 font-semibold text-xs hover:border-[#28628f] hover:text-[#28628f] transition-all">
                        {{ $cat }}
                    </button>
                    @endforeach
                </div>
            </div>
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Tu Comentario</label>
                <textarea name="comentario" rows="5" required
                    placeholder="¿Cuál fue lo mejor de tu visita? ¿Tenés consejos para otros viajeros?"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#28628f] transition-colors resize-none"></textarea>
            </div>
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Fotos</label>
                <div onclick="document.getElementById('fotos-input-modal').click()"
                    class="border-2 border-dashed border-slate-200 rounded-xl p-8 flex flex-col items-center justify-center bg-slate-50 hover:bg-slate-100 transition-colors cursor-pointer group">
                    <div class="w-12 h-12 bg-[#28628f]/10 rounded-full flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-[#28628f] text-[28px]">upload_file</span>
                    </div>
                    <p class="text-sm font-semibold text-slate-700">Arrastrá y soltá tus fotos acá</p>
                    <p class="text-xs text-slate-400 mt-1">O hacé clic para explorar (hasta 5 fotos)</p>
                </div>
                <input type="file" id="fotos-input-modal" name="imagenes[]" multiple accept="image/*"
                    class="hidden" onchange="previewFotosModal(this)">
                <div id="fotos-preview-modal" class="flex gap-3 overflow-x-auto pb-2 mt-2 hidden"></div>
            </div>
            <div class="flex items-start gap-3 py-2 border-t border-slate-100">
                <input type="checkbox" name="anonima" value="1" id="anonima-modal"
                    class="mt-1 w-5 h-5 rounded border-slate-300 text-[#28628f] focus:ring-[#28628f]">
                <label for="anonima-modal" class="text-sm text-slate-600 cursor-pointer">
                    Publicar de forma anónima
                    <span class="block text-xs text-slate-400 mt-0.5 italic">Tu nombre y foto no serán visibles para otros viajeros.</span>
                </label>
            </div>
            <div class="flex flex-col md:flex-row-reverse gap-3 pt-2">
                <button type="submit"
                    class="w-full md:w-auto px-8 py-3 bg-[#28628f] text-white font-bold rounded-xl hover:bg-[#1a4669] transition-all shadow-sm">
                    Publicar Reseña
                </button>
                <button type="button" onclick="document.getElementById('modal-resena').classList.add('hidden')"
                    class="w-full md:w-auto px-8 py-3 border border-[#28628f] text-[#28628f] font-bold rounded-xl hover:bg-blue-50 transition-all">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal plato --}}
<div id="modal-plato" class="hidden fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden">
        <div class="h-56 bg-slate-200 relative overflow-hidden">
            <img id="modal-plato-img" src="" alt="" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
            <button onclick="document.getElementById('modal-plato').classList.add('hidden')"
                class="absolute top-4 right-4 bg-black/40 hover:bg-black/60 text-white rounded-full p-1.5 transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
            <span id="modal-plato-categoria" class="absolute bottom-4 left-4 bg-white/20 backdrop-blur-sm text-white text-xs font-bold px-3 py-1 rounded-full border border-white/30"></span>
        </div>
        <div class="p-6">
            <h2 id="modal-plato-nombre" class="text-2xl font-black text-slate-800 tracking-tight mb-3"></h2>
            <p id="modal-plato-descripcion" class="text-sm text-slate-600 leading-relaxed"></p>
        </div>
    </div>
</div>

<script>
    function abrirModalPlato(nombre, descripcion, categoria, imagen) {
        document.getElementById('modal-plato-nombre').textContent = nombre;
        document.getElementById('modal-plato-descripcion').textContent = descripcion;
        document.getElementById('modal-plato-categoria').textContent = categoria;
        document.getElementById('modal-plato-img').src = imagen || 'https://images.unsplash.com/photo-1544025162-d76694265947?w=800&q=80';
        document.getElementById('modal-plato').classList.remove('hidden');
    }

    function setCalificacionModal(valor) {
        document.getElementById('calificacion-modal').value = valor;
        document.querySelectorAll('.estrella-modal').forEach(function(e) {
            const v = parseInt(e.dataset.valor);
            e.style.fontVariationSettings = v <= valor ? "'FILL' 1" : "'FILL' 0";
            e.classList.toggle('text-amber-400', v <= valor);
            e.classList.toggle('text-slate-300', v > valor);
        });
    }

    function toggleCategoria(btn) {
        btn.classList.toggle('border-[#28628f]');
        btn.classList.toggle('text-[#28628f]');
        btn.classList.toggle('bg-[#28628f]/5');
        btn.classList.toggle('border-slate-200');
        btn.classList.toggle('text-slate-600');
    }

    function previewFotosModal(input) {
        const preview = document.getElementById('fotos-preview-modal');
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

    function filtrarPorPrecio(rango) {
        const rangos = ['todos', 'Bajo', 'Medio', 'Alto'];
        rangos.forEach(r => {
            const btn = document.getElementById('btn-filtro-' + r);
            if (btn) {
                if (r === rango) {
                    btn.className = "px-4 py-1.5 rounded-full bg-[#28628f] text-white font-bold text-xs shadow-sm transition-all cursor-pointer";
                } else {
                    btn.className = "px-4 py-1.5 rounded-full bg-white border border-slate-200 text-slate-600 font-bold text-xs hover:border-[#28628f] hover:text-[#28628f] transition-all cursor-pointer";
                }
            }
        });

        const cards = document.querySelectorAll('.destino-card');
        let count = 0;
        cards.forEach(card => {
            if (rango === 'todos' || card.dataset.precio === rango) {
                card.style.display = 'flex';
                count++;
            } else {
                card.style.display = 'none';
            }
        });

        const sinResultados = document.getElementById('sin-resultados-precio');
        if (sinResultados) {
            sinResultados.classList.toggle('hidden', count > 0);
        }
    }
</script>
@endsection