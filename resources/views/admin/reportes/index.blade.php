@extends('layouts.admin')

@section('title', 'Surify - Reportes')

@section('content')

<div class="mb-8">
    <p class="text-xs font-bold uppercase tracking-widest text-amber-600 mb-2">Panel de Control</p>
    <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight" style="font-family: 'Inter', sans-serif;">Reportes</h1>
    <p class="text-slate-500 mt-2 text-sm max-w-2xl">Elegí el período y las secciones, y se genera un PDF listo para descargar o imprimir.</p>
</div>

@if($errors->any())
<div class="mb-4 p-4 bg-rose-50 border border-rose-200 rounded-xl">
    @foreach($errors->all() as $error)
    <p class="text-rose-600 text-sm">⚠️ {{ $error }}</p>
    @endforeach
</div>
@endif

{{-- target _blank: el PDF se abre en otra pestaña y el panel queda como estaba --}}
<form method="GET" action="{{ route('admin.reportes.pdf') }}" target="_blank" id="form-reporte" class="space-y-6">

    {{-- Período --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2 mb-4">
            <span class="material-symbols-outlined text-[#28628f]">date_range</span>
            Período
        </h2>

        <div class="flex flex-wrap gap-2 mb-5">
            @foreach([
                'hoy'     => 'Hoy',
                '7'       => 'Últimos 7 días',
                '30'      => 'Últimos 30 días',
                'mes'     => 'Este mes',
                'mes_ant' => 'Mes anterior',
                'anio'    => 'Este año',
            ] as $clave => $texto)
            <button type="button" data-atajo="{{ $clave }}"
                class="atajo-fecha px-4 py-1.5 rounded-full border border-slate-200 text-slate-600 text-xs font-bold hover:border-[#28628f] hover:text-[#28628f] transition-all">
                {{ $texto }}
            </button>
            @endforeach
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="desde" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Desde *</label>
                <input type="date" id="desde" name="desde" value="{{ old('desde', $desde) }}" required
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:ring-[#28628f] focus:border-[#28628f]">
            </div>
            <div>
                <label for="hasta" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Hasta *</label>
                <input type="date" id="hasta" name="hasta" value="{{ old('hasta', $hasta) }}" required
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:ring-[#28628f] focus:border-[#28628f]">
            </div>
        </div>
    </div>

    {{-- Secciones --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2 mb-1">
            <span class="material-symbols-outlined text-[#28628f]">checklist</span>
            Qué incluir
        </h2>
        <p class="text-xs text-slate-400 mb-4">El resumen general va siempre al principio.</p>

        @php
        $detalle = [
            'usuarios' => ['icono' => 'group',       'texto' => 'Altas por mes, cuentas con Google y 2FA, perfiles públicos o privados, usuarios más activos.'],
            'destinos' => ['icono' => 'landscape',   'texto' => 'Más visitados, más favoritos, mejor calificados, destinos por provincia y categoría.'],
            'resenas'  => ['icono' => 'chat_bubble', 'texto' => 'Aprobadas, pendientes y rechazadas, distribución de estrellas, últimas reseñas.'],
            'eventos'  => ['icono' => 'celebration', 'texto' => 'Eventos del período por provincia y tipo, sugerencias recibidas, eventos más visitados.'],
        ];
        $elegidas = old('secciones', array_keys($secciones));
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach($secciones as $clave => $titulo)
            <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 hover:border-[#28628f] cursor-pointer transition-colors has-[:checked]:border-[#28628f] has-[:checked]:bg-[#28628f]/5">
                <input type="checkbox" name="secciones[]" value="{{ $clave }}" {{ in_array($clave, $elegidas) ? 'checked' : '' }}
                    class="mt-1 w-4 h-4 rounded border-slate-300 text-[#28628f] focus:ring-[#28628f]">
                <span>
                    <span class="flex items-center gap-1.5 text-sm font-bold text-slate-800">
                        <span class="material-symbols-outlined text-[18px] text-[#28628f]">{{ $detalle[$clave]['icono'] }}</span>
                        {{ $titulo }}
                    </span>
                    <span class="block text-xs text-slate-500 mt-1">{{ $detalle[$clave]['texto'] }}</span>
                </span>
            </label>
            @endforeach
        </div>
    </div>

    <div class="flex flex-col sm:flex-row items-center justify-end gap-3">
        <p id="aviso-reporte" class="hidden text-xs text-slate-400 sm:mr-auto">El PDF se abrió en otra pestaña.</p>
        <button type="submit" id="btn-reporte"
            class="w-full sm:w-auto px-10 py-3 rounded-full bg-[#28628f] text-white font-bold text-sm hover:bg-[#1a4669] transition-all shadow-sm flex items-center justify-center gap-2">
            <span class="material-symbols-outlined text-[18px]">picture_as_pdf</span>
            Generar PDF
        </button>
    </div>
</form>

<script>
    (function() {
        const desde = document.getElementById('desde');
        const hasta = document.getElementById('hasta');
        const form = document.getElementById('form-reporte');
        if (!form) return;

        const iso = d => {
            const z = n => String(n).padStart(2, '0');
            return d.getFullYear() + '-' + z(d.getMonth() + 1) + '-' + z(d.getDate());
        };

        document.querySelectorAll('.atajo-fecha').forEach(btn => {
            btn.addEventListener('click', () => {
                const hoy = new Date();
                let ini = new Date(hoy), fin = new Date(hoy);

                switch (btn.dataset.atajo) {
                    case '7':  ini.setDate(hoy.getDate() - 6); break;
                    case '30': ini.setDate(hoy.getDate() - 29); break;
                    case 'mes': ini = new Date(hoy.getFullYear(), hoy.getMonth(), 1); break;
                    case 'mes_ant':
                        ini = new Date(hoy.getFullYear(), hoy.getMonth() - 1, 1);
                        fin = new Date(hoy.getFullYear(), hoy.getMonth(), 0);
                        break;
                    case 'anio': ini = new Date(hoy.getFullYear(), 0, 1); break;
                }

                desde.value = iso(ini);
                hasta.value = iso(fin);

                document.querySelectorAll('.atajo-fecha').forEach(b => b.classList.remove('bg-[#28628f]', 'text-white', 'border-[#28628f]'));
                btn.classList.add('bg-[#28628f]', 'text-white', 'border-[#28628f]');
            });
        });

        form.addEventListener('submit', e => {
            if (desde.value && hasta.value && desde.value > hasta.value) {
                e.preventDefault();
                Swal.fire({ icon: 'warning', title: 'Revisá las fechas', text: 'La fecha "desde" no puede ser posterior a "hasta".', confirmButtonColor: '#28628f' });
                return;
            }
            if (!form.querySelector('input[name="secciones[]"]:checked')) {
                e.preventDefault();
                Swal.fire({ icon: 'warning', title: 'Elegí al menos una sección', confirmButtonColor: '#28628f' });
                return;
            }
            document.getElementById('aviso-reporte').classList.remove('hidden');
        });
    })();
</script>

@endsection