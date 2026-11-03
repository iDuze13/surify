@extends('layouts.admin')

@section('title', 'Surify - Enviar Anuncio')

@section('content')

<div class="mb-8">
    <h1 class="text-4xl font-black text-slate-900 tracking-tight" style="font-family: 'Inter', sans-serif;">Enviar Anuncio</h1>
    <p class="text-slate-500 mt-2 text-sm max-w-2xl">El mensaje se envía a todos los usuarios registrados: les aparece en la campanita de notificaciones y por correo.</p>
</div>

@if($errors->any())
    <div class="mb-4 p-4 bg-rose-50 border border-rose-200 rounded-xl">
        @foreach($errors->all() as $error)
            <p class="text-rose-600 text-sm">⚠️ {{ $error }}</p>
        @endforeach
    </div>
@endif

<form method="POST" action="{{ route('admin.anuncios.store') }}" class="space-y-6">
    @csrf

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-5">
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Título *</label>
            <input type="text" name="titulo" value="{{ old('titulo') }}" required maxlength="150"
                placeholder="Ej: Nueva función disponible"
                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:ring-[#28628f] focus:border-[#28628f]">
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Mensaje *</label>
            <textarea name="mensaje" rows="5" required maxlength="1000"
                placeholder="Escribí el contenido del anuncio..."
                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:ring-[#28628f] focus:border-[#28628f]">{{ old('mensaje') }}</textarea>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Enlace (opcional)</label>
            <input type="text" name="url" value="{{ old('url') }}"
                placeholder="https://... o una ruta interna, ej: /eventos"
                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-700 focus:ring-[#28628f] focus:border-[#28628f]">
            <p class="text-xs text-slate-400 mt-1">Si lo completás, el botón "Ver más" del email y el click en la notificación llevan ahí.</p>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-2">
        <button type="submit"
            class="w-full sm:w-auto px-10 py-3 rounded-full bg-[#28628f] text-white font-bold text-sm hover:bg-[#1a4669] transition-all shadow-sm flex items-center justify-center gap-2">
            <span class="material-symbols-outlined text-[18px]">campaign</span>
            Enviar Anuncio
        </button>
    </div>
</form>

@endsection