@extends('layouts.app')

@section('title', 'Surify - Eventos y Festividades')

@section('content')
<x-animaciones />

<div class="max-w-[1280px] w-full mx-auto py-4">

    <div class="mb-8 flex items-start justify-between">
        <div>
            <h1 class="text-4xl font-black text-slate-900 tracking-tighter mb-2 hero-in" style="--d:.05s">Festivales y Eventos</h1>
            <p class="text-base text-slate-500 max-w-2xl hero-in" style="--d:.15s">Planifica tu viaje en torno a las celebraciones culturales y festividades más vibrantes de Argentina.</p>
        </div>
        @auth
        <button onclick="document.getElementById('modal-sugerir-evento').classList.remove('hidden')"
            class="bg-[#28628f] text-white px-5 py-2.5 rounded-xl font-bold hover:bg-[#1a4669] transition-all shadow-sm flex items-center gap-2 shrink-0 hero-in" style="--d:.25s">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Sugerir festival
        </button>
        @endauth
    </div>

    {{-- Contenedor que se reemplaza por AJAX al cambiar de mes o de provincia --}}
    <div id="eventos-contenido" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start transition-opacity duration-200">

        <!-- SIDEBAR -->
        <aside class="col-span-1 lg:col-span-4 flex flex-col gap-4">

            {{-- Navegación mes --}}
            @php
            $mesAnterior = \Carbon\Carbon::create($año, $mes, 1)->subMonth();
            $mesSiguiente = \Carbon\Carbon::create($año, $mes, 1)->addMonth();
            @endphp
            <div class="bg-slate-100 rounded-2xl p-4 flex items-center justify-between border border-slate-200/60 hero-in" style="--d:.2s">
                <a data-cal-nav href="{{ route('eventos.index', ['año' => $mesAnterior->year, 'mes' => $mesAnterior->month, 'provincia_id' => request('provincia_id')]) }}"
                    class="w-9 h-9 rounded-full flex items-center justify-center bg-white hover:bg-slate-50 border border-slate-200 transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-slate-500 text-sm">chevron_left</span>
                </a>
                <div class="flex flex-col items-center select-none">
                    <span class="text-[10px] font-bold text-[#28628f] uppercase tracking-widest flex items-center gap-1"><span class="material-symbols-outlined text-[12px] pin-bounce">event</span>Calendario Anual</span>
                    <h2 class="text-lg font-black text-slate-800">{{ ucfirst(\Carbon\Carbon::create($año, $mes, 1)->translatedFormat('F Y')) }}</h2>
                </div>
                <a data-cal-nav href="{{ route('eventos.index', ['año' => $mesSiguiente->year, 'mes' => $mesSiguiente->month, 'provincia_id' => request('provincia_id')]) }}"
                    class="w-9 h-9 rounded-full flex items-center justify-center bg-white hover:bg-slate-50 border border-slate-200 transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-slate-500 text-sm">chevron_right</span>
                </a>
            </div>

            {{-- Filtros provincia --}}
            <div class="flex gap-1.5 overflow-x-auto pb-1 scrollbar-none select-none hero-in" style="--d:.3s">
                <a data-cal-nav href="{{ route('eventos.index', ['año' => $año, 'mes' => $mes]) }}"
                    class="px-3 py-1.5 rounded-full text-xs font-bold whitespace-nowrap {{ !request('provincia_id') ? 'bg-[#28628f] text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600' }}">
                    Todas
                </a>
                @foreach($provincias as $prov)
                <a data-cal-nav href="{{ route('eventos.index', ['año' => $año, 'mes' => $mes, 'provincia_id' => $prov->id]) }}"
                    class="px-3 py-1.5 rounded-full text-xs font-bold whitespace-nowrap {{ request('provincia_id') == $prov->id ? 'bg-[#28628f] text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600' }}">
                    {{ $prov->nombre }}
                </a>
                @endforeach
            </div>

            {{-- Lista eventos --}}
            <div class="flex flex-col gap-3 max-h-[580px] overflow-y-auto pr-1">
                @forelse($eventos as $index => $evento)

                {{-- ✅ CAMBIADO: div onclick → <a href> --}}
                <div onclick="abrirModalEvento({{ $evento->id }}, '{{ addslashes($evento->nombre) }}', '{{ addslashes($evento->tipo ?? 'Festival') }}', '{{ $evento->fecha_inicio->format('d/m/Y') }}', '{{ $evento->fecha_fin ? $evento->fecha_fin->format('d/m/Y') : '' }}', '{{ addslashes($evento->descripcion ?? '') }}', '{{ addslashes($evento->imagen_url ?? '') }}', '{{ addslashes($evento->provincia->nombre ?? '') }}', '{{ addslashes($evento->destino->nombre ?? '') }}', '{{ addslashes($evento->rango_precio ?? '') }}')"
                    class="bg-white rounded-2xl p-4 border {{ $index === 0 ? 'border-[#28628f] ring-1 ring-[#28628f]/20' : 'border-slate-200' }} hover:border-[#28628f] hover:shadow-md hover:-translate-y-0.5 transition-all group relative overflow-hidden text-decoration-none block cursor-pointer pop" style="--pd: {{ 0.35 + min($loop->index, 8) * 0.08 }}s">
                    @if($index === 0)
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-[#28628f]"></div>
                    @endif
                    <div class="flex gap-4">
                        <div class="flex flex-col items-center justify-center bg-slate-50 rounded-xl w-14 h-14 shrink-0 border border-slate-200 group-hover:scale-110 group-hover:border-[#28628f] transition-all">
                            <span class="text-[9px] font-bold text-[#28628f] uppercase leading-none mb-0.5">{{ $evento->fecha_inicio->translatedFormat('M') }}</span>
                            <span class="text-base font-black text-slate-800 leading-none">{{ $evento->fecha_inicio->format('d') }}</span>
                        </div>
                        <div class="truncate">
                            <span class="inline-block px-2 py-0.5 bg-slate-100 rounded-md font-bold text-[9px] text-slate-500 uppercase tracking-wider mb-1">
                                {{ $evento->tipo ?? 'Festival' }}
                            </span>
                            <h3 class="text-base font-bold text-slate-800 group-hover:text-[#28628f] transition-colors truncate leading-tight">{{ $evento->nombre }}</h3>
                            <p class="text-slate-500 text-xs mt-1 line-clamp-2 leading-relaxed">{{ $evento->descripcion ?? 'Disfruta de las celebraciones tradicionales.' }}</p>
                            @if($evento->provincia)
                            <span class="text-[10px] text-slate-400 font-medium flex items-center gap-0.5 mt-2">
                                <span class="material-symbols-outlined text-xs text-[#28628f]">location_on</span>
                                {{ $evento->provincia->nombre }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                {{-- ✅ FIN cambio sidebar --}}

                @empty
                <div class="bg-white rounded-2xl p-6 border border-slate-200 text-center text-slate-400 text-sm pop" style="--pd:.35s">
                    <span class="material-symbols-outlined text-3xl text-slate-300 block mb-1 float-y">event_busy</span>
                    No hay eventos para este mes.
                </div>
                @endforelse
            </div>
        </aside>

        <!-- CALENDARIO -->
        <section class="col-span-1 lg:col-span-8">
            <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden gal-in" style="--d:.2s">
                <div class="grid grid-cols-7 border-b border-slate-200 bg-slate-50 select-none">
                    @foreach(['DOM','LUN','MAR','MIE','JUE','VIE','SAB'] as $dia)
                    <div class="py-3 text-center text-xs font-bold text-slate-400">{{ $dia }}</div>
                    @endforeach
                </div>
                @php
                $primerDia = \Carbon\Carbon::create($año, $mes, 1);
                $diasEnMes = $primerDia->daysInMonth;
                $iniciaSemana = $primerDia->dayOfWeek;
                @endphp
                <div class="grid grid-cols-7 bg-slate-200 gap-[1px]">

                    {{-- Celdas vacías al inicio --}}
                    @for($i = 0; $i < $iniciaSemana; $i++)
                        <div class="bg-slate-50 min-h-[100px] p-2">
                </div>
                @endfor

                {{-- Días del mes --}}
                @for($d = 1; $d <= $diasEnMes; $d++)
                    @php
                    $eventoDelDia=$eventos->first(function($e) use ($d) {
                    $inicio = $e->fecha_inicio->day;
                    $fin = $e->fecha_fin ? $e->fecha_fin->day : $inicio;
                    return $d >= $inicio && $d <= $fin;
                        });
                        $hoy=now()->day == $d && now()->month == $mes && now()->year == $año;
                        @endphp

                        @if($eventoDelDia)
                        {{-- ✅ CAMBIADO: div onclick → <a href> --}}
                        <div onclick="abrirModalEvento({{ $eventoDelDia->id }}, '{{ addslashes($eventoDelDia->nombre) }}', '{{ addslashes($eventoDelDia->tipo ?? 'Festival') }}', '{{ $eventoDelDia->fecha_inicio->format('d/m/Y') }}', '{{ $eventoDelDia->fecha_fin ? $eventoDelDia->fecha_fin->format('d/m/Y') : '' }}', '{{ addslashes($eventoDelDia->descripcion ?? '') }}', '{{ addslashes($eventoDelDia->imagen_url ?? '') }}', '{{ addslashes($eventoDelDia->provincia->nombre ?? '') }}', '{{ addslashes($eventoDelDia->destino->nombre ?? '') }}', '{{ addslashes($eventoDelDia->rango_precio ?? '') }}')"
                            class="bg-emerald-50/40 min-h-[100px] p-2 border-2 border-emerald-500/30 hover:bg-emerald-50 transition-colors block text-decoration-none cursor-pointer pop" style="--pd: {{ 0.3 + $d * 0.015 }}s">
                            <span class="text-xs font-bold text-emerald-700">{{ $d }}</span>
                            <div class="mt-1 bg-emerald-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded truncate">{{ Str::limit($eventoDelDia->nombre, 12) }}</div>
                        </div>
                        {{-- ✅ FIN cambio calendario --}}
                        @else
                        <div class="bg-white min-h-[100px] p-2 hover:bg-slate-50/80 transition-colors pop {{ $hoy ? 'ring-2 ring-inset ring-[#28628f]' : '' }}" style="--pd: {{ 0.3 + $d * 0.015 }}s">
                            <span class="text-xs font-semibold {{ $hoy ? 'text-[#28628f] font-black' : 'text-slate-400' }}">{{ $d }}</span>
                        </div>
                        @endif
                        @endfor

                        {{-- Celdas vacías al final --}}
                        @php $celdasRestantes = (7 - ($iniciaSemana + $diasEnMes) % 7) % 7; @endphp
                        @for($i = 0; $i < $celdasRestantes; $i++)
                            <div class="bg-slate-50 min-h-[100px] p-2">
            </div>
            @endfor

    </div>
</div>
</section>

</div>
</div>

{{-- función abrirModalEvento eliminada — ya no se usa --}}
<script>
    function abrirModalEvento(id, nombre, tipo, fechaInicio, fechaFin, descripcion, imagen, provincia, destino, precio) {
        document.getElementById('modal-evento-nombre').textContent = nombre;
        document.getElementById('modal-evento-tipo').textContent = tipo;
        document.getElementById('modal-evento-fecha-inicio').textContent = fechaInicio;
        document.getElementById('modal-evento-fecha-fin').textContent = fechaFin || '—';
        document.getElementById('modal-evento-descripcion').textContent = descripcion || 'Sin descripción disponible.';
        document.getElementById('modal-evento-precio').textContent = precio || '—';

        const provinciaEl = document.getElementById('modal-evento-provincia');
        provinciaEl.innerHTML = `<span class="material-symbols-outlined text-[14px]">location_on</span> ${provincia}`;

        const destinoEl = document.getElementById('modal-evento-destino');
        destinoEl.textContent = destino ? `• ${destino}` : '';

        const img = document.getElementById('modal-evento-img');
        if (imagen) {
            img.src = imagen.startsWith('http') ? imagen : '/storage/' + imagen;
        } else {
            img.src = 'https://images.unsplash.com/photo-1501854140801-50d01698950b?w=800';
        }
        document.getElementById('modal-evento-link').href = `/eventos/${id}`;

        document.getElementById('modal-evento').classList.remove('hidden');
    }

    function previewFotosEvento(input) {
        const preview = document.getElementById('fotos-evento-preview');
        preview.innerHTML = '';
        preview.classList.remove('hidden');
        Array.from(input.files).slice(0, 3).forEach(function(file) {
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
</script>

{{-- Cambiar de mes / provincia sin recargar toda la página --}}
<script>
    (function() {
        const ID = 'eventos-contenido';
        let cargando = false;

        async function cargarCalendario(url, agregarAlHistorial) {
            const actual = document.getElementById(ID);
            if (!actual || cargando) return;
            cargando = true;
            actual.style.opacity = '0.5';
            actual.style.pointerEvents = 'none';

            try {
                const resp = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (!resp.ok) throw new Error(resp.status);

                const html = await resp.text();
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const nuevo = doc.getElementById(ID);
                if (!nuevo) throw new Error('sin contenido');

                // Sin la animación de entrada del encabezado (solo la cascada del calendario)
                nuevo.querySelectorAll('.hero-in').forEach(el => el.classList.remove('hero-in'));
                // Al terminar la cascada se saca .pop para que no pise el hover
                nuevo.querySelectorAll('.pop').forEach(function(el) {
                    el.addEventListener('animationend', function(e) {
                        if (e.target === el) el.classList.remove('pop');
                    });
                });

                actual.replaceWith(nuevo);
                if (agregarAlHistorial) history.pushState({
                    calendario: true
                }, '', url);

                // Si el sitio está en otro idioma, traducir el contenido nuevo
                const idioma = localStorage.getItem('surify-idioma') || 'es';
                if (idioma !== 'es' && typeof window.cambiarIdioma === 'function') {
                    window.cambiarIdioma(idioma);
                }
            } catch (e) {
                // Si algo falla, se navega de la forma normal
                window.location.href = url;
            } finally {
                cargando = false;
                const el = document.getElementById(ID);
                if (el) {
                    el.style.opacity = '';
                    el.style.pointerEvents = '';
                }
            }
        }

        // Flechas de mes y filtros de provincia (delegado: sigue andando después de reemplazar el contenido)
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a[data-cal-nav]');
            if (!link) return;
            // Ctrl/Cmd + clic o clic del medio: abrir en otra pestaña como siempre
            if (e.ctrlKey || e.metaKey || e.shiftKey || e.button !== 0) return;
            e.preventDefault();
            cargarCalendario(link.href, true);
        });

        // Botones Atrás / Adelante del navegador
        window.addEventListener('popstate', function() {
            cargarCalendario(window.location.href, false);
        });
    })();
</script>

{{-- Modal Evento --}}
<div id="modal-evento" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="document.getElementById('modal-evento').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden z-10 modal-in">
        <img id="modal-evento-img" src="" class="w-full h-52 object-cover" alt="">
        <button onclick="document.getElementById('modal-evento').classList.add('hidden')"
            class="absolute top-3 right-3 w-8 h-8 bg-black/40 text-white rounded-full flex items-center justify-center hover:bg-black/60 transition-all">
            <span class="material-symbols-outlined text-[18px]">close</span>
        </button>
        <div class="p-6 space-y-3">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <span id="modal-evento-tipo" class="text-[10px] font-bold text-[#28628f] uppercase tracking-wider bg-[#28628f]/10 px-2 py-0.5 rounded-full"></span>
                    <h3 id="modal-evento-nombre" class="text-xl font-black text-slate-800 mt-1" style="font-family: 'Inter', sans-serif;"></h3>
                </div>
                <span id="modal-evento-precio" class="shrink-0 text-xs font-bold bg-amber-50 text-amber-600 px-2.5 py-1 rounded-lg border border-amber-100"></span>
            </div>
            <p id="modal-evento-descripcion" class="text-sm text-slate-500 leading-relaxed line-clamp-3"></p>
            <div class="flex items-center gap-4 text-xs text-slate-400 font-semibold">
                <span id="modal-evento-provincia" class="flex items-center gap-1"></span>
                <span id="modal-evento-destino"></span>
            </div>
            <div class="flex items-center gap-2 text-xs text-slate-500 pt-1">
                <span class="material-symbols-outlined text-[14px] text-[#28628f]">calendar_today</span>
                <span id="modal-evento-fecha-inicio"></span>
                <span>→</span>
                <span id="modal-evento-fecha-fin"></span>
            </div>
            <div class="pt-2">
                <a id="modal-evento-link" href="#"
                    class="w-full bg-[#28628f] text-white font-bold py-3 rounded-xl hover:bg-[#1a4669] transition-all text-sm flex items-center justify-center gap-2 text-decoration-none">
                    <span class="material-symbols-outlined text-[18px]">open_in_new</span>
                    Ver detalles completos
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Modal para sugerir festival (componente compartido con la página de destino) --}}
<x-modal-sugerir-evento :provincias="$provincias" :destinos="$destinosSugerencia ?? null" />

@endsection