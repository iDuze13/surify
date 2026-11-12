@extends('layouts.app')

@section('title', 'Surify - ' . $evento->nombre)

@section('content')

{{-- Hero --}}
<div class="relative w-full h-80 rounded-3xl overflow-hidden mb-8 shadow-lg">
    @if($evento->imagen_url)
    <img src="{{ $evento->imagen_url
    ? (str_starts_with($evento->imagen_url, 'http')
        ? $evento->imagen_url
        : asset('storage/' . $evento->imagen_url))
    : '' }}"
        class="w-full h-full object-cover" alt="{{ $evento->nombre }}">
    @else
    <div class="w-full h-full bg-gradient-to-br from-[#28628f] to-[#1a4669] flex items-center justify-center">
        <span class="material-symbols-outlined text-white text-[80px] opacity-30">celebration</span>
    </div>
    @endif
    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/20 to-transparent"></div>

    {{-- Breadcrumb --}}
    <div class="absolute top-5 left-5">
        <a href="{{ route('eventos.index') }}" class="inline-flex items-center gap-1 bg-white/20 backdrop-blur-sm text-white text-xs font-bold px-3 py-1.5 rounded-full hover:bg-white/30 transition-all text-decoration-none">
            <span class="material-symbols-outlined text-[14px]">arrow_back</span>
            Volver a Eventos
        </a>
    </div>

    {{-- Info sobre la imagen --}}
    <div class="absolute bottom-5 left-6 right-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                @if($evento->tipo)
                <span class="inline-block bg-white/20 backdrop-blur-sm text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider mb-2">
                    {{ $evento->tipo }}
                </span>
                @endif
                <h1 class="text-3xl md:text-4xl font-black text-white tracking-tight leading-tight" style="font-family: 'Inter', sans-serif;">
                    {{ $evento->nombre }}
                </h1>
                @if($evento->provincia)
                <p class="text-white/80 text-sm font-medium mt-1 flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">location_on</span>
                    {{ $evento->provincia->nombre }}
                    @if($evento->destino) • {{ $evento->destino->nombre }} @endif
                </p>
                @endif
            </div>
            @if($evento->rango_precio)
            <span class="shrink-0 bg-amber-400 text-slate-900 text-xs font-black px-3 py-1.5 rounded-xl">
                {{ $evento->rango_precio }}
            </span>
            @endif
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    {{-- Columna principal --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Descripción --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h2 class="text-lg font-black text-slate-800 mb-3 flex items-center gap-2">
                <span class="material-symbols-outlined text-[#28628f]">description</span>
                Sobre este evento
            </h2>
            <p class="text-slate-600 leading-relaxed">
                {{ $evento->descripcion ?? 'No hay descripción disponible para este evento.' }}
            </p>
        </div>

        {{-- Reseñas --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h2 class="text-lg font-black text-slate-800 mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-[#28628f]">chat_bubble</span>
                Reseñas de asistentes
                <span class="text-sm font-bold text-slate-400">({{ $evento->resenas->count() }})</span>
            </h2>

            @forelse($evento->resenas as $resena)
            <div class="flex gap-4 py-4 border-b border-slate-100 last:border-0">
                <div class="w-10 h-10 rounded-full bg-[#28628f]/10 flex items-center justify-center text-sm font-black text-[#28628f] shrink-0">
                    {{ ($resena->anonima || !$resena->user) ? '?' : strtoupper(substr($resena->user->name, 0, 1)) }}
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-sm font-bold text-slate-700">
                            @if($resena->anonima || !$resena->user)
                            Viajero Anónimo
                            @else
                            <a href="{{ route('usuarios.show', $resena->user) }}" class="hover:underline text-decoration-none">{{ $resena->user->name }}</a>
                            @endif
                        </p>
                        <div class="flex items-center gap-0.5">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="material-symbols-outlined text-[14px] {{ $i <= ($resena->calificacion ?? 5) ? 'text-amber-400' : 'text-slate-200' }}"
                                style="font-variation-settings: 'FILL' 1;">star</span>
                                @endfor
                        </div>
                    </div>
                    <p class="text-sm text-slate-500 leading-relaxed">{{ $resena->comentario }}</p>
                    <p class="text-xs text-slate-300 mt-1">{{ $resena->created_at->format('d/m/Y') }}</p>
                </div>
            </div>
            @empty
            <div class="text-center py-8">
                <span class="material-symbols-outlined text-4xl text-slate-300 block mb-2">chat_bubble</span>
                <p class="text-slate-400 text-sm">Todavía no hay reseñas para este evento.</p>
                <p class="text-slate-300 text-xs mt-1">¡Sé el primero en comentar!</p>
            </div>
            @endforelse

            {{-- Formulario de reseña --}}
            <div class="mt-6 pt-6 border-t border-slate-100">
                @auth
                <h3 class="text-sm font-black text-slate-700 mb-4">Dejá tu opinión</h3>
                <form action="{{ route('resenas.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="evento_id" value="{{ $evento->id }}">

                    <div>
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-2">Calificación</label>
                        <div class="flex gap-2">
                            @foreach([5,4,3,2,1] as $star)
                            <label class="cursor-pointer">
                                <input type="radio" name="calificacion" value="{{ $star }}" class="sr-only" {{ $star === 5 ? 'checked' : '' }}>
                                <span class="material-symbols-outlined text-[28px] text-slate-200 hover:text-amber-400 transition-colors star-btn"
                                    style="font-variation-settings: 'FILL' 1;" data-value="{{ $star }}">star</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-2">Tu comentario</label>
                        <textarea name="comentario" rows="3" required
                            placeholder="¿Cómo fue tu experiencia en este evento?"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#28628f] resize-none"></textarea>
                    </div>

                    <button type="submit"
                        class="w-full bg-[#28628f] text-white font-bold py-3 rounded-xl hover:bg-[#1a4669] transition-all text-sm">
                        Publicar Reseña
                    </button>
                </form>
                @else
                <div class="text-center py-4 bg-slate-50 rounded-xl border border-slate-200">
                    <p class="text-slate-500 text-sm mb-3">Iniciá sesión para dejar una reseña</p>
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-1 bg-[#28628f] text-white text-sm font-bold px-5 py-2 rounded-xl hover:bg-[#1a4669] transition-all text-decoration-none">
                        <span class="material-symbols-outlined text-[16px]">login</span>
                        Iniciar Sesión
                    </a>
                </div>
                @endauth
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-5">

        {{-- Fechas --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h3 class="text-sm font-black text-slate-700 mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-[#28628f] text-[18px]">calendar_month</span>
                Fechas
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Inicio</span>
                    <span class="text-sm font-bold text-slate-700">{{ $evento->fecha_inicio->format('d/m/Y') }}</span>
                </div>
                @if($evento->fecha_fin)
                <div class="flex justify-between items-center">
                    <span class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Fin</span>
                    <span class="text-sm font-bold text-slate-700">{{ $evento->fecha_fin->format('d/m/Y') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Duración</span>
                    <span class="text-sm font-bold text-[#28628f]">
                        {{ $evento->fecha_inicio->diffInDays($evento->fecha_fin) + 1 }} días
                    </span>
                </div>
                @endif
                @if($evento->pasado)
                <span class="inline-flex items-center gap-1 text-xs font-bold px-2.5 py-1 bg-slate-100 text-slate-400 rounded-full">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400 inline-block"></span>
                    Evento pasado
                </span>
                @else
                <span class="inline-flex items-center gap-1 text-xs font-bold px-2.5 py-1 bg-emerald-50 text-emerald-600 rounded-full">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block animate-pulse"></span>
                    Próximo
                </span>
                @endif
            </div>
        </div>

        {{-- Acciones sobre el evento --}}
        @auth
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col gap-3">
            <button id="btn-favorito" data-evento-id="{{ $evento->id }}"
                onclick="toggleFavorito(this.dataset.eventoId)"
                class="{{ $esFavorito ? 'bg-pink-400 text-white border-pink-400' : 'bg-white text-slate-600 border-slate-200 hover:border-pink-300 hover:text-pink-400' }} w-full px-4 py-2.5 rounded-xl font-bold transition-all flex items-center gap-2 justify-center border text-sm">
                <span id="icon-favorito" class="material-symbols-outlined text-[18px]" style='font-variation-settings: "FILL" {{ $esFavorito ? 1 : 0 }};'>
                    favorite
                </span>
                <span id="texto-favorito">{{ $esFavorito ? 'En favoritos ♥' : 'Agregar a favoritos' }}</span>
            </button>

            <button id="btn-visitado" data-evento-id="{{ $evento->id }}"
                onclick="toggleVisitado(this.dataset.eventoId)"
                class="{{ $esVisitado ? 'bg-emerald-500 text-white border-emerald-500' : 'bg-white text-slate-600 border-slate-200 hover:border-[#28628f] hover:text-[#28628f]' }} w-full px-4 py-2.5 rounded-xl font-bold transition-all flex items-center gap-2 justify-center border text-sm">
                <span id="icon-visitado" class="material-symbols-outlined text-[18px]" style='font-variation-settings: "FILL" {{ $esVisitado ? 1 : 0 }};'>
                    verified
                </span>
                <span id="texto-visitado">{{ $esVisitado ? 'Visitado ✓' : 'Marcar como visitado' }}</span>
            </button>
        </div>
        @endauth

        {{-- Ubicación --}}
        @if($evento->provincia || $evento->destino)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h3 class="text-sm font-black text-slate-700 mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-[#28628f] text-[18px]">location_on</span>
                Ubicación
            </h3>
            @if($evento->provincia)
            <a href="{{ route('provincia.show', $evento->provincia->nombre) }}"
                class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl hover:bg-[#28628f]/5 transition-all text-decoration-none group">
                <span class="material-symbols-outlined text-[#28628f] text-[20px]">map</span>
                <div>
                    <p class="text-xs text-slate-400 font-semibold">Provincia</p>
                    <p class="text-sm font-bold text-slate-700 group-hover:text-[#28628f] transition-colors">{{ $evento->provincia->nombre }}</p>
                </div>
            </a>
            @endif
            @if($evento->destino)
            <a href="{{ route('destinos.show', $evento->destino->id) }}"
                class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl hover:bg-[#28628f]/5 transition-all text-decoration-none group mt-2">
                <span class="material-symbols-outlined text-[#28628f] text-[20px]">landscape</span>
                <div>
                    <p class="text-xs text-slate-400 font-semibold">Destino</p>
                    <p class="text-sm font-bold text-slate-700 group-hover:text-[#28628f] transition-colors">{{ $evento->destino->nombre }}</p>
                </div>
            </a>
            @endif
        </div>
        @endif

        {{-- Eventos relacionados --}}
        @if($eventosRelacionados->count() > 0)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <h3 class="text-sm font-black text-slate-700 mb-4 flex items-center gap-2">
                <span class="material-symbols-outlined text-[#28628f] text-[18px]">celebration</span>
                Otros eventos en la zona
            </h3>
            <div class="space-y-3">
                @foreach($eventosRelacionados as $rel)
                <a href="{{ route('eventos.show', $rel->id) }}"
                    class="flex gap-3 p-3 bg-slate-50 rounded-xl hover:bg-[#28628f]/5 transition-all text-decoration-none group">
                    <div class="w-10 h-10 rounded-lg bg-[#28628f]/10 flex items-center justify-center shrink-0 overflow-hidden">
                        @if($rel->imagen_url)
                        <img src="{{ $rel->imagen_url
                                ? (str_starts_with($rel->imagen_url, 'http')
                                    ? $rel->imagen_url
                                    : asset('storage/' . $rel->imagen_url))
                                : '' }}"
                            @else
                            <span class="material-symbols-outlined text-[#28628f] text-[18px]">celebration</span>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-slate-700 truncate group-hover:text-[#28628f] transition-colors">{{ $rel->nombre }}</p>
                        <p class="text-xs text-slate-400">{{ $rel->fecha_inicio->format('d/m/Y') }}</p>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

<script>
    // Estrellas interactivas
    document.querySelectorAll('.star-btn').forEach(star => {
        star.addEventListener('click', function() {
            const val = parseInt(this.dataset.value);
            document.querySelectorAll('.star-btn').forEach((s, i) => {
                s.style.color = (6 - i) <= val ? '#fbbf24' : '#e2e8f0';
            });
            document.querySelector(`input[name="calificacion"][value="${val}"]`).checked = true;
        });
    });

    function toggleVisitado(eventoId) {
        fetch('{{ route("eventos.visitados.toggle") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    evento_id: eventoId
                })
            })
            .then(r => r.json())
            .then(data => {
                const btn = document.getElementById('btn-visitado');
                const icon = document.getElementById('icon-visitado');
                const texto = document.getElementById('texto-visitado');

                if (data.visitado) {
                    btn.className = 'bg-emerald-500 text-white border-emerald-500 w-full px-4 py-2.5 rounded-xl font-bold transition-all flex items-center gap-2 justify-center border text-sm';
                    icon.style.fontVariationSettings = "'FILL' 1";
                    texto.textContent = 'Visitado ✓';
                } else {
                    btn.className = 'bg-white text-slate-600 border-slate-200 hover:border-[#28628f] hover:text-[#28628f] w-full px-4 py-2.5 rounded-xl font-bold transition-all flex items-center gap-2 justify-center border text-sm';
                    icon.style.fontVariationSettings = "'FILL' 0";
                    texto.textContent = 'Marcar como visitado';
                }
            });
    }

    function toggleFavorito(eventoId) {
        fetch('{{ route("favoritos.toggle") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    evento_id: eventoId
                })
            })
            .then(r => r.json())
            .then(data => {
                const btn = document.getElementById('btn-favorito');
                const icon = document.getElementById('icon-favorito');
                const texto = document.getElementById('texto-favorito');

                if (data.favorito) {
                    btn.className = 'bg-pink-400 text-white border-pink-400 w-full px-4 py-2.5 rounded-xl font-bold transition-all flex items-center gap-2 justify-center border text-sm';
                    icon.style.fontVariationSettings = "'FILL' 1";
                    texto.textContent = 'En favoritos ♥';
                } else {
                    btn.className = 'bg-white text-slate-600 border-slate-200 hover:border-pink-300 hover:text-pink-400 w-full px-4 py-2.5 rounded-xl font-bold transition-all flex items-center gap-2 justify-center border text-sm';
                    icon.style.fontVariationSettings = "'FILL' 0";
                    texto.textContent = 'Agregar a favoritos';
                }
            });
    }
</script>

@endsection