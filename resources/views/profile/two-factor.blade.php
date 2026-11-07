@extends('layouts.app')

@section('title', 'Autenticación de dos factores - Surify')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Encabezado --}}
    <div>
        <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-[#28628f] hover:underline text-decoration-none mb-3">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            Volver al perfil
        </a>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-2" style="font-family: 'Inter', sans-serif;">
            <span class="material-symbols-outlined text-[#28628f] text-[32px]" style="font-variation-settings: 'FILL' 1;">verified_user</span>
            Autenticación de dos factores
        </h1>
        <p class="text-sm text-slate-500 mt-2 leading-relaxed">
            Además de tu contraseña, te vamos a pedir un código de 6 dígitos que genera una app en tu celular.
            Aunque alguien consiga tu contraseña, no va a poder entrar sin ese código.
        </p>
    </div>

    {{-- Códigos de recuperación recién generados (se muestran una sola vez) --}}
    @if(!empty($codigosNuevos))
    <section class="bg-amber-50 border border-amber-200 rounded-2xl p-6">
        <h2 class="text-lg font-bold text-amber-800 flex items-center gap-2 mb-1">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">key</span>
            Tus códigos de recuperación
        </h2>
        <p class="text-sm text-amber-700 leading-relaxed mb-4">
            Guardalos en un lugar seguro <strong>ahora</strong>: no se vuelven a mostrar. Si perdés el celular, cada código te deja entrar una sola vez.
        </p>
        <div class="grid grid-cols-2 gap-2 mb-4">
            @foreach($codigosNuevos as $codigo)
            <code class="bg-white border border-amber-200 rounded-lg px-3 py-2 text-center font-mono text-sm font-bold text-slate-800 select-all">{{ $codigo }}</code>
            @endforeach
        </div>
        <button type="button" id="btn-copiar-codigos"
            class="bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold px-4 py-2 rounded-lg transition-all cursor-pointer border-none inline-flex items-center gap-1">
            <span class="material-symbols-outlined text-[16px]">content_copy</span>
            <span id="btn-copiar-texto">Copiar todos</span>
        </button>
    </section>
    @endif

    @if($activado)
    {{-- ==================== YA ACTIVADO ==================== --}}
    <section class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-emerald-600 text-[26px]" style="font-variation-settings: 'FILL' 1;">check_circle</span>
            </div>
            <div>
                <p class="font-bold text-slate-800">Está activado</p>
                <p class="text-sm text-slate-500">
                    Te quedan <strong class="text-slate-700">{{ $codigosRestantes }}</strong> código(s) de recuperación sin usar.
                </p>
            </div>
        </div>
    </section>

    <section class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
        <h2 class="text-lg font-bold text-slate-800 mb-1">Generar códigos de recuperación nuevos</h2>
        <p class="text-sm text-slate-500 mb-4">Los códigos anteriores dejan de funcionar. Te pedimos tu contraseña para confirmar que sos vos.</p>
        <form method="POST" action="{{ route('two-factor.recovery') }}" class="flex flex-col sm:flex-row gap-3">
            @csrf
            <div class="flex-1">
                <input type="password" name="current_password" required autocomplete="current-password" placeholder="Tu contraseña actual"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#28628f] transition-colors">
                @error('current_password', 'regenerar')
                <p class="text-sm text-rose-600 mt-2 font-semibold">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit"
                class="border border-[#28628f] text-[#28628f] font-bold px-5 py-3 rounded-xl hover:bg-blue-50 transition-all cursor-pointer bg-transparent">
                Generar nuevos
            </button>
        </form>
    </section>

    <section class="bg-red-50 border border-red-100 rounded-2xl p-6">
        <h2 class="text-lg font-bold text-red-600 mb-1">Desactivar la verificación en dos pasos</h2>
        <p class="text-sm text-red-500 mb-4">Tu cuenta va a quedar protegida solo con la contraseña.</p>
        <form method="POST" action="{{ route('two-factor.disable') }}" class="flex flex-col sm:flex-row gap-3">
            @csrf
            @method('DELETE')
            <div class="flex-1">
                <input type="password" name="current_password" required autocomplete="current-password" placeholder="Tu contraseña actual"
                    class="w-full bg-white border border-red-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-red-400 transition-colors">
                @error('current_password', 'desactivar')
                <p class="text-sm text-rose-600 mt-2 font-semibold">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit"
                class="bg-red-500 hover:bg-red-600 text-white font-bold px-5 py-3 rounded-xl transition-all cursor-pointer border-none">
                Desactivar
            </button>
        </form>
    </section>

    @else
    {{-- ==================== ACTIVAR ==================== --}}
    <section class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-6">

        <div class="flex gap-4">
            <div class="w-8 h-8 rounded-full bg-[#28628f] text-white flex items-center justify-center font-bold text-sm shrink-0">1</div>
            <div>
                <h2 class="font-bold text-slate-800">Instalá una app autenticadora</h2>
                <p class="text-sm text-slate-500 mt-1 leading-relaxed">
                    Por ejemplo Google Authenticator, Microsoft Authenticator o Authy (están en Play Store y App Store).
                </p>
            </div>
        </div>

        <div class="flex gap-4">
            <div class="w-8 h-8 rounded-full bg-[#28628f] text-white flex items-center justify-center font-bold text-sm shrink-0">2</div>
            <div class="flex-1">
                <h2 class="font-bold text-slate-800">Escaneá este código QR</h2>
                <p class="text-sm text-slate-500 mt-1 mb-4">Desde la app, elegí "Agregar cuenta" y apuntá la cámara.</p>
                <div class="flex flex-col sm:flex-row items-center gap-6">
                    <div class="bg-white p-3 border border-slate-200 rounded-xl shadow-sm shrink-0">
                        <img src="{{ $qr }}" alt="Código QR para activar la verificación en dos pasos" width="220" height="220" class="block">
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">¿No podés escanear? Ingresá esta clave a mano</p>
                        <code class="block bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 font-mono text-sm font-bold text-slate-800 select-all break-all">{{ chunk_split($secreto, 4, ' ') }}</code>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex gap-4">
            <div class="w-8 h-8 rounded-full bg-[#28628f] text-white flex items-center justify-center font-bold text-sm shrink-0">3</div>
            <div class="flex-1">
                <h2 class="font-bold text-slate-800">Confirmá con el código de 6 dígitos</h2>
                <p class="text-sm text-slate-500 mt-1 mb-4">Ingresá el código que muestra la app para terminar de activarlo.</p>
                <form method="POST" action="{{ route('two-factor.confirm') }}" class="flex flex-col sm:flex-row gap-3">
                    @csrf
                    <div class="flex-1">
                        <input type="text" name="code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" required autocomplete="one-time-code" placeholder="123456"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-center text-xl tracking-[0.3em] font-bold text-slate-800 focus:outline-none focus:border-[#28628f] transition-colors">
                        @error('code')
                        <p class="text-sm text-rose-600 mt-2 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit"
                        class="bg-[#28628f] text-white font-bold px-6 py-3 rounded-xl hover:bg-[#1a4669] transition-all shadow-sm cursor-pointer border-none">
                        Activar
                    </button>
                </form>
            </div>
        </div>
    </section>
    @endif

</div>

@if(!empty($codigosNuevos))
<script>
    document.getElementById('btn-copiar-codigos').addEventListener('click', function () {
        const codigos = @json($codigosNuevos);
        navigator.clipboard.writeText(codigos.join('\n')).then(function () {
            document.getElementById('btn-copiar-texto').textContent = '¡Copiados!';
        }).catch(function () {
            document.getElementById('btn-copiar-texto').textContent = 'Seleccionalos y copialos a mano';
        });
    });
</script>
@endif
@endsection