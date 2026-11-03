@extends('layouts.admin')

@section('title', 'Surify - Moderación de Reseñas')

@section('content')

<div class="mb-8">
    <p class="text-xs font-bold uppercase tracking-widest text-[#28628f] mb-1">Administración</p>
    <h1 class="text-4xl font-black text-slate-900 tracking-tight" style="font-family: 'Inter', sans-serif;">Moderación de Reseñas</h1>
    <p class="text-slate-500 mt-2 text-sm">Revisá, aprobá o rechazá las reseñas enviadas por los usuarios.</p>
</div>

{{-- Tabs de estado --}}
<section class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
    <div class="flex bg-slate-100 rounded-xl p-1.5 w-fit gap-1">
        <a href="{{ route('admin.resenas.index', ['estado' => 'pendiente']) }}"
           class="px-5 py-2 rounded-lg text-sm font-bold transition-all text-decoration-none
               {{ $estado === 'pendiente' ? 'bg-white shadow-sm text-[#28628f]' : 'text-slate-500 hover:bg-white/50' }}">
            Pendientes
            <span class="ml-1.5 bg-[#28628f]/10 text-[#28628f] px-2 py-0.5 rounded-full text-[10px]">{{ $countPendientes }}</span>
        </a>
        <a href="{{ route('admin.resenas.index', ['estado' => 'aprobada']) }}"
           class="px-5 py-2 rounded-lg text-sm font-bold transition-all text-decoration-none
               {{ $estado === 'aprobada' ? 'bg-white shadow-sm text-emerald-600' : 'text-slate-500 hover:bg-white/50' }}">
            Aprobadas
            <span class="ml-1.5 bg-emerald-50 text-emerald-600 px-2 py-0.5 rounded-full text-[10px]">{{ $countAprobadas }}</span>
        </a>
        <a href="{{ route('admin.resenas.index', ['estado' => 'rechazada']) }}"
           class="px-5 py-2 rounded-lg text-sm font-bold transition-all text-decoration-none
               {{ $estado === 'rechazada' ? 'bg-white shadow-sm text-rose-500' : 'text-slate-500 hover:bg-white/50' }}">
            Rechazadas
            <span class="ml-1.5 bg-rose-50 text-rose-500 px-2 py-0.5 rounded-full text-[10px]">{{ $countRechazadas }}</span>
        </a>
    </div>
</section>

{{-- Lista de reseñas --}}
<div class="flex flex-col gap-4">
    @forelse($resenas as $resena)
        <article class="bg-white rounded-2xl border shadow-sm hover:shadow-md transition-all overflow-hidden
            {{ is_null($resena->aprobada) ? 'border-slate-200' : ($resena->aprobada ? 'border-emerald-200' : 'border-l-4 border-l-rose-400 border-slate-200') }}">

            {{-- Borde lateral para spam/rechazadas --}}
            <div class="flex flex-col lg:flex-row gap-5 p-6">

                {{-- Avatar --}}
                <div class="shrink-0">
                    @if($resena->user?->avatar)
                        <img src="{{ $resena->user->avatar }}" class="w-14 h-14 rounded-full border-2 border-[#28628f]/10 object-cover" alt="{{ $resena->user->name }}">
                    @else
                        <div class="w-14 h-14 rounded-full bg-[#28628f]/10 flex items-center justify-center text-lg font-black text-[#28628f]">
                            {{ strtoupper(substr($resena->user?->name ?? '?', 0, 2)) }}
                        </div>
                    @endif
                </div>

                {{-- Contenido --}}
                <div class="flex-1">
                    <div class="flex flex-col md:flex-row md:items-start justify-between gap-2 mb-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-bold text-slate-800 text-base">
                                    {{ $resena->anonima ? 'Usuario Anónimo' : ($resena->user?->name ?? 'Usuario eliminado') }}
                                </h3>
                                @if(is_null($resena->aprobada))
                                    <span class="text-[10px] font-bold px-2 py-0.5 bg-amber-50 text-amber-600 rounded-full border border-amber-100">PENDIENTE</span>
                                @elseif($resena->aprobada)
                                    <span class="text-[10px] font-bold px-2 py-0.5 bg-emerald-50 text-emerald-600 rounded-full border border-emerald-100">APROBADA</span>
                                @else
                                    <span class="text-[10px] font-bold px-2 py-0.5 bg-rose-50 text-rose-500 rounded-full border border-rose-100">RECHAZADA</span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-400 mt-0.5">
                                {{ $resena->created_at->diffForHumans() }}
                                @if($resena->destino)
                                    • <span class="text-[#28628f] font-medium">{{ $resena->destino->nombre }}</span>
                                @elseif($resena->evento)
                                    • <span class="text-amber-500 font-medium">{{ $resena->evento->nombre }}</span>
                                @endif
                            </p>
                        </div>

                        {{-- Estrellas --}}
                        <div class="flex items-center gap-1 shrink-0">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="material-symbols-outlined text-[18px] {{ $i <= $resena->calificacion ? 'text-amber-400' : 'text-slate-200' }}"
                                      style="font-variation-settings: 'FILL' {{ $i <= $resena->calificacion ? '1' : '0' }};">star</span>
                            @endfor
                            <span class="text-sm font-bold text-slate-700 ml-1">{{ $resena->calificacion }}.0</span>
                        </div>
                    </div>

                    {{-- Título --}}
                    @if($resena->titulo)
                        <p class="text-sm font-bold text-slate-700 mb-1">{{ $resena->titulo }}</p>
                    @endif

                    {{-- Comentario --}}
                    <p class="text-sm text-slate-600 leading-relaxed mb-4 line-clamp-3">{{ $resena->comentario }}</p>

                    {{-- Imágenes --}}
                    @if($resena->imagenes && $resena->imagenes->count() > 0)
                        <div class="flex gap-2 mb-4 overflow-x-auto pb-1">
                            @foreach($resena->imagenes->take(4) as $img)
                                <img src="{{ $img->url ?? $img->imagen_url }}"
                                     class="w-20 h-20 rounded-xl object-cover border border-slate-200 shrink-0"
                                     alt="Imagen reseña">
                            @endforeach
                        </div>
                    @endif

                    {{-- Acciones --}}
                    <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                        <span class="text-xs text-slate-400">ID: #{{ $resena->id }}</span>

                        {{-- 🔐 PERMISO: Toda la botonera de moderación requiere el permiso específico --}}
                        @can('administrar_reseñas')
                        <div class="flex gap-2">
                            @if(!$resena->aprobada || is_null($resena->aprobada))
                                <form method="POST" action="{{ route('admin.resenas.aprobar', $resena) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="px-5 py-2 rounded-xl bg-[#28628f] text-white text-xs font-bold hover:bg-[#1a4669] transition-all shadow-sm flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[16px]">check_circle</span>
                                        Aprobar
                                    </button>
                                </form>
                            @endif

                            @if($resena->aprobada || is_null($resena->aprobada))
                                <form method="POST" action="{{ route('admin.resenas.rechazar', $resena) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="px-5 py-2 rounded-xl border border-rose-200 text-rose-500 text-xs font-bold hover:bg-rose-50 transition-all flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[16px]">cancel</span>
                                        Rechazar
                                    </button>
                                </form>
                            @endif

                            <form method="POST" action="{{ route('admin.resenas.destroy', $resena) }}"
                                  class="form-eliminar" data-title="¿Eliminar reseña?" data-text="¿Seguro que querés eliminar esta reseña permanentemente?">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-rose-50 hover:text-rose-500 text-slate-400 flex items-center justify-center transition-all"
                                    title="Eliminar permanentemente">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </form>
                        </div>
                        @endcan
                    </div>
                </div>
            </div>
        </article>
    @empty
        <div class="bg-white rounded-2xl border border-slate-200 p-16 text-center">
            <span class="material-symbols-outlined text-5xl text-slate-300 block mb-3">chat_bubble</span>
            <p class="text-slate-400 font-medium">
                @if($estado === 'pendiente') No hay reseñas pendientes de revisión.
                @elseif($estado === 'aprobada') No hay reseñas aprobadas todavía.
                @else No hay reseñas rechazadas.
                @endif
            </p>
        </div>
    @endforelse
</div>

{{-- Paginación --}}
@if($resenas->hasPages())
    <div class="mt-6 flex items-center justify-between">
        <p class="text-sm text-slate-500">
            Mostrando {{ $resenas->firstItem() }}–{{ $resenas->lastItem() }} de {{ $resenas->total() }} reseñas
        </p>
        <div class="flex items-center gap-1">
            @if($resenas->onFirstPage())
                <span class="w-10 h-10 flex items-center justify-center rounded-xl border border-slate-200 text-slate-300">
                    <span class="material-symbols-outlined">chevron_left</span>
                </span>
            @else
                <a href="{{ $resenas->previousPageUrl() }}" class="w-10 h-10 flex items-center justify-center rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-50 transition-all text-decoration-none">
                    <span class="material-symbols-outlined">chevron_left</span>
                </a>
            @endif

            @foreach($resenas->getUrlRange(1, $resenas->lastPage()) as $page => $url)
                <a href="{{ $url }}" class="w-10 h-10 flex items-center justify-center rounded-xl text-sm font-bold transition-all text-decoration-none
                    {{ $page == $resenas->currentPage() ? 'bg-[#28628f] text-white shadow-sm' : 'border border-slate-200 text-slate-500 hover:bg-slate-50' }}">
                    {{ $page }}
                </a>
            @endforeach

            @if($resenas->hasMorePages())
                <a href="{{ $resenas->nextPageUrl() }}" class="w-10 h-10 flex items-center justify-center rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-50 transition-all text-decoration-none">
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

@endsection