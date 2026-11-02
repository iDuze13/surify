@extends('layouts.admin')

@section('title', 'Surify - Nuevo Evento')

@section('content')

@if($errors->any())
    <div class="mb-4 p-4 bg-rose-50 border border-rose-200 rounded-xl">
        @foreach($errors->all() as $error)
            <p class="text-rose-600 text-sm">⚠️ {{ $error }}</p>
        @endforeach
    </div>
@endif

<div class="mb-8 flex items-end justify-between">
    <div>
        <nav class="flex items-center gap-1 text-xs font-bold uppercase tracking-wider mb-2">
            <a href="{{ route('admin.eventos.index') }}" class="text-slate-400 hover:text-[#28628f] transition-colors text-decoration-none">Eventos</a>
            <span class="material-symbols-outlined text-slate-300 text-[14px]">chevron_right</span>
            <span class="text-[#28628f]">Nuevo Evento</span>
        </nav>
        <h1 class="text-4xl font-black text-slate-900 tracking-tight">Crear Nuevo Evento</h1>
        <p class="text-slate-500 mt-2 text-sm">Configurá los detalles del festival o evento cultural.</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.eventos.index') }}"
           class="px-6 py-3 rounded-xl border border-slate-200 text-slate-500 font-bold hover:border-slate-300 transition-all text-decoration-none">
            Cancelar
        </a>
        <button form="form-evento" type="submit"
                class="px-6 py-3 rounded-xl bg-[#28628f] text-white font-bold hover:bg-[#1a4669] transition-all shadow-sm">
            Publicar Evento
        </button>
    </div>
</div>

<form id="form-evento" action="{{ route('admin.eventos.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- Columna izquierda --}}
        <div class="lg:col-span-8 flex flex-col gap-6">

            {{-- Información básica --}}
            <section class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 bg-[#28628f]/10 rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined text-[#28628f] text-[20px]">info</span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">Información Básica</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2 space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Nombre del Festival *</label>
                        <input type="text" name="nombre" required value="{{ old('nombre') }}"
                               placeholder="Ej: Festival Nacional del Folklore"
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#28628f] transition-colors">
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Provincia</label>
                        <select name="provincia_id"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#28628f] transition-colors">
                            <option value="">Seleccioná una provincia</option>
                            @foreach($provincias as $provincia)
                                <option value="{{ $provincia->id }}" {{ old('provincia_id') == $provincia->id ? 'selected' : '' }}>
                                    {{ $provincia->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Destino (opcional)</label>
                        <select name="destino_id"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#28628f] transition-colors">
                            <option value="">Sin destino específico</option>
                            @foreach($destinos as $destino)
                                <option value="{{ $destino->id }}" {{ old('destino_id') == $destino->id ? 'selected' : '' }}>
                                    {{ $destino->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Tipo</label>
                        <select name="tipo"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#28628f] transition-colors">
                            <option value="">Tipo de evento</option>
                            @foreach(['Cultural', 'Musical', 'Gastronómico', 'Deportivo', 'Tradicional', 'Religioso'] as $tipo)
                                <option value="{{ $tipo }}" {{ old('tipo') == $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Rango de precio</label>
                        <select name="rango_precio"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#28628f] transition-colors">
                            <option value="">Sin especificar</option>
                            <option value="$" {{ old('rango_precio') == '$' ? 'selected' : '' }}>$ — Gratuito / Bajo</option>
                            <option value="$$" {{ old('rango_precio') == '$$' ? 'selected' : '' }}>$$ — Medio</option>
                            <option value="$$$" {{ old('rango_precio') == '$$$' ? 'selected' : '' }}>$$$ — Alto</option>
                        </select>
                    </div>
                </div>
            </section>

            {{-- Descripción --}}
            <section class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 bg-[#28628f]/10 rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined text-[#28628f] text-[20px]">description</span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">Descripción del Evento</h3>
                </div>
                <textarea name="descripcion" rows="8"
                          placeholder="Describí la historia, importancia y cronograma del festival..."
                          class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#28628f] transition-colors resize-none">{{ old('descripcion') }}</textarea>
            </section>

        </div>

        {{-- Columna derecha --}}
        <div class="lg:col-span-4 flex flex-col gap-6">

            {{-- Fechas --}}
            <section class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 bg-[#28628f]/10 rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined text-[#28628f] text-[20px]">calendar_today</span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">Fechas</h3>
                </div>
                <div class="flex flex-col gap-4">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Fecha de inicio *</label>
                        <input type="date" name="fecha_inicio" required value="{{ old('fecha_inicio') }}"
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#28628f] transition-colors">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Fecha de fin</label>
                        <input type="date" name="fecha_fin" value="{{ old('fecha_fin') }}"
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#28628f] transition-colors">
                    </div>
                </div>
            </section>

            {{-- Imagen --}}
            <section class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 bg-[#28628f]/10 rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined text-[#28628f] text-[20px]">add_a_photo</span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">Imagen del Evento</h3>
                </div>

                <div onclick="document.getElementById('imagen-evento-input').click()"
                     class="border-2 border-dashed border-slate-200 rounded-xl p-8 flex flex-col items-center justify-center bg-slate-50 hover:bg-slate-100 transition-colors cursor-pointer group mb-4">
                    <span class="material-symbols-outlined text-[#28628f] text-[36px] mb-2 group-hover:scale-110 transition-transform">upload_file</span>
                    <p class="text-sm font-semibold text-slate-700">Subí la imagen del evento</p>
                    <p class="text-xs text-slate-400 mt-1">JPG, PNG — Máx 10MB</p>
                </div>
                <input type="file" id="imagen-evento-input" name="imagen" accept="image/*"
                       class="hidden" onchange="previewImagenEvento(this)">

                <div id="imagen-preview" class="hidden">
                    <img id="imagen-preview-img" src="" class="w-full rounded-xl object-cover h-40" alt="Preview">
                </div>
                @error('imagen') <p class="text-rose-500 text-xs mt-2">{{ $message }}</p> @enderror
            </section>

            {{-- Publicar --}}
            <section class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 bg-[#28628f]/10 rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined text-[#28628f] text-[20px]">visibility</span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800">Publicación</h3>
                </div>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="activo" value="1" checked
                           class="w-5 h-5 rounded border-slate-300 text-[#28628f] focus:ring-[#28628f]">
                    <div>
                        <p class="text-sm font-bold text-slate-700">Publicar inmediatamente</p>
                        <p class="text-xs text-slate-400">El evento será visible para todos los usuarios</p>
                    </div>
                </label>
            </section>

        </div>
    </div>
</form>

<script>
function previewImagenEvento(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagen-preview-img').src = e.target.result;
            document.getElementById('imagen-preview').classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

@endsection