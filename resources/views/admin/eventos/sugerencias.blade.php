@extends('layouts.admin')

@section('title', 'Surify - Sugerencias de Festivales')

@section('content')

<div class="mb-8">
    <p class="text-xs font-bold uppercase tracking-widest text-[#28628f] mb-1">Administración</p>
    <h1 class="text-4xl font-black text-slate-900 tracking-tight" style="font-family: 'Inter', sans-serif;">Sugerencias de Festivales</h1>
    <p class="text-slate-500 mt-2 text-sm">Revisá los festivales y eventos que sugieren los usuarios. Al aprobarlos se publican en el calendario; al rechazarlos, se le avisa a quien lo sugirió.</p>
</div>

{{-- Tabs de estado --}}
<section class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
    <div class="flex bg-slate-100 rounded-xl p-1.5 w-fit gap-1">
        <a href="{{ route('admin.eventos.sugerencias', ['estado' => 'pendiente']) }}"
            class="px-5 py-2 rounded-lg text-sm font-bold transition-all text-decoration-none
               {{ $estado === 'pendiente' ? 'bg-white shadow-sm text-[#28628f]' : 'text-slate-500 hover:bg-white/50' }}">
            Pendientes
            <span class="ml-1.5 bg-[#28628f]/10 text-[#28628f] px-2 py-0.5 rounded-full text-[10px]">{{ $countPendientes }}</span>
        </a>
        <a href="{{ route('admin.eventos.sugerencias', ['estado' => 'aprobada']) }}"
            class="px-5 py-2 rounded-lg text-sm font-bold transition-all text-decoration-none
               {{ $estado === 'aprobada' ? 'bg-white shadow-sm text-emerald-600' : 'text-slate-500 hover:bg-white/50' }}">
            Aprobadas
            <span class="ml-1.5 bg-emerald-50 text-emerald-600 px-2 py-0.5 rounded-full text-[10px]">{{ $countAprobadas }}</span>
        </a>
        <a href="{{ route('admin.eventos.sugerencias', ['estado' => 'rechazada']) }}"
            class="px-5 py-2 rounded-lg text-sm font-bold transition-all text-decoration-none
               {{ $estado === 'rechazada' ? 'bg-white shadow-sm text-rose-500' : 'text-slate-500 hover:bg-white/50' }}">
            Rechazadas
            <span class="ml-1.5 bg-rose-50 text-rose-500 px-2 py-0.5 rounded-full text-[10px]">{{ $countRechazadas }}</span>
        </a>
    </div>
</section>

{{-- Lista de sugerencias --}}
<div class="flex flex-col gap-4">
    @forelse($sugerencias as $evento)
    @php
    $estadoEvento = $evento->estado_sugerencia;
    $autor = $evento->autorSugerencia;
    @endphp
    <article class="bg-white rounded-2xl border shadow-sm hover:shadow-md transition-all overflow-hidden
            {{ $estadoEvento === 'pendiente' ? 'border-slate-200' : ($estadoEvento === 'aprobada' ? 'border-emerald-200' : 'border-l-4 border-l-rose-400 border-slate-200') }}">
        <div class="flex flex-col lg:flex-row gap-5 p-6">

            {{-- Imagen --}}
            <div class="shrink-0 w-full lg:w-44 h-32 rounded-xl overflow-hidden bg-slate-100">
                @if($evento->imagen_url)
                <a href="{{ $evento->imagen_url }}" target="_blank" title="Ver imagen completa">
                    <img src="{{ $evento->imagen_url }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500" alt="{{ $evento->nombre }}">
                </a>
                @else
                <div class="w-full h-full flex flex-col items-center justify-center text-slate-300">
                    <span class="material-symbols-outlined text-4xl">celebration</span>
                    <span class="text-[10px] font-bold uppercase tracking-wider mt-1">Sin imagen</span>
                </div>
                @endif
            </div>

            {{-- Contenido --}}
            <div class="flex-1 min-w-0">
                <div class="flex flex-col md:flex-row md:items-start justify-between gap-2 mb-3">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="font-bold text-slate-800 text-lg leading-tight">{{ $evento->nombre }}</h3>
                            @if($estadoEvento === 'pendiente')
                            <span class="text-[10px] font-bold px-2 py-0.5 bg-amber-50 text-amber-600 rounded-full border border-amber-100">PENDIENTE</span>
                            @elseif($estadoEvento === 'aprobada')
                            <span class="text-[10px] font-bold px-2 py-0.5 bg-emerald-50 text-emerald-600 rounded-full border border-emerald-100">APROBADA</span>
                            @else
                            <span class="text-[10px] font-bold px-2 py-0.5 bg-rose-50 text-rose-500 rounded-full border border-rose-100">RECHAZADA</span>
                            @endif
                            @if($evento->pasado)
                            <span class="text-[10px] font-bold px-2 py-0.5 bg-slate-100 text-slate-400 rounded-full border border-slate-200" title="La fecha del evento ya pasó">YA PASÓ</span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-400 mt-1">
                            Sugerido por
                            @if($autor)
                            <a href="{{ route('usuarios.show', $autor) }}" target="_blank" class="font-semibold text-[#28628f] hover:underline text-decoration-none">{{ $autor->name }}</a>
                            @else
                            <span class="font-semibold">usuario eliminado</span>
                            @endif
                            • {{ $evento->created_at?->diffForHumans() }}
                        </p>
                    </div>
                    <span class="shrink-0 text-xs font-bold px-2.5 py-1 bg-[#28628f]/10 text-[#28628f] rounded-full w-fit">{{ $evento->tipo ?? 'Sin tipo' }}</span>
                </div>

                {{-- Datos del evento --}}
                <div class="flex flex-wrap gap-x-5 gap-y-1.5 text-xs text-slate-500 mb-3">
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px] text-[#28628f]">location_on</span>
                        {{ $evento->provincia?->nombre ?? 'Sin provincia' }}@if($evento->destino) — {{ $evento->destino->nombre }}@endif
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px] text-[#28628f]">calendar_month</span>
                        {{ $evento->fecha_inicio?->format('d/m/Y') }}@if($evento->fecha_fin) al {{ $evento->fecha_fin->format('d/m/Y') }}@endif
                    </span>
                    @if($evento->rango_precio)
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px] text-[#28628f]">payments</span>
                        {{ $evento->rango_precio }}
                    </span>
                    @endif
                </div>

                {{-- Descripción --}}
                <p class="text-sm text-slate-600 leading-relaxed mb-4 line-clamp-4">{{ $evento->descripcion }}</p>

                {{-- Resultado de la revisión --}}
                @if($estadoEvento === 'rechazada')
                <div class="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-100 text-xs text-rose-600">
                    <span class="font-bold">Motivo del rechazo:</span>
                    {{ $evento->motivo_rechazo ?: 'Sin motivo indicado.' }}
                </div>
                @endif

                {{-- Acciones --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-4 border-t border-slate-100">
                    <span class="text-xs text-slate-400">
                        ID: #{{ $evento->id }}
                        @if($estadoEvento !== 'pendiente' && $evento->revisor)
                        • {{ $estadoEvento === 'aprobada' ? 'Aprobado' : 'Rechazado' }} por {{ $evento->revisor->name }} {{ $evento->revisado_at?->diffForHumans() }}
                        @endif
                    </span>

                    <div class="flex flex-wrap gap-2">
                        @if($estadoEvento === 'pendiente')
                        <form method="POST" action="{{ route('admin.eventos.aprobar', $evento) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                class="px-5 py-2 rounded-xl bg-[#28628f] text-white text-xs font-bold hover:bg-[#1a4669] transition-all shadow-sm flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">check_circle</span>
                                Aprobar y publicar
                            </button>
                        </form>

                        <a href="{{ route('admin.eventos.edit', $evento) }}"
                            class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-50 transition-all flex items-center gap-1 text-decoration-none"
                            title="Corregir datos antes de publicar (marcá 'Activo' al guardar para publicarlo)">
                            <span class="material-symbols-outlined text-[16px]">edit</span>
                            Editar
                        </a>

                        <form method="POST" action="{{ route('admin.eventos.rechazar', $evento) }}" class="form-rechazar-sugerencia" data-nombre="{{ $evento->nombre }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="motivo" value="">
                            <button type="submit"
                                class="px-5 py-2 rounded-xl border border-rose-200 text-rose-500 text-xs font-bold hover:bg-rose-50 transition-all flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">cancel</span>
                                Rechazar
                            </button>
                        </form>

                        @elseif($estadoEvento === 'aprobada')
                        <a href="{{ route('eventos.show', $evento->id) }}" target="_blank"
                            class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-50 transition-all flex items-center gap-1 text-decoration-none">
                            <span class="material-symbols-outlined text-[16px]">open_in_new</span>
                            Ver publicado
                        </a>

                        @else
                        <form method="POST" action="{{ route('admin.eventos.restaurar', $evento) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-50 transition-all flex items-center gap-1"
                                title="Volver a ponerla como pendiente para revisarla de nuevo">
                                <span class="material-symbols-outlined text-[16px]">undo</span>
                                Volver a pendientes
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </article>
    @empty
    <div class="bg-white rounded-2xl border border-slate-200 p-16 text-center">
        <span class="material-symbols-outlined text-5xl text-slate-300 block mb-3">celebration</span>
        <p class="text-slate-400 font-medium">
            @if($estado === 'pendiente') No hay sugerencias pendientes de revisión. ¡Todo al día!
            @elseif($estado === 'aprobada') Todavía no se aprobó ninguna sugerencia.
            @else No hay sugerencias rechazadas.
            @endif
        </p>
    </div>
    @endforelse
</div>

{{-- Paginación --}}
@if($sugerencias->hasPages())
<div class="mt-6 flex items-center justify-between">
    <p class="text-sm text-slate-500">
        Mostrando {{ $sugerencias->firstItem() }}–{{ $sugerencias->lastItem() }} de {{ $sugerencias->total() }} sugerencias
    </p>
    <div class="flex items-center gap-1">
        @if($sugerencias->onFirstPage())
        <span class="w-10 h-10 flex items-center justify-center rounded-xl border border-slate-200 text-slate-300">
            <span class="material-symbols-outlined">chevron_left</span>
        </span>
        @else
        <a href="{{ $sugerencias->previousPageUrl() }}" class="w-10 h-10 flex items-center justify-center rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-50 transition-all text-decoration-none">
            <span class="material-symbols-outlined">chevron_left</span>
        </a>
        @endif

        @foreach($sugerencias->getUrlRange(1, $sugerencias->lastPage()) as $page => $url)
        <a href="{{ $url }}" class="w-10 h-10 flex items-center justify-center rounded-xl text-sm font-bold transition-all text-decoration-none
                {{ $page == $sugerencias->currentPage() ? 'bg-[#28628f] text-white shadow-sm' : 'border border-slate-200 text-slate-500 hover:bg-slate-50' }}">
            {{ $page }}
        </a>
        @endforeach

        @if($sugerencias->hasMorePages())
        <a href="{{ $sugerencias->nextPageUrl() }}" class="w-10 h-10 flex items-center justify-center rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-50 transition-all text-decoration-none">
            <span class="material-symbols-outlined">chevron_right</span>
        </a>
        @else
        <span class="w-10 h-10 flex items-center justify-center rounded-xl border border-slate-200 text-slate-300">
            <span class="material-symbols-outlined">chevron_right</span>
        </span>
        @endif
    </div>
</div>
@endif

{{-- Rechazar: pide un motivo opcional que le llega al usuario en la notificación --}}
<script>
    document.querySelectorAll('.form-rechazar-sugerencia').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            if (form.dataset.confirmado === '1') return;
            e.preventDefault();

            if (!window.Swal) {
                if (confirm('¿Rechazar "' + form.dataset.nombre + '"?')) {
                    form.dataset.confirmado = '1';
                    form.submit();
                }
                return;
            }

            Swal.fire({
                title: '¿Rechazar sugerencia?',
                text: '"' + form.dataset.nombre + '" no se va a publicar. Podés contarle al usuario por qué (opcional).',
                icon: 'warning',
                input: 'textarea',
                inputPlaceholder: 'Ej: Ya existe en el calendario / faltan datos de la fecha...',
                inputAttributes: {
                    maxlength: 500
                },
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Sí, rechazar',
                cancelButtonText: 'Cancelar',
                background: document.documentElement.classList.contains('dark') ? '#1e293b' : '#fff',
                color: document.documentElement.classList.contains('dark') ? '#f8fafc' : '#1e293b'
            }).then(function(result) {
                if (!result.isConfirmed) return;
                form.querySelector('input[name="motivo"]').value = (result.value || '').trim();
                form.dataset.confirmado = '1';
                form.submit();
            });
        });
    });
</script>

@endsection