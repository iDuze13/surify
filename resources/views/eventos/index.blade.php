@extends('layouts.app')

@section('title', 'Surify - Eventos y Festividades')

@section('content')
<div class="max-w-[1280px] w-full mx-auto py-4">

    <div class="mb-8 flex items-start justify-between">
        <div>
            <h1 class="text-4xl font-black text-slate-900 tracking-tighter mb-2">Festivales y Eventos</h1>
            <p class="text-base text-slate-500 max-w-2xl">Planifica tu viaje en torno a las celebraciones culturales y festividades más vibrantes de Argentina.</p>
        </div>
        @auth
        <button onclick="document.getElementById('modal-sugerir-evento').classList.remove('hidden')"
            class="bg-[#28628f] text-white px-5 py-2.5 rounded-xl font-bold hover:bg-[#1a4669] transition-all shadow-sm flex items-center gap-2 shrink-0">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Sugerir festival
        </button>
        @endauth
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- SIDEBAR -->
        <aside class="col-span-1 lg:col-span-4 flex flex-col gap-4">

            {{-- Navegación mes --}}
            @php
            $mesAnterior = \Carbon\Carbon::create($año, $mes, 1)->subMonth();
            $mesSiguiente = \Carbon\Carbon::create($año, $mes, 1)->addMonth();
            @endphp
            <div class="bg-slate-100 rounded-2xl p-4 flex items-center justify-between border border-slate-200/60">
                <a href="{{ route('eventos.index', ['año' => $mesAnterior->year, 'mes' => $mesAnterior->month, 'provincia_id' => request('provincia_id')]) }}"
                    class="w-9 h-9 rounded-full flex items-center justify-center bg-white hover:bg-slate-50 border border-slate-200 transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-slate-500 text-sm">chevron_left</span>
                </a>
                <div class="flex flex-col items-center select-none">
                    <span class="text-[10px] font-bold text-[#28628f] uppercase tracking-widest">Calendario Anual</span>
                    <h2 class="text-lg font-black text-slate-800">{{ ucfirst(\Carbon\Carbon::create($año, $mes, 1)->translatedFormat('F Y')) }}</h2>
                </div>
                <a href="{{ route('eventos.index', ['año' => $mesSiguiente->year, 'mes' => $mesSiguiente->month, 'provincia_id' => request('provincia_id')]) }}"
                    class="w-9 h-9 rounded-full flex items-center justify-center bg-white hover:bg-slate-50 border border-slate-200 transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-slate-500 text-sm">chevron_right</span>
                </a>
            </div>

            {{-- Filtros provincia --}}
            <div class="flex gap-1.5 overflow-x-auto pb-1 scrollbar-none select-none">
                <a href="{{ route('eventos.index', ['año' => $año, 'mes' => $mes]) }}"
                    class="px-3 py-1.5 rounded-full text-xs font-bold whitespace-nowrap {{ !request('provincia_id') ? 'bg-[#28628f] text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600' }}">
                    Todas
                </a>
                @foreach($provincias as $prov)
                <a href="{{ route('eventos.index', ['año' => $año, 'mes' => $mes, 'provincia_id' => $prov->id]) }}"
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
                    class="bg-white rounded-2xl p-4 border {{ $index === 0 ? 'border-[#28628f] ring-1 ring-[#28628f]/20' : 'border-slate-200' }} hover:border-[#28628f] hover:shadow-xs transition-all group relative overflow-hidden text-decoration-none block cursor-pointer">
                    @if($index === 0)
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-[#28628f]"></div>
                    @endif
                    <div class="flex gap-4">
                        <div class="flex flex-col items-center justify-center bg-slate-50 rounded-xl w-14 h-14 shrink-0 border border-slate-200">
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
                <div class="bg-white rounded-2xl p-6 border border-slate-200 text-center text-slate-400 text-sm">
                    No hay eventos para este mes.
                </div>
                @endforelse
            </div>
        </aside>

        <!-- CALENDARIO -->
        <section class="col-span-1 lg:col-span-8">
            <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
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
                            class="bg-emerald-50/40 min-h-[100px] p-2 border-2 border-emerald-500/30 hover:bg-emerald-50 transition-colors block text-decoration-none cursor-pointer">
                            <span class="text-xs font-bold text-emerald-700">{{ $d }}</span>
                            <div class="mt-1 bg-emerald-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded truncate">{{ Str::limit($eventoDelDia->nombre, 12) }}</div>
                        </div>
                        {{-- ✅ FIN cambio calendario --}}
                        @else
                        <div class="bg-white min-h-[100px] p-2 hover:bg-slate-50/80 transition-colors {{ $hoy ? 'ring-2 ring-inset ring-[#28628f]' : '' }}">
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

{{-- Modal Evento --}}
<div id="modal-evento" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="document.getElementById('modal-evento').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden z-10">
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

@auth
<div id="modal-sugerir-evento" class="hidden fixed inset-0 z-50">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"
        onclick="document.getElementById('modal-sugerir-evento').classList.add('hidden')"></div>

    <div class="relative flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-2xl bg-white dark:bg-[#1e293b] shadow-2xl">

            {{-- Encabezado --}}
            <div class="flex items-start justify-between gap-4 border-b border-slate-200 dark:border-slate-700 px-6 py-5">
                <div>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white">Sugerir festival o evento</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Ayudanos a completar el calendario. El equipo de Surify revisará tu sugerencia antes de publicarla.
                    </p>
                </div>
                <button type="button"
                    onclick="document.getElementById('modal-sugerir-evento').classList.add('hidden')"
                    class="rounded-lg p-1 text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-slate-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form method="POST" action="{{ route('eventos.sugerir') }}" enctype="multipart/form-data"
                class="space-y-4 px-6 py-5"
                onsubmit="
                      const b = document.getElementById('btn-enviar-sugerencia');
                      b.disabled = true;
                      document.getElementById('btn-sugerencia-icono').classList.add('hidden');
                      document.getElementById('btn-sugerencia-spinner').classList.remove('hidden');
                      document.getElementById('btn-sugerencia-texto').textContent = 'Enviando...';
                  ">
                @csrf

                @if ($errors->sugerencia->any())
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900 dark:bg-red-950/40 dark:text-red-300">
                    <ul class="list-disc space-y-1 pl-5">
                        @foreach ($errors->sugerencia->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @php
                $inputClass = 'w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 px-4 py-2.5 text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-[#28628f]';
                $labelClass = 'mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400';
                $listaProvincias = $provincias ?? \App\Models\Provincia::orderBy('nombre')->get();
                @endphp

                <div>
                    <label class="{{ $labelClass }}">Nombre del evento *</label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" required maxlength="255"
                        placeholder="Ej: Fiesta Nacional del Pomelo" class="{{ $inputClass }}">
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="{{ $labelClass }}">Tipo *</label>
                        <select name="tipo" required class="{{ $inputClass }}">
                            <option value="">Seleccioná...</option>
                            @foreach (['Festival', 'Fiesta popular', 'Música', 'Gastronomía', 'Cultura', 'Deporte', 'Religioso', 'Otro'] as $tipo)
                            <option value="{{ $tipo }}" @selected(old('tipo')===$tipo)>{{ $tipo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">Provincia *</label>
                        <select name="provincia_id" required class="{{ $inputClass }}">
                            <option value="">Seleccioná...</option>
                            @foreach ($listaProvincias as $prov)
                            <option value="{{ $prov->id }}" @selected(old('provincia_id')==$prov->id)>{{ $prov->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="{{ $labelClass }}">Lugar</label>
                    <input type="text" name="ubicacion" value="{{ old('ubicacion') }}" maxlength="255"
                        placeholder="Ej: Predio Ferial, Laguna Blanca" class="{{ $inputClass }}">
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <label class="{{ $labelClass }}">Fecha de inicio *</label>
                        <input type="date" name="fecha_inicio" value="{{ old('fecha_inicio') }}" required class="{{ $inputClass }}">
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">Fecha de fin</label>
                        <input type="date" name="fecha_fin" value="{{ old('fecha_fin') }}" class="{{ $inputClass }}">
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">Precio</label>
                        <input type="text" name="rango_precio" value="{{ old('rango_precio') }}" maxlength="100"
                            placeholder="Ej: Gratis" class="{{ $inputClass }}">
                    </div>
                </div>

                <div>
                    <label class="{{ $labelClass }}">Descripción *</label>
                    <textarea name="descripcion" rows="4" required maxlength="2000"
                        placeholder="Contanos de qué se trata, qué actividades tiene, por qué vale la pena ir..."
                        class="{{ $inputClass }}">{{ old('descripcion') }}</textarea>
                </div>

                <div>
                    <label class="{{ $labelClass }}">Imagen (opcional, máx. 5 MB)</label>
                    <input type="file" name="imagen" accept="image/*"
                        class="block w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:rounded-lg file:border-0 file:bg-[#28628f] file:px-4 file:py-2 file:text-sm file:font-bold file:text-white hover:file:bg-[#1a4669]">
                </div>

                {{-- Pie --}}
                <div class="flex justify-end gap-3 border-t border-slate-200 dark:border-slate-700 pt-4">
                    <button type="button"
                        onclick="document.getElementById('modal-sugerir-evento').classList.add('hidden')"
                        class="rounded-xl px-5 py-2.5 font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700">
                        Cancelar
                    </button>
                    <button type="submit" id="btn-enviar-sugerencia"
                        class="flex items-center gap-2 rounded-xl bg-[#28628f] px-5 py-2.5 font-bold text-white shadow-sm transition-all hover:bg-[#1a4669] disabled:cursor-not-allowed disabled:opacity-70">
                        <span id="btn-sugerencia-icono" class="material-symbols-outlined text-[18px]">send</span>
                        <svg id="btn-sugerencia-spinner" class="hidden h-[18px] w-[18px] animate-spin" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        <span id="btn-sugerencia-texto">Enviar sugerencia</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Cerrar con Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') document.getElementById('modal-sugerir-evento')?.classList.add('hidden');
    });
</script>

{{-- Si hubo errores de validación, reabrir el modal con los datos cargados --}}
@if ($errors->sugerencia->any())
<script>
    document.getElementById('modal-sugerir-evento').classList.remove('hidden');
</script>
@endif

{{-- Cartel de confirmación después de enviar --}}
@if (session('sugerencia_enviada'))
<script>
    window.addEventListener('load', () => {
        if (window.Swal) {
            Swal.fire({
                icon: 'success',
                title: '¡Sugerencia enviada!',
                text: 'Gracias por ayudarnos a mejorar Surify. Vamos a revisarla y te avisaremos cuando esté publicada.',
                confirmButtonText: 'Genial',
                confirmButtonColor: '#28628f',
            });
        } else {
            alert('¡Sugerencia enviada! Gracias por ayudarnos a mejorar Surify.');
        }
    });
</script>
@endif
@endauth

@endsection