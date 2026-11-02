@extends('layouts.app')

@section('title', 'Surify Admin - Editar Evento')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">

    {{-- Botón Volver --}}
    <div class="mb-6">
        <a href="{{ route('admin.eventos.index') }}" class="inline-flex items-center gap-1 text-slate-400 hover:text-[#28628f] text-sm font-medium transition-colors text-decoration-none">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            Volver a Eventos
        </a>
    </div>

    {{-- Encabezado --}}
    <div class="mb-8">
        <p class="text-xs font-bold uppercase tracking-widest text-amber-500 mb-1">Agenda Cultural</p>
        <h1 class="text-4xl font-black text-slate-900 tracking-tight" style="font-family: 'Inter', sans-serif;">Editar Evento</h1>
        <p class="text-slate-500 mt-2 text-sm">Modificá los datos del festival o evento: <span class="font-bold text-[#28628f]">{{ $evento->nombre }}</span>.</p>
    </div>

    @if($errors->any())
        <div class="mb-4 p-4 bg-rose-50 border border-rose-200 rounded-xl">
            @foreach($errors->all() as $error)
                <p class="text-rose-600 text-sm">⚠️ {{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- Formulario Principal --}}
    <form action="{{ route('admin.eventos.update', $evento->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-5">
            <h2 class="text-base font-black text-slate-700 mb-2 flex items-center gap-2">
                <span class="material-symbols-outlined text-[#28628f]">info</span>
                Información Básica
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Nombre del evento *</label>
                    <input type="text" name="nombre" value="{{ old('nombre', $evento->nombre) }}" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:border-[#28628f] focus:ring-1 focus:ring-[#28628f]">
                    @error('nombre') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Destino asociado</label>
                    <select name="destino_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:border-[#28628f]">
                        <option value="">Sin destino específico</option>
                        @foreach($destinos as $destino)
                            <option value="{{ $destino->id }}" {{ old('destino_id', $evento->destino_id) == $destino->id ? 'selected' : '' }}>
                                {{ $destino->nombre }} ({{ $destino->provincia?->nombre }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Provincia</label>
                    <select name="provincia_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:border-[#28628f]">
                        <option value="">Sin provincia</option>
                        @foreach($provincias as $prov)
                            <option value="{{ $prov->id }}" {{ old('provincia_id', $evento->provincia_id) == $prov->id ? 'selected' : '' }}>
                                {{ $prov->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Tipo</label>
                    <select name="tipo" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:border-[#28628f]">
                        <option value="">Sin tipo</option>
                        @foreach(['Cultural', 'Musical', 'Gastronómico', 'Deportivo', 'Tradicional', 'Religioso'] as $tipo)
                            <option value="{{ $tipo }}" {{ old('tipo', $evento->tipo) == $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Fecha de inicio *</label>
                    <input type="date" name="fecha_inicio" value="{{ old('fecha_inicio', $evento->fecha_inicio?->format('Y-m-d')) }}" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:border-[#28628f]">
                    @error('fecha_inicio') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Fecha de fin</label>
                    <input type="date" name="fecha_fin" value="{{ old('fecha_fin', $evento->fecha_fin?->format('Y-m-d')) }}"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:border-[#28628f]">
                </div>

                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Rango de precio</label>
                    <select name="rango_precio" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:border-[#28628f]">
                        <option value="">Sin especificar</option>
                        <option value="$" {{ old('rango_precio', $evento->rango_precio) == '$' ? 'selected' : '' }}>$ — Gratuito / Bajo</option>
                        <option value="$$" {{ old('rango_precio', $evento->rango_precio) == '$$' ? 'selected' : '' }}>$$ — Medio</option>
                        <option value="$$$" {{ old('rango_precio', $evento->rango_precio) == '$$$' ? 'selected' : '' }}>$$$ — Alto</option>
                    </select>
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Descripción del evento</label>
                <textarea name="descripcion" rows="4" placeholder="Contale a los turistas de qué se trata el festival..."
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:border-[#28628f] resize-none">{{ old('descripcion', $evento->descripcion) }}</textarea>
            </div>
        </div>

        {{-- Imagen --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <h2 class="text-base font-black text-slate-700 flex items-center gap-2">
                <span class="material-symbols-outlined text-[#28628f]">image</span>
                Imagen del Evento
            </h2>

            @if($evento->imagen_url)
                <div class="w-full h-40 rounded-xl overflow-hidden bg-slate-100 mb-3">
                    <img src="{{ str_starts_with($evento->imagen_url, 'http') ? $evento->imagen_url : asset('storage/' . $evento->imagen_url) }}"
                         class="w-full h-full object-cover" alt="Imagen actual">
                </div>
            @endif

            <div class="space-y-1">
                <label class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Imagen nueva (opcional)</label>
                <input type="file" name="imagen" accept="image/*"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:outline-none focus:border-[#28628f] file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-[#28628f] file:text-white file:font-bold file:text-xs">
                <p class="text-xs text-slate-400 mt-1">JPG o PNG, máx. 10MB. Dejalo vacío para mantener la imagen actual.</p>
                @error('imagen') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Estado --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="activo" value="1" {{ $evento->activo ? 'checked' : '' }}
                    class="w-5 h-5 rounded text-[#28628f] focus:ring-[#28628f]">
                <div>
                    <p class="text-sm font-bold text-slate-700">Evento activo</p>
                    <p class="text-xs text-slate-400">Si está activo, será visible para todos los usuarios.</p>
                </div>
            </label>
        </div>

        {{-- Botones --}}
        <div class="flex gap-4">
            <button type="submit"
                class="flex-1 bg-[#28628f] text-white font-bold py-3 rounded-xl hover:bg-[#1a4669] transition-all shadow-sm text-sm">
                Guardar cambios del evento
            </button>
            <a href="{{ route('admin.eventos.index') }}"
                class="px-8 py-3 bg-slate-100 text-slate-600 font-bold rounded-xl text-sm hover:bg-slate-200 transition-all text-decoration-none text-center">
                Cancelar
            </a>
        </div>
    </form>

</div>
@endsection