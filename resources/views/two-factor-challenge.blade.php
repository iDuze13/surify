<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verificación en dos pasos - Surify</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Outfit', sans-serif; }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            line-height: 1;
        }
    </style>
</head>

<body class="min-h-screen bg-slate-50 text-slate-800 antialiased flex items-center justify-center px-4 py-10">

    <div class="w-full max-w-md">

        {{-- Logo --}}
        <div class="flex justify-center mb-6">
            <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-xl border border-slate-200 shadow-sm select-none">
                <span class="material-symbols-outlined text-[26px] text-[#28628f]" style="font-variation-settings: 'FILL' 1;">explore</span>
                <span class="text-xl font-black text-[#191c1d] tracking-tighter" style="font-family: 'Inter', sans-serif;">Surify</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl p-8">

            <div class="flex flex-col items-center text-center mb-6">
                <div class="w-14 h-14 rounded-full bg-[#28628f]/10 flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-[#28628f] text-[30px]" style="font-variation-settings: 'FILL' 1;">verified_user</span>
                </div>
                <h1 class="text-2xl font-black tracking-tight text-slate-800" style="font-family: 'Inter', sans-serif;">Verificación en dos pasos</h1>
                <p class="text-sm text-slate-500 mt-2 leading-relaxed">
                    Abrí tu app autenticadora e ingresá el código de 6 dígitos de <strong class="text-slate-700">Surify</strong>.
                </p>
            </div>

            <form method="POST" action="{{ route('two-factor.challenge.store') }}" id="form-2fa" class="space-y-4">
                @csrf

                <div>
                    <label for="code" class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-2">Código</label>
                    <input id="code" name="code" type="text" inputmode="text" autocomplete="one-time-code"
                        autofocus required maxlength="32" placeholder="123456"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-center text-2xl tracking-[0.3em] font-bold text-slate-800 focus:outline-none focus:border-[#28628f] transition-colors">
                    @error('code')
                    <p class="text-sm text-rose-600 mt-2 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full bg-[#28628f] text-white font-bold px-6 py-3 rounded-xl hover:bg-[#1a4669] transition-all shadow-sm cursor-pointer">
                    Verificar
                </button>
            </form>

            <p class="text-xs text-slate-400 mt-5 leading-relaxed text-center">
                ¿Perdiste el celular? Ingresá en el mismo campo uno de tus
                <strong class="text-slate-500">códigos de recuperación</strong> (formato <code>xxxxx-xxxxx</code>). Cada uno sirve una sola vez.
            </p>

            <div class="border-t border-slate-100 mt-6 pt-5 text-center">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm font-semibold text-slate-500 hover:text-[#28628f] transition-colors cursor-pointer bg-transparent border-none">
                        Cancelar y cerrar sesión
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Envía solo cuando se completan exactamente 6 dígitos (los códigos de recuperación no se autoenvían)
        document.getElementById('code').addEventListener('input', function () {
            if (/^\d{6}$/.test(this.value.trim())) {
                document.getElementById('form-2fa').submit();
            }
        });
    </script>
</body>

</html>