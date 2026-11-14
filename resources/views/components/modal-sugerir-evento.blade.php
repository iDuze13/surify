{{--
    Modal "Sugerir festival o evento".
    - Sin destino: el usuario elige provincia y lugar (calendario de eventos).
    - Con destino (:destino="$destino"): la sugerencia queda registrada directo
      en ese destino y su provincia (página del destino).
    Se abre con: document.getElementById('modal-sugerir-evento').classList.remove('hidden')
--}}
@props(['destino' => null, 'provincias' => null, 'destinos' => null])

@auth
<div id="modal-sugerir-evento" class="hidden fixed inset-0 z-50">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"
        onclick="document.getElementById('modal-sugerir-evento').classList.add('hidden')"></div>

    <div class="relative flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-2xl bg-white dark:bg-[#1e293b] shadow-2xl modal-in">

            {{-- Encabezado --}}
            <div class="flex items-start justify-between gap-4 border-b border-slate-200 dark:border-slate-700 px-6 py-5">
                <div>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white">
                        {{ $destino ? 'Sugerir evento en ' . $destino->nombre : 'Sugerir festival o evento' }}
                    </h2>
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
                @endphp

                <div>
                    <label class="{{ $labelClass }}">Nombre del evento *</label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" required maxlength="255"
                        placeholder="Ej: Fiesta Nacional del Pomelo" class="{{ $inputClass }}">
                </div>

                <div>
                    <label class="{{ $labelClass }}">Tipo *</label>
                    <select name="tipo" required class="{{ $inputClass }}">
                        <option value="">Seleccioná...</option>
                        @foreach (['Festival', 'Fiesta popular', 'Música', 'Gastronomía', 'Cultura', 'Deporte', 'Religioso', 'Otro'] as $tipo)
                        <option value="{{ $tipo }}" @selected(old('tipo')===$tipo)>{{ $tipo }}</option>
                        @endforeach
                    </select>
                </div>

                @if ($destino)
                {{-- Viene de la página de un destino: queda registrado en ese destino y su provincia --}}
                <input type="hidden" name="provincia_id" value="{{ $destino->provincia_id }}">
                <input type="hidden" name="destino_id" value="{{ $destino->id }}">
                <div class="flex items-center gap-3 rounded-xl border border-[#28628f]/20 bg-[#28628f]/5 px-4 py-3">
                    <span class="material-symbols-outlined text-[#28628f]">location_on</span>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">Lugar</p>
                        <p class="text-sm font-bold text-slate-800 dark:text-slate-100">{{ $destino->nombre }}<span class="font-medium text-slate-500 dark:text-slate-400"> — {{ $destino->provincia->nombre ?? '' }}</span></p>
                    </div>
                </div>
                @else
                @php
                $listaProvincias = $provincias ?? \App\Models\Provincia::orderBy('nombre')->get();
                @endphp
                <div>
                    <label class="{{ $labelClass }}">Provincia *</label>
                    <select name="provincia_id" id="sugerencia-provincia" required class="{{ $inputClass }}">
                        <option value="">Seleccioná...</option>
                        @foreach ($listaProvincias as $prov)
                        <option value="{{ $prov->id }}" @selected(old('provincia_id')==$prov->id)>{{ $prov->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                @php
                $listaDestinos = $destinos ?? \App\Models\Destino::where('activo', true)->orderBy('nombre')->get(['id', 'nombre', 'provincia_id']);
                @endphp
                <div>
                    <label class="{{ $labelClass }}">Lugar</label>
                    <select name="destino_id" id="sugerencia-destino" class="{{ $inputClass }}">
                        <option value="">Primero elegí una provincia</option>
                        @foreach ($listaDestinos as $dest)
                        <option value="{{ $dest->id }}" data-provincia="{{ $dest->provincia_id }}" @selected(old('destino_id')==$dest->id)>{{ $dest->nombre }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-slate-400 dark:text-slate-500">Solo aparecen los destinos de la provincia elegida. Si no está, dejalo en "Otro lugar de la provincia".</p>
                </div>
                @endif

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
    // Lugar: muestra solo los destinos de la provincia elegida
    (function() {
        const provincia = document.getElementById('sugerencia-provincia');
        const destino = document.getElementById('sugerencia-destino');
        if (!provincia || !destino) return;

        // Copia de todas las opciones (menos la primera, que es el texto de ayuda)
        const opciones = Array.from(destino.options).slice(1).map(o => o.cloneNode(true));
        const ayuda = destino.options[0];

        function filtrar() {
            const elegido = destino.value;
            destino.innerHTML = '';
            destino.appendChild(ayuda);

            if (!provincia.value) {
                ayuda.textContent = 'Primero elegí una provincia';
                destino.disabled = true;
                return;
            }

            const deLaProvincia = opciones.filter(o => o.dataset.provincia === provincia.value);
            ayuda.textContent = deLaProvincia.length ? 'Otro lugar de la provincia' : 'No hay destinos cargados en esta provincia';
            deLaProvincia.forEach(o => destino.appendChild(o));
            destino.value = deLaProvincia.some(o => o.value === elegido) ? elegido : '';
            destino.disabled = false;
        }

        provincia.addEventListener('change', filtrar);
        filtrar(); // al abrir (y si volvió con errores, conserva lo elegido)
    })();

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