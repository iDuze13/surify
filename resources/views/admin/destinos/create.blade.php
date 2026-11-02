@extends('layouts.admin')

@section('title', 'Surify - Crear Destino')

@section('content')

<div class="mb-8">
    <div class="flex items-center gap-2 text-slate-400 mb-3 text-xs font-semibold">
        <a href="{{ route('dashboard') }}" class="hover:text-[#28628f] transition-colors text-decoration-none">Dashboard</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <a href="{{ route('admin.destinos.index') }}" class="hover:text-[#28628f] transition-colors text-decoration-none">Destinos</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="text-[#28628f]">Crear Nuevo</span>
    </div>
    <h1 class="text-4xl font-black text-slate-900 tracking-tight" style="font-family: 'Inter', sans-serif;">Dar vida a un nuevo paisaje</h1>
    <p class="text-slate-500 mt-2 text-sm max-w-2xl">Completá los detalles para registrar un nuevo destino turístico en la plataforma.</p>
</div>

<form method="POST" action="{{ route('admin.destinos.store') }}" enctype="multipart/form-data" class="space-y-6">
    @csrf

    {{-- Información básica --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h2 class="text-base font-black text-slate-700 mb-5 flex items-center gap-2">
            <span class="material-symbols-outlined text-[#28628f]">info</span>
            Información Básica
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Nombre del Destino *</label>
                <input type="text" name="nombre" value="{{ old('nombre') }}" required
                    placeholder="Ej: Glaciar Perito Moreno"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:ring-[#28628f] focus:border-[#28628f] @error('nombre') border-rose-400 @enderror">
                @error('nombre') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Provincia *</label>
                <select name="provincia_id" required
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:ring-[#28628f] focus:border-[#28628f] @error('provincia_id') border-rose-400 @enderror">
                    <option value="">Seleccioná una provincia</option>
                    @foreach($provincias as $provincia)
                        <option value="{{ $provincia->id }}" {{ old('provincia_id') == $provincia->id ? 'selected' : '' }}>
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
                            class="categoria-btn px-4 py-2 rounded-full border border-slate-200 text-slate-500 text-xs font-bold hover:border-[#28628f] hover:text-[#28628f] transition-all">
                            {{ $cat }}
                        </button>
                    @endforeach
                </div>
                <input type="hidden" name="categoria" id="categoria-input" value="{{ old('categoria') }}">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Rango de Precio</label>
                <select name="rango_precio"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:ring-[#28628f] focus:border-[#28628f]">
                    <option value="">Seleccioná un rango</option>
                    <option value="Bajo" {{ old('rango_precio') == 'Bajo' ? 'selected' : '' }}>💚 Bajo</option>
                    <option value="Medio" {{ old('rango_precio') == 'Medio' ? 'selected' : '' }}>💛 Medio</option>
                    <option value="Alto" {{ old('rango_precio') == 'Alto' ? 'selected' : '' }}>🔴 Alto</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Descripción</label>
                <textarea name="descripcion" rows="5"
                    placeholder="Describí la experiencia que ofrece este destino..."
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:ring-[#28628f] focus:border-[#28628f]">{{old('descripcion') }}</textarea>
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Imagen del Destino</label>
                <input type="file" name="imagen" accept="image/*"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:ring-[#28628f] focus:border-[#28628f] file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-[#28628f] file:text-white file:font-bold file:text-xs @error('imagen') border-rose-400 @enderror">
                <p class="text-xs text-slate-400 mt-1">JPG o PNG, máx. 5MB.</p>
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
        <p class="text-xs text-slate-400 mb-5">Ingresá las coordenadas del destino. Cuando esté disponible la API de Maps, se podrá seleccionar desde el mapa.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Latitud</label>
                <input type="text" name="latitud" value="{{ old('latitud') }}"
                    placeholder="Ej: -50.4967"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:ring-[#28628f] focus:border-[#28628f]">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Longitud</label>
                <input type="text" name="longitud" value="{{ old('longitud') }}"
                    placeholder="Ej: -73.1377"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:ring-[#28628f] focus:border-[#28628f]">
            </div>
        </div>

        <div class="mt-4 p-4 bg-amber-50 rounded-xl border border-amber-100 flex items-start gap-3">
            <span class="material-symbols-outlined text-amber-500 text-[20px] shrink-0">tips_and_updates</span>
            <p class="text-xs text-amber-700">Las coordenadas se guardan como punto geográfico (PostGIS). Cuando esté disponible la API de Maps, se podrá seleccionar directamente desde el mapa.</p>
        </div>
    </div>

    {{-- Estado --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h2 class="text-base font-black text-slate-700 mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-[#28628f]">toggle_on</span>
            Estado de Publicación
        </h2>
        <label class="flex items-center gap-3 cursor-pointer">
            <input type="checkbox" name="activo" value="1" checked
                class="w-5 h-5 rounded text-[#28628f] focus:ring-[#28628f]">
            <div>
                <p class="text-sm font-bold text-slate-700">Publicar destino</p>
                <p class="text-xs text-slate-400">Si está activo, será visible para todos los usuarios de la plataforma.</p>
            </div>
        </label>
    </div>

    {{-- Botones --}}
    <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-2">
        <a href="{{ route('admin.destinos.index') }}"
           class="w-full sm:w-auto px-8 py-3 rounded-full border border-slate-200 text-slate-500 font-bold text-sm hover:bg-slate-50 transition-all text-center text-decoration-none">
            Cancelar
        </a>
        <button type="submit"
            class="w-full sm:w-auto px-10 py-3 rounded-full bg-[#28628f] text-white font-bold text-sm hover:bg-[#1a4669] transition-all shadow-sm flex items-center justify-center gap-2">
            <span class="material-symbols-outlined text-[18px]">publish</span>
            Publicar Destino
        </button>
    </div>
</form>

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