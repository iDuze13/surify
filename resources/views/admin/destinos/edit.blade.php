@extends('layouts.admin')

@section('title', 'Surify - Editar Destino')

@section('content')

<div class="mb-8">
    <div class="flex items-center gap-2 text-slate-400 mb-3 text-xs font-semibold">
        <a href="{{ route('dashboard') }}" class="hover:text-[#28628f] transition-colors text-decoration-none">Dashboard</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <a href="{{ route('admin.destinos.index') }}" class="hover:text-[#28628f] transition-colors text-decoration-none">Destinos</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="text-[#28628f]">Editar</span>
    </div>
    <h1 class="text-4xl font-black text-slate-900 tracking-tight" style="font-family: 'Inter', sans-serif;">Editar Destino</h1>
    <p class="text-slate-500 mt-2 text-sm">Modificá los datos de <span class="font-bold text-[#28628f]">{{ $destino->nombre }}</span>.</p>
</div>

{{-- ✅ FORM PRINCIPAL: solo contiene los campos de edición y el botón Guardar --}}
<form method="POST" action="{{ route('admin.destinos.update', $destino) }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @method('PUT')

    {{-- Información básica --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h2 class="text-base font-black text-slate-700 mb-5 flex items-center gap-2">
            <span class="material-symbols-outlined text-[#28628f]">info</span>
            Información Básica
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Nombre del Destino *</label>
                <input type="text" name="nombre" value="{{ old('nombre', $destino->nombre) }}" required
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:ring-[#28628f] focus:border-[#28628f] @error('nombre') border-rose-400 @enderror">
                @error('nombre') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Provincia *</label>
                <select name="provincia_id" required
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:ring-[#28628f] focus:border-[#28628f]">
                    <option value="">Seleccioná una provincia</option>
                    @foreach($provincias as $provincia)
                    <option value="{{ $provincia->id }}" {{ old('provincia_id', $destino->provincia_id) == $provincia->id ? 'selected' : '' }}>
                        {{ $provincia->nombre }}
                    </option>
                    @endforeach
                </select>
                @error('provincia_id') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Categoría</label>
                <div class="flex flex-wrap gap-2" id="categorias-container">
                    @foreach(['Patrimonio Natural', 'Aventura', 'Gastronomía', 'Familiar', 'Cultural', 'Nieve', 'Playa', 'Ciudad'] as $cat)
                    <button type="button"
                        onclick="toggleCategoria(this, '{{ $cat }}')"
                        class="categoria-btn px-4 py-2 rounded-full border text-xs font-bold transition-all
                                {{ old('categoria', $destino->categoria) == $cat
                                    ? 'activo bg-[#28628f]/10 border-[#28628f] text-[#28628f]'
                                    : 'border-slate-200 text-slate-500 hover:border-[#28628f] hover:text-[#28628f]' }}">
                        {{ $cat }}
                    </button>
                    @endforeach
                </div>
                <input type="hidden" name="categoria" id="categoria-input" value="{{ old('categoria', $destino->categoria) }}">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Rango de Precio</label>
                <select name="rango_precio"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:ring-[#28628f] focus:border-[#28628f]">
                    <option value="">Seleccioná un rango</option>
                    <option value="Bajo" {{ old('rango_precio', $destino->rango_precio) == 'Bajo' ? 'selected' : '' }}>💚 Bajo</option>
                    <option value="Medio" {{ old('rango_precio', $destino->rango_precio) == 'Medio' ? 'selected' : '' }}>💛 Medio</option>
                    <option value="Alto" {{ old('rango_precio', $destino->rango_precio) == 'Alto' ? 'selected' : '' }}>🔴 Alto</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Descripción</label>
                <textarea name="descripcion" rows="5"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:ring-[#28628f] focus:border-[#28628f]">{{old('descripcion', $destino->descripcion) }}</textarea>
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Imagen del Destino</label>
                @if($destino->imagen_url)
                <div class="mb-3 flex items-center gap-3">
                    <img src="{{ $destino->imagen_url }}" class="w-16 h-16 rounded-xl object-cover border border-slate-200" alt="Imagen actual">
                    <p class="text-xs text-slate-400">Imagen actual. Subí una nueva para reemplazarla.</p>
                </div>
                @endif
                <input type="file" name="imagen" accept="image/*"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:ring-[#28628f] focus:border-[#28628f] file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-[#28628f] file:text-white file:font-bold file:text-xs @error('imagen') border-rose-400 @enderror">
                <p class="text-xs text-slate-400 mt-1">JPG o PNG, máx. 5MB. Dejalo vacío para mantener la imagen actual.</p>
                @error('imagen') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    {{-- Ubicación --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h2 class="text-base font-black text-slate-700 mb-2 flex items-center gap-2">
            <span class="material-symbols-outlined text-[#28628f]">location_on</span>
            Ubicación
        </h2>
        <p class="text-xs text-slate-400 mb-5">Coordenadas actuales del destino en el mapa.</p>

        @php
        $coords = null;
        if ($destino->ubicacion) {
        $coords = \DB::select('SELECT ST_Y(ubicacion::geometry) as lat, ST_X(ubicacion::geometry) as lng FROM destinos WHERE id = ?', [$destino->id])[0] ?? null;
        }
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Latitud</label>
                <input type="text" name="latitud"
                    value="{{ old('latitud', $coords->lat ?? '') }}"
                    placeholder="Ej: -50.4967"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:ring-[#28628f] focus:border-[#28628f]">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Longitud</label>
                <input type="text" name="longitud"
                    value="{{ old('longitud', $coords->lng ?? '') }}"
                    placeholder="Ej: -73.1377"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:ring-[#28628f] focus:border-[#28628f]">
            </div>
        </div>
    </div>

    {{-- Estado --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h2 class="text-base font-black text-slate-700 mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-[#28628f]">toggle_on</span>
            Estado de Publicación
        </h2>
        <label class="flex items-center gap-3 cursor-pointer">
            <input type="checkbox" name="activo" value="1" {{ $destino->activo ? 'checked' : '' }}
                class="w-5 h-5 rounded text-[#28628f] focus:ring-[#28628f]">
            <div>
                <p class="text-sm font-bold text-slate-700">Destino activo</p>
                <p class="text-xs text-slate-400">Si está activo, será visible para todos los usuarios.</p>
            </div>
        </label>
    </div>

    {{-- Botones guardar --}}
    <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-2">
        <a href="{{ route('admin.destinos.index') }}"
            class="w-full sm:w-auto px-8 py-3 rounded-full border border-slate-200 text-slate-500 font-bold text-sm hover:bg-slate-50 transition-all text-center text-decoration-none">
            Cancelar
        </a>
        <button type="submit"
            class="w-full sm:w-auto px-10 py-3 rounded-full bg-[#28628f] text-white font-bold text-sm hover:bg-[#1a4669] transition-all shadow-sm flex items-center justify-center gap-2">
            <span class="material-symbols-outlined text-[18px]">save</span>
            Guardar Cambios
        </button>
    </div>

</form>
{{-- ✅ FIN del form principal — Zona de Peligro va FUERA de este form --}}

{{-- Zona peligro: form separado, fuera del form principal --}}
<div class="bg-white rounded-2xl border border-rose-100 shadow-sm p-6 mt-6">
    <h2 class="text-base font-black text-rose-500 mb-3 flex items-center gap-2">
        <span class="material-symbols-outlined">warning</span>
        Zona de Peligro
    </h2>
    <p class="text-xs text-slate-500 mb-4">Eliminar este destino borrará también todas sus reseñas y favoritos asociados.</p>
    <form method="POST" action="{{ route('admin.destinos.destroy', $destino) }}"
        class="form-eliminar" data-title="¿Eliminar destino?" data-text="¿Seguro que querés eliminar {{ $destino->nombre }}? Esta acción no se puede deshacer.">
        @csrf
        @method('DELETE')
        <button type="submit"
            class="inline-flex items-center gap-2 bg-rose-500 hover:bg-rose-600 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition-all">
            <span class="material-symbols-outlined text-[16px]">delete</span>
            Eliminar Destino
        </button>
    </form>
</div>

<script>
    function toggleCategoria(btn, valor) {
        const input = document.getElementById('categoria-input');
        const activo = btn.classList.contains('activo');

        document.querySelectorAll('.categoria-btn').forEach(b => {
            b.classList.remove('activo', 'bg-[#28628f]/10', 'border-[#28628f]', 'text-[#28628f]');
            b.classList.add('border-slate-200', 'text-slate-500');
        });

        if (!activo) {
            btn.classList.add('activo', 'bg-[#28628f]/10', 'border-[#28628f]', 'text-[#28628f]');
            btn.classList.remove('border-slate-200', 'text-slate-500');
            input.value = valor;
        } else {
            input.value = '';
        }
    }
</script>

@endsection