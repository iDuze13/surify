@extends('layouts.admin')

@section('title', 'Surify - Gestión de Eventos')

@section('content')

<div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
        <p class="text-xs font-bold uppercase tracking-widest text-[#28628f] mb-1">Administración</p>
        <h1 class="text-4xl font-black text-slate-900 tracking-tight" style="font-family: 'Inter', sans-serif;">Gestión de Eventos</h1>
        <p class="text-slate-500 mt-2 text-sm">Administrá los eventos y festividades de la plataforma.</p>
    </div>
    <a href="{{ route('admin.eventos.create') }}"
        class="inline-flex items-center gap-2 bg-[#28628f] text-white px-5 py-3 rounded-xl font-bold text-sm hover:bg-[#1a4669] transition-all shadow-sm shrink-0 text-decoration-none">
        <span class="material-symbols-outlined text-[18px]">add</span>
        Nuevo Evento
    </a>
</div>

{{-- Barra de búsqueda y filtros --}}
<form method="GET" action="{{ route('admin.eventos.index') }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-4">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
        <input type="text" name="buscar" value="{{ request('buscar') }}"
            placeholder="Buscar evento..."
            class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#28628f]">

        <select name="provincia_id" class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#28628f]">
            <option value="">Todas las provincias</option>
            @foreach($provincias as $prov)
            <option value="{{ $prov->id }}" {{ request('provincia_id') == $prov->id ? 'selected' : '' }}>{{ $prov->nombre }}</option>
            @endforeach
        </select>

        <select name="tipo" class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#28628f]">
            <option value="">Todos los tipos</option>
            @foreach(['Cultural', 'Musical', 'Gastronómico', 'Deportivo', 'Tradicional', 'Religioso'] as $tipo)
            <option value="{{ $tipo }}" {{ request('tipo') == $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
            @endforeach
        </select>

        <select name="filtro" class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#28628f]">
            <option value="todos" {{ request('filtro', 'todos') == 'todos' ? 'selected' : '' }}>Todos</option>
            <option value="proximos" {{ request('filtro') == 'proximos' ? 'selected' : '' }}>Próximos</option>
            <option value="pasados" {{ request('filtro') == 'pasados' ? 'selected' : '' }}>Pasados</option>
        </select>
    </div>
    <div class="flex gap-2 mt-3">
        <button type="submit" class="bg-[#28628f] text-white px-5 py-2 rounded-xl font-bold text-sm hover:bg-[#1a4669] transition-all">
            Filtrar
        </button>
        <a href="{{ route('admin.eventos.index') }}" class="border border-slate-200 text-slate-600 px-5 py-2 rounded-xl font-bold text-sm hover:bg-slate-50 transition-all text-decoration-none">
            Limpiar
        </a>
    </div>
</form>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50">
                    <th class="text-left px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Evento</th>
                    <th class="text-left px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Provincia</th>
                    <th class="text-left px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Tipo</th>
                    <th class="text-left px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Fechas</th>
                    <th class="text-left px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Estado</th>
                    <th class="text-right px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($eventos as $evento)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-slate-100 overflow-hidden shrink-0">
                                @if($evento->imagen_url)
                                <img src="{{ $evento->imagen_url }}" class="w-full h-full object-cover" alt="{{ $evento->nombre }}">
                                @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <span class="material-symbols-outlined text-slate-300 text-[18px]">celebration</span>
                                </div>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800">{{ $evento->nombre }}</p>
                                @if($evento->sugerido_por)
                                <span class="text-[10px] font-bold text-amber-500 uppercase tracking-wider">Sugerido por usuario</span>
                                @else
                                <p class="text-xs text-slate-400">ID: {{ $evento->id }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm text-slate-600">{{ $evento->provincia?->nombre ?? '—' }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs font-bold px-2.5 py-1 bg-[#28628f]/10 text-[#28628f] rounded-full">
                            {{ $evento->tipo ?? 'Sin tipo' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-xs text-slate-600 font-semibold">{{ \Carbon\Carbon::parse($evento->fecha_inicio)->format('d/m/Y') }}</p>
                        @if($evento->fecha_fin)
                        <p class="text-xs text-slate-400">hasta {{ \Carbon\Carbon::parse($evento->fecha_fin)->format('d/m/Y') }}</p>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($evento->pasado)
                        <span class="inline-flex items-center gap-1 text-xs font-bold px-2.5 py-1 bg-slate-100 text-slate-400 rounded-full border border-slate-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400 inline-block"></span>
                            Pasado
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 text-xs font-bold px-2.5 py-1 bg-emerald-50 text-emerald-600 rounded-full border border-emerald-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block animate-pulse"></span>
                            Próximo
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            @if(!$evento->activo)
                            <form method="POST" action="{{ route('admin.eventos.update', $evento) }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="nombre" value="{{ $evento->nombre }}">
                                <input type="hidden" name="fecha_inicio" value="{{ $evento->fecha_inicio }}">
                                <input type="hidden" name="activo" value="1">
                                <button type="submit"
                                    class="w-9 h-9 bg-emerald-50 hover:bg-emerald-100 text-emerald-500 rounded-lg flex items-center justify-center transition-all"
                                    title="Aprobar">
                                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                                </button>
                            </form>
                            @endif
                            <a href="{{ route('admin.eventos.edit', $evento) }}"
                                class="w-9 h-9 bg-slate-100 hover:bg-[#28628f]/10 hover:text-[#28628f] text-slate-400 rounded-lg flex items-center justify-center transition-all text-decoration-none"
                                title="Editar">
                                <span class="material-symbols-outlined text-[18px]">edit</span>
                            </a>
                            <form method="POST" action="{{ route('admin.eventos.destroy', $evento) }}"
                                onsubmit="return confirm('¿Seguro que querés eliminar {{ $evento->nombre }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-9 h-9 bg-slate-100 hover:bg-rose-50 hover:text-rose-500 text-slate-400 rounded-lg flex items-center justify-center transition-all"
                                    title="Eliminar">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center">
                        <span class="material-symbols-outlined text-5xl text-slate-300 block mb-3">celebration</span>
                        <p class="text-slate-400 font-medium">No hay eventos cargados todavía.</p>
                        <a href="{{ route('admin.eventos.create') }}" class="inline-flex items-center gap-1 mt-3 text-[#28628f] font-bold text-sm hover:underline text-decoration-none">
                            <span class="material-symbols-outlined text-[16px]">add</span> Crear el primer evento
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection