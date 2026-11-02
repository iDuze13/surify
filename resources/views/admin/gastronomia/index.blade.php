@extends('layouts.admin')

@section('title', 'Surify Admin - Gastronomía')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">Gastronomía Regional</h1>
            <p class="text-slate-500 text-sm mt-1">Administrá los platos típicos por provincia.</p>
        </div>

        {{-- 🔐 PERMISO: Crear plato --}}
        @can('gestionar_gastronomia')
        <button onclick="document.getElementById('modal-agregar').classList.remove('hidden')"
            class="bg-[#28628f] text-white px-5 py-2.5 rounded-xl font-bold hover:bg-[#1a4669] transition-all shadow-sm flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Agregar plato
        </button>
        @endcan
    </div>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('admin.gastronomia.index') }}" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <input type="text" name="buscar" value="{{ request('buscar') }}"
                placeholder="Buscar plato..."
                class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#28628f]">

            <select name="provincia_id" class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#28628f]">
                <option value="">Todas las provincias</option>
                @foreach($provincias as $prov)
                <option value="{{ $prov->id }}" {{ request('provincia_id') == $prov->id ? 'selected' : '' }}>{{ $prov->nombre }}</option>
                @endforeach
            </select>

            <select name="categoria" class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#28628f]">
                <option value="">Todas las categorías</option>
                @foreach(['Plato principal', 'Entrada', 'Postre', 'Bebida', 'Infusión', 'Snack'] as $cat)
                <option value="{{ $cat }}" {{ request('categoria') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2 mt-3">
            <button type="submit" class="bg-[#28628f] text-white px-5 py-2 rounded-xl font-bold text-sm hover:bg-[#1a4669] transition-all">
                Filtrar
            </button>
            <a href="{{ route('admin.gastronomia.index') }}" class="border border-slate-200 text-slate-600 px-5 py-2 rounded-xl font-bold text-sm hover:bg-slate-50 transition-all text-decoration-none">
                Limpiar
            </a>
        </div>
    </form>

    {{-- Lista por provincia --}}
    @foreach($provincias as $provincia)
    @if($provincia->gastronomia->count() > 0)
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-black text-slate-700 flex items-center gap-2">
                <span class="material-symbols-outlined text-[#28628f] text-[20px]">location_on</span>
                {{ $provincia->nombre }}
                <span class="text-xs font-semibold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">{{ $provincia->gastronomia->count() }} platos</span>
            </h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($provincia->gastronomia as $plato)
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm flex flex-col">
                <div class="h-36 bg-slate-100 overflow-hidden">
                    <img src="{{ $plato->imagen_url ?: 'https://images.unsplash.com/photo-1544025162-d76694265947?w=400&q=80' }}"
                        class="w-full h-full object-cover" alt="{{ $plato->nombre }}">
                </div>
                <div class="p-4 flex flex-col flex-grow">
                    <div class="flex items-start justify-between gap-2 mb-1">
                        <h3 class="font-bold text-slate-800 text-sm leading-tight">{{ $plato->nombre }}</h3>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-[#28628f]/10 text-[#28628f] whitespace-nowrap shrink-0">{{ $plato->categoria }}</span>
                    </div>
                    <p class="text-xs text-slate-500 leading-relaxed line-clamp-2 flex-grow">{{ $plato->descripcion }}</p>

                    {{-- 🔐 PERMISO: Acciones de Modificar y Eliminar --}}
                    @can('gestionar_gastronomia')
                    <div class="flex gap-2 mt-3 pt-3 border-t border-slate-100">
                        <button onclick="abrirEditar(this)"
                            data-id="{{ $plato->id }}"
                            data-nombre="{{ $plato->nombre }}"
                            data-descripcion="{{ $plato->descripcion }}"
                            data-categoria="{{ $plato->categoria }}"
                            data-imagen="{{ $plato->imagen_url }}"
                            class="flex-1 text-xs font-bold text-[#28628f] border border-[#28628f] rounded-lg py-1.5 hover:bg-blue-50 transition-colors">
                            Editar
                        </button>
                        <form action="{{ route('admin.gastronomia.destroy', $plato->id) }}" method="POST"
                            class="form-eliminar" data-title="¿Eliminar plato?" data-text="¿Seguro que querés eliminar {{ $plato->nombre }}?">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="text-xs font-semibold text-rose-500 border border-rose-200 rounded-lg py-1.5 px-3 hover:bg-rose-50 transition-colors">
                                Eliminar
                            </button>
                        </form>
                    </div>
                    @endcan
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
    @endforeach

    @if($provincias->every(fn($p) => $p->gastronomia->count() === 0))
    <div class="text-center py-20 text-slate-400">
        <span class="material-symbols-outlined text-5xl block mb-3">restaurant</span>
        <p class="font-semibold">Todavía no hay platos cargados.</p>
        <p class="text-sm mt-1">Agregá el primero haciendo clic en "Agregar plato".</p>
    </div>
    @endif

</div>

{{-- 🔐 ESTRUCTURAS DE MODALES: Solo se renderizan en el HTML si tiene el permiso asignado --}}
@can('gestionar_gastronomia')
{{-- Modal Agregar --}}
<div id="modal-agregar" class="hidden fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-lg font-bold text-slate-800">Agregar plato típico</h2>
            <button onclick="document.getElementById('modal-agregar').classList.add('hidden')"
                class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form action="{{ route('admin.gastronomia.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            <div class="space-y-1">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Provincia *</label>
                <select name="provincia_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#28628f]">
                    <option value="">Seleccioná una provincia</option>
                    @foreach($provincias as $prov)
                    <option value="{{ $prov->id }}">{{ $prov->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-1">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Nombre del plato *</label>
                <input type="text" name="nombre" required placeholder="Ej: Locro, Empanadas salteñas..."
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#28628f]">
            </div>
            <div class="space-y-1">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Categoría *</label>
                <select name="categoria" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#28628f]">
                    <option value="Plato principal">Plato principal</option>
                    <option value="Entrada">Entrada</option>
                    <option value="Postre">Postre</option>
                    <option value="Bebida">Bebida</option>
                    <option value="Infusión">Infusión</option>
                    <option value="Snack">Snack</option>
                </select>
            </div>
            <div class="space-y-1">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Descripción *</label>
                <textarea name="descripcion" rows="3" required placeholder="Describí el plato, sus ingredientes principales y su origen..."
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#28628f] resize-none"></textarea>
            </div>
            <div class="space-y-1">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Imagen (opcional)</label>
                <input type="file" name="imagen" accept="image/*"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#28628f] file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-[#28628f] file:text-white file:font-bold file:text-xs">
                <p class="text-xs text-slate-400">JPG o PNG, máx. 5MB.</p>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit"
                    class="flex-1 bg-[#28628f] text-white font-bold py-3 rounded-xl hover:bg-[#1a4669] transition-all">
                    Guardar plato
                </button>
                <button type="button" onclick="document.getElementById('modal-agregar').classList.add('hidden')"
                    class="flex-1 border border-slate-200 text-slate-600 font-bold py-3 rounded-xl hover:bg-slate-50 transition-all">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Editar --}}
<div id="modal-editar" class="hidden fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-lg font-bold text-slate-800">Editar plato</h2>
            <button onclick="document.getElementById('modal-editar').classList.add('hidden')"
                class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form id="form-editar" action="" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf @method('PUT')
            <div class="space-y-1">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Nombre *</label>
                <input type="text" name="nombre" id="editar-nombre" required
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#28628f]">
            </div>
            <div class="space-y-1">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Categoría *</label>
                <select name="categoria" id="editar-categoria" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#28628f]">
                    <option value="Plato principal">Plato principal</option>
                    <option value="Entrada">Entrada</option>
                    <option value="Postre">Postre</option>
                    <option value="Bebida">Bebida</option>
                    <option value="Infusión">Infusión</option>
                    <option value="Snack">Snack</option>
                </select>
            </div>
            <div class="space-y-1">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Descripción *</label>
                <textarea name="descripcion" id="editar-descripcion" rows="3" required
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#28628f] resize-none"></textarea>
            </div>
            <div class="space-y-1">
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Imagen (opcional)</label>
                <img id="editar-imagen-preview" src="" class="hidden w-20 h-20 rounded-xl object-cover border border-slate-200 mb-2" alt="Imagen actual">
                <input type="file" name="imagen" accept="image/*"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#28628f] file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-[#28628f] file:text-white file:font-bold file:text-xs">
                <p class="text-xs text-slate-400">Dejalo vacío para mantener la imagen actual.</p>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit"
                    class="flex-1 bg-[#28628f] text-white font-bold py-3 rounded-xl hover:bg-[#1a4669] transition-all">
                    Guardar cambios
                </button>
                <button type="button" onclick="document.getElementById('modal-editar').classList.add('hidden')"
                    class="flex-1 border border-slate-200 text-slate-600 font-bold py-3 rounded-xl hover:bg-slate-50 transition-all">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function abrirEditar(btn) {
        document.getElementById('editar-nombre').value = btn.dataset.nombre;
        document.getElementById('editar-descripcion').value = btn.dataset.descripcion;
        document.getElementById('editar-categoria').value = btn.dataset.categoria;

        const preview = document.getElementById('editar-imagen-preview');
        if (btn.dataset.imagen) {
            preview.src = btn.dataset.imagen;
            preview.classList.remove('hidden');
        } else {
            preview.classList.add('hidden');
        }

        document.getElementById('form-editar').action = `/admin/gastronomia/${btn.dataset.id}`;
        document.getElementById('modal-editar').classList.remove('hidden');
    }
</script>
@endcan

@endsection